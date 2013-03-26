<?php

class Application_Model_DbTable_USambiti extends Application_Model_DbTable_TabellaComuni {

    public function getSubAmbitiByMacroAmbito($id_macro_ambito, $data) {

        $righe = null;

        if ($this->getName()) {

            $select = $this->select()
                    ->from($this->getName(), array('id_u_sambiti', 'descrizione'))
                    ->where('record_attivo = 1 AND id_u_mambiti = ?', $id_macro_ambito)
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data)
                    ->order("numero_subambito");

            $righe = $this->fetchAll($select);
            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getSubAmbitiByMacroAmbito");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

    public function getVolumetria($id_sub_ambito, $data) {

        $righe = null;

        if ($this->getName()) {

            $select = $this->select()
                    ->from($this->getName(), array('indice_calcolo_capacita_edificatoria'))
                    ->where('record_attivo = 1 AND id_u_sambiti = ?', $id_sub_ambito)
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data)
                    ->order("id_u_sambiti");

            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getVolumetria");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

    public function getAll($id_u_sambiti, $data) {

        $righe = null;

        if ($this->getName()) {

            $select = $this->select()
                    ->where('id_u_sambiti = ?', $id_u_sambiti)
                    ->where('record_attivo = 1')
                    // ->where('id_u_mambiti = ?', $id_u_mambiti)
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data)
                    ->order('id_u_mambiti');

            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getAll di uSambiti");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

}

?>
