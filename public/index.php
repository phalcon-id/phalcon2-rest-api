<?php

error_reporting(E_ALL);
date_default_timezone_set('Asia/Bangkok');

use Phalcon\Mvc\Application as WebApplication,
    Phalcon\DI\FactoryDefault as ApplicationManager,
    Phalcon\Config\Adapter\Ini as ConfigIni,
    Phalcon\Debug as ModeDebug;

class Application extends WebApplication {
    
    private $config;        // การตั้งค่า Config | by Object Array
    private $pathConfig;    // Path ไฟล์ Config
    
    // ทำงานอัตโนมัติ
    public function __construct() {
        $this->pathConfig   = APPLICATION_PATH . '/commons';
        $this->config = new ConfigIni($this->pathConfig . '/config/main.ini');   // Read the configuration
    }   
    
    /* ลงทะเบียน (Register Services)*/
    private function _registerServices(){
        $debug = new ModeDebug();
        $debug->listen(Phalcon_Debug);
        $manager = new ApplicationManager();
        include_once $this->pathConfig . '/autoloader.php';     // Read auto-loader
        include_once $this->pathConfig . '/routers.php';         // Read Router
        include_once $this->pathConfig . '/services.php';        // Read services
        $this->setDI($manager);
    }
    
    /* แสดงเว็บแอพพลิเคชั่น (Run Web Application) */
    public function run() {
        try { 
            $this->_registerServices(); 
            include_once $this->pathConfig . '/modules.php'; // read modules
            echo $this->handle()->getContent();
        } catch(\Phalcon\Exception $e) {
            echo 'PhalconException: ' . $e->getMessage();
        } catch (\Exception $e) {
            echo 'PhpException: ' . $e->getMessage();
        }
    }
    
}

/* ==================================================
 * กำหนดค่า Default (ค่าเริ่มต้น)
 * ================================================== */

// Path Root (เริ่มต้น)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__FILE__)));
    define('APPLICATION_PATH', ROOT_PATH . '/application');
}

define('Phalcon_Debug', true);  // ต้องการแสดง Debug หรือไม่ (true,false)
define('DevMode', true);        // อยู่ในโหมดพัฒนา หรือไม่ (true,false)

/* ==================================================
 * ลงทะเบียนและแสดงเว็บแอพพลิดเคชั่นยน Web Browser
 * ================================================== */

$application = new Application();
$application->run();