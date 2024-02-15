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

class CreateCandidatCommand extends Command
{
    protected static $defaultName = 'app:create-candidat';
    protected static $defaultDescription = 'Add a candidat from csv';

    

    public function __construct(protected EntityManagerInterface $_entityManager,
                                protected ParameterBagInterface $_parameterBag,
                                protected CandidatRepository $_candidatRepository)
    {
        parent::__construct();

       
    }
    
    protected function configure(): void
    {
        
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
       
        $projectDir = $this->_parameterBag->get('project_dir');
        $fileCsv = $projectDir.'/public/data/csv/CANDIDAT.csv' ;
        $filesystem = new Filesystem(); 
        if($filesystem->exists($fileCsv)){
           
            $file = fopen($fileCsv,"r");

            $delimiter = ';'; // Change this to your desired delimiter
            $num       = 0;
            while (($data = fgetcsv($file, 0, $delimiter)) !== false) {
                if($num > 0){
                    $number    = $data[0] ;
                    $civility  = "" ;
                    $lastname  = $data[2] ;
                    $firstname = $data[3] ;

                    $candidat = $this->_candidatRepository->findOneBy(['number' => $number]) ;
                    if(empty($candidat)){
                        $candidat = new Candidat() ;
                    }

                    $candidat->setNumber($number) ;
                    $candidat->setCivility($civility) ;
                    $candidat->setLastname($lastname) ;
                    $candidat->setFirstname($firstname) ;
                    $candidat->setStatus(true) ;
                    
                    $this->_entityManager->persist($candidat) ;
                    $this->_entityManager->flush() ;
                }

                $num++;
            }
            fclose($file);
        }

        return Command::SUCCESS;
    }
}
