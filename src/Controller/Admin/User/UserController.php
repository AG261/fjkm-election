<?php

namespace App\Controller\Admin\User;

use App\Common\Constants\UserConstants;
use App\DataTable\UserDataTableType;
use App\Entity\User;
use App\Form\UserType;
use App\Manager\UserManager;
use App\Services\Common\DataTableService ;
use App\Services\Common\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/user', name : '.user')]
class UserController extends AbstractController
{   
    /**
     * @var FileUploader
     */
    private $fileUploader;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UserPasswordHasherInterface
     */
    private $userPasswordHasher;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UserManager
     */
    private $userManager ;

    /**
     * @var LocalAreaRepository
     */
    private $localAreaRepository ;

    /**
     * Construct
     *
     * @param FileUploader $fileUploader
     * @param TranslatorInterface $translator
     * @param UserPasswordHasherInterface $_userPasswordHasher
     * @param EntityManagerInterface $_em
     * @param UserManager $_userManager
     */
    public function __construct(FileUploader $fileUploader, TranslatorInterface $translator,
         UserPasswordHasherInterface $_userPasswordHasher, EntityManagerInterface $_em,
         UserManager $_userManager)
    {
        $this->fileUploader        = $fileUploader;
        $this->translator          = $translator;
        $this->userPasswordHasher  = $_userPasswordHasher;
        $this->em                  = $_em;
        $this->userManager         = $_userManager;
    }
    
    #[Route('/team/', name: '.team.index', defaults: ['type' => 'team'])]
    #[Route('/client/', name: '.client.index', defaults: ['type' => 'client'])]
    public function listUser(Request $_request, DataTableService $_dataTableService): Response
    {   
        
        $routeParams = $_request->attributes->get('_route_params');

        $ajaxRequest = $_request->isXmlHttpRequest();
        $options     = [] ;
        
        if($ajaxRequest){
            $searchs = $_request->get('search', []) ;
            if(isset($searchs['value']) && !empty($searchs['value'])){
                $options['query'] = $searchs['value'];                    
            }
            
        }

        $table = $_dataTableService->createDataTable(UserDataTableType::class, $options);
        $table->handleRequest($_request);

        if ($table->isCallback()) {
            return $table->getResponse();
        }
        
        return $this->render('User/index.html.twig', [
            'datatable' => $table,
        ]);
    }

    #[Route('/team/new', name: '.team.new', defaults: ['type' => 'team'])]
    #[Route('/client/new', name: '.client.new', defaults: ['type' => 'client'])]
    public function addUser(Request $_request, EntityManagerInterface $_em): Response
    {

        $routeParams = $_request->attributes->get('_route_params');
        $isClient    = (isset($routeParams['type']) && $routeParams['type'] == 'team') ? false : true  ;
        $isProfil    = (isset($routeParams['action']) && $routeParams['action'] == 'profil') ? true : false  ;

        $user = new User();
        
        $form = $this->createForm(UserType::class, $user, ['isClient' => $isClient]);
        if($isClient == true){
            $form->get('roles')->setData([UserConstants::USER_ROLE_CUSTOMER]) ;
           
        }
        
        $form->handleRequest($_request);
        
        if ($form->isSubmitted()) {
            $email = $user->getEmail();
            $user->setUsername($email);
            
            $profileFile = $form->get('photo')->getData();
            if ($profileFile) {
                $profileFileName = $this->fileUploader->upload($profileFile, $this->getParameter("profil_upload_dir"));
                $user->setPhoto($profileFileName);
            }
            
            $address = $form->get('address')->getData();
            $validations = $this->userManager->validation($form, $user, $_request) ;
            $error       = $validations['error'];
            $form        = $validations['form'];

            if(empty($error)){
                if ($address instanceof Address) {
                    $address->addUser($user);
                    $_em->persist($address);
                }
                
                $user = $this->userManager->savePassword($form, $user, $_request) ;

                $_em->persist($user);
                $_em->flush();

                $redirection = $isClient == true ? 'app.admin.user.client.index' : 'app.admin.user.team.index' ;
                return $this->redirectToRoute($redirection);
            }
        }
        
        return $this->render('User/action.html.twig', [
            'form'      => $form->createView(),
            'user'      => $user,
            'isProfil'  => $isProfil ,
            'isClient'  => $isClient 
        ]);
    }

