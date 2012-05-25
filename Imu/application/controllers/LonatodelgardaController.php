<?php

class LonatodelgardaController extends Zend_Controller_Action {

    private $lonato_s_rifunitariedest;
    private $lonato_s_tstima;
    private $lonato_s_zone;
    private $lonato_u_cessioni;
    private $lonato_u_destammesse;
    private $lonato_u_mambiti;
    private $lonato_u_mdestinazioni;
    private $lonato_u_modinterv;
    private $lonato_u_sambiti;
    private $lonato_u_sdestinazioni;
    private $lonato_log;

    public function init() {
        $this->lonato_s_rifunitariedest = Factory_dbTable::getClass("017092", "s_rifunitariedest");
        $this->lonato_s_tstima = Factory_dbTable::getClass("017092", "s_tstima");
        $this->lonato_s_zone = Factory_dbTable::getClass("017092", "s_zone");
        $this->lonato_u_cessioni = Factory_dbTable::getClass("017092", "u_cessioni");
        $this->lonato_u_destammesse = Factory_dbTable::getClass("017092", "u_destammesse");
        $this->lonato_u_mambiti = Factory_dbTable::getClass("017092", "u_mambiti");
        $this->lonato_u_mdestinazioni = Factory_dbTable::getClass("017092", "u_mdestinazioni");
        $this->lonato_u_modinterv = Factory_dbTable::getClass("017092", "u_modinterv");
        $this->lonato_u_sambiti = Factory_dbTable::getClass("017092", "u_sambiti");
        $this->lonato_u_sdestinazioni = Factory_dbTable::getClass("017092", "u_sdestinazioni");
        $this->lonato_log = Factory_dbTable::getClass("017092", "log");
    }

