<?php

class Application_Model_DbTable_STsima extends Application_Model_DbTable_TabellaComuni
{
    /**
     * Ritorna il fattore conversione standard qualità per il calcolo stima
     * dato il riferimento
     * 
     * @param type $riferimento
     * @return type 
     */
     public function getFcspq($riferimento) {
        
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
 
    
    /**
     * Ritorna id fattore conversione per il calcolo stima
     * dato l'id s_tstima
     * @param type $riferimento
     * @return type 
     */
     public function getFc($id_s_tstima) {
        
        $righe = null;
   
        if ($this->getName()) {

            $select = $this->select()
                           ->where('record_attivo = 1 AND id_s_tstima = ?', $id_s_tstima);
                              
            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getFc");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }
}
?>
