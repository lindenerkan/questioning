<?php
namespace Student\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class StudentSubmission
{

    public $id;
    
    public $ip;

    public $subID;

    public $formID;

    public $student_id;

    protected $inputFilter;

    public function exchangeArray ($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->ip = (isset($data['ip'])) ? $data['ip'] : null;
        $this->subID = (isset($data['subID'])) ? $data['subID'] : null;
        $this->formID = (isset($data['formID'])) ? $data['formID'] : null;
        $this->student_id = (isset($data['student_id'])) ? $data['student_id'] : null;
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
                'name' => 'subID',
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
            		'name' => 'ip',
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
            		'name' => 'formID',
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
                'name' => 'student_id',
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