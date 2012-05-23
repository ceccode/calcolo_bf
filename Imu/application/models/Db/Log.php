<?php

class Application_Model_DbTable_Log extends Application_Model_DbTable_TabellaComuni 
{
    /**
     * Ritorna la stima unitaria prevista
     * 
     * @param Int $id_subdestinazione identificativo subdestinazione
     * @param Int $id_zona identificativo zona
     * @param String $tipo_stima tipo stima "v" o "u" volumetrica o utilizzazione
     */
    public function inserisciLog($nome, $cognome, $cf){
        
        if (true) { // se ho un nome settato
            
        }
        else
            throw new Exception("Erroe nella registrazione del log.");   
    }
}
?>