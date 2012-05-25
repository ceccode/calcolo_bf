<?php

class Application_Model_DbTable_UModinterv extends Application_Model_DbTable_TabellaComuni
{
    public function getModIntByUrbanizzazione($q) {
        
        $righe = null;
   
        if ($this->getName()) {

            $select = $this->select()
                           ->from($this->getName(), array('id_u_modinterv', 'descrizione_estesa'))
                           ->where('record_attivo = 1 AND area_urbanizzata = ?', $q)
                           ->order("id_u_modinterv");           
                               
            
            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getModIntByUrbanizzazione");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }
    
        public function getAll($id_u_modinterv ){
        
        $righe = null;
   
        if ($this->getName()) {

            $select = $this->select()
                           ->where('record_attivo = 1 AND id_u_modinterv  = ?', $id_u_modinterv );
                              
            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getAll di UModinterv");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }
}
?>
