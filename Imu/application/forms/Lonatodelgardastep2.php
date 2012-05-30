<?php

class Application_Form_Lonatodelgardastep2 extends Zend_Form
{
    private $_id_u_mambito = '';

    public function __construct(array $params = array())
    {
        $this->_id_u_mambito = $params['id_u_mambito'];
        parent::__construct(); // <---- INSTEAD of $this->init()
    }  
    
    public function init()
    {
        
       
        /* Form Elements & Other Definitions Here ... */
        $this->setName("calcolo_imu_lonato_step2");
        $this->setMethod('post');

        // data corrente
        $session = new Zend_Session_Namespace('step1');
        $data_calcolo = $session->data_calcolo;
        
//        
//        //incidenza viabilità
//        $this->addElement('text', 'incidenza_viabilita', array(
//            'label'      => 'Incidenza viabilità:',
//            'validators' => array(
//                array('validator' => 'float'
//                    )
//                )
//        )); 
//        
//      
        
        //destinazioni ammesse                       
        $lonato_u_destammesse = Factory_dbTable::getClass("017092", "u_destammesse");        
        $stmt5 = $lonato_u_destammesse->filtroDestinazioniAmmesse($this->_id_u_mambito,$data_calcolo);
        
        foreach ($stmt5 as $value) {

            $label = $value->descrizione;
            $id    = $value->id_u_destammesse;
            $quota = $value->quota_massima_ammissibile;   
            
            //cepp
            $cepp = $this->createElement('text', $id, array());
            $cepp->setLabel($label);
            
//            $ValidateFloat = new Zend_Validate_Float();
//            $ValidateFloat->setLocale(new Zend_Locale('it_IT'));
//            
//            $cepp->addValidator($ValidateFloat);
            
            $cepp->addValidator('Float', false, array('messages' => 'Solo cifre separate da virgola'));
            $cepp->addValidator('Between', false, array('min' => 0, 'max' => $quota, 'messages' => "Questo valore non può essere maggiore di $quota" ));
            $this->addElement($cepp);       
            
        }        
        
        $this->addElement('submit', 'continua_stampa', array(
            'class' => 'button doc-button',
            'required' => false,
            'ignore'   => true,
            'label'    => 'Continua per stampare',
        ));        
        
        
    }
        


}

