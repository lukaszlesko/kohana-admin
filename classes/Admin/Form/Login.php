<?php

class Admin_Form_Login extends Admin_Form_Base
{
    protected function validationRules()
    {
        $this->setFormMarkerField('login_form');
        
        $this->addRule('username', 'not_empty');
        $this->addRule('password', 'not_empty');
    }
}