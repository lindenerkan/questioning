<?php
namespace Student\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Lesson
{

    public $id;

    public $course_section_id;

    public $is_active;

    protected $inputFilter;

    public function exchangeArray ($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->course_section_id = (isset($data['course_section_id'])) ? $data['course_section_id'] : null;
        $this->is_active = (isset($data['is_active'])) ? $data['is_active'] : null;
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
                'name' => 'id',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            )));
            
            $this->inputFilter->add($factory->createInput(array(
            		'name' => 'course_section_id',
            		'required' => true,
            		'filters' => array(
            				array(
            						'name' => 'Int'
            				)
            		)
            )));
            
            $this->inputFilter->add($factory->createInput(array(
            		'name' => 'is_active',
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