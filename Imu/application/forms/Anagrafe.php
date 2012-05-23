<?php

class Application_Form_Anagrafe extends Zend_Form
{

    public function init()
    {
        /* Form Elements & Other Definitions Here ... */
        $this->setName("inserisci_persona");
        $this->setMethod('post');

        
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Campo obbligatorio');         
        
        //nome
        /*
        $this->addElement('text', 'nome', array(
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(0, 255)),
            ),
            'required'   => true,
            'label'      => 'Nome: *',
        ));
        */
        $nome = $this->createElement('text','nome');
        $nome->setLabel('Nome: *');
        $nome->setRequired(true);
        $nome->addValidator($notEmpty, true);
        $this->addElement($nome);        
        
        
        //cognome        
        $cognome = $this->createElement('text','cognome');
        $cognome->setLabel('Cognome: *');
        $cognome->setRequired(true);
        $cognome->addValidator($notEmpty, true);
        $this->addElement($cognome);  
        
//        $this->addElement('text', 'cognome', array(
//            'filters'    => array('StringTrim'),
//            'validators' => array(
//                array('StringLength', true, array(0, 255)),
//            ),
//            'required'   => true,
//            'label'      => 'Cognome: *',
//        ));
        
   
        //cf
        $cf = $this->createElement('text','cf');
        $cf->setLabel('Codice Fiscale: *');
        $cf->setRequired(true);
        $cf->addValidator($notEmpty, true);
        $cf->addPrefixPath('CV_Validate', 'CV/Validate/', 'validate');
        $cf->addValidator('isValidCF');
        $this->addElement($cf);
        
        
        //privacy
        $privacy = $this->createElement('checkbox','privacy');
        $privacy->setLabel('Accetto le condizioni d\' uso e l\'informativa sulla privacy *');
        $privacy->addValidator('GreaterThan', false, array(0,'messages' => "Per continuare devi accetare le condizioni d'utilizzo."));
        //$privacy->addDecorator('Label', array('class' => 'privacy'));
        $this->addElement($privacy);       
                
        //submit
        $submit = $this->createElement('submit','stampa_soc');
        $submit->setLabel('Stampa il documento');
        $submit->setRequired(true);
        $submit->class = 'button doc-button';
        $this->addElement($submit);
        
    }


    
}

