<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SignupConfirmForm
 *
 * @author kimsreng
 */

namespace Users\Form;

use Zend\Form\Form;

class SignupConfirmForm extends Form{

    public function __construct($sm) {
        parent::__construct('signup_confirm');
        $this->setAttribute('method', 'post');
        $this->add(array(
            'name' => 'agree',
            'attributes' => array(
                'type' => 'Checkbox',
                'value'=>'1'
            ),
        ));
        $this->add(array(
            'name' => 'back',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Back'
            ),
        ));
        $this->add(array(
            'name' => 'done',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Done'
            ),
        ));
        $this->add(array(
            'name' => 'cancel',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Cancel',
                'id'=>'cancel'
            ),
        ));
    }

}

?>
