<?php
namespace Student\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Student
{

    public $studentID;

    public $email;

    public $username;
    
    public $display_name;

    protected $inputFilter;

    public function exchangeArray ($data)
    {
        $this->studentID = (isset($data['studentID'])) ? $data['studentID'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->display_name = (isset($data['display_name'])) ? $data['display_name'] : null;
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
                'name' => 'email',
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
            		'name' => 'display_name',
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
                'name' => 'studentID',
                'required' => true,
                'filters' => array(
                    array(
                        'name' => 'Int'
                    )
                )
            )));
            
            $this->inputFilter->add(
                $factory->createInput(
                    array(
                        'name' => 'username',
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