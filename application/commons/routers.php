<?php

use Phalcon\Mvc\Router;

/* ==================================================
 * ลงทะเบียน "เส้นทางเว็บแอพพลิเคชั่น" (Router)
 * Registering a router
 * ================================================== */

$config = $this->config;   // Read the configuration
        
$manager->set('router', function() use ($config){
    
    $router = new Router();
    $router->setDefaultModule($config->router->moduleDefault);
    $router->setDefaultController($config->router->controllerDefault);
    $router->setDefaultAction($config->router->actionDefault);
    $router->removeExtraSlashes(TRUE);
    
    $addModule = explode(',',$config->module->moduleLists);
    
    foreach ($addModule as $module) {
        $pathModule = APPLICATION_PATH . '/modules/' . $module . '/Router.php';
        $nameModule = ucfirst($module) . 'Router';
        if(file_exists($pathModule)){
            include_once $pathModule;
            $router->mount(new $nameModule($config)); 
        }
    }
    
    return $router;
    
});
