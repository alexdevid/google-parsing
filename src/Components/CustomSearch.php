<?php
namespace Components;


use Symfony\Component\Yaml\Yaml;

class CustomSearch {

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

    public function __construct() {
        $config = Yaml::parse(file_get_contents(\Kernel::getInstance()->getRootDir() . 'config/search.yml'));
        $this->localUrl = $config['localUrl'];
        $this->baseUrl = $config['baseUrl'];
        $this->baseUrl .= '&key=' . $config['key'] . '&cx=' . $config['cx'];
        $this->baseUrl .= (isset($config['fields']) || $config['fields']) ? '&fields=' . $config['fields'] : '';
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
     * @param bool $asArray
     * @return string
     */
    public function getBody($asArray = false) {
        return json_decode($this->body, $asArray);
    }
}