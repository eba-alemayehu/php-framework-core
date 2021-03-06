<?php

namespace Application;

use App\Http\Controllers;
use App\Commander\Middlewares;
use Application\Http\Router;

session_start();
define("APPLICATION_ROOT", __DIR__."/../../../");
class Kernel{
    private $router = null; 

    public function __construct(){
        $this->init();
        $this->run();
    }

    private function init(){
        $config = require_once (APPLICATION_ROOT."config/app.config.php");
        $this->router = new Http\Router; 

        ini_set("display_errors", ($config['debug'])?1:0);
        ini_set("error_log", APPLICATION_ROOT."/storage/logs/".date("d-m-y").".log");
    }
    public function run(){
        if($this->router->route === null){
            http_response_code(404);
            require_once (APPLICATION_ROOT."app/Resource/views/error/404.php");
            die();
        }
        
        $middelwares = [];

        foreach($this->router->route->middlewares as $middelware){
            $m = "\\App\\Http\\Middlewares\\".$middelware;
            $m_obj = new $m();
            $m_obj->before();
            array_push($middelwares, $m_obj);
        }

        $controller = $this->router->route->controllerClass();
        $method = $this->router->route->controllerMethod();

        $response = null;
        $runable = new $controller;
        // $runable_method = $runable->$method();
        $runable_method = call_user_func_array(array($runable, $method), array_map(function($param){
            return $param->value; 
        }, $this->router->route->params(Router::url())));

        if(is_object($runable_method)){
            if(get_class($runable_method) == "Application\Support\View"){
                $response = $runable_method->show();
            }else{
                $response = json_encode($runable_method);
            }
        }else{
            $response = json_encode($runable_method);
        }

        foreach($middelwares as $middelware){
            $middelware->after($response);
        }
        
        if(is_string($response))
            echo $response;
    }
}
