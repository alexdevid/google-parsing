<?php

use Slim\Views\Twig;
use Components\Route;
use Slim\Views\TwigExtension;
use Symfony\Component\Yaml\Yaml;

class Kernel {

    /**
     * @var \Slim\Slim
     */
    public $app;

    /**
     * @var \Predis\Client
     */
    public $db;

    /**
     * @var array
     */
    public $config;

    /**
     * @var array
     */
    public $routes;

    /**
     * @return $this
     */
    public function run() {
        $this->routes = Yaml::parse(file_get_contents(__DIR__ . '/../config/routes.yml'))['routes'];
        $this->config = Yaml::parse(file_get_contents(__DIR__ . '/../config/config.yml'));
        $this->app = new \Slim\Slim([
            'view' => new Twig()
        ]);
        $this->app->view()->parserOptions = [
            'debug' => true
        ];
        $this->app->view()->parserExtensions = [
            new TwigExtension(),
            new \Twig_Extensions_Extension_Text(),
            new \Twig_Extensions_Extension_Array(),
            new \Twig_Extensions_Extension_Date(),
            new \Twig_Extensions_Extension_I18n(),
            new \Twig_Extensions_Extension_Intl(),
        ];
        $this->app->view()->setTemplatesDirectory(__DIR__ . '/Views/');
        $this->instantiateRoutes()->app->run();
        return $this;
    }

    /**
     * @return $this
     */
    private function instantiateRoutes() {
        foreach ($this->routes as $routeParams) {
            $route = new Route($routeParams);
            $method = $route->method;
            $this->app->$method($route->path, function () use ($route) {
                $arguments = func_get_args();
                call_user_func_array([
                    $route->controller,
                    $route->action
                ], $arguments);
                $this->app->stop();
            });
        }
        return $this;
    }

    /**
     * @return Kernel
     */
    public static function getInstance() {
        static $inst = null;
        if ($inst === null) {
            $inst = new Kernel();
        }
        return $inst;
    }

    public function getRootDir() {
        return __DIR__ . '/../';
    }

    private function __construct() {

    }
}