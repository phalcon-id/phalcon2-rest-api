<?php

use Phalcon\Mvc\Router\Group;

class ExampleRouter extends Group {
    
    private $moduleDefault = 'example';
    private $controllerDefault;
    private $actionDefault;
    
    public function __construct($config) {
        $this->controllerDefault = $config->router->controllerDefault;
        $this->actionDefault     = $config->router->actionDefault;
        parent::__construct();
    }
    
    public function initialize(){
        
        // Default paths
        $this->setPaths(array(
            'module' => $this->moduleDefault,
        ));
        
        $this->setPrefix('/' . $this->moduleDefault);
         
        $this->add('', array(
            'module'        => $this->moduleDefault,
            'controller'    => $this->controllerDefault,
            'action'        => $this->actionDefault
        ));

        $this->add('/', array(
            'module'        => $this->moduleDefault,
            'controller'    => $this->controllerDefault,
            'action'        => $this->actionDefault
        ));
        
    }
    
    
    
}