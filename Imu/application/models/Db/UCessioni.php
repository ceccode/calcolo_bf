<?php

class Application_Model_DbTable_UCessioni extends Application_Model_DbTable_TabellaComuni {

    /**
     * Questo metodo ritorna la quantità di cessione dato il macroambito la modalità di 
     * intervento e la subdestinazione
     *
     * @param type $id_u_mambiti
     * @param type $id_u_modinterv
     * @param type $id_u_sdestinazioni
     * @return type 
     */
    public function getQuantitaCessione($id_u_mambiti,$id_u_modinterv,$id_u_sdestinazioni) {
        $righe = null;

        if ($this->getName()) {
            // u destammesse
            $select = $this->select();
            $select->from($this->getName(), array("id_u_cessioni","quantita_cessione"))
                   ->where("id_u_mambiti = ?",$id_u_mambiti )
                   ->where("id_u_modinterv = ?", $id_u_modinterv)
                   ->where("id_u_sdestinazioni = ?", $id_u_sdestinazioni)
                   ->where("record_attivo = 1");

            $righe = $this->fetchAll($select);
            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getQuantitaCessione");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

}

?>
