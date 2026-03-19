<?php

namespace App\Controller;

use App\Entity\Event;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class EventController extends AbstractController
{
    #[Route('/event', name: 'app_event_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // 1. On instancie un objet PHP normal (notre Entité)
        $event = new Event();

// 2. On hydrate l'objet avec ses Setters (Encapsulation)
        $event->setTitre('Conférence Cybersécurité : Les ransomwares');
        $event->setDescription('Une analyse technique et des démonstrations de ransomwares modernes.');
        $event->setDateDebut(new \DateTime('2026-10-15 09:00:00'));
        $event->setCapaciteMax(150);
        $event->setIsPublished(true);
        $event->setLieu('Paris, France');
// 3. On prévient Doctrine qu'on veut sauvegarder cet objet (Mise dans le panier)

        $entityManager->persist($event);
// 4. On exécute la transaction SQL (Le vrai INSERT INTO...)
        $entityManager->flush();
// 5. On retourne une réponse pour confirmer
        return new Response('Event créé avec l\'ID : ' . $event->getId());
    }
}
