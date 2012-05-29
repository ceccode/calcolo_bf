<?php

class Application_Form_SelezioneIniziale extends Zend_Form {

    public function init() {
        // nome form e metodo
        $this->setName("selezione_iniziale");
        $this->setMethod('post');

        // validator required
        $obbligatorio = new Zend_Validate_NotEmpty();
        $obbligatorio->setMessage('Campo obbligatorio');

        // picker ora
        $data = new Zend_Dojo_Form_Element_DateTextBox('data_calcolo');
        $data->setLabel('Data da utilizzare per il calcolo: *');
        $data->addValidator($obbligatorio,true);
        $this->addElement($data);

        $comune = $this->createElement('select', 'comune');
        $comune->setLabel('Comune da utilizzare: *');
        $comune->addMultiOptions(array("Lonatodelgarda" => "Lonato"));
        $comune->addValidator($obbligatorio,true);
        $this->addElement($comune);



        // pulsante invia
        $this->addElement('submit', 'submit-index', array(
            'ignore' => true,
            'label' => 'Continua',
        ));
    }

}

