<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Application\Model\Users;
use Application\Model\UsersTable;
use Application\Form;
use Zend\Crypt\Password\Bcrypt;

class IndexController extends AbstractActionController
{
    protected $usersTable;
    
    protected function identity ()
    {
        $locale = $this->getEvent()
            ->getRouteMatch()
            ->getParam('locale', 'tr-TR');
        \Locale::setDefault($locale);
        $loc = $this->getServiceLocator();
        $translator = $loc->get('translator');
        $translator->addTranslationFile("phparray", './module/Application/language/lang.array.' . str_replace('-', '_', $locale) . '.php');
        
        $loc->get('ViewHelperManager')
            ->get('translate')
            ->setTranslator($translator);
        //$this->zfcUserAuthentication()->getIdentity()->getEmail();
        if (! $this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login', array(
                'locale' => \Locale::getDefault($locale)
            ));
        }
    }

    public function indexAction ()
    {
    if (! $this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login', array(
            ));
        }
        
        if ($this->zfcUserAuthentication()->getIdentity()->getAdmin()) {
        	return $this->redirect()->toRoute('instructor', array());
        }
        else 
            return $this->redirect()->toRoute('student', array());
        
        return array(
        );
    }
    
    public function changedisplaynameAction()
    {
    if (! $this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login', array(
            ));
        }
        
        $id=$this->zfcUserAuthentication()->getIdentity()->getId();
        
        $form = new Form\ChangeDisplayNameForm('changedisplayname-form');
        
        if ($this->getRequest()->isPost()) {
        	$user = new Users();
        	// Postback
        	$data = array_merge_recursive(
        			$this->getRequest()->getPost()->toArray(),
        			$this->getRequest()->getFiles()->toArray()
        	);
        
        	$form->setData($data);
        	if ($form->isValid()) {
        
        		$data = $form->getData();
        		$user->exchangeArray($data);
        		$this->getUsersTable()->saveDisplayName($user,$id);
        		
        		$this->redirect()->toRoute('home');
        	}
        }
        return array('form' => $form);
    }
    
    public function changepasswordAction()
    {
    if (! $this->zfcUserAuthentication()->hasIdentity()) {
            return $this->redirect()->toRoute('zfcuser/login', array(
            ));
        }
    	$id=$this->zfcUserAuthentication()->getIdentity()->getId();
    	$form = new Form\ChangePasswordForm('changepassword-form');
    	if ($this->getRequest()->isPost()) {
    		$user = new Users();
    		// Postback
    		$data = array_merge_recursive(
    				$this->getRequest()->getPost()->toArray(),
    				$this->getRequest()->getFiles()->toArray()
    		);
    		
    		$form->setData($data);
    		if ($form->isValid()) {
    			$data = $form->getData();
    			$user->exchangeArray($data);
    			$this->getUsersTable()->savePassword($user,$id);
    			$this->redirect()->toRoute('home');
    		}
    	}
    	return array('form' => $form);
    }
    
    public function getUsersTable()
    {
    	if (!$this->usersTable) {
    		$sm = $this->getServiceLocator();
    		$this->usersTable = $sm->get('Application\Model\UsersTable');
    	}
    	return $this->usersTable;
    }
    
}
