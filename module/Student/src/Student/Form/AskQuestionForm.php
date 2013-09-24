<?php

namespace Student\Form;

//use Zend\InputFilter;
use Zend\Form\Form;
use Zend\Form\Element;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class AskQuestionForm extends Form
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
        		'name' => 'value',
        		'options' => array(
        				'label' => 'Question',
        		),
        		'attributes' => array(
        				'type' => 'text',
        		),
        ));
        
        $this->add(array(
             'type' => 'Zend\Form\Element\Checkbox',
             'name' => 'name',
             'options' => array(
                     'label' => 'Use my real name',
                     'use_hidden_element' => true,
                     'checked_value' => '1',
                     'unchecked_value' => '0'
             )
     ));
        
        $this->add(array(
        		'name' => 'course_section_lesson_id',
        		'options' => array(
        				'label' => 'Lesson Id',
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