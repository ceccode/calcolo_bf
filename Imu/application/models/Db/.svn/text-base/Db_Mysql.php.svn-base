<?php
/**
 * Questa classe fornisce i metodi generici che saranno poi esteri
 * per l'adapter di ogni classe
 */
class Db_Mysql {
    //put your code here
    public static function conn(){

        $connParams= array("host" => "127.0.0.1",
            "port" => "3306",
            "username" => "root",
            "password" => "",
            "dbname" => "imu");

        $db = new Zend_Db_Adapter_Pdo_Mysql($connParams);

        return $db;
    }    
}
?>
