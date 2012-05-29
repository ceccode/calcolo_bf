<?php

class Application_Model_DbTable_SZone extends Application_Model_DbTable_TabellaComuni {

    public function getAll($id_s_zone,$data) {

        $righe = null;

        if ($this->getName()) {

            $select = $this->select()
                    ->where('record_attivo = 1 AND id_s_zone = ?', $id_s_zone)
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine > ? OR data_fine = 0000-00-00', $data);

            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getAll di Szone");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

}

?>
