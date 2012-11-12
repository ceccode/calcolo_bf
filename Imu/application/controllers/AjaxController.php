<?php

class AjaxController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }
    
    public function searchSubambitiAction()
    {
        // action body
        $this->_helper->_layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);                
        
        // data corrente
        $session = new Zend_Session_Namespace('step1');
        $data_calcolo = $session->data_calcolo;
        
        $lonato_u_sambiti = Factory_dbTable::getClass("017092", "u_sambiti");
        
        $q  = stripcslashes($this->_request->getParam('categoria')); 
        if(!empty($q)) {
            $result = $lonato_u_sambiti->getSubAmbitiByMacroAmbito($q,$data_calcolo);
        } 
        
        $ret = array();
        
        foreach ($result as $value) {            
          $descrizione = htmlspecialchars_decode(html_entity_decode($value->descrizione));
          $id_u_sambiti = intval($value->id_u_sambiti);
          $ret[] = array('id'=>$id_u_sambiti,'descrizione'=>$descrizione);
        }       
                       
        echo json_encode($ret);          
    }   
    
    
    
    public function searchModalitainterventoAction()
    {
        // action body
        $this->_helper->_layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);                
        
        $lonato_u_modinterv = Factory_dbTable::getClass("017092", "u_modinterv");
       
        $q  = stripcslashes($this->_request->getParam('categoria')); 
        if(isset($q)) {
            $result = $lonato_u_modinterv->getModIntByUrbanizzazione($q);
        } 
        
        $ret = array();
        
        foreach ($result as $value) {            
          $descrizione = htmlspecialchars_decode(html_entity_decode($value->descrizione_estesa));
          $id_u_modinterv = intval($value->id_u_modinterv);
          $ret[] = array('id'=>$id_u_modinterv,'descrizione'=>$descrizione);
        }       
                       
        echo json_encode($ret);          
    }  
    
    public function searchVolumetriaAction()
    {
        // action body
        $this->_helper->_layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);                
        
        // data corrente
        $session = new Zend_Session_Namespace('step1');
        $data_calcolo = $session->data_calcolo;
        
        $lonato_u_sambiti = Factory_dbTable::getClass("017092", "u_sambiti");
       
        $q  = stripcslashes($this->_request->getParam('categoria')); 
        if(isset($q)) {
            $result = $lonato_u_sambiti->getVolumetria($q,$data_calcolo);
        }
                    
        
        foreach ($result as $value) {            
          $descrizione = htmlspecialchars_decode(html_entity_decode($value->indice_calcolo_capacita_edificatoria));
          $label = "Imputare volumetria: ";
          $ret = array('label'=>$label,'descrizione'=>$descrizione);
        }
               
        echo json_encode($ret);          
    }    
    
    

    /*
     * -------------------------------------------------------------------------
     * controlli ajax dei form
     */

    
    public function verificaQuoteAction() {
        
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('Layout')->disableLayout();
        
        $session = new Zend_Session_Namespace('step1');
        $values = $session->step1;
        
        $f = new Application_Form_Lonatodelgardastep2(array(
                    'id_u_mambito' => $values['id_m_ambiti'],
                ));        
              
        $input_utente = $this->_getAllParams();
                          
        unset($input_utente['continua_stampa']);
        unset($input_utente['controller']);
        unset($input_utente['action']);
        unset($input_utente['module']);       
                        
        $indice = 0;
        $new_input_utente = array();
        foreach ($input_utente as $key => $value) {
            $new_input_utente[$indice] = $value;
            $indice++;
        }
                       
        // effettuo il calcolo della stima e capacità edificatoria
        require_once APPLICATION_PATH . "/models/Elaborazione/stima.php";
        // capacita edificatoria
        // metto in sessione la stima unitaria
        $somma_quote = Stima::verificaQuote($new_input_utente);
        
        $ret = array('somma_quote' => $somma_quote);
        
        
        $json = $ret;      
        header('Content-type: application/json');
        echo Zend_Json::encode($json);      
        
    }
    
    public function validateFormStep1Action(){
        
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('Layout')->disableLayout();
        
        $f = new Application_Form_Lonatodelgarda();
        $f->isValid($this->_getAllParams());
        $json = $f->getMessages();
        header('Content-type: application/json');
        echo Zend_Json::encode($json);
    } 
    
    
    public function validateAnagrafeAction(){
        
         $this->_helper->viewRenderer->setNoRender();
         $this->_helper->getHelper('Layout')->disableLayout();
        
         $f = new Application_Form_Anagrafe();
         $f2 = new Application_Form_Anagrafe();
         $input = $this->_getAllParams();
        
         unset($input['stampa_soc']);
         unset($input['controller']);
         unset($input['action']);
         unset($input['module']);        
        
         $ret = $f->isValid($input);

        $session = new Zend_Session_Namespace('anagrafe');
        $session->anagrafe = $input;
        $anagrafe = $session->anagrafe;       
        
        $json = $f->getMessages();
        if ($ret){
            $json['valid'] = true;
            }else {
                $json['valid'] = false;
            }
        
        header('Content-type: application/json');
        echo Zend_Json::encode($json);
    }     
    
    
    public function validateFormStep2Action(){
        
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('Layout')->disableLayout();
        
        $session = new Zend_Session_Namespace('step1');
        $values = $session->step1;
        
        $f = new Application_Form_Lonatodelgardastep2(array(
                    'id_u_mambito' => $values['id_m_ambiti'],
                ));        
              
        $f->isValid($this->_getAllParams());
        $json = $f->getMessages();
        
        header('Content-type: application/json');
        echo Zend_Json::encode($json);
    }
    
    
    
    public function calcolaValoreStimatoAction(){
        
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('Layout')->disableLayout();
        
        $session = new Zend_Session_Namespace('step1');
        $values = $session->step1;
        
        $f = new Application_Form_Lonatodelgardastep2(array(
                    'id_u_mambito' => $values['id_m_ambiti'],
                ));        
              
//        $f->isValid($this->_getAllParams());
        
        $input_utente = $this->_getAllParams();
        
//        $f = '{"1":"1","2":"","3":"","4":"","5":"","6":"","7":"","8":"","continua_stampa":"Continua per stampare"}';
        
//        echo "<pre>";
//        print_r($f);
//        echo "</pre>";  
        //array numerico che parte da 0
 //       $input_utente = (array) json_decode($f);
        
//        echo "<pre>";
//        print_r($input_utente);
//        echo "</pre>";   
                
        unset($input_utente['continua_stampa']);
        unset($input_utente['controller']);
        unset($input_utente['action']);
        unset($input_utente['module']);       
                
//        echo "<pre>";
//        print_r($input_utente);
//        echo "</pre>";      
        
        $indice = 0;
        $new_input_utente = array();
        foreach ($input_utente as $key => $value) {
            $new_input_utente[$indice] = $value;
            $indice++;
        }
         
//        echo "<pre>";
//        print_r($new_input_utente);
//        echo "</pre>";        
        
        // effettuo il calcolo della stima e capacit√† edificatoria
        require_once APPLICATION_PATH . "/models/Elaborazione/stima.php";
        require_once APPLICATION_PATH . "/models/Utility.php";
        
        // capacita edificatoria
        // metto in sessione la stima unitaria
        $valore_stimato = Stima::calcolaStimaSingolaLonato($new_input_utente);   
        $valore_stimato2 = $valore_stimato * $session->capacita_edificatoria;    
        
        $ret = array('valore_area_calcolata' => Utility::formattaNumeroPerStampa($valore_stimato,true), 'valore_area_edificabile' => Utility::formattaNumeroPerStampa($valore_stimato2,true));
        
//        print_r($ret);
        
        $json = $ret;      
        header('Content-type: application/json');
        echo Zend_Json::encode($json);
    }    
        

}

