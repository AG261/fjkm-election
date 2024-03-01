<?php

namespace App\Controller\Admin\User;

use App\Constants\UserConstants;
use App\DataTable\UserDataTableType;
use App\Entity\Account\User;
use App\Form\UserType;
use App\Manager\UserManager;
use App\Repository\Voting\VoteRepository;
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
        
        return $this->render('Admin/User/index.html.twig', [
            'datatable' => $table,
        ]);
    }

    #[Route('/team/new', name: '.team.new', defaults: ['type' => 'team'])]
    public function addUser(Request $_request, EntityManagerInterface $_em): Response
    {

        $routeParams = $_request->attributes->get('_route_params');
        $isClient    = (isset($routeParams['type']) && $routeParams['type'] == 'team') ? false : true  ;
        $isProfil    = (isset($routeParams['action']) && $routeParams['action'] == 'profil') ? true : false  ;

        $user = new User();
        
        $form = $this->createForm(UserType::class, $user, ['isClient' => $isClient]);
        
        
        $form->handleRequest($_request);
        
        if ($form->isSubmitted()) {
            $email = $user->getEmail();
            $user->setUsername($email);
            $user->setCreated(new \Datetime());
            $user->setUpdated(new \Datetime());

            $profileFile = $form->get('photo')->getData();
            if ($profileFile) {
                $profileFileName = $this->fileUploader->upload($profileFile, $this->getParameter("profil_upload_dir"));
                $user->setPhoto($profileFileName);
            }
            
            $validations = $this->userManager->validation($form, $user, $_request) ;
            $error       = $validations['error'];
            $form        = $validations['form'];

            if(empty($error)){
                
                $alls    = $_request->request->all() ;
                $datas   = $alls['user'] ;

                $role    = $datas['roles'] ;
                $user->setRoles([$role]) ;
                $user = $this->userManager->savePassword($form, $user, $_request) ;

                $_em->persist($user);
                $_em->flush();

                $redirection = 'app.admin.user.team.index' ;
                return $this->redirectToRoute($redirection);
            }
        }
        
        return $this->render('Admin/User/action.html.twig', [
            'form'      => $form->createView(),
            'user'      => $user,
            'isProfil'  => $isProfil ,
        ]);
    }

    #[Route('/team/edit/{id}', name: '.team.edit', defaults: ['type' => 'team', 'action' => 'edit'])]
    #[Route('/profil', name: '.team.profil', defaults: ['type' => 'team', 'action' => 'profil'])]
    public function editUser(Request $_request, EntityManagerInterface $_em, User $_user = null): Response
    {

        $routeParams = $_request->attributes->get('_route_params');
        $isClient    = (isset($routeParams['type']) && $routeParams['type'] == 'team') ? false : true  ;
        $isProfil    = (isset($routeParams['action']) && $routeParams['action'] == 'profil') ? true : false  ;
        
        if(empty($_user)){
            $_user = $this->getUser();
        }
        
        $roles = $_user->getRoles();
        if (($key = array_search('ROLE_USER', $roles)) !== false) {
            unset($roles[$key]);
        }
        
        $form = $this->createForm(UserType::class, $_user, [
            'validation_groups' => ['update'],
            'isProfil'          => $isProfil
        ]);
        $form->get('roles')->setData($roles[0]) ;

        $form->handleRequest($_request);
        
        if ($form->isSubmitted()) {
            
            $email = $form->get('email')->getData();
            $_user->setUsername($email);

            $profileFile = $form->get('photo')->getData();
            if ($profileFile) {
                $profileFileName = $this->fileUploader->upload($profileFile, $this->getParameter("profil_upload_dir"));
                $_user->setPhoto($profileFileName);
            }
            $_user->setUpdated(new \Datetime());
            
            $validations = $this->userManager->validation($form, $_user, $_request) ;
            $error       = $validations['error'];
            $form        = $validations['form'];
            
            if(empty($error)){
                $alls    = $_request->request->all() ;
                $datas   = $alls['user'] ;

                $role    = $datas['roles'] ;
                $_user->setRoles([$role]) ;
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

        return $this->render('Admin/User/action.html.twig', [
            'form' => $form->createView(),
            'user' => $_user,
            'isClient'  => $isClient ,
            'isProfil'  => $isProfil ,
        ]);
    }

    #[Route('/delete/{id}', name: '.team.delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager, VoteRepository $voteRepository): Response
    {
        $error = false;
        if ($this->isCsrfTokenValid('delete-user'.$user->getId(), $request->request->get('_token'))) {

            $roles = $user->getRoles() ;
                
            //Get all vote with thsi user
            $votes = $voteRepository->findBy(['user' => $user]) ;
            if(count($votes) > 0){
                $error = true;
                
            }else{
                $entityManager->remove($user);
                $entityManager->flush();

                return $this->redirectToRoute('app.admin.user.team.index', [], Response::HTTP_SEE_OTHER);
            }
            
        }

        return $this->render('Admin/User/delete.html.twig', [
            'user'  => $user,
            'error' => $error
        ]);

        
    }
}
