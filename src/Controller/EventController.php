<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\EventCapacityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class EventController extends AbstractController
{
    #[Route('/event/create', name: 'app_event_create')]
    public function new(EntityManagerInterface $emi): Response
    {
        // 1. On crée une instance de l'entité (un objet vide)
        $event = new Event();
// 2. On hydrate l'objet avec ses Setters (Encapsulation)
        $event->setTitre('Conférence Cybersécurité : Les ransomwares');
        $event->setDescription('Une analyse technique et des démonstrations de ransomwares modernes.');
        $event->setDateDebut(new \DateTime('2026-10-15 09:00:00'));
        $event->setCapaciteMax(150);
        $event->setIsPublished(true);
        $event->setLieu('Paris, France');
// 3. On prévient Doctrine qu'on veut sauvegarder cet objet (Mise dans le panier)

        $emi->persist($event);
// 4. On exécute la transaction SQL (Le vrai INSERT INTO...)
        $emi->flush();
// 5. On retourne une réponse pour confirmer
        return new Response('Event créé avec l\'ID : ' . $event->getId());
    }

    #[Route('/admin/event/newev', name: 'app_event_new')] 
    public function create(Request $request, EntityManagerInterface $emi): Response
    {   $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $emi->persist($event);
            $emi->flush();
                $this->addFlash('success', 'Event créé avec succès !');
            return $this->redirectToRoute('app_event_new');
        }

        return $this->render('event/newev.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/api/events', name: 'app_event_list')]
    public function show(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findAll();

        return $this->render('event/show.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/{id}/register', name: 'app_event_register', methods: ['POST'])]
    public function eventRegister(Event $event, EventCapacityManager $ecm): Response
    {
        if($ecm->isEventFull($event)){
            return $this->render('event/detail_full.html.twig', [
                'event' => $event,
            ]);
        }
        else{
            $availablePlaces = $ecm->getAvailablePlaces($event);
            return $this->render('event/detail.html.twig', [
                'event' => $event,
                'availablePlaces' => $availablePlaces,
            ]);
        }
    }

    #[Route('/admin/event/{id}/edit/', name: 'admin_event_edit', methods: ['PATCH'])]
    public function modify(Event $event, Request $request, EntityManagerInterface $emi): Response
    {
        $data = json_decode($request->getContent(), true);
        if (isset($data['isPublished'])) {
            $event->setIsPublished($data['isPublished']);
            $emi->flush();
            return new Response('Event mis à jour avec succès !', 200);
        }
        return new Response('Données invalides', 400);
    }

    #[Route('/admin/event/{id}', name: 'app_event_delete', methods: ['DELETE'])]
    public function delete(Event $event, EntityManagerInterface $emi): Response
    {
        $emi->remove($event);
        $emi->flush();
        return new Response('Event supprimé avec succès !', 200);
    }
}

