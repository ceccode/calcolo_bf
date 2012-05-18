<?php

class Application_Form_Anagrafe extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        $this->setName("inserisci_persona");
        $this->setMethod('post');

        
        //nome
        $this->addElement('text', 'nome', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(0, 255)),
            ),
            'required'   => true,
            'label'      => 'Nome: *',
        ));
        
        //cognome
        $this->addElement('text', 'cognome', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(0, 255)),
            ),
            'required'   => true,
            'label'      => 'Cognome: *',
        ));
        
   
        //cf
        $cf = $this->createElement('text','cf');
        $cf->setLabel('Codice Fiscale: *');
        $cf->setRequired(true);
        $cf->addPrefixPath('CV_Validate', 'CV/Validate/', 'validate');
        $cf->addValidator('isValidCF');
        $this->addElement($cf);
        
        
        $this->addElement('submit', 'stampa doc', array(
            'class'=>'button doc-button',
            'required' => false,
            'ignore'   => true,
            'label'    => 'Stampa il documento',
        ));        
        
    }


    
}

