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
        
        // nome
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
        
   
        //cf
        $cf = $this->createElement('text','cf');
        $cf->setLabel('Codice Fiscale: *');
        $cf->setRequired(true);
        $cf->addValidator($notEmpty, true);
        //$cf->addPrefixPath('CV_Validate', 'CV/Validate/', 'validate');
        //$cf->addValidator('isValidCF');
        $this->addElement($cf);
        
        
        //privacy
        $privacy = $this->createElement('checkbox','privacy');
        $privacy->setLabel('Accetto le condizioni d\' uso e l\'informativa sulla privacy *');
        $privacy->addValidator('GreaterThan', false, array(0,'messages' => "Per continuare devi accetare le condizioni d'utilizzo."));
        //$privacy->addDecorator('Label', array('class' => 'privacy'));
        $this->addElement($privacy);       
                
        //submit        
        //$url = $this->getView()->url(array('controller'=>'Lonatodelgarda','action'=>'stampa'));
        $submit = $this->createElement('submit','stampa_soc' /*, array('onClick'=>'doValidation2()')*/);
        $submit->setLabel('Stampa il documento');
        $submit->setRequired(true);
        $submit->class = 'button doc-button';
        $this->addElement($submit);
        
    }
}

