<?php

use Phalcon\Mvc\Url as UrlManager;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Session\Adapter\Files as SessionAdapter;

$config = $this->config; // ดึงข้อมูล Config จากไฟล์ /public/index.php

/* ==================================================
 * กำหนด Url เบื้องต้น
 * ================================================== */

$manager->set('url', function () use ($config) {
    $url = new UrlManager();
    $url->setBaseUri($config->application->baseUri);
    return $url;
}, true);

/* ==================================================
 * ตั้งค่าการเชื่อมต่อฐานข้อมูล
 * ================================================== */

$manager->set('db', function () use ($config) {
    return new DbAdapter(array(
        'host'      => $config->database->host,
        'username'  => $config->database->username,
        'password'  => $config->database->password,
        'dbname'    => $config->database->dbname,
        'options'   => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES ' . $config->database->charset
        )
    ));
});

/* ==================================================
 * ตั้งค่าการเปิดใช้งาน Session
 * ================================================== */

$manager->set('session', function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
});

/* ==================================================
 * กำหนดค่ามาตรฐานข้อมูลของ Phalcon Fraemwork version 2.0.2
 * ================================================== */

// ดึงข้อมูล Config จากไฟล์ public/index.php 
$manager->set('config', function () use ($config) {
    return $config;
}, true);
$manager->set('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/* ==================================================
 * ลงทะเบียน Component & Librarys ที่เราสร้างขึ้นเอง
 * ================================================== */

// ดึงข้อมูลหลัก เช่น ข้อมูลการตั้งค่าต่าง ๆ 
$manager->set('base', function(){
    return new CBaseSystem();
});
// ดึงข้อมูลทั่วไป
$manager->set('main', function(){
    return new CMainSystem();
});