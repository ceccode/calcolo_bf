<?php

class LonatodelgardaController extends Zend_Controller_Action {

    public function init() {
        $lonato_s_rifunitariedest = Factory_dbTable::getClass("017092", "s_rifunitariedest");
        $lonato_s_tstima = Factory_dbTable::getClass("017092", "s_tstima");
        $lonato_s_zone = Factory_dbTable::getClass("017092", "s_zone");
        $lonato_u_cessioni = Factory_dbTable::getClass("017092", "u_cessioni");
        $lonato_u_destammesse = Factory_dbTable::getClass("017092", "u_destammesse");
        $lonato_u_mambiti = Factory_dbTable::getClass("017092", "u_mambiti");
        $lonato_u_mdestinazioni = Factory_dbTable::getClass("017092", "u_mdestinazioni");
        $lonato_u_modinterv = Factory_dbTable::getClass("017092", "u_modinterv");
        $lonato_u_sambiti = Factory_dbTable::getClass("017092", "u_sambiti");
        $lonato_u_sdestinazioni = Factory_dbTable::getClass("017092", "u_sdestinazioni");
        $lonato_log = Factory_dbTable::getClass("017092", "log");
    }

    public function indexAction() {
        // action body
        $form = new Application_Form_Lonatodelgarda();

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {

                if ($this->_process_lonato_imu($form->getValues())) {
                    $urlOptions = array('controller' => 'Lonatodelgarda', 'action' => 'stima');
                    $this->view->notifica = '<style>.notifica{ background-color:green; padding:2px;}</style>Modulo salvato con successo.';
                    $this->_helper->redirector->gotoRoute($urlOptions);
                } else {
                    //$urlOptions = array('controller' => 'azioni', 'action' => 'verbale-di-contestazione');
                    //$this->_helper->redirector->gotoRoute($urlOptions,'azioni');
                    $this->view->notifica = '<span style="padding:2px;">Ops, si è verificato un errore.</span>';
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
        $lonato_u_sambiti = Factory_dbTable::getClass("017092", "u_sambiti");
        $db_row_sambiti = $lonato_u_sambiti->getAll($values["id_u_sambiti"]);
        // creo l'output formattato html4
        foreach ($db_row_sambiti as $chiave_sambiti => $valore_sambiti_riga) {
            $stampa = "";
            $tipo_stima = $valore_sambiti_riga->indice_calcolo_capacita_edificatoria;
            if (strtolower($tipo_stima[0]) == "v") {
                // inizio la tabella
                $stampa.='<table id="indici-sambiti" class="left">';
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
                // inizio la tabella
                $stampa.='<table id="indici-sambiti" class="left">';
                // indice utilizzazione fondiaria
                $stampa.='<tr class="header-tabella1"><td>';
                $stampa.="<td>Indice utilizzazione fondiaria</td>";
                $stampa.="<td>" . $valore_sambiti_riga->utilizzazione_fondiaria . "</td>";
                $stampa.="<td>(slp in m2slp/m2)</td></tr>";
                // incremento lotti saturi
                $stampa.='<tr><td>';
                $stampa.="<td>Incremento lotti saturi</td>";
                $stampa.="<td>" . $valore_sambiti_riga->incremento_lotti_saturi_u . "</td>";
                $stampa.="<td>(% slp da indice)</td></tr>";
                // utilizzazione  territoriale
                $stampa.='<tr class="header-tabella1"><td>';
                $stampa.="<td>Indice utilizzazione territoriale</td>";
                $stampa.="<td>" . $valore_sambiti_riga->utilizzazione_territoriale . "</td>";
                $stampa.="<td>(slp in m2slp/m2)</td></tr>";
                // utilizzazione  preesistente               
                $stampa.='<tr><td>';
                $stampa.="<td>Utilizzazione preesistente</td>";
                $stampa.="<td>" . $valore_sambiti_riga->utilizzazione_preesistente . "</td>";
                $stampa.="<td>(slp in m2slp)</td></tr>";
                // utilizzazione incremento   
                $stampa.='<tr class="header-tabella1"><td>';
                $stampa.="<td>Utilizzazione  incremento</td>";
                $stampa.="<td>" . $valore_sambiti_riga->utilizzazione_incremento . "</td>";
                $stampa.="<td>(slp in m2slp)</td></tr>";
                // utilizzazione predefinita
                $stampa.='<tr><td>';
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

        // ottengo gli indici di mambito
        $lonato_u_mambiti = Factory_dbTable::getClass("017092", "u_mambiti");
        // ottengo la dbtable
        $db_row_mambiti = $lonato_u_mambiti->getAll($values["id_m_ambiti"]);
        $stampa = "";
        foreach ($db_row_mambiti as $chiave_mambiti => $valore_mambiti_riga) {
            // inizio la tabella
            $stampa.='<table id="indici-mambiti" class="left">';
            // contributo compensativo aggiuntivo
            $stampa.='<tr class="header-tabella1"><td>';
            $stampa.="<td>Contributo compensativo aggiuntivo</td>";
            $stampa.="<td>" . $valore_mambiti_riga->contributo_compensativo_aggiuntivo . "</td>";
            //$stampa.="<td>(volume in m3/m2)</td></tr>";
            // valore compensativo unitario
            $stampa.='<tr><td>';
            $stampa.="<td>Valore compensativo unitario</td>";
            $stampa.="<td>" . $valore_mambiti_riga->valore_comprensativo_unitario . "</td>";
            //$stampa.="<td>(volume in m3/m2)</td></tr>";
            // standard pubblico qualità
            $stampa.='<tr class="header-tabella1"><td>';
            $stampa.="<td>Standard pubblico qualità</td>";
            $stampa.="<td>" . $valore_mambiti_riga->standard_pubblico_qualita . "</td>";
            //$stampa.="<td>(% di volume da indice)</td></tr>";
            // chiudo la tabella     
            $stampa.='</table>';
        }
        // salvo in sessione
        $session->indici_mambiti_stampa = $stampa;
        
        
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
        // dati form1
        $form1 = $session->step1;
        $lonato_u_destammesse = Factory_dbTable::getClass("017092", "u_destammesse");
        $stmt5 = $lonato_u_destammesse->filtroDestinazioniAmmesse($form1['id_m_ambiti']);

        // preparo i dati di far per la step2: devono essere tutti in indice da 0 a n
        $indice = 0;
        foreach ($valori as $chiave => $valore) {
            $percentualeQuote[$indice] = $valore;
            $indice++;
        }

        // effettuo il calcolo della stima e capacit√† edificatoria
        require_once APPLICATION_PATH . "/models/Elaborazione/Stima.php";
        // metto in sessione le quota
        $session->step2 = $var;
        // capacita edificatoria
        $session->capacitaEdificatoria = Stima::calcolaCapacitaEdificatoriaLonato();
        // metto in sessione la stima unitaria
        $session->stimaUnitaria = Stima::calcolaStimaSingolaLonato($stmt5, $percentualeQuote, $session->capacitaEdificatoria);
        // calcolo valore area edificabile: semplice moltiplicazione
        $session->valoraAreaEdificabile = $session->stimaUnitaria * $session->capacitaEdificatoria;
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

        $lonato_log = Factory_dbTable::getClass("017092", "log");
        $ret = $lonato_log->inserisciLog($values['nome'], $values['cognome'], $values['cf']);
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
    }

}

