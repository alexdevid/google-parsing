<?php
namespace Controllers;

use Components\Controller;
use Services\Search;

class DefaultController extends Controller {

    public function homepage() {
        $keywords = ['Казахстан', 'грузовики', "покупка", "продажа", "обслуживание", "ремонт", "тягачи", "грузовые авто", "разборка", "запчасти", "автосервис", "грузовиков", "грузовых"];
        $keystring = implode(',', $keywords);

        return $this->render('homepage.twig', ['keywords' => $keystring]);
    }

    public function search($query) {
        $query = str_replace(', ', '%20', $query);
        $query = str_replace(' ', '%20', $query);
        $query = str_replace(',', '%20', $query);
        $api = new Search();
        return $this->render('data.twig', ['items' => $api->sendRequest($query)->getBody()]);
    }
}