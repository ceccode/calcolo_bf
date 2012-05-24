<?php

class Application_Model_DbTable_Log extends Application_Model_DbTable_TabellaComuni 
{

    /**
     * creo un record di log nel db
     * 
     * @param type $nome
     * @param type $cognome
     * @param type $cf 
     */
    public function inserisciLog($nome, $cognome, $cf){
        
        $nome_tab = $this->getName();
        
        if ($nome_tab) {

            $data = array(
                        'nome'           => $nome,
                        'cognome'        => $cognome,
                        'codice_fiscale' => $cf,
                        'data'           => new Zend_Db_Expr('NOW()')
            );            

            $insert = $this->insert($data);
            
            return $insert;
        }else
            throw new Exception("Errore in inserisciLog: nome tabella non trovato!");   
    }
}
?>