<?php
/**
 * Description of Utility
 * in questa classe sono presenti delle funzioni di
 * utilizzo generale
 *
 * @author francesco
 */
Class Utility {
    
    
      public static function formattaDataOra($data){
          $ret=null;
          $data=explode("-",$data);
          $data_new = explode(" ",$data[2]);
          $time = mktime(0, 0, 0, $data[1], $data_new[0], $data[0]);
          $ora = $data_new[1];
          $ret = date("d/m/Y", $time);
          $ret .= ", ".$ora;
          return $ret;
      }    
    
}




?>
