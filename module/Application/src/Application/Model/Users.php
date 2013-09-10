<?php
namespace Application\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Users
{

    public $id;

    public $email;

    public $username;
    
    public $display_name;
    
    public $state;
    
    public $admin;
    
    public $password;
    
    public $current;
    
    public $new;
    
    public $verifynew;

    protected $inputFilter;

    public function exchangeArray ($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
        $this->username = (isset($data['username'])) ? $data['username'] : null;
        $this->display_name = (isset($data['displayname'])) ? $data['displayname'] : null;
        $this->state = (isset($data['state'])) ? $data['state'] : null;
        $this->admin = (isset($data['admin'])) ? $data['admin'] : null;
        $this->password = (isset($data['password'])) ? $data['password'] : null;
        $this->current = (isset($data['current'])) ? $data['current'] : null;
        $this->new = (isset($data['new'])) ? $data['new'] : null;
        $this->verifynew = (isset($data['verifynew'])) ? $data['verifynew'] : null;
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
            		'name' => 'current',
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
            		'name' => 'new',
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
            		'name' => 'verifynew',
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
            		'name' => 'password',
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
            		'name' => 'state',
            		'required' => true,
            		'filters' => array(
            				array(
            						'name' => 'Int'
            				)
            		)
            )));
            
            $this->inputFilter->add($factory->createInput(array(
            		'name' => 'admin',
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
            
            $this->inputFilter->add(
            		$factory->createInput(
            				array(
            						'name' => 'displayname',
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