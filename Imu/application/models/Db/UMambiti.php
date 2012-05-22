<?php

class Application_Model_DbTable_UMambiti extends Application_Model_DbTable_TabellaComuni
{
    
   public function getSpqVcu($id_u_mambiti) {
        
        $righe = null;
   
        if ($this->getName()) {

            $select = $this->select()
                           ->from($this->getName(), array('standard_pubblico_qualita', 'valore_comprensativo_unitario'))
                           ->where('record_attivo = 1 AND id_u_mambiti = ?', $id_u_mambiti);
                              
            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getSpqVcu");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }
    
    public function getPdrOPdp($id_u_mambiti){
        
        $righe = null;
   
        if ($this->getName()) {

            $select = $this->select()
                           ->from($this->getName(), array('prd_o_ddp'))
                           ->where('record_attivo = 1 AND id_u_mambiti = ?', $id_u_mambiti);
                              
            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getDoc");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

}
?>
