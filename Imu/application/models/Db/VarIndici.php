<?php

class Application_Model_DbTable_VarIndici extends Application_Model_DbTable_TabellaComuni {

    /**
     * Questo metodo ritorna il valore di un dato della 
     * var_indici data la sua descizione e la data
     * 
     * @param type $descrizione descrizione del valore
     * @param type $data data di validità
     * @return Zend_db_table tabella con i dati
     */
    public function getDato($nome, $data) {

        $righe = null;

        if ($this->getName()) {

            $select = $this->select()
                    ->from($this->getName(), array('valore'))
                    ->where('record_attivo = 1')
                    ->where('nome = ?', $nome)
                    ->where('data_inizio <= ?', $data)
                    ->where('data_fine <= ?', $data);

            $righe = $this->fetchAll($select);

            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getDato di VarIndici.");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }

}

?>
