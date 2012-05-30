<?php


class Application_Model_DbTable_Anni extends Application_Model_DbTable_TabellaComuni
{
    public function getAnni(){
        
        $righe = null;
   
        if ($this->getName()) {

            $select = $this->select()
                           ->from($this->getName(), 'anno')
                           ->where('record_attivo = 1');
                              
            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getAnni");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    
    }

}
?>
