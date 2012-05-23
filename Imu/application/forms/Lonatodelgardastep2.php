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
        $lonato_u_destammesse = Factory_dbTable::getClass("lonato", "u_destammesse");        
        $stmt5 = $lonato_u_destammesse->filtroDestinazioniAmmesse($this->_id_u_mambito);
        
        foreach ($stmt5 as $value) {

            $label = $value->descrizione;
            $id    = $value->id_u_destammesse;
            
            //cepp
            $this->addElement('text', $id, array(
                'label'      => $label,
                'validators' => array(                
                    array('validator' => 'float',
//                        new Zend_Validate_Between(
//                            array(
//                                'min' => 0,
//                                'max' => 10,
//                                'inclusive' => false
//                                )
//                            )
                   )                       
                 ),        
            ));
                      
            $this->addValidator('Between', false, array('min' => 1, 'max' => 65, 'messages' => 'This is Required!' ));
            
        }        

        $this->addElement('submit', 'continua_stampa', array(
            'class' => 'button doc-button cec',
            'required' => false,
            'ignore'   => true,
            'label'    => 'Continua per stampare',
        ));        
        
        
    }
        


}

