<?php

class Application_Model_DbTable_VarIndici extends Application_Model_DbTable_TabellaComuni {

    /**
     * Questo metodo ritorna il valore di un dato della 
     * var_indici data la sua descrizione e la data
     * la data deve essere compresa tra inizio e fine oppure non deve avere
     * data di fine ma essere maggiore di data inizio
     * si presuppone che l'ultima data_inizi abbia null come data fine
     * ATTENZIONE: la data fine di un nuovo periodo deve essere uguale 
     * alla data inizio del nuovo periodo.
     * 
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
                    ->where('data_fine > ? OR data_fine = 0000-00-00', $data);

            //debug throw new Exception($select);
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
