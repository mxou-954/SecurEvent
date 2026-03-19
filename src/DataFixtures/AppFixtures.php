<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // 9 utilisateurs
        $users = [
            ['prenom' => 'Alice',   'nom' => 'Martin',   'email' => 'alice@test.com'],
            ['prenom' => 'Bob',     'nom' => 'Bernard',  'email' => 'bob@test.com'],
            ['prenom' => 'Claire',  'nom' => 'Dupont',   'email' => 'claire@test.com'],
            ['prenom' => 'David',   'nom' => 'Durand',   'email' => 'david@test.com'],
            ['prenom' => 'Emma',    'nom' => 'Leroy',    'email' => 'emma@test.com'],
            ['prenom' => 'Fabien',  'nom' => 'Moreau',   'email' => 'fabien@test.com'],
            ['prenom' => 'Grace',   'nom' => 'Simon',    'email' => 'grace@test.com'],
            ['prenom' => 'Hugo',    'nom' => 'Laurent',  'email' => 'hugo@test.com'],
            ['prenom' => 'Inès',    'nom' => 'Michel',   'email' => 'ines@test.com'],
        ];

        foreach ($users as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPrenom($data['prenom']);
            $user->setNom($data['nom']);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, 'Password1!'));
            $manager->persist($user);
        }

        // 1 administrateur
        $admin = new User();
        $admin->setEmail('admin@securevent.com');
        $admin->setPrenom('Super');
        $admin->setNom('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'Admin1234!'));
        $manager->persist($admin);

        $manager->flush();
    }
}
