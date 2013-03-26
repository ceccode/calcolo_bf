<?php

class Stima {

    /**
     * Questo metodo corregge i float con virgola in input 
     * e li rende utilizzabili per effettuare calcoli in php
     * 
     * @param type $numeri 
     */
    public static function correggiFloat($numeri) {
        $corretto = array();
        if (is_array($numeri)) { // se array faccio un foreach
            foreach ($numeri as $chiave => $valore) {
                $valore = str_replace(",", ".", $valore);
                $corretto[$chiave] = floatval($valore);
            }
        } else { // altrimenti aggiusto solo un valore
            $corretto = str_replace(",", ".", $numeri);
            $corretto = floatval($corretto);
        }

        return $corretto;
    }

    /**
     * Questo metodo corregge le quote in input percentuali nella
     * percentuale effettiva decimale i
     * 
     * @param type $numeri
     * @return type 
     */
    public static function convertiPercentualeStima($numeri) {
        $corretto = array();
        foreach ($numeri as $chiave => $valore) {
            $corretto[$chiave] = (float) ($valore / 100);
        }
        //$corretto= Stima::correggiFloat($corretto);
        return $corretto;
    }

    /**
     * Questo metodo ritorna la somma delle quote percentuali
     * arrotonda i valori in input(i valori dovrebbero essere interi) 
     * ATTENZIONE: non controlle se  un array
     */
    public static function verificaQuote($quote) {
        $somma = 0;
        foreach ($quote as $key => $value) {

            $somma+=round($value);
        }

        return $somma;
    }

