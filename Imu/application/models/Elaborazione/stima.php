<?php

class Stima {

    public static function stimaSingolaLonato($form2, $form2Input) {

        // inizializzo le classi per l'accesso al db
        $lonato_s_rifunitariedest = Factory_dbTable::getClass("lonato", "s_rifunitariedest");
        $lonato_s_tstima = Factory_dbTable::getClass("lonato", "s_tstima");
        $lonato_s_zone = Factory_dbTable::getClass("lonato", "s_zone");
        $lonato_u_cessioni = Factory_dbTable::getClass("lonato", "u_cessioni");
        $lonato_u_destammesse = Factory_dbTable::getClass("lonato", "u_destammesse");
        $lonato_u_mambiti = Factory_dbTable::getClass("lonato", "u_mambiti");
        $lonato_u_mdestinazioni = Factory_dbTable::getClass("lonato", "u_mdestinazioni");
        $lonato_u_modinterv = Factory_dbTable::getClass("lonato", "u_modinterv");
        $lonato_u_sambiti = Factory_dbTable::getClass("lonato", "u_sambiti");
        $lonato_u_sdestinazioni = Factory_dbTable::getClass("lonato", "u_sdestinazioni");


        $stima = 0; // stima unitaria: inizializzata a 0
        // prendo i valori del form1 dalla sessione
        $session = new Zend_Session_Namespace('step1');
        $valoriForm1 = $session->step1;

        var_dump($valoriForm1);
        //var_dump($form2);

        foreach ($form2 as $chiaveForm2 => $valoriForm2) { // perogni riga del form2
            $volumetria = $lonato_u_sambiti->getVolumetria($valoriForm1["id_u_sambiti"]);
            // se volumetrica "v" o utilizzazione "u"
            $tipo_stima = strtolower($volumetria[0]->indice_calcolo_capacita_edificatoria); // prendo il tipo di misura
            // tipo_stima[0] perchè voglio solo l'iniziale "v" o "u"
            $stima_unitaria_righe = $lonato_s_rifunitariedest->ritornaStimaUnitaria($valoriForm2['id_u_sdestinazioni'], $valoriForm1["id_s_zone"], $tipo_stima[0]);

            foreach ($stima_unitaria_righe as $riga) { // prendo 
                if ($tipo_stima[0] == "v") {
                    $stima_unitaria = $riga->stima_riferimento_unitaria_volume;
                } elseif ($tipo_stima[0] == "u") {
                    $stima_unitaria = $riga->stima_riferimento_unitaria_superficie;
                }
                else
                    throw new Exception("Errore in Stima.php: la stima di riferimento unitaria è errata");
            }
            if ($form2Input) {             // se l'utente ha messo una quota percentuale calcola la parte
                if ($valoriForm1["area_urbanizzata"] == 1) { // se la zona è urbanizzata
                    // il calcolo è pari a quello della tabella parzializzata secondo la quota
                    // utente inserita
                    $stima += floatval($stima_unitaria) * floatval($form2Input[$chiaveForm2]);
                } else { // effettuo il calcolo più complicato
                    
                    // prelievo i dati per la cessione
                    $dati_cessione = $lonato_u_cessioni->getQuantitaCessione($valoriForm1["id_m_ambiti"], $valoriForm1["id_u_modinterv"], $valoriForm2["id_u_sdestinazioni"]);
                    foreach ($dati_cessione as $dati_cessione_riga) {
                        $id_u_cessione = $dati_cessione_riga->id_u_cessioni;
                        $quota_cessione = $dati_cessione_riga->quantita_cessione;
                        //echo $id_u_cessione . "_" . $quota_cessione . "<br/>";
                    }

                    // prelevo i dati per la t_sima
                    $dati_u_tstima= $lonato_s_tstima->getFcFcspq($tipo_stima[0]);
                    foreach ($dati_u_tstima as $dati_u_tstima_riga){
                        $fattore_conversione= $dati_u_tstima_riga->fattore_conversione;
                        $fcspq= $dati_u_tstima_riga->fcspq;
                       // echo $fcspq . "_" . $fattore_conversione;
                    }
                    
                    // prelevo i dati per il macroambito
                    $dati_macro_ambito = $lonato_u_mambiti->getSpqVcu($valoriForm1["id_m_ambiti"]);
                    foreach ($dati_macro_ambito as $dati_macro_ambito_riga) {
                        $standard_pubblico_qualita=$dati_macro_ambito_riga->standard_pubblico_qualita;
                        $valore_comprensativo_unitario=$dati_macro_ambito_riga->valore_comprensativo_unitario;
                        //echo $standard_pubblico_qualita . "_" . $valore_comprensativo_unitario . "<br/>";
                    }
                    
                    // calcolo indice capacità edificatoria
                    //if($tipo_stima[0]=="v")
                       // $indice_capacità_edificatoria=
            }
        }
        
        }

        return $stima;
    }

}

?>
