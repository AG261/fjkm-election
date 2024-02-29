<?php

namespace App\Command;

use App\Entity\Voting\Candidat;
use App\Repository\Voting\CandidatRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

class ReIndexCandidatCommand extends Command
{
    //php8.2 bin/console app:reindex-candidat men
    protected static $defaultName = 'app:reindex-candidat';
    protected static $defaultDescription = 'Add a candidat from csv';

    

    public function __construct(protected EntityManagerInterface $_entityManager,
                                protected ParameterBagInterface $_parameterBag,
                                protected CandidatRepository $_candidatRepository)
    {
        parent::__construct();

       
    }
    
    protected function configure(): void
    {
        $this
            ->addArgument('type', InputArgument::OPTIONAL, 'Candidat type : women or men')
            
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $type      = $input->getArgument("type");
        if(!empty($type)){
            $civility = $type == 'women' ? 'Mme' : 'Mr';
            $candidats = $this->_candidatRepository->findBy(['civility' => $civility]);
            if(count($candidats) > 0){
                $numberNew = 1;
                foreach($candidats as $candidat){
                    $candidat->setNumber($numberNew) ;

                    $this->_entityManager->persist($candidat) ;
                    $this->_entityManager->flush() ;

                    $numberNew++;
                }
            }
        }

        return Command::SUCCESS;
    }
}
