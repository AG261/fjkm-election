<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\AddressFactory;
use App\Factory\CategoryFactory;
use App\Factory\DistributorFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
use App\Factory\Voting\CandidatFactory;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct()
    {
    }

    public function load(ObjectManager $manager): void
    {
        CandidatFactory::createMany(10);
        $manager->flush();
    }
}
