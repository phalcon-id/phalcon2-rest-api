<?php

namespace Main;

use Phalcon\Loader;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\View;
use Phalcon\Mvc\ModuleDefinitionInterface as CreateModule;

//use Multiple\Plugins\SecurityPlugin as SecurityPlugin;

class Module implements CreateModule {
    
    private $moduleName = 'main';
    
    /* ==================================================
     * ลงทะเบียน Module auto-loader
     * Registers the module auto-loader
     * ================================================== */
    
    public function registerAutoloaders(\Phalcon\DiInterface $manager = NULL){
        
        $loader = new Loader();
        $loader->registerNamespaces(array(
            ucfirst($this->moduleName) . '\Controllers'     =>  __DIR__ . '/controllers/',
            'Multiple\Components'                           =>  APPLICATION_PATH . $manager->get('config')->application->componentsDir,
            'Multiple\Models'                               =>  APPLICATION_PATH . $manager->get('config')->application->modelsDir,
            'Multiple\Plugins'                              =>  APPLICATION_PATH . $manager->get('config')->application->pluginsDir,
        ));
        $loader->register();
        
        $manager->set('logger', function () use ($manager){
            $monthNow = date("Y-m-d",time());
            $pathLog = APPLICATION_PATH . $manager->get('config')->application->logsDir . '/' . $this->moduleName . '/' . $monthNow . '.log';
            $logger = new LogFile($pathLog);
            return $logger;
        });
        
    }
    
    /* ==================================================
     * ลงทะเบียนระบบ 
     * Registers the module-only services
     * ================================================== */
    
    public function registerServices(\Phalcon\DiInterface $manager){
       
        // ความปลอดภัย
        $this->setSecurity($manager);
        
        // การแสดงผล
        $this->setView($manager);
        
    }
    
    /* ==================================================
     * ตั้งค่าการแสดงผล, ความปลอดภัย 
     * ================================================== */ 
    
    private function setView($manager){
        
        /* ==================================================
         * ตั้งค่าเรียกใช้งานไฟล์ View ทั้งหมด
         * Setting up the view component
         * ================================================== */
        
        $manager->set('view', function () {
            $view = new View();
            $view->disable();
            return $view;
        });
        
    }
    
    private function setSecurity($manager){
        
        /* ==================================================
         * ตั้งค่าความปลอดภัย 
         * Setting security
         * ================================================== */   
        
        $manager->set('dispatcher', function () use ($manager) {
            $eventsManager = $manager->getShared('eventsManager');
            /*
            $security = new SecurityPlugin($manager);
            $security->setModule($this->moduleName);
            $eventsManager->attach('dispatch', $security);
            */
            $dispatcher = new Dispatcher();
            $dispatcher->setEventsManager($eventsManager);
            $dispatcher->setDefaultNamespace(ucfirst($this->moduleName) . "\Controllers");
            return $dispatcher;
        }); 
        
    }
    
}
