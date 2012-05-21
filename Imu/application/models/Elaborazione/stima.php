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
        $tstima = 0; // stima temporanea usata per i calcoli 
        // prendo i valori del form1 dalla sessione
        $session = new Zend_Session_Namespace('step1');
        $valoriForm1 = $session->step1;

        //var_dump($valoriForm1);
        //var_dump($form2);

        foreach ($form2 as $chiaveForm2 => $valoriForm2) { // perogni riga del form2
            $volumetria = $lonato_u_sambiti->getVolumetria($valoriForm1["id_u_sambiti"]);
            // se volumetrica "v" o utilizzazione "u"
            $tipo_stima = strtolower($volumetria[0]->indice_calcolo_capacita_edificatoria); // prendo il tipo di misura
            // tipo_stima[0] perchè voglio solo l'iniziale "v" o "u"
            $stima_unitaria_righe = $lonato_s_rifunitariedest->ritornaStimaUnitaria($valoriForm2['id_u_sdestinazioni'], $valoriForm1["id_s_zone"], $tipo_stima[0]);

            foreach ($stima_unitaria_righe as $riga) {
                $tstima = 0;
                if ($tipo_stima[0] == "v") {
                    $stima_unitaria = $riga->stima_riferimento_unitaria_volume;
                } elseif ($tipo_stima[0] == "u") {
                    $stima_unitaria = $riga->stima_riferimento_unitaria_superficie;
                }
                else
                    throw new Exception("Errore
                        in Stima.php: la stima di riferimento unitaria è errata");
            }
            if ($form2Input[$chiaveForm2]) { // se l'utente ha messo una quota percentuale calcola la parte
                if ($valoriForm1["area_urbanizzata"] == 1) { // se la zona è urbanizzata
                    // il calcolo è pari a quello della tabella parzializzata secondo la quota inserita dell'utente
                    // calcolo la stima di una riga
                    $tstima = floatval($stima_unitaria) * floatval($form2Input[$chiaveForm2]);
                } else { // effettuo il calcolo più complicato
                    // prelievo i dati per la cessione
                    $dati_cessione = $lonato_u_cessioni->getQuantitaCessione($valoriForm1["id_m_ambiti"], $valoriForm1["id_u_modinterv"], $valoriForm2["id_u_sdestinazioni"]);
                    foreach ($dati_cessione as $dati_cessione_riga) {
                        $id_u_cessione = $dati_cessione_riga->id_u_cessioni;
                        $quota_cessione = $dati_cessione_riga->quantita_cessione;
                        // debug $ret[4][$chiaveForm2] = "id_u_cessione " . $id_u_cessione . " quota cessione: " . $quota_cessione . "<br/>";
                    }

                    // prelevo i dati per la t_sima
                    $dati_u_tstima = $lonato_s_tstima->getFcFcspq($tipo_stima[0]);
                    foreach ($dati_u_tstima as $dati_u_tstima_riga) {
                        $fattore_conversione = $dati_u_tstima_riga->fattore_conversione;
                        $fcspq = $dati_u_tstima_riga->fcspq;
                        // debug $ret[5][$chiaveForm2] = "fcspq: " . $fcspq . " fattore conversione: " . $fattore_conversione;
                    }

                    // prelevo i dati per il macroambito
                    $dati_macro_ambito = $lonato_u_mambiti->getSpqVcu($valoriForm1["id_m_ambiti"]);
                    foreach ($dati_macro_ambito as $dati_macro_ambito_riga) {
                        $standard_pubblico_qualita = $dati_macro_ambito_riga->standard_pubblico_qualita;
                        $valore_comprensativo_unitario = $dati_macro_ambito_riga->valore_comprensativo_unitario;
                        // debug $ret[6][$chiaveForm2] = "standard pubblico qualita: " . $standard_pubblico_qualita . " vcu: " . $valore_comprensativo_unitario . "<br/>";
                    }

                    // calcolo indice capacità edificatoria
                    if ($tipo_stima[0] == "v")
                        $indice_capacità_edificatoria = floatval($valoriForm1["capacita_edificatoria"]) / floatval($valoriForm1["superficie"]); // CHIEDI A DIEGO CONFERMA
                    elseif ($tipo_stima[0] == "u")
                        $indice_capacità_edificatoria = floatval($valoriForm1["capacita_edificatoria"]) * 3 / floatval($valoriForm1["superficie"]); // CHIEDI A DIEGO CONFERMA
                    else
                        throw new Exception("Errore in Stima.php: cacolo indice capacità edificatoria");

                    // incidenza costo viabilità sull'indice edificatorio
                    $fiv = 0;
                    // incidenza standard
                    $fispq = 3;
                    // incidenza viabilità INPUT UTENTE!!! CHIEDI A DIEGO
                    $incidenza_viabilità = 0.2;
                    // costo unitario della viabilità ceduta: espresso in euro/mq
                    // da implementare una tabella start ove leggere questo valore
                    // in base alla scelta dell'anno e del comune
                    $costo_cessione_viabilità = 80;
                    // costo cessione degli standard. da implementare una tabella
                    // ove leggere questo valore in base alla scelta dell'anno di imposta
                    // e del comune
                    $costo_cessione_standard = 80;
                    // tasso di auttualizzazione. da implementare una tabella start ove
                    // leggere questo valore in  base alla scelta dell'anno di imposta e del comune
                    $frate = 0.08;
                    // orizzonte temporale calcolo attualizzazione. da implementare una tabella
                    // start ove leggere questo valore in base alla scelta dell'anno e del comune
                    $orizzonte_temporale = 3;
                    // anno corrente
                    $anno_calcolo = 2012; // da prendere dalla sessione!!!
                    // incidenza viabilità sull'indice edificatorio
                    $fattore_incidenza_viabilità = $incidenza_viabilità / $indice_capacità_edificatoria;
                    // deubg $ret[7][$chiaveForm2] = "fattore incidenza viabilità: " . $fattore_incidenza_viabilità;
                    // fattore incidenza degli standard
                    $fattore_incidenza_standard = $quota_cessione / $fattore_conversione;
                    // debug $ret[8][$chiaveForm2] = "fattore incidenza std: " . $fattore_incidenza_standard;
                    // fattore incidenza degli standard di qualità
                    $fattore_incidenza_standard_qualità = $standard_pubblico_qualita / $fcspq;
                    // debug $ret[9][$chiaveForm2] = "fattore incidenza std qualità: " . $fattore_incidenza_standard_qualità;
                    // se il tipo di stma è v faccio questo calcolo
                    $fattore_incidenza_calcolo_cessioni = floatval($fattore_incidenza_standard) * floatval($costo_cessione_standard) +
                            floatval($fattore_incidenza_viabilità) * floatval($costo_cessione_viabilità) +
                            floatval($fattore_incidenza_standard_qualità) * floatval($valore_comprensativo_unitario);
                    // debug $ret[10][$chiaveForm2] = "fattore incidenza calcolo cessioni: " . $fattore_incidenza_calcolo_cessioni;
                    // a seconda del tipo di stima
                    if ($tipo_stima[0] == "v") {
                        // ho già i dati
                    } elseif ($tipo_stima[0] == "u") {
                        $fattore_incidenza_calcolo_cessioni = floatval($fattore_incidenza_calcolo_cessioni) * floatval($fattore_conversione);
                    } else
                        throw new Exception('Errore in Stima.php: nel tipo di stima "v" o "u"');

                    // calcolo la stima di una riga
                    $tstima = (floatval($stima_unitaria) * pow((1 / (1 + floatval($frate))),$orizzonte_temporale)- $fattore_incidenza_calcolo_cessioni)
                            * floatval($form2Input[$chiaveForm2]);
                    
                    // debug $ret[11][$chiaveForm2] = "parte1: " . floatval($stima_unitaria);
                    // debug $ret[12][$chiaveForm2] = "parte2: " . pow((1 / (1 + floatval($frate))),$orizzonte_temporale);
                    // debug $ret[13][$chiaveForm2] = "parte3: " . $fattore_incidenza_calcolo_cessioni;
                    // debug $ret[14][$chiaveForm2] = "parte4: " . floatval($form2Input[$chiaveForm2]);
                }
            }
            $stima+=$tstima;
            // debug $ret[0][$chiaveForm2] = "tstima: " . $tstima;
            // debug $ret[1][$chiaveForm2] = "stima unitaria: " . $stima_unitaria;
            // debug $ret[2][$chiaveForm2] = "valori input quota: " . $form2Input[$chiaveForm2];
        }
        //$ret[3] = round($stima);
        return round($stima);
    }

}

?>
