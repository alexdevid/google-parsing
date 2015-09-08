<?php
namespace Services;

use Symfony\Component\Yaml\Yaml;

class Search {

    /**
     * @var string
     */
    private $baseUrl = '';

    /**
     * @var string
     */
    public $body;

    /**
     * @var string
     */
    private $localUrl;

    /**
     * @var array
     */
    private $countries;

    /**
     * Results number
     *
     * @var int
     */
    private $num = 50;

    public function __construct() {
        $config = \Kernel::getInstance()->config['search'];
        $params = Yaml::parse(file_get_contents(\Kernel::getInstance()->getRootDir() . 'config/search.yml'));
        $this->countries = $params['countries'];
        $this->localUrl = $config['localUrl'];
        $this->baseUrl = $config['baseUrl'];
        $this->baseUrl .= '&key=' . $config['key'] . '&cx=' . $config['cx'];
        $this->baseUrl .= '&num=' . $this->num;
        $this->baseUrl .= (isset($config['fields']) || $config['fields']) ? '&fields=' . $config['fields'] : '';
    }

    public function translateQuery($query) {
        $translate = new Translate($query);

    }

    public function sendRequests($query) {
        $data = [];
        foreach ($this->countries as $countryName => $codes) {
            $cr = '&cr=country' . $codes[0];
            foreach ($codes[1] as $langCode) {
                $translation = new Translation($query, $langCode);
                $data[] = $translation->getResult();
            }
        }
        return $data;
    }

    /**
     * TODO query as array of query object
     *
     * @param string $query
     * @return $this
     */
    public function sendRequest($query) {
        $url = $this->baseUrl . '&q=' . $query;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $this->localUrl);
        $this->body = curl_exec($ch);
        curl_close($ch);
        return $this;
    }

    /**
     * @return array
     */
    public function getBody() {
        $response = json_decode($this->body);
        $data = [];
        foreach ($response->items as $item) {
            if (isset($item->pagemap->organization)) {
                $orgs = [];
                foreach ($item->pagemap->organization as $org) {
                    if (isset($org->telephone) || isset($org->email) || isset($org->faxNumber)) {
                        $orgs[] = $org;
                    }
                }
                if (!empty($orgs)) {
                    $data[] = [
                        'htmlTitle' => $item->htmlTitle,
                        'link' => $item->link,
                        'displayLink' => $item->displayLink,
                        'organizations' => $orgs
                    ];
                }
            }
        }
        return $data;
    }
}