<?php

class Application_Model_DbTable_STsima extends Application_Model_DbTable_TabellaComuni
{
    /**
     * Ritorna id dati per il calcolo stima
     * 
     * @param type $riferimento
     * @return type 
     */
     public function getFcFcspq($riferimento) {
        
        $righe = null;
   
        if ($this->getName()) {

            $select = $this->select()
                           ->where('record_attivo = 1 AND riferimento = ?', strtoupper($riferimento));
                              
            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getFcFcspq");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }
    
}
?>
