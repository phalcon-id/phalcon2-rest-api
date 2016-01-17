<?php

/* ==================================================
 * ลงทะเบียน Modules
 * Register application modules
 * ================================================== */

$config = $this->config;   // Read the configuration
$addModule = explode(',',$config->module->moduleLists);

$modules = array();
foreach ($addModule as $recode) {
    $modules[$recode] = array(
        'className' => ucfirst($recode) . "\Module",
        'path'      => APPLICATION_PATH . '/modules/' . $recode . '/Module.php',
    );
}
$this->registerModules($modules);