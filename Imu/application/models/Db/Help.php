<?php
class Application_Model_DbTable_Help extends Application_Model_DbTable_TabellaComuni {
	
	/**
	 * prelevo i testi di Help dal db
	 * $nome 	
	 */
	public function getHelp($nome) {
		$nome_tab = $this->getName ();
		
		if ($nome_tab) {
			
			$select = $this->select();
			$select->from($this->getName(), "descrizione")
				->where("nome = ?", $nome)
				->where("record_attivo = 1");

            $righe = $this->fetchRow($select);
            if ($righe)
                return $righe;
            else
                throw new Exception("Errore nella query getHelp");
        }
        else
            throw new Exception("Nome tabella non settato in filtroDestinazioniAmmesse");
    }
}
?>