<?php

class Application_Model_DbTable_UCessioni extends Application_Model_DbTable_TabellaComuni {

    /**
     * Questo metodo ritorna la quantità di cessione dato il macroambito la modalità di 
     * intervento e la subdestinazione
     *
     * @param type $id_u_mambiti
     * @param type $id_u_modinterv
     * @param type $id_u_sdestinazioni
     * @param String data
     * @return type 
     */
    public function getQuantitaCessione($id_u_mambiti, $id_u_modinterv, $id_u_sdestinazioni, $data) {
        $righe = null;

        if ($this->getName()) {
            // u destammesse
            $select = $this->select();
            $select->from($this->getName(), array("id_u_cessioni", "quantita_cessione","id_s_tstima"))
                    ->where("id_u_mambiti = ?", $id_u_mambiti)
                    ->where("id_u_modinterv = ?", $id_u_modinterv)
                    ->where("id_u_sdestinazioni = ?", $id_u_sdestinazioni)
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data)
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

    /**
     * Questo metodo ritorna cessione dato il macroambito e la subdestinazione
     *
     * @param type $id_u_mambiti
     * @param type $id_u_sdestinazioni
     * @param int $id_u_modinterv modalità intervento id
     * @patam String $data
     * @return type 
     */
    public function getAll($id_u_mambiti, $id_u_sdestinazioni, $id_u_modinterv, $data) {
        $righe = null;

        if ($this->getName()) {
            // u destammesse
            $select = $this->select();
            $select->where("id_u_mambiti = ?", $id_u_mambiti)
                    ->where("id_u_modinterv = ?", $id_u_modinterv)
                    ->where("id_u_sdestinazioni = ?", $id_u_sdestinazioni)
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data)
                    ->where("record_attivo = 1");

            $righe = $this->fetchAll($select);
            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getAll");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

}

?>
