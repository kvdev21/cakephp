<?php
require_once(ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'Kaleidovision' . DS . 'Runtime.php');

$config = array(
    'KVRuntime' => array(
        'os' => Runtime::getOs(),
        'invokeMethod' => Runtime::getInvokeMethod(),
        'ip' => Runtime::getIp(),
        'url' => Runtime::getUrl(),
        'environment' => Runtime::getEnvironment(),
        'databases' => Runtime::getDatabases(),
        'filePath' => Runtime::getFilePath(),
        'emailConfig'=> Runtime::getEmailConfig()
    )
);