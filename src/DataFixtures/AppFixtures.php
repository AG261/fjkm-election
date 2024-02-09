<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\AddressFactory;
use App\Factory\CategoryFactory;
use App\Factory\DistributorFactory;
use App\Factory\ProductFactory;
use App\Factory\UserFactory;
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
        for ($i = 0; $i < 10; $i++) {
//            address and user
            $addressProxy = AddressFactory::createOne();
            $address = $addressProxy->object();
            $manager->persist($address);

            $userProxy = UserFactory::createOne();
            $user = $userProxy->object();
            $user->addAddress($address);
            $manager->persist($user);

//            categories and product
            $categoryProxy = CategoryFactory::createOne();
            $category = $categoryProxy->object();
            $manager->persist($category);

            $productProxy = ProductFactory::createOne();
            $product = $productProxy->object();
            $product->addCategory($category);
            $manager->persist($product);
        }

        DistributorFactory::createMany(5);

        $manager->flush();
    }
}
