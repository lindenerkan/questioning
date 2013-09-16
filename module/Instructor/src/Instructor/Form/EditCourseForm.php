<?php

namespace Instructor\Form;

//use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class EditCourseForm extends Form
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
        		'name' => 'id',
        		'options' => array(
        				'label' => 'Id',
        		),
        		'attributes' => array(
        				'type' => 'hidden',
        		),
        ));
        
        $this->add(array(
        		'name' => 'code',
        		'options' => array(
        				'label' => 'Course Code',
        		),
        		'attributes' => array(
        				'type' => 'text',
        		),
        ));
        
        $this->add(array(
        		'name' => 'name',
        		'options' => array(
        				'label' => 'Course Name',
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