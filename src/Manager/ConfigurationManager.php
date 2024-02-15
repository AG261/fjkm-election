<?php

/**
 * Configuration Manager
 */

namespace App\Manager;

use App\Entity\Configuration\Configuration;
use App\Repository\Configuration\ConfigurationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Request;

class ConfigurationManager
{
    public function __construct(private readonly EntityManagerInterface $_entityManager,
                                protected ConfigurationRepository $_configurationRepository
                                )
    {
    }

    /**
     * Get configuration
     *
     * @return Configuration
     */
    public function getConfiguration(){
        
        $configurations = $this->_configurationRepository->findAll() ;
        if(count($configurations) > 0){
            $configuration = $configurations[0];
        }else{
            $configuration = new Configuration();
            $configuration->setNumberMen(1) ;
            $configuration->setNumberWomen(1) ;
            $this->_entityManager->persist($configuration) ;
            $this->_entityManager->flush() ;
        }

        return $configuration;
    }
}
