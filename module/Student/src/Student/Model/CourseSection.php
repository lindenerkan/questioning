<?php
namespace Student\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class CourseSection
{

    public $id;

    public $course_id;

    public $instructor_id;
    
    public $name;

    protected $inputFilter;

    public function exchangeArray ($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->course_id = (isset($data['course_id'])) ? $data['course_id'] : null;
        $this->instructor_id = (isset($data['instructor_id'])) ? $data['instructor_id'] : null;
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
            
            $this->inputFilter->add($factory->createInput(array(
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            )));

            $this->inputFilter->add($factory->createInput(array(
            		'name' => 'course_id',
            		'required' => true,
            		'filters' => array(
            				array(
            						'name' => 'Int'
            				)
            		)
            )));
           
            $this->inputFilter->add($factory->createInput(array(
            		'name' => 'instructor_id',
            		'required' => true,
            		'filters' => array(
            				array(
            						'name' => 'Int'
            				)
            		)
            )));
            
            $this->inputFilter = $inputFilter;
        }
        
        return $this->inputFilter;
    }
}