    #[Route('/team/edit/{id}', name: '.team.edit', defaults: ['type' => 'team', 'action' => 'edit'])]
    #[Route('/profil', name: '.team.profil', defaults: ['type' => 'team', 'action' => 'profil'])]
    #[Route('/client/edit/{id}', name: '.client.edit', defaults: ['type' => 'client'])]
    public function editUser(Request $_request, EntityManagerInterface $_em, User $_user = null): Response
    {

        $routeParams = $_request->attributes->get('_route_params');
        $isClient    = (isset($routeParams['type']) && $routeParams['type'] == 'team') ? false : true  ;
        $isProfil    = (isset($routeParams['action']) && $routeParams['action'] == 'profil') ? true : false  ;
        
        if(empty($_user)){
            $_user = $this->getUser();
        }

        $form = $this->createForm(UserType::class, $_user, [
            'validation_groups' => ['update'],
            'isClient'          => $isClient,
            'isProfil'          => $isProfil
        ]);

        $addresses = $_user->getAddresses() ;
        if(count($addresses) > 0){
            $address = $addresses['0'];
            $form->get('address')->get('address')->setData($address->getAddress()) ;
            $form->get('address')->get('city')->setData($address->getCity()) ;
            $form->get('address')->get('zip')->setData($address->getZip()) ;
        }

        $form->handleRequest($_request);
        
        if ($form->isSubmitted()) {
            
            $email = $form->get('email')->getData();
            $_user->setUsername($email);

            $profileFile = $form->get('photo')->getData();
            if ($profileFile) {
                $profileFileName = $this->fileUploader->upload($profileFile, $this->getParameter("profil_upload_dir"));
                $_user->setPhoto($profileFileName);
            }

            $address     = $form->get('address')->getData();
            
            $validations = $this->userManager->validation($form, $_user, $_request) ;
            $error       = $validations['error'];
            $form        = $validations['form'];
            
            if(empty($error)){
                if ($address instanceof Address) {
                    
                    $_user->getAddresses()->clear();
                    $_user->addAddress($address);
                }

                $user = $this->userManager->savePassword($form, $_user, $_request) ;

                $_em->flush();
                
                $redirection = $isClient == true ? 'app.admin.user.client.edit' : 'app.admin.user.team.edit' ;
                $params      = ['id' => $_user->getId()] ;
                if($isProfil){
                    $redirection = 'app.admin.user.team.profil';
                    $params      = [] ;
                }
                return $this->redirectToRoute($redirection, $params);
            }
        }

        return $this->render('User/action.html.twig', [
            'form' => $form->createView(),
            'user' => $_user,
            'isClient'  => $isClient ,
            'isProfil'  => $isProfil ,
            'addresses' => $_user->getAddresses()
        ]);
    }

    

    
    #[Route('/client/search', name: '.client.search.ajax', defaults: [])]
    public function clientSearch(Request $_request): Response
    {   
        $results     = [] ;
        $type        = $_request->get('type', 'all') ;
        $isCount     = $_request->get('isCount', '') ;
        $dateStart   = $_request->get('dateStart', '') ;
        $dateEnd     = $_request->get('dateEnd', '') ;
        $clientId    = $_request->get('client', '') ;
        $localAreaId = $_request->get('localArea', '') ;
        

        $params  = [] ;
        $params['role'] = UserConstants::USER_ROLE_CUSTOMER ;
        if(!empty($dateStart)){
            list($day, $month, $year) = explode('/', $dateStart) ;
            $params['dateStart'] = new \Datetime($year.'-'.$month.'-'.$day) ;
        }

        if(!empty($dateEnd)){
            list($day, $month, $year) = explode('/', $dateEnd) ;
            $params['dateEnd'] = new \Datetime($year.'-'.$month.'-'.$day) ;
        }

        if(!empty($localAreaId)){
            $localArea = $this->localAreaRepository->find($localAreaId) ;
            if(!empty($localArea)){
                $params['localArea'] = $localArea ;
            }
        }

        if($type == 'all'){
            unset($params['dateStart']) ;
            unset($params['dateEnd']) ;
        }

        if(!empty($clientId)){
            unset($params['dateStart']) ;
            unset($params['dateEnd']) ;
            unset($params['localArea']) ;
            $params['id'] = $clientId ;
        }
        
        if(!empty($isCount)){

            $params['count'] = true ;
            $users = $this->userManager->search($params) ;
            
            $results['count'] = $users['count'];
        }


        
        
        return new JsonResponse($results) ;
    }
}
