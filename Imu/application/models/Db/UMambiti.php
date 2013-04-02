<?php

class Application_Model_DbTable_UMambiti extends Application_Model_DbTable_TabellaComuni {

    public function getSpqVcu($id_u_mambiti, $data) {

        $righe = null;

        if ($this->getName()) {

            $select = $this->select()
                    ->from($this->getName(), array('standard_pubblico_qualita', 'valore_comprensativo_unitario'))
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data)
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

    public function getPdrODdp($id_u_mambiti, $data) {

        $righe = null;

        if ($this->getName()) {

            $select = $this->select()
                    ->from($this->getName(), array('pdr_o_ddp'))
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data)
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

    public function getAll($id_u_mambiti, $data) {

        $righe = null;

        if ($this->getName()) {

            $select = $this->select()
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data)
                    ->where('record_attivo = 1 AND id_u_mambiti = ?', $id_u_mambiti)
                    ->order("numero_ambito");

            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getAll di uMambiti");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

}

?>
