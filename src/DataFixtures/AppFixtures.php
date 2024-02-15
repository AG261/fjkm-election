<?php

namespace App\DataFixtures;

use App\Entity\Account\User;
use App\Entity\Voting\Candidat;
use App\Entity\Voting\Vote;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr');
        for ($i = 0; $i < 4; $i++) {
            $user = new User();
            $manager->persist($user);
            $user->setEmail($faker->email())
                ->setRoles(['ROLE_ADMIN'])
                ->setLastname($faker->lastname())
                ->setFirstname($faker->firstName())
                ->setUsername($faker->userName())
                ->setStatus(1)
                ->setCivility($faker->randomElement(['Mr', 'Mme', 'Mlle']));
            $password = $this->hasher->hashPassword($user, '123456');
            $user->setPassword($password);
            $manager->flush();
        }

        $users = $manager->getRepository(User::class)->findAll();
        for ($i = 0; $i < 20; $i++) {
            $this->createCandidate($manager, $faker);
            $this->createVote($faker->randomElement($users), $manager, $faker);
        }

        $manager->flush();
    }

    public function createCandidate(ObjectManager $manager, $faker): void
    {

        $candidate = new Candidat();
        $candidate->setFirstname($faker->firstName())
            ->setLastname($faker->name())
            ->setCivility($faker->randomElement(['Mr', 'Mme', 'Mlle']))
            ->setStatus(1)
            ->setPhoto('default.jpeg');
        $manager->persist($candidate);
    }

    public function createVote($user, ObjectManager $manager, $faker): void
    {
        $vote = new Vote();
        $vote->setNum($faker->randomNumber(8))
            ->setUser($user)
            ->setIsDead($faker->randomElement([true, false]));
        $manager->persist($vote);
    }
}
