<?php

class Application_Form_SelezionaData extends Zend_Form
{

    public function init()
    {
    // nome form e metodo
        $this->setName("seleziona_data");
        $this->setMethod('post');

        // validator required
        $obbligatorio = new Zend_Validate_NotEmpty();
        $obbligatorio->setMessage('Campo obbligatorio');

        // picker ora
        $data = new Zend_Dojo_Form_Element_DateTextBox('data_calcolo');
        $data->setLabel('Data da utilizzare per il calcolo: *');
        $data->addValidator($obbligatorio,true);
        $this->addElement($data);

        // pulsante invia
        $this->addElement('submit', 'submit-data-1', array(
            'ignore' => true,
            'label' => 'Continua',
        ));    }


}

