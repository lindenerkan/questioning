<?php

namespace Application\Form;

//use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ChangeDisplayNameForm extends Form
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
        // display name Input
        $text = new Element\Text('displayname');
        $text->setLabel('Display Name');
        $this->add($text);
    }

    public function createInputFilter()
    {
    	$this->inputFilter = new InputFilter();
    	$factory = new InputFactory();
    	
    	
        return $this->inputFilter;
    }
}