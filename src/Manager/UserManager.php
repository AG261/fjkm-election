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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface ;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
    
}
