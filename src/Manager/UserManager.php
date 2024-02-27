<?php

/**
 * User Manager
 */
namespace App\Manager;

use App\Entity\Account\User;
use App\Repository\Account\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
//use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface ;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserManager {

    /**
     * @var UserRepository The entity repository
     */
    private $repository;

    /**
    * @var UserPasswordHasherInterface $userPasswordHasher
    */
    private $userPasswordHasher;
    
    protected $requestStack;
    
    protected $request;
    
    protected $urlGenerator;
    
    /**User Manager constructor
     * 
     * @param EntityManagerInterface $_entityManager
     * @param UserPasswordHasher $_userPasswordHasher
     * @param RequestStack $_requestStack
     * @param UrlGeneratorInterface $_urlGenerator
     */
    public function __construct(protected EntityManagerInterface $_entityManager,
            protected UserPasswordHasherInterface $_userPasswordHasher, 
            protected RequestStack $_requestStack,
            private readonly TranslatorInterface $_translator,
            protected UrlGeneratorInterface $_urlGenerator)
    {

        $this->repository           = $_entityManager->getRepository(User::class);
        $this->userPasswordHasher   = $_userPasswordHasher;
        $this->requestStack         = $_requestStack ;
        $this->request              = $this->requestStack->getCurrentRequest();
        $this->urlGenerator         = $_urlGenerator;
    }
    
    public function getList(array $searchCriteria = [], bool $count = false): ?array
    {
        return $this->repository->getUsers($searchCriteria, $count);
    }

    /**
     * Register new User
     * 
     * @param User $_user
     * @param string $_plainPassword
     * @return User
     */
    public function registerUser(User $_user, string $_plainPassword): User
    {
        $encodedPassword = $this->encryptePassword($_user, $_plainPassword);

        $_user->setPassword($encodedPassword);

        if (!$_user->getUsername()) {
            $_user->setUsername($_user->getEmail());
        }

        $this->_entityManager->persist($_user);
        $this->_entityManager->flush();
        
        return $_user;
    }
    
    /**
     * Save update user
     * @param User $_user
     * @param string $_plainPassword
     * @return User
     */
    public function saveUpdate(User $_user, string $_plainPassword = null): User
    {
        if ($_plainPassword) {
            $encodedPassword = $this->encryptePassword($_user, $_plainPassword);
            $_user->setPassword($encodedPassword);
        }

        $this->_entityManager->persist($_user);
        $this->_entityManager->flush();
        return $_user;
    }
    
    /**
     * Toogle status
     * @param User $_user
     * @return User
     */
    public function toggleUserStatus(User $_user): User
    {
        $_user->setStatus(!$_user->getStatus());

        $this->_entityManager->persist($_user);
        $this->_entityManager->flush();
        return $_user;
    }
    
    /**
     * Encrypt user password
     * @param User $_user
     * @param string $_plainPassword
     * @return string
     */
    public function encryptePassword(User $_user, string $_plainPassword): string
    {
        return $this->userPasswordHasher->hashPassword($_user, $_plainPassword);
        
    }

    /**
     * Validation form
     *
     * @param Form $_form
     * @param User $_user
     * @param Request $_request
     * @return void
     */
    public function validation(Form $_form, User $_user, Request $_request){

        $error = 0;

        $alls    = $_request->request->all() ;
        $datas   = $alls['user'] ;
        
        $emptyError = new FormError($this->_translator->trans("The field must be not empty"));
        $roleError = new FormError($this->_translator->trans("You must check at least one function"));

        if(empty($_user->getId())){
            $passwordUid = $_form->get('password')->getData();
            if(empty($passwordUid)){
                $_form->get('password')->addError($emptyError);
                $error++;
            }

        }

        //Verification unique user email
        $email = $_form->get('email')->getData();
        $user = $this->_entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if(!empty($user) && $user->getId() != $_user->getId()){
            $emailError = new FormError($this->_translator->trans("This email is already in use"));
            $_form->get('email')->addError($emailError);
            $error++;
        }

        //Verification firstname
        $firstName = $_form->get('firstName')->getData();
        if(empty($firstName)){
            
            $_form->get('firstName')->addError($emptyError);
            $error++;
        }

        //Verification las name
        $lastName = $_form->get('lastName')->getData();
        if(empty($lastName)){
            
            $_form->get('lastName')->addError($emptyError);
            $error++;
        }
        
        $password = $datas['password'] ;
        if(isset($password['first']) && ($password['first'] != $password['second'])){
            
            $error++;
        }
        
        $roles   = isset($datas['roles']) ? $datas['roles'] : [];
        if(count($roles) == 0){
            
            $_form->get('roles')->addError($roleError);
            $error++;
        }
        return ['form' => $_form, 'error' => $error] ;
    }

    /**
     * Save user password
     *
     * @param Form $_form
     * @param User $_user
     * @param Request $_request
     * @return void
     */
    public function savePassword(Form $_form, User $_user, Request $_request){

        $passwordUid = $_form->get('password')->getData();

        $alls    = $_request->request->all() ;
        $datas   = $alls['user'] ;

        $password = $datas['password'] ;
        if (isset($password['password']['first']) && !empty($password['password']['first'])) {
            $passwordUid = $password['password']['first'] ; 
        }

        if (!empty($passwordUid)) {

            $password = $this->_userPasswordHasher->hashPassword($_user, $passwordUid);
            $_user->setPassword($password);
        }

        return $_user ;
   
    }
    
}
