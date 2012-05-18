<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  Validator per il codice fiscale
 */
class CV_Validate_isValidDate extends Zend_Validate_Abstract {
    //Validator code
    const Date = 'Date';

    //messaggio di ritotno nel caso il controllo non sia valido
    protected $_messageTemplates = array(
        self::Date => "'%value%' non è una data valida."
    );

    public function isValid($value) {
        $this->_setValue($value);

        $isValid = true;

        // timestamp
        $timestamp = time();
        // data di oggi
        $today = mktime(0, 0, 0, date("m", $timestamp), date("d", $timestamp), date("Y", $timestamp));
        $today=date("Y-m-d", $today);
        echo $today . ' ' . $value;
        if ($value >= $today) {
            $this->_error(self::Date);
            return false;
        }
        return $isValid;
    }

}

?>
