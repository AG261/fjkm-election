<?php

namespace App\Command;

use App\Constants\Content ;
use App\Entity\Account\User;
use App\Manager\UserManager;
use App\Repository\Account\UserRepository;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CreateUserCommand extends Command
{
    protected static $defaultName = 'app:create-user';
    protected static $defaultDescription = 'Add a short description for your command';

    /**
    * @var UserManager $userManager
    */
    private $userManager;

    public function __construct(protected UserManager $_userManager,
                                protected UserRepository $_userRepository)
    {
        parent::__construct();

        $this->userManager = $_userManager;
    }
    
    protected function configure(): void
    {
        //php bin/console app:create-user admin@voting.mg Admin123456! admin
        $this
            ->addArgument('email', InputArgument::OPTIONAL, 'User email')
            ->addArgument('password', InputArgument::OPTIONAL, 'Mot de passe du compte')
            ->addArgument('role', InputArgument::OPTIONAL, 'Role de l\'utilisateur')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email      = $input->getArgument("email");
        $password 	= $input->getArgument("password");
        $role 		= strtolower($input->getArgument("role"));
        $function = null; 
        $roleUser = Content::ROLE_CUSTOMER;
        switch ($role) {
            case 'admin':
                $roleUser = Content::ROLE_ADMIN;
                
        }

        $userByUsername = $this->_userRepository->findOneBy(['username' => $email]);

        if (!$userByUsername) {
            $user = new User();
            $dateCurrent = new \DateTime();
            $user
                ->setUsername($email)
                ->setEmail($email)
                ->setRoles([$roleUser])
                ->setStatus(Content::USER_ENABLE)
                ->setCreated($dateCurrent) 
                ->setUpdated($dateCurrent) 
            ;

            $this->userManager->registerUser($user, $password);

        }
        else {
            $io->warning("Désolé!!! L'username ou l'email a déjà été prise par un autre utilisateur");
        }


        return Command::SUCCESS;
    }
}