    public function indexAction() {
        // action body
        $form = new Application_Form_Lonatodelgarda();

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                if ($this->_process_lonato_imu($form->getValues())) {
                    $urlOptions = array('controller' => 'Lonatodelgarda', 'action' => 'stima');
                    //$this->view->notifica = '<style>.notifica{ background-color:green; padding:2px;}</style>Modulo salvato con successo.';
                    $this->_helper->redirector->gotoRoute($urlOptions);
                } else {
                    //$this->_helper->redirector->gotoRoute($urlOptions,'azioni');
                    //$this->view->notifica = '<span style="padding:2px;">Ops, si è verificato un errore.</span>';
                }
            }
        }

        $this->view->form = $form;
    }

    protected function _process_lonato_imu($values) {
        // dati variabili
        $session = new Zend_Session_Namespace('step1');
        $session->step1 = $values;
        $session->anno_calcolo = 2012; // per ora imposto a mano l'anno del calcolo
        // ottengo gli indici subambiti da mostrare e li metto in sessione (possibile metodo a parte da sviluppare volendo)
        // prendo i dati da mostrare
        // FARE UN HELPER??????????
        $db_row_sambiti = $this->lonato_u_sambiti->getAll($values["id_u_sambiti"]);
        // creo l'output formattato html4
        // sub ambiti
        foreach ($db_row_sambiti as $chiave_sambiti => $valore_sambiti_riga) {
            $stampa = "";
            // inizio la tabella
            $stampa.='<table id="indici-sambiti" class="right">';
            // intestazione
            $stampa.='<tr>';
            $stampa.="<td colspan='3' style='font-weight:bold; font-size:14px;'>Indici sub ambiti:</td></tr>";
            $tipo_stima = $valore_sambiti_riga->indice_calcolo_capacita_edificatoria;
            if (strtolower($tipo_stima[0]) == "v") {
                // indice fondiario
                $stampa.='<tr class="header-tabella1"><td>';
                $stampa.="<td>Indice fondiario</td>";
                $stampa.="<td>" . $valore_sambiti_riga->indice_fondiario . "</td>";
                $stampa.="<td>(volume in m3/m2)</td></tr>";
                // incremento lotti saturi
                $stampa.='<tr><td>';
                $stampa.="<td>Incremento lotti saturi</td>";
                $stampa.="<td>" . $valore_sambiti_riga->incremento_lotti_saturi_i . "</td>";
                $stampa.="<td>(% di volume da indice)</td></tr>";
                // indice territoriale
                $stampa.='<tr class="header-tabella1"><td>';
                $stampa.="<td>Indice territoriale</td>";
                $stampa.="<td>" . $valore_sambiti_riga->indice_territoriale . "</td>";
                $stampa.="<td>(volume in m3/m2)</td></tr>";
                // volumetria preesistente               
                $stampa.='<tr><td>';
                $stampa.="<td>Volume preesistente</td>";
                $stampa.="<td>" . $valore_sambiti_riga->volume_preesistente . "</td>";
                $stampa.="<td>(volume in m3)</td></tr>";
                // volume incremento   
                $stampa.='<tr class="header-tabella1"><td>';
                $stampa.="<td>Volume incremento</td>";
                $stampa.="<td>" . $valore_sambiti_riga->volume_incremento . "</td>";
                $stampa.="<td>(volume in m3)</td></tr>";
                // volume predefinito
                $stampa.='<tr><td>';
                $stampa.="<td>Volume predefinito</td>";
                $stampa.="<td>" . $valore_sambiti_riga->volume_predefinito_d . "</td>";
                $stampa.="<td>(volume in m3)</td></tr>";
                // indice fondiario aggiuntivo
                $stampa.='<tr class="header-tabella1"><td>';
                $stampa.="<td>Indice fondiario aggiuntivo</td>";
                $stampa.="<td>" . $valore_sambiti_riga->indice_fondiario_aggiuntivo . "</td>";
                $stampa.="<td>(volume in m3/m2)</td></tr>";
                // chiudo la tabella
                $stampa.='</table>';
            } elseif (strtolower($tipo_stima[0]) == "u") {
                // indice utilizzazione fondiaria
                $stampa.='<tr><td>';
                $stampa.="<td>Indice utilizzazione fondiaria</td>";
                $stampa.="<td>" . $valore_sambiti_riga->utilizzazione_fondiaria . "</td>";
                $stampa.="<td>(slp in m2slp/m2)</td></tr>";
                // incremento lotti saturi
                $stampa.='<tr class="header-tabella1"><td>';
                $stampa.="<td>Incremento lotti saturi</td>";
                $stampa.="<td>" . $valore_sambiti_riga->incremento_lotti_saturi_u . "</td>";
                $stampa.="<td>(% slp da indice)</td></tr>";
                // utilizzazione  territoriale
                $stampa.='<tr><td>';
                $stampa.="<td>Indice utilizzazione territoriale</td>";
                $stampa.="<td>" . $valore_sambiti_riga->utilizzazione_territoriale . "</td>";
                $stampa.="<td>(slp in m2slp/m2)</td></tr>";
                // utilizzazione  preesistente               
                $stampa.='<tr  class="header-tabella1"><td>';
                $stampa.="<td>Utilizzazione preesistente</td>";
                $stampa.="<td>" . $valore_sambiti_riga->utilizzazione_preesistente . "</td>";
                $stampa.="<td>(slp in m2slp)</td></tr>";
                // utilizzazione incremento   
                $stampa.='<tr><td>';
                $stampa.="<td>Utilizzazione  incremento</td>";
                $stampa.="<td>" . $valore_sambiti_riga->utilizzazione_incremento . "</td>";
                $stampa.="<td>(slp in m2slp)</td></tr>";
                // utilizzazione predefinita
                $stampa.='<tr class="header-tabella1"><td>';
                $stampa.="<td>Utilizzazione predefinita</td>";
                $stampa.="<td>" . $valore_sambiti_riga->utilizzazione_predefinita_d . "</td>";
                $stampa.="<td>(slp in m2slp)</td></tr>";
                // chiudo la tabella     
                $stampa.='</table>';
            } else {
                throw new Exception("Errore in process_lonato_imu: tipo stima non valida: " . $tipo_stima);
            }
        }
        // salvo in sessione
        $session->indici_sambiti_stampa = $stampa;
        // salvo in sessione per stampa
        $session->indici_sambiti_dati = $stampa;


        // ottengo gli indici di mambito
        // ottengo la dbtable
        $db_row_mambiti = $this->lonato_u_mambiti->getAll($values["id_m_ambiti"]);
        $stampa = "";
        foreach ($db_row_mambiti as $chiave_mambiti => $valore_mambiti_riga) {
            // inizio la tabella
            $stampa.='<table id="indici-mambiti" class="left">';
            // intestazione
            $stampa.='<tr>';
            $stampa.="<td colspan='3' style='font-weight:bold; font-size:14px;'>Indici macro ambiti:</td></tr>";
            // valore compensativo aggiuntivo
            $stampa.='<tr class="header-tabella1"><td>';
            $stampa.="<td>Valore compensativo unitario</td>";
            $valore_comprensativo_unitario = $valore_mambiti_riga->valore_comprensativo_unitario;
            $stampa.="<td>" . $valore_mambiti_riga->valore_comprensativo_unitario . "</td>";
            // contributo compensativo aggiuntivo
            $stampa.='<tr><td>';
            $stampa.="<td>Contributo compensativo aggiuntivo</td>";
            $contributo_compensativo_aggiuntivo = $valore_mambiti_riga->contributo_compensativo_aggiuntivo;
            $stampa.="<td>" . $valore_mambiti_riga->contributo_compensativo_aggiuntivo . "</td>";
            // standard pubblico qualità
            $stampa.='<tr class="header-tabella1"><td>';
            $stampa.="<td>Standard pubblico qualità</td>";
            $standard_pubblico_qualita = $valore_mambiti_riga->standard_pubblico_qualita;
            $stampa.="<td>" . $valore_mambiti_riga->standard_pubblico_qualita . "</td>";
            // chiudo la tabella     
            $stampa.='</table>';
        }
        // salvo in sessione
        $session->indici_mambiti_stampa = $stampa;
        // salvo in sessione per stampa
        $session->indici_mambiti_stampa_txt = array("valore_comprensativo_unitario" => $valore_comprensativo_unitario,
            "contributo_compensativo_aggiuntivo" => $contributo_compensativo_aggiuntivo,
            "standard_pubblico_qualita" => $standard_pubblico_qualita);

        // ottengo i dati riassuntivi form precedente
        // query al db per ottenere i dati dai mostrare
        // nome macro ambito
        $nome_macro_ambito_rowset = $this->lonato_u_mambiti->getAll($values["id_m_ambiti"]);
        foreach ($nome_macro_ambito_rowset as $chiave => $valore) {
            $nome_macro_ambito = $valore->descrizione;
        }
        // nome sub ambito
        $nome_sub_ambito_rowset = $this->lonato_u_sambiti->getAll($values["id_u_sambiti"]);
        foreach ($nome_sub_ambito_rowset as $chiave => $valore) {
            $nome_sub_ambito = $valore->descrizione;
        }
        // nome zona
        $nome_zona_rowset = $this->lonato_s_zone->getAll($values["id_s_zone"]);
        foreach ($nome_zona_rowset as $chiave => $valore) {
            $nome_zona = $valore->descrizione_tipo_stima;
        }
        // modalità di intervento
        $modalita_intervento_rowset = $this->lonato_u_modinterv->getAll($values["id_u_modinterv"]);
        foreach ($modalita_intervento_rowset as $chiave => $valore) {
            $modalita_intervento = $valore->descrizione_estesa;
        }

        // preparo la stampa della tabella
        $stampa = "";
        // inizio la tabella
        $stampa.='<table id="indici-form1" class="left">';
        // intestazione
        $stampa.='<tr>';
        $stampa.="<td colspan='3' style='font-weight:bold; font-size:14px;'>Dati scelti in precedenza:</td></tr>";
        // macro ambito
        $stampa.='<tr class="header-tabella1"><td>';
        $stampa.="<td>Macro ambito</td>";
        $stampa.="<td>" . $nome_macro_ambito . "</td>";
        // sub ambito
        $stampa.='<tr><td>';
        $stampa.="<td>Sub ambito</td>";
        $stampa.="<td>" . $nome_sub_ambito . "</td>";
        // zona
        $stampa.='<tr class="header-tabella1"><td>';
        $stampa.="<td>Nome zona</td>";
        $stampa.="<td>" . $nome_zona . "</td>";
        // valore urbanizzata
        $stampa.='<tr><td>';
        $stampa.="<td>Area urbanizzata</td>";
        $stampa.="<td>";
        $area_urbanizzata = ($values["area_urbanizzata"] == 1) ? "Si" : "No";
        $stampa.=$area_urbanizzata;
        $stampa.="</td>";
        $lotto_saturo = ($values["lotto_saturo"] == 1) ? "Si" : "No";
        if ($values["area_urbanizzata"] == 1) {
            // lotto saturo: solo se urbanizzata
            $stampa.='<tr class="header-tabella1"><td></td>';
            $stampa.="<td>Lotto saturo</td>";
            $stampa.="<td>";
            $stampa.=$lotto_saturo;
            $stampa.="</td>";
        }
        // modalità di intervento
        $stampa.='<tr ';
        $stampa.=($values["area_urbanizzata"] == 1) ? "" : "class='header-tabella1'";
        $stampa.='><td></td>';
        $stampa.="<td>Modalità di intervento</td>";
        $stampa.="<td>" . $modalita_intervento . "</td>";

        // superficie
        $stampa.='<tr ';
        $stampa.=($values["area_urbanizzata"] == 1) ? "class='header-tabella1'" : "";
        $stampa.='><td></td>';
        $stampa.="<td>Superficie edificatoria</td>";
        $stampa.="<td>" . $values["superficie"] . "</td>";
        // volumetria
        $stampa.='<tr ';
        $stampa.=($values["area_urbanizzata"] == 1) ? "" : "class='header-tabella1'";
        $stampa.='><td></td>';
        $stampa.="<td>Volumetria</td>";
        $stampa.="<td>" . $values["capacita_edificatoria"] . "</td>";
        // chiudo la tabella     
        $stampa.='</table>';
        // salvo in sessione per stampa
        $session->riassunto_step1_txt = array("nome_macro_ambito" => $nome_macro_ambito,
            "nome_sub_ambito" => $nome_sub_ambito,
            "nome_zona" => $nome_zona,
            "area_urbanizzata" => $area_urbanizzata,
            "lotto_saturo" => $lotto_saturo,
            "modalita_intervento" => $modalita_intervento,
            "superficie" => $values["superficie"],
            "volumetria" => $values["capacita_edificatoria"]);
        // salvo in sessione
        $session->riassunto_step1 = $stampa;


        return true; // non ho incontrato errori
    }

    public function stimaAction() {
        // action body
        $session = new Zend_Session_Namespace('step1');
        $values = $session->step1;

        $this->view->values = $values;

        require_once APPLICATION_PATH . "/models/Elaborazione/Stima.php";
        $session->capacita_edificatoria = Stima::calcolaCapacitaEdificatoriaLonato();

        $this->view->capacita_edificatoria = $session->capacita_edificatoria;

        $form = new Application_Form_Lonatodelgardastep2(array(
                    'id_u_mambito' => $values['id_m_ambiti'],
                ));

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                if ($this->_process_lonato_imu_step2($form->getValues())) {
                    $urlOptions = array('controller' => 'Lonatodelgarda', 'action' => 'anagrafe');
                    $this->view->notifica = '<style>.notifica{ background-color:green; padding:2px;}</style>Modulo salvato con successo.';
//                    
//                    echo "<pre>";
//                    print_r($this->_getAllParams());
//                    echo "</pre>";                    
                    $this->_helper->redirector->gotoRoute($urlOptions);
                } else {
                    $urlOptions = array('controller' => 'azioni', 'action' => 'verbale-di-contestazione');
                    $this->_helper->redirector->gotoRoute($urlOptions, 'azioni');
                    $this->view->notifica = '<span style="padding:2px;">Ops, si è verificato un errore.</span>';
                }
            }
        }

        $this->view->form = $form;
    }

    protected function _process_lonato_imu_step2($valori) {

        $session = new Zend_Session_Namespace('step1');

        // preparo i dati di far per la step2: devono essere tutti in indice da 0 a n
        $indice = 0;
        foreach ($valori as $chiave => $valore) {
            if ($valore)
                $percentualeQuote[$indice] = $valore;
            else
                $percentualeQuote[$indice] = 0;
            
            $indice++;
        }
        $session->quote = $percentualeQuote;
        // effettuo il calcolo della stima e capacit√† edificatoria
        require_once APPLICATION_PATH . "/models/Elaborazione/Stima.php";
        // capacita edificatoria
        $session->capacitaEdificatoria = Stima::calcolaCapacitaEdificatoriaLonato();
        // metto in sessione la stima unitaria
        $session->stimaUnitaria = Stima::calcolaStimaSingolaLonato($percentualeQuote);
        // calcolo valore area edificabile: semplice moltiplicazione
        $session->valoreAreaEdificabile = $session->stimaUnitaria * $session->capacitaEdificatoria;
        return true; // non ho incontrato errori
    }

    public function anagrafeAction() {
        $session = new Zend_Session_Namespace('step1');
        $values = $session->step1;

        $this->view->values = $values;

        $form = new Application_Form_Anagrafe();

        $request = $this->getRequest();
        if ($request->isPost()) {

            if ($form->isValid($request->getPost())) {

                if ($this->_process_anagrafe($form->getValues())) {
                    $urlOptions = array('controller' => 'Lonatodelgarda', 'action' => 'stampa');
                    $this->view->notifica = '<style>.notifica{ background-color:green; padding:2px;}</style>Modulo salvato con successo.';
                    $this->_helper->redirector->gotoRoute($urlOptions);
                } else {
                    $this->view->notifica = '<span style="padding:2px;">Ops, si è verificato un errore.</span>';
                }
            }
        }

        $this->view->form = $form;
    }

    protected function _process_anagrafe($values) {

        $session2 = new Zend_Session_Namespace('anagrafe');
        $session2->anagrafe = $values;
        $anagrafe = $session2->anagrafe;
        $var = $values;

        $ret = $this->lonato_log->inserisciLog($values['nome'], $values['cognome'], $values['cf']);
        return $ret;
    }

    public function stampaAction() {
        // action body
        $this->_helper->_layout->setLayout('stampa');

        $session = new Zend_Session_Namespace('step1');
        $session2 = new Zend_Session_Namespace('anagrafe');

        $values = $session->step1;
        $anagrafe = $session2->anagrafe;

        $this->view->values = $values;
        $this->view->anagrafe = $anagrafe;
        $this->view->riassunto_step1_txt = $session->riassunto_step1_txt;
        $this->view->capacitaEdificatoria = $session->capacitaEdificatoria;
        $this->view->stimaUnitaria = $session->stimaUnitaria;
        $this->view->valoreAreaEdificabile = $session->valoreAreaEdificabile;
        $this->view->indici_sambiti_dati = $session->indici_sambiti_dati;
        $this->view->indici_mambiti_stampa = $session->indici_mambiti_stampa_txt;
        $this->view->quote = $session->quote;
    }

}

