<?php

class Stima {

    /**
     * Metodo per il calcolo della stima per il comune di lonato
     * 
     * @param type $form2 dati del form2
     * @param type $form2Input dati del form2 in input: quote percentuali per zona/area
     * @param type $capacita_edificatoria calcolata in precedenza
     * @return float stima
     */
    public static function calcolaStimaSingolaLonato($form2, $form2Input, $capacita_edificatoria) {

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
                } else { // effettuo il calcolo più complicato se la zona non è urbanizzata
                    // prelievo i dati per la cessione
                    $dati_cessione = $lonato_u_cessioni->getQuantitaCessione($valoriForm1["id_m_ambiti"], $valoriForm1["id_u_modinterv"], $valoriForm2["id_u_sdestinazioni"]);
                    foreach ($dati_cessione as $dati_cessione_riga) {
                        $id_u_cessioni = $dati_cessione_riga->id_u_cessioni;
                        $quota_cessione = $dati_cessione_riga->quantita_cessione;
                        //$ret[4][$chiaveForm2] = $valoriForm1["id_m_ambiti"] . " ". $valoriForm1["id_u_modinterv"] . " " . $valoriForm2["id_u_sdestinazioni"] . " id_u_cessioni " . $id_u_cessioni . " quota cessione: " . $quota_cessione . "<br/>";
                    }

                    // prelevo i dati per la t_sima
                    $dati_u_tstima = $lonato_s_tstima->getFcFcspq($tipo_stima[0]);
                    foreach ($dati_u_tstima as $dati_u_tstima_riga) {
                        $fattore_conversione = $dati_u_tstima_riga->fattore_conversione;
                        $fcspq = $dati_u_tstima_riga->fcspq;
                        //$ret[5][$chiaveForm2] = "fcspq: " . $fcspq . " fattore conversione: " . $fattore_conversione;
                    }
                    // prelevo i dati per il macroambito
                    $dati_macro_ambito = $lonato_u_mambiti->getSpqVcu($valoriForm1["id_m_ambiti"]);
                    foreach ($dati_macro_ambito as $dati_macro_ambito_riga) {
                        $standard_pubblico_qualita = $dati_macro_ambito_riga->standard_pubblico_qualita;
                        $valore_comprensativo_unitario = $dati_macro_ambito_riga->valore_comprensativo_unitario;
                        //$ret[6][$chiaveForm2] = "standard pubblico qualita: " . $standard_pubblico_qualita . " vcu: " . $valore_comprensativo_unitario . "<br/>";
                    }

                    // calcolo indice capacità edificatoria
                    if ($tipo_stima[0] == "v")
                        $indice_capacità_edificatoria = $capacita_edificatoria / floatval($valoriForm1["superficie"]); //floatval($valoriForm1["capacita_edificatoria"]) / floatval($valoriForm1["superficie"]); // CHIEDI A DIEGO CONFERMA
                    elseif ($tipo_stima[0] == "u")
                        $indice_capacità_edificatoria = $capacita_edificatoria * 3 / floatval($valoriForm1["superficie"]); //floatval($valoriForm1["capacita_edificatoria"]) * 3 / floatval($valoriForm1["superficie"]); // CHIEDI A DIEGO CONFERMA
                    else
                        throw new Exception("Errore in Stima.php: cacolo indice capacità edificatoria");

                    // incidenza costo viabilità sull'indice edificatorio MAGIC NUMBER CHIEDI DIEGO!!!
                    $fiv = 0;
                    // fattore incidenza standard MAGIC NUMBER CHIEDI DIEGO!!!
                    $fistd = 0;
                    // incidenza standard qualità espresso in m2 di slq dovendo ricondurre il volume
                    // sempre a 3 MAGIC NUMBER CHIEDI DIEGO!!!
                    $fispq = 3;
                    // incidenza viabilità 
                    $incidenza_viabilità = 0.1; // MAGIC NUMBER CHIEDI DIEGO
                    // costo unitario della viabilità ceduta: espresso in euro/mq
                    // da implementare una tabella start ove leggere questo valore
                    // in base alla scelta dell'anno e del comune
                    $costo_cessione_viabilità = 80; // MAGIC NUMBER CHIEDI DIEGO
                    // costo cessione degli standard. da implementare una tabella
                    // ove leggere questo valore in base alla scelta dell'anno di imposta
                    // e del comune
                    $costo_cessione_standard = 80; // MAGIC NUMBER CHIEDI DIEGO
                    // tasso di auttualizzazione. da implementare una tabella start ove4
                    // leggere questo valore in  base alla scelta dell'anno di imposta e del comune
                    $frate = 0.08; // MAGIC NUMBER CHIEDI DIEGO!!!
                    // orizzonte temporale calcolo attualizzazione. da implementare una tabella
                    // start ove leggere questo valore in base alla scelta dell'anno e del comune
                    $orizzonte_temporale = 3; // MAGIC NUMBER CHIEDI DIEGO!!!
                    // anno corrente
                    $anno_calcolo = 2012; // DA PARAMETRIZZARE!!! 
                    // incidenza viabilità sull'indice edificatorio
                    $fattore_incidenza_viabilità = $incidenza_viabilità / $indice_capacità_edificatoria;
                    //$ret[7][$chiaveForm2] = "fattore incidenza viabilità: " . $fattore_incidenza_viabilità  ."incidenza viabilita: " . $incidenza_viabilità . " indice cap edific: ".                     $fattore_incidenza_viabilità = $incidenza_viabilità / $indice_capacità_edificatoria;

                    // fattore incidenza degli standard
                    $fattore_incidenza_standard = $quota_cessione / $fattore_conversione;
                    //$ret[8][$chiaveForm2] = "fattore incidenza std: " . $fattore_incidenza_standard;
                    // fattore incidenza degli standard di qualità
                    // ------------
                    $fattore_incidenza_standard_qualità = $standard_pubblico_qualita / $fcspq;
                    //$ret[9][$chiaveForm2] = "fattore incidenza std qualità: " . $fattore_incidenza_standard_qualità;
                    // se il tipo di stma è v faccio questo calcolo
                    $fattore_incidenza_calcolo_cessioni = floatval($fattore_incidenza_standard) * floatval($costo_cessione_standard) +
                            floatval($fattore_incidenza_viabilità) * floatval($costo_cessione_viabilità) +
                            floatval($fattore_incidenza_standard_qualità) * floatval($valore_comprensativo_unitario);
                    //$ret[10][$chiaveForm2] = "fattore incidenza calcolo cessioni: " . $fattore_incidenza_calcolo_cessioni;
                    // a seconda del tipo di stima
                    if ($tipo_stima[0] == "v") {
                        // ho già i dati
                    } elseif ($tipo_stima[0] == "u") {
                        $fattore_incidenza_calcolo_cessioni = floatval($fattore_incidenza_calcolo_cessioni) * floatval($fattore_conversione);
                    } else
                        throw new Exception('Errore in Stima.php: nel tipo di stima "v" o "u"');
                    
                    // calcolo la stima di una riga
                    $tstima = (floatval($stima_unitaria) * (1 / pow((1 + floatval($frate)), $orizzonte_temporale)) - $fattore_incidenza_calcolo_cessioni)
                            * floatval($form2Input[$chiaveForm2]);

                    //$ret[11][$chiaveForm2] = "parte1: " . floatval($stima_unitaria);
                    //$ret[12][$chiaveForm2] = "parte2: " . pow((1 / (1 + floatval($frate))),$orizzonte_temporale);
                    //$ret[13][$chiaveForm2] = "parte3: " . $fattore_incidenza_calcolo_cessioni;
                    //$ret[14][$chiaveForm2] = "parte4: " . floatval($form2Input[$chiaveForm2]);
                }
            }
            $stima+=$tstima;
            //$ret[0][$chiaveForm2] = "tstima: " . $tstima;
            //$ret[1][$chiaveForm2] = "stima unitaria: " . $stima_unitaria;
            //$ret[2][$chiaveForm2] = "valori input quota: " . $form2Input[$chiaveForm2];
        }
        //$ret[3] = round($stima);
        return round($stima);
    }

    public static function calcolaCapacitaEdificatoriaLonato() {

        // valore di ritorno capacità edificatoria calcolata
        $capacita_edificatoria = null;

        // inizializzo db adapter
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


        // prendo i valori del form1 dalla sessione
        $session = new Zend_Session_Namespace('step1');
        $valoriForm1 = $session->step1;

        $volumetria = $lonato_u_sambiti->getVolumetria($valoriForm1["id_u_sambiti"]);
        // se volumetrica "v1/2/3" o utilizzazione "u1/2/3"
        $tipo_stima = strtolower($volumetria[0]->indice_calcolo_capacita_edificatoria); // prendo il tipo di misura
        // dati u_sambiti generici utilizzati dopo
        $u_sambiti = $lonato_u_sambiti->getAll($valoriForm1["id_u_sambiti"], $valoriForm1["id_m_ambiti"]);
        foreach ($u_sambiti as $chiaveU_sambiti => $u_sambiti_riga) { // prendo l'indice fondiario
            $indice_fondiario = $u_sambiti_riga->indice_fondiario;
            $incremento_lotti_saturi_i = $u_sambiti_riga->incremento_lotti_saturi_i;
            $utilizzazione_fondiaria = $u_sambiti_riga->utilizzazione_fondiaria;
            $indice_territoriale = $u_sambiti_riga->indice_territoriale;
            $volume_incremento = $u_sambiti_riga->volume_incremento;
        }

        if ($tipo_stima == "v1" || $tipo_stima == "v2" || $tipo_stima == "u1" || $tipo_stima == "u2") { // per 1-2
            $capacita_edificatoria = $valoriForm1["capacita_edificatoria"];
        } elseif ($tipo_stima == "v3" || $tipo_stima == "u3") { // se è v3 o u3: if else complessi!!!
            $prd_o_ddp = $lonato_u_mambiti->getPdrODdp($valoriForm1["id_m_ambiti"]);
            foreach ($prd_o_ddp as $chiave_prd_o_ddp => $prd_o_ddp_riga) {
                $doc = strtolower($prd_o_ddp_riga->pdr_o_ddp);
            }
            if ($doc == "pdr") { // nel caso di pdr
                if (intval($valoriForm1["lotto_saturo"]) == 1) { // se lotto saturo
                    if ($tipo_stima == "v3") { // se v3 pdr
                        $capacita_edificatoria = floatval($indice_fondiario) * $valoriForm1["superficie"]
                                * (1 + floatval($incremento_lotti_saturi_i));
                    } else { // u3 pdr
                        $capacita_edificatoria = floatval($utilizzazione_fondiaria) * $valoriForm1["superficie"]; // manca qualcosa chiedi a DIEGO!!!
                    }
                } else { // lotto non saturo
                    if ($tipo_stima == "v3") // se v3 ddp
                        $capacita_edificatoria = floatval($indice_fondiario) * floatval($valoriForm1["superficie"]);
                    else // u3 ddp
                        $capacita_edificatoria = floatval($utilizzazione_fondiaria) * floatval($valoriForm1["superficie"]);
                }
            } elseif ($doc == "ddp") { // nel caso di ddp:
                $capacita_edificatoria = floatval($indice_territoriale) * floatval($valoriForm1["superficie"]);
            } else
                throw new Exception("Errore in calcolaCapacitaEdificatoriaLonato: tipo doc sbagliato: prd o pdp!: " . $doc  . " " . $valoriForm1["id_m_ambiti"]);
        } elseif ($tipo_stima == "v4") {

            $capacita_edificatoria = $valoriForm1["superficie"] + $volume_incremento;
        } elseif ($tipo_stima == "u4") {
            $capacita_edificatoria=floatval($valoriForm1["capacita_edificatoria"])* (float)(1+ $utilizzazione_fondiaria);
            
        } else
            throw new Exception("Errore in calcolaCapcitaEdificatoria: il tipo di stima non è valido!" . $tipo_stima);


        if ($capacita_edificatoria)
            return $capacita_edificatoria;
        else
            throw new Exception("Errore in calcolaCapacitaEdificatoriaLonato: capacità vuota o nulla: " . $tipo_stima . "capacita: ". $capacita_edificatoria);
    }

}

?>
