<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        // setto il layout
        $this->_helper->_layout->setLayout('dojo');

        // comune
        $comune = null;
        
        // action body
        $form = new Application_Form_SelezioneIniziale();

        $request = $this->getRequest();
        if ($request->isPost()) {
            if ($form->isValid($request->getPost())) {
                $comune=$this->_process_index($form->getValues());
                if ($comune) {
                    $urlOptions = array('controller' => $comune, 'action' => 'index');
                    //$this->view->notifica = '<style>.notifica{ background-color:green; padding:2px;}</style>Modulo salvato con successo.';
                    $this->_helper->redirector->gotoRoute($urlOptions);
                } else {
                    //$this->_helper->redirector->gotoRoute($urlOptions,'azioni');
                    //$this->view->notifica = '<span style="padding:2px;">Ops, si è verificato un errore.</span>';
                }
            }
        }

        $this->view->form = $form;
    }

    protected function _process_index($values) {
        $comune = $values["comune"];

        $session = new Zend_Session_Namespace('step1');
        $session->comune=$comune;
        
        //throw new Exception($data);
        return $comune;
    }

}

