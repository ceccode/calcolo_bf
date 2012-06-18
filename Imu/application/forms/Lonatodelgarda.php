<?php

class Application_Form_Lonatodelgarda extends Zend_Form {

    public function init() {


        $this->setName("calcolo_imu_lonato");
        $this->setMethod('post');

        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Campo obbligatorio');

        // data corrente
        $session = new Zend_Session_Namespace('step1');
        $data_calcolo = $session->data_calcolo;

        //macro ambiti
        $macro_ambito = $this->createElement('select', 'id_m_ambiti', array('onChange' => 'selSubAmbiti(this.value)'));
        $macro_ambito->getDecorator('label')->setOption('escape', false);
        $macro_ambito->setLabel('Macro ambito: * <img name="help" class="help-img" id="help_m_ambiti" src="/public/media/css/images/help.png"/>');

        $lonato_u_mambiti = Factory_dbTable::getClass("017092", "u_mambiti");
        $select = $lonato_u_mambiti->select()
                ->from($lonato_u_mambiti->getName(), array('id_u_mambiti', 'descrizione'))
                ->where('record_attivo = 1')
                ->where('data_inizio <= ?', $data_calcolo)
                ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data_calcolo)
                ->order("id_u_mambiti");

        $stmt = $lonato_u_mambiti->fetchAll($select);

        foreach ($stmt as $value) {
            $descrizione = htmlspecialchars_decode(html_entity_decode($value->descrizione));
            $id_u_mambiti = intval($value->id_u_mambiti);
            $macro_ambito->addMultiOptions(array($id_u_mambiti => $descrizione));
        }
        $macro_ambito->setValue(1);
        $macro_ambito->setRequired(true);
        

//         $macro_ambito->getDecorator('Description')->setEscape(false);
//         $macro_ambito->getDecorator('Description')->setTag(false);
//         $macro_ambito->setDescription('<img name="help" class="help-img" id="help_m_ambiti" src="/public/media/css/images/help.png"/>');
        $this->addElement($macro_ambito);

        //sub ambiti
        $sub_ambito = $this->createElement('select', 'id_u_sambiti', array('onChange' => 'inputVolumetria(this.value)'));
        $sub_ambito->getDecorator('label')->setOption('escape', false);
        $sub_ambito->setLabel('Sub ambito: * <img name="help" class="help-img" id="help_s_ambiti" src="/public/media/css/images/help.png"/>');
        $sub_ambito->addValidator($notEmpty, true);

        $lonato_u_sambiti = Factory_dbTable::getClass("017092", "u_sambiti");
        $select2 = $lonato_u_sambiti->select()
                ->from($lonato_u_sambiti->getName(), array('id_u_sambiti', 'descrizione'))
                ->where('record_attivo = 1 AND id_u_mambiti=1')
                ->where('data_inizio <= ?', $data_calcolo)
                ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data_calcolo)
                ->order("id_u_sambiti");

        $stmt2 = $lonato_u_sambiti->fetchAll($select2);

        foreach ($stmt2 as $value) {
            $descrizione = htmlspecialchars_decode(html_entity_decode($value->descrizione));
            $id_u_sambiti = intval($value->id_u_sambiti);
            $sub_ambito->addMultiOptions(array($id_u_sambiti => $descrizione));
        }
        $sub_ambito->setRequired(true)
                ->setRegisterInArrayValidator(false);
        //$macro_ambito->setValue(1);//original
        $sub_ambito->setValue(1);//?
//         $sub_ambito->getDecorator('Description')->setEscape(false);
//         $sub_ambito->getDecorator('Description')->setTag(false);
//         $sub_ambito->setDescription('<img name="help" class="help-img" id="help_s_ambiti" src="/public/media/css/images/help.png"/>');
        $this->addElement($sub_ambito);


        //localizzazione
        $s_zona = $this->createElement('select', 'id_s_zone', array());
        $s_zona->getDecorator('label')->setOption('escape', false);
        $s_zona->setLabel('Zona: * <img name="help" class="help-img" id="help_s_zona" src="/public/media/css/images/help.png"/>');
        
        $lonato_s_zone = Factory_dbTable::getClass("017092", "s_zone");
        $select3 = $lonato_s_zone->select()
                ->from($lonato_s_zone->getName(), array('id_s_zone', 'descrizione_tipo_stima'))
                ->where('record_attivo = 1')
                ->where('data_inizio <= ?', $data_calcolo)
                ->where('data_fine > ? OR data_fine = \'0000-00-00\'', $data_calcolo)
                ->order("id_s_zone");

        $stmt3 = $lonato_s_zone->fetchAll($select3);

        foreach ($stmt3 as $value) {
            $descrizione = htmlspecialchars_decode(html_entity_decode($value->descrizione_tipo_stima));
            $id_s_zona = intval($value->id_s_zone);
            $s_zona->addMultiOptions(array($id_s_zona => $descrizione));
        }
        $s_zona->setRequired(true)
                ->setRegisterInArrayValidator(false);

        $s_zona->setValue('Barcuzzi - Lido');
