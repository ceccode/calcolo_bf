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
        
        $lonato_u_sambiti = Factory_dbTable::getClass("lonato", "u_sambiti");
        
        $q  = stripcslashes($this->_request->getParam('categoria')); 
        if(!empty($q)) {
            $result = $lonato_u_sambiti->getSubAmbitiByMacroAmbito($q);
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
        
        $lonato_u_modinterv = Factory_dbTable::getClass("lonato", "u_modinterv");
       
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
        
        $lonato_u_sambiti = Factory_dbTable::getClass("lonato", "u_sambiti");
       
        $q  = stripcslashes($this->_request->getParam('categoria')); 
        if(isset($q)) {
            $result = $lonato_u_sambiti->getVolumetria($q);
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
    
    
    public function validateFormStep1Action(){
        
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('Layout')->disableLayout();
        
        $f = new Application_Form_Lonatodelgarda();
        $f->isValid($this->_getAllParams());
        $json = $f->getMessages();
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


}

