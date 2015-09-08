<?php
namespace Controllers;

use Components\Controller;
use Services\Search;

class DefaultController extends Controller {

    public function homepage() {
        $keywords = ['trucks', "buy", "sell", "maintenance", "repair", "tractor", "lorry", "demolition", "spare", "car", "truck"];
        $keystring = implode(',', $keywords);
        return $this->render('homepage.twig', ['keywords' => $keystring]);
    }

    public function search($query) {
        $api = new Search();
        var_dump($api->sendRequests($query)); die();
        return $this->render('data.twig', ['items' => $api->sendRequest($query)->getBody()]);
    }
}