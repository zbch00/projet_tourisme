<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher ;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");



        for($i=0;$i<=30;$i++){
            $roles = array("ROLE_USER","ROLE_RESTAURANT","ROLE_ADMIN");
            $user = new User();
            $user->setPrenom($faker->firstName());
            $user->setNom($faker->lastName());
            $user->setPseudo($faker->word());
            $user->setEmail($faker->email());
            $hashPassword = $this->hasher->hashPassword(
                $user,
                $faker->password(8)
            );
            $user->setPassword($hashPassword);
            $user->setCreatedAt($faker->dateTimeBetween('-10 years'));
            $user->setUpdatedUp($faker->dateTimeBetween('-10 years'));
            shuffle($roles);
            $roles= array($roles[0]);
            $user->setRoles($roles);
            $user->setActif($faker->numberBetween(0,1));
            $manager->persist($user);

        }

        $manager->flush();
    }
}
