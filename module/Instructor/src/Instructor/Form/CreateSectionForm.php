<?php

namespace Instructor\Form;

//use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class CreatesectionForm extends Form
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
        		'name' => 'name',
        		'options' => array(
        				'label' => 'Section Name',
        		),
        		'attributes' => array(
        				'type' => 'text',
        		),
        ));
        
        $this->add(array(
        		'name' => 'course_id',
        		'options' => array(
        				'label' => 'cours_id',
        		),
        		'attributes' => array(
        				'type' => 'text',
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