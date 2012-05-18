<?php

/**
 * Questa classe dato il comune i il nome tabella 
 * ritorna una dbTable apposita per la gesitone della tabella in quel comune
 * 
 */
class Factory_dbTable {
    /* metodologia consigliata per le query
     * 
     * $select = $table->select();
     * $select->where('bug_status = ?', 'NEW');
     * $rows = $table->fetchAll($select);
     */

    public static function getClass($comune, $nomeTabella) {

        require_once APPLICATION_PATH . "/models/Db/SRifunitariedest.php";
        require_once APPLICATION_PATH . "/models/Db/STstima.php";
        require_once APPLICATION_PATH . "/models/Db/SZone.php";
        require_once APPLICATION_PATH . "/models/Db/UCessioni.php";
        require_once APPLICATION_PATH . "/models/Db/UDestammesse.php";
        require_once APPLICATION_PATH . "/models/Db/UMambiti.php";
        require_once APPLICATION_PATH . "/models/Db/UMdestinazioni.php";
        require_once APPLICATION_PATH . "/models/Db/UModinterv.php";
        require_once APPLICATION_PATH . "/models/Db/USambiti.php";
        require_once APPLICATION_PATH . "/models/Db/USdestinazioni.php";

        $tabella=null;

        switch ($nomeTabella) {
            case 's_rifunitariedest':
                $tabella = new Application_Model_DbTable_SRifunitariedest();
                $tabella->setName('s_rifunitariedest');
                break;
            case 's_tstima':
                $tabella = new Application_Model_DbTable_STsima();
                $tabella->setName('s_tstima');
                break;
            case 's_zone':
                $tabella = new Application_Model_DbTable_SZone();
                $tabella->setName('s_zone');
                break;
            case 'u_cessioni':
                $tabella = new Application_Model_DbTable_UCessioni();
                $tabella->setName('u_cessioni');
                break;
            case 'u_destammesse':
                $tabella = new Application_Model_DbTable_UDestammesse();
                $tabella->setName('u_destammesse');
                break;
            case 'u_mambiti':
                $tabella = new Application_Model_DbTable_UMambiti();
                $tabella->setName('u_mambiti');
                break;
            case 'u_mdestinazioni':
                $tabella = new Application_Model_DbTable_UMdestinazioni();
                $tabella->setName('u_mdestinazioni');
                break;
            case 'u_modinterv':
                $tabella = new Application_Model_DbTable_UModinterv();
                $tabella->setName('u_modinterv');
                break;
            case 'u_sambiti':
                $tabella = new Application_Model_DbTable_USambiti();
                $tabella->setName('u_sambiti');
                break;
            case 'u_sdestinazioni':
                $tabella = new Application_Model_DbTable_USdestinazioni();
                $tabella->setName('u_sdestinazioni');
                break;
            default:
                throw new Exception("Nome tabella non valido in Factory_dbTable getClass");
        }

        // imposta il nome della tabella a seconda del comune
        if ($comune){
            $tabella->setComune($comune);
            //echo $tabella->getName() ." " . $tabella->getComune();
            $name=$tabella->getComune() . "_" . $tabella->getName();
            $tabella->setName($name);
        }
        else
            throw new Exception("Nome comune non esistente in Factory_dbTable getClass");

        return $tabella;
    }

}

?>
