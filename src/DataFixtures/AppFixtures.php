<?php

namespace App\DataFixtures;

use App\Entity\Event;
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
        // ── USERS ──────────────────────────────────────────
        $usersData = [
            ['prenom' => 'Alice',  'nom' => 'Martin',  'email' => 'alice@test.com'],
            ['prenom' => 'Bob',    'nom' => 'Bernard', 'email' => 'bob@test.com'],
            ['prenom' => 'Claire', 'nom' => 'Dupont',  'email' => 'claire@test.com'],
            ['prenom' => 'David',  'nom' => 'Durand',  'email' => 'david@test.com'],
            ['prenom' => 'Emma',   'nom' => 'Leroy',   'email' => 'emma@test.com'],
            ['prenom' => 'Fabien', 'nom' => 'Moreau',  'email' => 'fabien@test.com'],
            ['prenom' => 'Grace',  'nom' => 'Simon',   'email' => 'grace@test.com'],
            ['prenom' => 'Hugo',   'nom' => 'Laurent', 'email' => 'hugo@test.com'],
            ['prenom' => 'Inès',   'nom' => 'Michel',  'email' => 'ines@test.com'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPrenom($data['prenom']);
            $user->setNom($data['nom']);
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->hasher->hashPassword($user, 'Password1!'));
            $manager->persist($user);
            $users[] = $user; // ← on garde les références pour les inscriptions
        }

        $admin = new User();
        $admin->setEmail('admin@securevent.com');
        $admin->setPrenom('Super');
        $admin->setNom('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->hasher->hashPassword($admin, 'Admin1234!'));
        $manager->persist($admin);

        // ── EVENTS ─────────────────────────────────────────
        $events = [
            // Cas normal — publié, futur, places dispo
            [
                'titre'       => 'CTF Débutants 2026',
                'description' => 'Un CTF accessible pour apprendre les bases de la cybersécurité.',
                'dateDebut'   => new \DateTime('+10 days'),
                'lieu'        => 'Paris - La Défense',
                'capaciteMax' => 50,
                'isPublished' => true,
                'participants' => [$users[0], $users[1], $users[2]], // 3 inscrits
            ],
            // Cas normal — publié, futur, places dispo
            [
                'titre'       => 'Workshop Pentest Web',
                'description' => 'Atelier pratique sur les failles OWASP Top 10.',
                'dateDebut'   => new \DateTime('+20 days'),
                'lieu'        => 'Lyon - Confluence',
                'capaciteMax' => 30,
                'isPublished' => true,
                'participants' => [$users[3], $users[4]],
            ],
            // Cas COMPLET — pour tester que le bouton M'inscrire est bloqué
            [
                'titre'       => 'Conférence OSINT Avancé',
                'description' => 'Techniques avancées de recherche en source ouverte.',
                'dateDebut'   => new \DateTime('+5 days'),
                'lieu'        => 'Bordeaux - Darwin',
                'capaciteMax' => 3, // ← capacité max = 3
                'isPublished' => true,
                'participants' => [$users[0], $users[1], $users[2]], // ← déjà 3 = COMPLET
            ],
            // Cas NON PUBLIÉ — ne doit PAS apparaître sur la liste publique
            [
                'titre'       => 'Workshop Cryptographie (brouillon)',
                'description' => 'Introduction à la cryptographie moderne.',
                'dateDebut'   => new \DateTime('+15 days'),
                'lieu'        => 'Nantes - IUT',
                'capaciteMax' => 20,
                'isPublished' => false, // ← non publié
                'participants' => [],
            ],
            // Cas DATE PASSÉE — ne doit PAS apparaître sur la liste publique
            [
                'titre'       => 'CTF Historique 2025',
                'description' => 'Un ancien CTF pour les archives.',
                'dateDebut'   => new \DateTime('-30 days'), // ← passé
                'lieu'        => 'Paris - EPITA',
                'capaciteMax' => 100,
                'isPublished' => true,
                'participants' => [$users[5], $users[6]],
            ],
            // Cas normal
            [
                'titre'       => 'Conférence Zero Trust Architecture',
                'description' => 'Comprendre et implémenter le modèle Zero Trust en entreprise.',
                'dateDebut'   => new \DateTime('+30 days'),
                'lieu'        => 'Toulouse - Capitole',
                'capaciteMax' => 200,
                'isPublished' => true,
                'participants' => [$users[7], $users[8]],
            ],
            [
                'titre'       => 'Workshop Reverse Engineering',
                'description' => 'Démontage et analyse de binaires malveillants.',
                'dateDebut'   => new \DateTime('+45 days'),
                'lieu'        => 'Grenoble - Minatec',
                'capaciteMax' => 25,
                'isPublished' => true,
                'participants' => [],
            ],
            [
                'titre'       => 'CTF Hardware Hacking',
                'description' => 'Exploitation de vulnérabilités matérielles et IoT.',
                'dateDebut'   => new \DateTime('+60 days'),
                'lieu'        => 'Sophia Antipolis',
                'capaciteMax' => 15,
                'isPublished' => true,
                'participants' => [$users[0]],
            ],
            [
                'titre'       => 'Conférence Threat Intelligence',
                'description' => 'Analyse des menaces et renseignement sur les cyberattaques.',
                'dateDebut'   => new \DateTime('+90 days'),
                'lieu'        => 'Rennes - Cybercampus',
                'capaciteMax' => 80,
                'isPublished' => true,
                'participants' => [],
            ],
            // Non publié futur
            [
                'titre'       => 'Workshop Malware Analysis (bientôt)',
                'description' => 'Analyse statique et dynamique de malwares.',
                'dateDebut'   => new \DateTime('+120 days'),
                'lieu'        => 'Lille - Euratechnologies',
                'capaciteMax' => 20,
                'isPublished' => false, // ← non publié
                'participants' => [],
            ],
        ];

        foreach ($events as $data) {
            $event = new Event();
            $event->setTitre($data['titre']);
            $event->setDescription($data['description']);
            $event->setDateDebut($data['dateDebut']);
            $event->setLieu($data['lieu']);
            $event->setCapaciteMax($data['capaciteMax']);
            $event->setIsPublished($data['isPublished']);

            foreach ($data['participants'] as $participant) {
                $event->addParticipant($participant);
            }

            $manager->persist($event);
        }

        $manager->flush();
    }
}