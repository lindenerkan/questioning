<?php

namespace Application\Form;

//use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ChangePasswordForm extends Form
{
	protected $inputFilter;
	
    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
        $this->addElements();
        $this->setInputFilter($this->createInputFilter());
    }

    public function addElements()
    {
        $this->add(array(
        		'name' => 'new',
        		'options' => array(
        				'label' => 'New Password',
        		),
        		'attributes' => array(
        				'type' => 'password',
        		),
        ));
    }

    public function createInputFilter()
    {
    	$this->inputFilter = new InputFilter();
    	$factory = new InputFactory();
    	
    	
        return $this->inputFilter;
    }
}