<?php

// loader per tabelle
require_once APPLICATION_PATH . "/models/Db/TabellaComuni.php";
require_once APPLICATION_PATH . '/models/Db/Factory_dbTable.php';

Zend_Session::start();
 
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    protected function _initAutoload() {
        $autoLoader = Zend_Loader_Autoloader::getInstance();

        $resourceLoader = new Zend_Loader_Autoloader_Resource(array(
                'basePath' => APPLICATION_PATH,
                'namespace' => '',
            ));

        $resourceLoader->addResourceType('validate', 'library/', 'CV_Validate_');

        $autoLoader->pushAutoloader($resourceLoader);

    }  
    
    
}

