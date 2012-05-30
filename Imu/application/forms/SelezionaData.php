<?php

class Application_Form_SelezionaData extends Zend_Form {

    public function init() {
        // nome form e metodo
        $this->setName("seleziona_data");
        $this->setMethod('post');

        // validator required
        $obbligatorio = new Zend_Validate_NotEmpty();
        $obbligatorio->setMessage('Campo obbligatorio');

        // picker ora
        //$data = new Zend_Dojo_Form_Element_DateTextBox('data_calcolo');
        //$data->setLabel('Data da utilizzare per il calcolo: *');
        //$data->setDatePattern("yyyy");
        //$data->addValidator($obbligatorio,true);
        //$this->addElement($data);
        // selezione anno
        $anno = $this->createElement('select', 'anno_calcolo');
        $anno->setLabel('Anno in per il calcolo: *');

        // setto gli anni a seconda del database
        $lonato_anni = Factory_dbTable::getClass("017092", "anni");
        $anni = $lonato_anni->getAnni();
        foreach ($anni as $chiave => $valore) {
            $tanno = htmlspecialchars_decode(html_entity_decode($valore->anno));
            // setto la data per l'anno aggiungendogli -00-00
            $data=$tanno . "-01-01";
            $anno->addMultiOptions(array($data => $tanno));
        }
        $anno->addValidator($obbligatorio, true);
        $this->addElement($anno);




        // pulsante invia
        $this->addElement('submit', 'submit-data-1', array(
            'ignore' => true,
            'label' => 'Continua',
        ));
    }

}