    /**
     * Metodo per il calcolo della stima per il comune di lonato
     * IMPORTANTE!!! si presuppone che sia stato chiamato prima
     * calcolaCapacitˆEdificatoraLonato e ci siano i valori del form
     * in sessione!!!
     * 
     * Array quote_input quote percentuali per subambito-zona
     * @return float stima
     */
    public static function calcolaStimaSingolaLonato($quote_input) {

        // inizializzo le classi per l'accesso al db
        $lonato_s_rifunitariedest = Factory_dbTable::getClass("017092", "s_rifunitariedest");
        $lonato_s_tstima = Factory_dbTable::getClass("017092", "s_tstima");
        $lonato_u_cessioni = Factory_dbTable::getClass("017092", "u_cessioni");
        $lonato_u_destammesse = Factory_dbTable::getClass("017092", "u_destammesse");
        $lonato_u_mambiti = Factory_dbTable::getClass("017092", "u_mambiti");
        $lonato_u_mdestinazioni = Factory_dbTable::getClass("017092", "u_mdestinazioni");
        $lonato_u_modinterv = Factory_dbTable::getClass("017092", "u_modinterv");
        $lonato_u_sambiti = Factory_dbTable::getClass("017092", "u_sambiti");
        $lonato_u_sdestinazioni = Factory_dbTable::getClass("017092", "u_sdestinazioni");
        $lonato_var_indici = Factory_dbTable::getClass("017092", "var_indici");
        // quote in input corrette
        $quote = Stima::convertiPercentualeStima($quote_input);
        $stima = 0; // stima unitaria: inizializzata a 0
        $tstima = 0; // stima temporanea usata per i calcoli 
        // prendo i valori del form1 dalla sessione
        $session = new Zend_Session_Namespace('step1');
        $valoriForm1 = $session->step1;
        // data del calcolo
        $data_calcolo = $session->data_calcolo;
        // capacitˆ edificatoria
        $capacita_edificatoria = $session->capacita_edificatoria;
        //form2
        $form2 = $lonato_u_destammesse->filtroDestinazioniAmmesse($valoriForm1['id_m_ambiti'], $data_calcolo);

        if (!$capacita_edificatoria)
            throw new Exception("Errore in calcolaStimaSingolaLonato: La capacitˆ edificatoria  nulla");

        //var_dump($valoriForm1);
        //var_dump($form2);

        foreach ($form2 as $chiaveForm2 => $valoriForm2) { // perogni riga del form2
            $volumetria = $lonato_u_sambiti->getVolumetria($valoriForm1["id_u_sambiti"], $data_calcolo);
            // se volumetrica "v" o utilizzazione "u"
            $tipo_stima = strtolower($volumetria[0]->indice_calcolo_capacita_edificatoria); // prendo il tipo di misura
            // tipo_stima[0] perch voglio solo l'iniziale "v" o "u"
            $stima_unitaria_righe = $lonato_s_rifunitariedest->ritornaStimaUnitaria($valoriForm2['id_u_sdestinazioni'], $valoriForm1["id_s_zone"], $tipo_stima[0], $data_calcolo);

            foreach ($stima_unitaria_righe as $riga) {
                $tstima = 0;
                if ($tipo_stima[0] == "v") {
                    $stima_unitaria = $riga->stima_riferimento_unitaria_volume;
                } elseif ($tipo_stima[0] == "u") {
                    $stima_unitaria = $riga->stima_riferimento_unitaria_superficie;
                }
                else
                    throw new Exception("Errore
                        in Stima.php: la stima di riferimento unitaria  errata");
                $ret[20][$chiaveForm2]= ($quote[$chiaveForm2] > 0)?"id rifunitariedest: " . $riga->id_s_rifunitariedest:null;
            }
            if ($quote[$chiaveForm2] > 0) { // se l'utente ha messo una quota percentuale calcola la parte
                if ($valoriForm1["area_urbanizzata"] == 1) { // se la zona  urbanizzata
                    // il calcolo  pari a quello della tabella parzializzata secondo la quota inserita dell'utente
                    // calcolo la stima di una riga
                    $tstima = $stima_unitaria * $quote[$chiaveForm2];
                } else { // effettuo il calcolo pi complicato se la zona non  urbanizzata
                    // prelievo i dati per la cessione
                    $dati_cessione = $lonato_u_cessioni->getQuantitaCessione($valoriForm1["id_m_ambiti"], $valoriForm1["id_u_modinterv"], $valoriForm2["id_u_sdestinazioni"], $data_calcolo);
                    foreach ($dati_cessione as $dati_cessione_riga) {
                        $id_u_cessioni = $dati_cessione_riga->id_u_cessioni;
                        $quota_cessione = $dati_cessione_riga->quantita_cessione;
                        $id_s_tstima = $dati_cessione_riga->id_s_tstima;
                        $ret[0][$chiaveForm2] = "id macro ambiti: " . $valoriForm1["id_m_ambiti"] ." id subambiti". $valoriForm1["id_u_sambiti"] . " modalitˆ di intervento id". $valoriForm1["id_u_modinterv"] . " id subdestinazioni" . $valoriForm2["id_u_sdestinazioni"] . " id_u_cessioni " . $id_u_cessioni . " quota cessione: " . $quota_cessione . "<br/>";
                    }

                    // prelevo i dati per la t_stima
                    // prelevo il fattore di conversione standard qualitˆ fcspq
                    $dati_u_tstima = $lonato_s_tstima->getFcspq($tipo_stima[0]);
                    foreach ($dati_u_tstima as $dati_u_tstima_riga) {
                        $fcspq = $dati_u_tstima_riga->fcspq;
                                               // $fattore_conversione = $dati_u_tstima_riga->fattore_conversione;
                                       
                        $ret[1][$chiaveForm2]="id_u_cessioni: ". $id_u_cessioni.  " id_s_tsima: " . $id_s_tstima.  "fcspq: " .$fcspq;
                    }
                    // prelevo il fattore conversione fc
                    $dati_u_tstima = $lonato_s_tstima->getFc($id_s_tstima);
                    
                    foreach ($dati_u_tstima as $dati_u_tstima_riga) {
                        $fattore_conversione = $dati_u_tstima_riga->fattore_conversione;
                        $ret[2][$chiaveForm2]="fattore conversione: ". $fattore_conversione;
                    }
                    // prelevo i dati per il macroambito
                    $dati_macro_ambito = $lonato_u_mambiti->getSpqVcu($valoriForm1["id_m_ambiti"], $data_calcolo);
                    foreach ($dati_macro_ambito as $dati_macro_ambito_riga) {
                        $standard_pubblico_qualita = $dati_macro_ambito_riga->standard_pubblico_qualita;
                        $valore_comprensativo_unitario = $dati_macro_ambito_riga->valore_comprensativo_unitario;
                        $ret[3][$chiaveForm2] = "standard pubblico qualita: " . $standard_pubblico_qualita . " valore compensativo unitario: " . $valore_comprensativo_unitario . "<br/>";
                    }


                    // calcolo indice capacitˆ edificatoria
                    if ($tipo_stima[0] == "v")
                        $indice_capacitˆ_edificatoria = $capacita_edificatoria / Stima::correggiFloat($valoriForm1["superficie"]); //floatval($valoriForm1["capacita_edificatoria"]) / floatval($valoriForm1["superficie"]); // CHIEDI A DIEGO CONFERMA
                    elseif ($tipo_stima[0] == "u")
                        $indice_capacitˆ_edificatoria = $capacita_edificatoria * 3 / Stima::correggiFloat($valoriForm1["superficie"]); //floatval($valoriForm1["capacita_edificatoria"]) * 3 / floatval($valoriForm1["superficie"]); // CHIEDI A DIEGO CONFERMA
                    else
                        throw new Exception("Errore in Stima.php: cacolo indice capacitˆ edificatoria");

                    $ret[4][$chiaveForm2]= "indice capacitˆ edificatoria: " . $indice_capacitˆ_edificatoria;
                    // incidenza viabilitˆ 
                    $tmp = null;
                    $incidenza_viabilitˆ = $lonato_var_indici->getDato("incidv", $session->data_calcolo);
                    foreach ($incidenza_viabilitˆ as $valore) {
                        $tmp = Stima::correggiFloat($valore->valore);
                    }
                    if ($tmp)
                        $incidenza_viabilitˆ = $tmp;
                    else
                        throw new Exception("Errore in calcolaStimaSingolaLonato: il valore incidv non  presente nella tabella var_indici");

                    
                    $ret[5][$chiaveForm2]= "indicenza viabilitˆ: " . $incidenza_viabilitˆ;
                    // costo unitario della viabilitˆ ceduta: espresso in euro/m2
                    // dati dipendenti dalla tabella var_indici
                    $tmp = null;
                    $costo_cessione_viabilitˆ = $lonato_var_indici->getDato("ccv", $session->data_calcolo);
                    foreach ($costo_cessione_viabilitˆ as $valore) {
                        $tmp = Stima::correggiFloat($valore->valore);
                    }
                    if ($tmp)
                        $costo_cessione_viabilitˆ = $tmp;
                    else
                        throw new Exception("Errore in calcolaStimaSingolaLonato: il valore ccv non  presente nella tabella var_indici");
                    // costo cessione degli standard. 
                    // dipende da var_indici
                    $tmp = null;
                    $costo_cessione_standard = $lonato_var_indici->getDato("ccs", $session->data_calcolo);
                    foreach ($costo_cessione_standard as $valore) {
                        $tmp = Stima::correggiFloat($valore->valore);
                    }
                    if ($tmp)
                        $costo_cessione_standard = $tmp;
                    else
                        throw new Exception("Errore in calcolaStimaSingolaLonato: il valore ccs non  presente nella tabella var_indici");

                    $ret[6][$chiaveForm2]="costo cessione standard: " . $costo_cessione_standard;
                    
                    // tasso di auttualizzazione. 
                    // dipende da var_indici
                    $tmp = null;
                    $frate = $lonato_var_indici->getDato("frate", $session->data_calcolo);
                    foreach ($frate as $valore) {
                        $tmp = Stima::correggiFloat($valore->valore);
                    }
                    if ($tmp)
                        $frate = $tmp;
                    else
                        throw new Exception("Errore in calcolaStimaSingolaLonato: il valore frate non  presente nella tabella var_indici");

                    
                    $ret[7][$chiaveForm2]= "fattore rate: ". $frate;
                    // orizzonte temporale calcolo attualizzazione. 
                    // dipende da var_indici
                    $tmp = null;
                    $orizzonte_temporale = $lonato_var_indici->getDato("otemp", $session->data_calcolo);
                    foreach ($orizzonte_temporale as $valore) {
                        $tmp = Stima::correggiFloat($valore->valore);
                    }
                    if ($tmp)
                        $orizzonte_temporale = $tmp;
                    else
                        throw new Exception("Errore in calcolaStimaSingolaLonato: il valore otemp non  presente nella tabella var_indici");

                    $ret[8][$chiaveForm2]= "orizzonte temporale: ". $orizzonte_temporale;
                    $ret[9][$chiaveForm2]="tipo stima: " . $tipo_stima;
                    // incidenza viabilitˆ sull'indice edificatorio
                    $fattore_incidenza_viabilitˆ = (float) ($incidenza_viabilitˆ / $indice_capacitˆ_edificatoria);
                    $ret[10][$chiaveForm2] = "fattore incidenza viabilitˆ: " . $fattore_incidenza_viabilitˆ  ."incidenza viabilita: " . $incidenza_viabilitˆ . " indice cap edific: ".                     $fattore_incidenza_viabilitˆ = $incidenza_viabilitˆ / $indice_capacitˆ_edificatoria;
                    // fattore incidenza degli standard
                    $fattore_incidenza_standard = (float) ($quota_cessione / $fattore_conversione);
                    $ret[11][$chiaveForm2] ="quota cessione: " . $quota_cessione . "fattore conversione: ". $fattore_conversione. "fattore incidenza std: " . $fattore_incidenza_standard;
                    // fattore incidenza degli standard di qualitˆ
                    // ------------
                    $fattore_incidenza_standard_qualitˆ = (float) ($standard_pubblico_qualita / $fcspq);
                    $ret[12][$chiaveForm2] = "fattore incidenza std qualitˆ: " . $fattore_incidenza_standard_qualitˆ;
                    // se il tipo di stma  v faccio questo calcolo
                    $ret['var'] = array($fattore_incidenza_standard,$costo_cessione_standard,$fattore_incidenza_viabilitˆ, $costo_cessione_viabilitˆ, $fattore_incidenza_standard_qualitˆ, $valore_comprensativo_unitario);
                    $fattore_incidenza_calcolo_cessioni = (float) ($fattore_incidenza_standard * $costo_cessione_standard) +
                            (float) ($fattore_incidenza_viabilitˆ * $costo_cessione_viabilitˆ) +
                            (float) ($fattore_incidenza_standard_qualitˆ * $valore_comprensativo_unitario);
                    //  throw new Exception ($fattore_incidenza_viabilitˆ. " ". $fattore_incidenza_standard . " ". $fattore_incidenza_standard_qualitˆ . " " . $fattore_incidenza_calcolo_cessioni);
                    $ret[13][$chiaveForm2] = "fattore incidenza calcolo cessioni: " . $fattore_incidenza_calcolo_cessioni;
                    // a seconda del tipo di stima
                    $ret['tipo_stima'] = $tipo_stima;
                    if ($tipo_stima[0] == "v") {
                        // ho giˆ i dati
                    } elseif ($tipo_stima[0] == "u") {
                    	$ret['fattore_incidenza_calcolo_cessioni_0'] = $fattore_incidenza_calcolo_cessioni;
                    	$fattore_incidenza_calcolo_cessioni = $fattore_incidenza_calcolo_cessioni * $fattore_conversione;
                        $ret['fattore_incidenza_calcolo_cessioni'] = $fattore_incidenza_calcolo_cessioni;
                    } else
                        throw new Exception('Errore in Stima.php: nel tipo di stima "v" o "u"');

                    // calcolo la stima di una riga
                    $tstima = ($stima_unitaria * (1 / pow((1 + $frate), $orizzonte_temporale)) - $fattore_incidenza_calcolo_cessioni)
                            * Stima::correggiFloat($quote[$chiaveForm2]);
                    $ret[14][$chiaveForm2] = "parte1 calcolo (stima_unitaria): " . $stima_unitaria;
                    $ret[15][$chiaveForm2] = "parte2 calcolo (1/pow((1+ frate),orizzonte_temporale)): " . (1 / pow((1 + $frate), $orizzonte_temporale));
                    $ret[16][$chiaveForm2] = "parte3 calclolo fattore incidenza calcolo cessioni: " . $fattore_incidenza_calcolo_cessioni;
                    $ret[17][$chiaveForm2] = "parte4 calclolo quota: " . Stima::correggiFloat($quote[$chiaveForm2]);
                    
                }
            }
            $stima+=$tstima;
            $ret[18][$chiaveForm2] = "stima per la quota singola: " . $tstima;
        }
        $ret[19] = "stima totale: ". round($stima);
        
		var_dump($ret);
        
        return round($stima);
    }

    public static function calcolaCapacitaEdificatoriaLonato() {

        // valore di ritorno capacitˆ edificatoria calcolata
        $capacita_edificatoria = null;

        // inizializzo db adapter
        $lonato_s_rifunitariedest = Factory_dbTable::getClass("017092", "s_rifunitariedest");
        $lonato_s_tstima = Factory_dbTable::getClass("017092", "s_tstima");
        $lonato_u_cessioni = Factory_dbTable::getClass("017092", "u_cessioni");
        $lonato_u_destammesse = Factory_dbTable::getClass("017092", "u_destammesse");
        $lonato_u_mambiti = Factory_dbTable::getClass("017092", "u_mambiti");
        $lonato_u_mdestinazioni = Factory_dbTable::getClass("017092", "u_mdestinazioni");
        $lonato_u_modinterv = Factory_dbTable::getClass("017092", "u_modinterv");
        $lonato_u_sambiti = Factory_dbTable::getClass("017092", "u_sambiti");
        $lonato_u_sdestinazioni = Factory_dbTable::getClass("017092", "u_sdestinazioni");


        // prendo i valori del form1 dalla sessione
        $session = new Zend_Session_Namespace('step1');
        $valoriForm1 = $session->step1;
        // data calcolo
        $data_calcolo = $session->data_calcolo;

        $volumetria = $lonato_u_sambiti->getVolumetria($valoriForm1["id_u_sambiti"], $data_calcolo);
        // se volumetrica "v1/2/3" o utilizzazione "u1/2/3"
        $tipo_stima = strtolower($volumetria[0]->indice_calcolo_capacita_edificatoria); // prendo il tipo di misura
        // dati u_sambiti generici utilizzati dopo
        // ATTENZIONE forse id_m_ambiti nn  necessario
        $u_sambiti = $lonato_u_sambiti->getAll($valoriForm1["id_u_sambiti"], $data_calcolo);
        foreach ($u_sambiti as $chiaveU_sambiti => $u_sambiti_riga) { // prendo l'indice fondiario
            $indice_fondiario = $u_sambiti_riga->indice_fondiario;
            $incremento_lotti_saturi_i = $u_sambiti_riga->incremento_lotti_saturi_i;
            $utilizzazione_fondiaria = $u_sambiti_riga->utilizzazione_fondiaria;
            $indice_territoriale = $u_sambiti_riga->indice_territoriale;
            $volume_incremento = $u_sambiti_riga->volume_incremento;
        }

        if ($tipo_stima == "v1" || $tipo_stima == "v2" || $tipo_stima == "u1" || $tipo_stima == "u2") { // per 1-2
            $capacita_edificatoria = $valoriForm1["capacita_edificatoria"];
        } elseif ($tipo_stima == "v3" || $tipo_stima == "u3") { // se  v3 o u3: if else complessi!!!
            $prd_o_ddp = $lonato_u_mambiti->getPdrODdp($valoriForm1["id_m_ambiti"], $data_calcolo);
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
                throw new Exception("Errore in calcolaCapacitaEdificatoriaLonato: tipo doc sbagliato: prd o pdp!: " . $doc . " " . $valoriForm1["id_m_ambiti"]);
        } elseif ($tipo_stima == "v4") {

            $capacita_edificatoria = $valoriForm1["superficie"] + $volume_incremento;
        } elseif ($tipo_stima == "u4") {
            $capacita_edificatoria = floatval($valoriForm1["capacita_edificatoria"]) * (float) (1 + $utilizzazione_fondiaria);
        } else
            throw new Exception("Errore in calcolaCapcitaEdificatoria: il tipo di stima non  valido!" . $tipo_stima);


        require_once APPLICATION_PATH . "/models/Utility.php";
        
        if ($capacita_edificatoria)
            return Utility::formattaNumeroPerSalvataggioDb($capacita_edificatoria);
        else
            throw new Exception("Errore in calcolaCapacitaEdificatoriaLonato: capacitˆ vuota o nulla. Tipo stima: " . $tipo_stima . " Capacita: " . $capacita_edificatoria);
    }

}

?>
