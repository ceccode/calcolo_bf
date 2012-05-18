<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *  Validator per la partita iva
 */
class CV_Validate_isValidPIVA extends Zend_Validate_Abstract {
    
    //Validator code
    const PIVA = 'piva';
    
    //messaggio di ritotno nel caso il controllo non sia valido
    protected $_messageTemplates = array(
        self::PIVA => "'%value%' non è un formato valido."
    );
    
    public function isValid($value)
    {
        $this->_setValue($value);
 
        $isValid = true;
        
        if( $pi == '' ){ 
            $this->_error(self::PIVA);
            return false;
        }	
        
        if( strlen($pi) != 11 ){ 
            $this->_error(self::PIVA);            
            return false;
        }
        if( ! ereg("^[0-9]+$", $pi) ){ 
            $this->_error(self::PIVA);            
            return false;
        }
	
        $s = 0;
	for( $i = 0; $i <= 9; $i += 2 )
	
        $s += ord($pi[$i]) - ord('0');
	for( $i = 1; $i <= 9; $i += 2 )
        {
            $c = 2*( ord($pi[$i]) - ord('0') );
            if( $c > 9 )  $c = $c - 9;
            $s += $c;
        }	
            
        if( ( 10 - $s%10 )%10 != ord($pi[10]) - ord('0') ){
            $this->_error(self::PIVA);            
            return false;
        }		
            
	return $isValid;
 
    }
    
    
}
?>
