<?php

/* =====================================================
 * โหลดอัตโนมัติ
 * ลงทะเบียนโฟล์เดอร์
 * ===================================================== */
$loader = new \Phalcon\Loader();
$loader->registerDirs(
    array(
        // เพิ่มเติม
        APPLICATION_PATH . $this->config->application->componentsDir, 
        APPLICATION_PATH . $this->config->application->componentsLibraryDir, 
        APPLICATION_PATH . $this->config->application->modelsDir,
        APPLICATION_PATH . $this->config->application->libraryDir,
        APPLICATION_PATH . $this->config->application->pluginsDir
    )
)->register();
