<?php

class Application_Form_Lonatodelgarda extends Zend_Form
{

    public function init()
    {
        
       
        $this->setName("calcolo_imu_lonato");
        $this->setMethod('post');
                        
        //macro ambiti
        $macro_ambito = $this->createElement('select', 'id_m_ambiti',array('onChange' => 'selSubAmbiti(this.value)'));
        $macro_ambito->setLabel('Macro ambito: *');        
                              
        $lonato_u_mambiti = Factory_dbTable::getClass("017092", "u_mambiti");
        $select = $lonato_u_mambiti->select()
                                   ->from($lonato_u_mambiti->getName(), array('id_u_mambiti', 'descrizione'))
                                   ->where('record_attivo = 1')
                                   ->order("id_u_mambiti");

        $stmt = $lonato_u_mambiti->fetchAll($select);   
        
        foreach ($stmt as $value) {            
          $descrizione = htmlspecialchars_decode(html_entity_decode($value->descrizione));
          $id_u_mambiti = intval($value->id_u_mambiti);
              $macro_ambito->addMultiOptions(array($id_u_mambiti => $descrizione));
        }
        $macro_ambito->setValue(1);
        $macro_ambito->setRequired(true);
        $this->addElement($macro_ambito);   
        
        
        //sub ambiti
        $sub_ambito = $this->createElement('select', 'id_u_sambiti',array('onChange' => 'inputVolumetria(this.value)'));
        $sub_ambito->setLabel('Sub ambito: *');
                              
        $lonato_u_sambiti = Factory_dbTable::getClass("017092", "u_sambiti");
        $select2 = $lonato_u_sambiti->select()
                                   ->from($lonato_u_sambiti->getName(), array('id_u_sambiti', 'descrizione'))
                                   ->where('record_attivo = 1 AND id_u_mambiti=1')
                                   ->order("id_u_sambiti");

        $stmt2 = $lonato_u_sambiti->fetchAll($select2);         
        
        foreach ($stmt2 as $value) {            
          $descrizione = htmlspecialchars_decode(html_entity_decode($value->descrizione));
          $id_u_sambiti = intval($value->id_u_sambiti);
              $sub_ambito->addMultiOptions(array($id_u_sambiti => $descrizione));
        }
        $sub_ambito->setRequired(true)
                   ->setRegisterInArrayValidator(false);
        $macro_ambito->setValue(1);
        $this->addElement($sub_ambito);
        
        
        //localizzazione
        $s_zona = $this->createElement('select', 'id_s_zone',array());
        $s_zona->setLabel('Zona: *');
                              
        $lonato_s_zone = Factory_dbTable::getClass("017092", "s_zone");
        $select3 = $lonato_s_zone->select()
                                   ->from($lonato_s_zone->getName(), array('id_s_zone', 'descrizione_tipo_stima'))
                                   ->where('record_attivo = 1')
                                   ->order("id_s_zone");

        $stmt3 = $lonato_s_zone->fetchAll($select3);         
        
        foreach ($stmt3 as $value) {
          $descrizione = htmlspecialchars_decode(html_entity_decode($value->descrizione_tipo_stima));
          $id_s_zona = intval($value->id_s_zone);
              $s_zona->addMultiOptions(array($id_s_zona => $descrizione));              
        }
        $s_zona->setRequired(true)
               ->setRegisterInArrayValidator(false);

        $macro_ambito->setValue('Barcuzzi - Lido');
        $this->addElement($s_zona);
        
        
        //area urbanizzata
        $urbanizzata = $this->createElement('radio','area_urbanizzata');
        $urbanizzata->setLabel('Area urbanizzata: ');
        $urbanizzata->addMultiOption('1','Si');
        $urbanizzata->addMultiOption('0','No');
        $urbanizzata->setValue("0");
        $urbanizzata->setRequired(true);
        $this->addElement($urbanizzata);  
                       
        //modalità intervento
        $intervento = $this->createElement('select', 'id_u_modinterv');
        $intervento->setLabel('Modalità intervento: *');
                              
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
        $this->addElement($intervento);        
        
        
        //lotto saturo
        $lotto_saturo = $this->createElement('radio','lotto_saturo');
        $lotto_saturo->setLabel('Lotto saturo: ');
        $lotto_saturo->addMultiOption('1','Si');
        $lotto_saturo->addMultiOption('0','No');
        $lotto_saturo->setValue("0");
        $lotto_saturo->setRequired(true);
        $this->addElement($lotto_saturo);          
        
        
        //superficie        
        $notEmpty = new Zend_Validate_NotEmpty();
        $notEmpty->setMessage('Campo obbligatorio');        
        
        $superficie = $this->createElement('text', 'superficie', array());
        $superficie->setLabel('Superficie territoriale: * (mq)');
        $superficie->addValidator('Float',false, array('messages' => 'Solo cifre separate da virgola'));       
        $superficie->setRequired(true);
        $superficie->addValidator($notEmpty, true);
        $this->addElement($superficie);         

        //capacita_edificatoria
        //dipende dal subambito
        $this->addElement('text', 'capacita_edificatoria', array(
            'label'      => 'Inputare volumetria: *',
            'validators' => array(
                array('validator' => 'float'
                    )
                )
        ));    
        
        // pulsante invia
        $this->addElement('submit', 'submit', array(
            'ignore' => true,
            'label' => 'Continua il calcolo',
        ));        
        
    }
      

}

