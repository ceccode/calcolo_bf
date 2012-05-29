<?php

class Application_Model_DbTable_UDestammesse extends Application_Model_DbTable_TabellaComuni {

    /**
     * Questo me
     * 
     * @param type $id_macro_ambiti
     * @return type 
     */
    public function filtroDestinazioniAmmesse($id_macro_ambiti, $data) {
        $righe = null;
        //prendo dbtable u_destammesse
        if ($this->getComune())
            $tabellaJoin = Factory_dbTable::getClass($this->getComune(), "u_sdestinazioni");
        else
            throw new Exception("Nome comune non settato in filtroDestinazioniAmmesse");

        if ($this->getName()) {
            // u destammesse
            $select = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART);
            $select->setIntegrityCheck(false)
                    // join su id_u_sdestinazionis
                    ->join($tabellaJoin->getName(), $this->getName() . '.id_u_sdestinazioni = ' . $tabellaJoin->getName() . '.id_u_sdestinazioni')
                    ->where($this->getName() . '.id_macro_ambito = ?', $id_macro_ambiti)
                    ->where($this->getName() . '.data_inizio <= ?', $data)
                    ->where($this->getName() . '.data_fine > ? OR ' . $this->getName(). '.data_fine = \'0000-00-00\'', $data);

            //throw new Exception($select);
            $righe = $this->fetchAll($select);
            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query filtroDestinazioniAmmesse");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

}

?>
