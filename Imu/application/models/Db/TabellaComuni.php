<?php

class Application_Model_DbTable_TabellaComuni extends Zend_Db_Table_Abstract {

    protected $_name = null;
    // comune associato
    private $comune = null;

    public function setComune($comune) {
        $this->comune = $comune;
    }

    public function getComune() {
        return $this->comune;
    }

    public function getName() {
        return $this->_name;
    }

    public function setName($name) {
        $this->_name = $name;
    }

}

?>
