<?php

class Application_Model_DbTable_SRifunitariedest extends Application_Model_DbTable_TabellaComuni 
{
    /**
     * Ritorna la stima unitaria prevista
     * 
     * @param Int $id_subdestinazione identificativo subdestinazione
     * @param Int $id_zona identificativo zona
     * @param String $tipo_stima tipo stima "v" o "u" volumetrica o utilizzazione
     */
    public function ritornaStimaUnitaria($id_subdestinazione, $id_zona, $tipo_stima){
        $righe = null;
  
        if ($this->getName()) { // se ho un nome settato
            
            // vedo dove prendere i dai
            if ($tipo_stima == "v" )
                $testoSelect= "stima_riferimento_unitaria_volume";
            elseif ($tipo_stima == "u")
                $testoSelect= "stima_riferimento_unitaria_superficie";
            else 
                throw new Exception("Errore in ritornaStimaUnitaria: tipo di stima riferimento unitaria: non è ne v ne u");
            
            $select = $this->select()
                           ->from($this->getName(), array($testoSelect))
                           ->where('id_u_sdestinazioni = ?', $id_subdestinazione)
                           ->where('id_s_zone = ?', $id_zona)
                           ->where("record_attivo = 1");

            //echo $select;
            $righe = $this->fetchAll($select);            
            
            if ($righe)
                return $righe;
            else
                throw new Exception("Errore in ritornaStimaUnitaria: nella query ritornaStimaUnitaria");
        }
        else
            throw new Exception("Erroe in ritornaStimaUnitaria: Nome tabella non settato in filtroDestinazioniAmmesse");   
    }
}
?>
