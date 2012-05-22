<?php

class LonatodelgardaController extends Zend_Controller_Action {

    public function init() {
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

        //$namespace->step1 = $values;
//        echo "<pre>";
//        print_r($values);
//        echo "</pre>";        
        return true; // non ho incontrato errori
    }

    public function stimaAction() {
        // action body
        $session = new Zend_Session_Namespace('step1');
        $values = $session->step1;

//        echo "<pre>";
//        print_r($values);
//        echo "</pre>"; 

        $this->view->values = $values;
        
        require_once APPLICATION_PATH . "/models/Elaborazione/Stima.php";                
        $session->capacita_edificatoria  = Stima::calcolaCapacitaEdificatoriaLonato();        

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
        $lonato_u_destammesse = Factory_dbTable::getClass("lonato", "u_destammesse");
        $stmt5 = $lonato_u_destammesse->filtroDestinazioniAmmesse($form1['id_m_ambiti']);

        // preparo i dati di far per la step2: devono essere tutti in indice da 0 a n
        $indice = 0;
        foreach ($valori as $chiave => $valore) {
            $percentualeQuote[$indice] = $valore;
            $indice++;
        }

        // effettuo il calcolo della stima
        require_once APPLICATION_PATH . "/models/Elaborazione/Stima.php";
        // metto in sessione le quote
        $session->step2 = $var;
        // metto in sessione la stima unitaria
        $session->stimaUnitaria         = Stima::stimaSingolaLonato($stmt5, $percentualeQuote);
        $session->capacitaEdificatoria  = Stima::calcolaCapacitaEdificatoriaLonato();
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
        
        echo "<pre>";
        print_r($var);
        echo "</pre>";         
        
        return true;
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

