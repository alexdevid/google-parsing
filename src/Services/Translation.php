<?php
namespace Services;

/**
 * https://tech.yandex.ru/translate/doc/dg/concepts/langs-docpage/
 *
 * Class Translate
 * @package Services
 */
class Translation {

    /**
     * @var string
     */
    private $baseUrl = '';

    /**
     * @var string
     */
    private $result = NULL;

    public function __construct($string, $lang) {
        $config = \Kernel::getInstance()->config['translate'];
        $this->baseUrl = $config['baseUrl'];
        $this->baseUrl .= 'key=' . $config['key'];
        $this->baseUrl .= '&text=' . $string;
        $this->baseUrl .= '&lang=' . $lang;
        $this->sendRequest();
    }

    private function sendRequest() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $res = json_decode($result);
        if ($res->code == 200) {
            $this->result = $res->text[0];
        }
        curl_close($ch);
        return $this;
    }

    public function getResult() {
        return $this->result;
    }

    public function __toString() {
        return $this->getResult();
    }
}