//         $s_zona->getDecorator('Description')->setEscape(false);
//         $s_zona->getDecorator('Description')->setTag(false);
//         $s_zona->setDescription('<img name="help" class="help-img" id="help_s_zona" src="/public/media/css/images/help.png"/>');        
        
        $this->addElement($s_zona);


        //area urbanizzata
        $urbanizzata = $this->createElement('radio', 'area_urbanizzata');
        $urbanizzata->getDecorator('label')->setOption('escape', false);
        $urbanizzata->setLabel('Area urbanizzata: <img name="help" class="help-img" id="help_urbanizzata" src="/public/media/css/images/help.png"/>');
        $urbanizzata->addMultiOption('1', 'Si');
        $urbanizzata->addMultiOption('0', 'No');
        $urbanizzata->setValue("0");
        $urbanizzata->setRequired(true);

//         $urbanizzata->getDecorator('Description')->setEscape(false);
//         $urbanizzata->getDecorator('Description')->setTag(false);
//         $urbanizzata->setDescription('<img name="help" class="help-img" id="help_urbanizzata" src="/public/media/css/images/help.png"/>');        
        
        $this->addElement($urbanizzata);

        //modalità intervento
        $intervento = $this->createElement('select', 'id_u_modinterv');
        $intervento->getDecorator('label')->setOption('escape', false);
        $intervento->setLabel('Modalità intervento: * <img name="help" class="help-img" id="help_intervento" src="/public/media/css/images/help.png"/>');

        $lonato_u_modinterv = Factory_dbTable::getClass("017092", "u_modinterv");
        $select4 = $lonato_u_modinterv->select()
                ->from($lonato_u_modinterv->getName(), array('id_u_modinterv', 'descrizione_estesa'))
                ->where('record_attivo = 1 AND area_urbanizzata=0')
                ->order("id_u_modinterv");

        $stmt4 = $lonato_u_modinterv->fetchAll($select4);

        foreach ($stmt4 as $value) {
            $descrizione = htmlspecialchars_decode(html_entity_decode($value->descrizione_estesa));
            $id_u_modinterv = intval($value->id_u_modinterv);
            $intervento->addMultiOptions(array($id_u_modinterv => $descrizione));
        }
        $intervento->setRequired(true)
                ->setRegisterInArrayValidator(false);
        $intervento->addValidator($notEmpty, true);
        
//         $intervento->getDecorator('Description')->setEscape(false);
//         $intervento->getDecorator('Description')->setTag(false);
//         $intervento->setDescription('<img name="help" class="help-img" id="help_intervento" src="/public/media/css/images/help.png"/>');        
        
        $this->addElement($intervento);


        //lotto saturo
        $lotto_saturo = $this->createElement('radio', 'lotto_saturo');
        $lotto_saturo->getDecorator('label')->setOption('escape', false);
        $lotto_saturo->setLabel('Lotto saturo: <img name="help" class="help-img" id="help_lotto_saturo" src="/public/media/css/images/help.png"/>');
        $lotto_saturo->addMultiOption('1', 'Si');
        $lotto_saturo->addMultiOption('0', 'No');
        $lotto_saturo->setValue("0");
        
//         $lotto_saturo->getDecorator('Description')->setEscape(false);
//         $lotto_saturo->getDecorator('Description')->setTag(false);
//         $lotto_saturo->setDescription('<img name="help" class="help-img" id="help_lotto_saturo" src="/public/media/css/images/help.png"/>');        
        
        $lotto_saturo->setRequired(true);

        //superficie                
        $this->addElement($lotto_saturo);


        //superficie        
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Campo obbligatorio');

        $superficie = $this->createElement('text', 'superficie', array());
        $superficie->getDecorator('label')->setOption('escape', false);
        $superficie->setLabel('Superficie territoriale: * (mq) <img name="help" class="help-img" id="help_superficie" src="/public/media/css/images/help.png"/>');
        $superficie->addValidator('Float', false, array('messages' => 'Solo cifre separate da virgola'));
        $superficie->setRequired(true);
        $superficie->addValidator($notEmpty, true);
        
//         $superficie->getDecorator('Description')->setEscape(false);
//         $superficie->getDecorator('Description')->setTag(false);
//         $superficie->setDescription('<img name="help" class="help-img" id="help_superficie" src="/public/media/css/images/help.png"/>');        
        
        $this->addElement($superficie);

        //capacita_edificatoria
        //dipende dal subambito
        $cepp = $this->createElement('text', 'capacita_edificatoria', array());

        $cepp->getDecorator('label')->setOption('escape', false);        
        $cepp->setLabel('Inputare volumetria: * (m3) <img name="help" class="help-img" id="help_cepp" src="/public/media/css/images/help.png"/>');
        $cepp->addValidator('Float', false, array('messages' => 'Solo cifre separate da virgola'));

//         $cepp->getDecorator('Description')->setEscape(false);
//         $cepp->getDecorator('Description')->setTag(false);
//         $cepp->setDescription('<img name="help" class="help-img" id="help_cepp" src="/public/media/css/images/help.png"/>');        
        
        $this->addElement($cepp);

        // pulsante invia
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Continua il calcolo',
        ));
    }

}

