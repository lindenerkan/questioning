<?php
namespace Instructor\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Quiz
{

    public $id;

    public $course_section_lesson_id;

    public $form_id;
    
    public $is_active;
    
    public $name;

    protected $inputFilter;

    public function exchangeArray ($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->course_section_lesson_id = (isset($data['course_section_lesson_id'])) ? $data['course_section_lesson_id'] : null;
        $this->form_id = (isset($data['form_id'])) ? $data['form_id'] : null;
        $this->is_active = (isset($data['is_active'])) ? $data['is_active'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
    }

    public function getArrayCopy ()
    {
        return get_object_vars($this);
    }
    
    // Add content to these methods:
    public function setInputFilter (InputFilterInterface $inputFilter)
    {
        // throw new \Exception("Not used");
    }

    public function getInputFilter ()
    {
        if (! $this->inputFilter) {
            $this->inputFilter = new InputFilter();
            $factory = new InputFactory();
            
            $this->inputFilter->add($factory->createInput(array(
                'name' => 'course_section_lesson_id',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            )));
            
            $this->inputFilter->add($factory->createInput(array(
            		'name' => 'is_active',
            		'required' => false,
            		'filters' => array(
            				array(
            						'name' => 'Int'
            				)
            		)
            )));
            
            $this->inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => false,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            )));
            
            $this->inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'form_id',
                        'required' => true,
                        'filters' => array(
                            array(
                                'name' => 'StripTags'
                            ),
                            array(
                                'name' => 'StringTrim'
                            )
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 0,
                                    'max' => 128
                                )
                            )
                        )
                    )));
            
            $this->inputFilter->add(
            		$factory->createInput(
            				array(
            						'name' => 'name',
            						'required' => true,
            						'filters' => array(
            								array(
            										'name' => 'StripTags'
            								),
            								array(
            										'name' => 'StringTrim'
            								)
            						),
            						'validators' => array(
            								array(
            										'name' => 'StringLength',
            										'options' => array(
            												'encoding' => 'UTF-8',
            												'min' => 0,
            												'max' => 128
            										)
            								)
            						)
            				)));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}