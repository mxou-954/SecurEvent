<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class EventController extends AbstractController
{

    #[IsGranted('ROLE_USER')]
    #[Route('/event/{id}/register', name: 'app_event_register', methods: ['POST'])]
    public function register(int $id, Request $request, EventRepository $eventRepository, EntityManagerInterface $em): Response
    {
        $event = $eventRepository->find($id);

        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }

        if (!$this->isCsrfTokenValid('register_event_' . $id, $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_event_show', ['id' => $id]);
        }

        /** @var User $user */
        $user = $this->getUser();

        if ($event->getParticipants()->contains($user)) {
            $this->addFlash('error', 'Vous êtes déjà inscrit.');
            return $this->redirectToRoute('app_event_show', ['id' => $id]);
        }

        if ($event->getPlacesRestantes() <= 0) {
            $this->addFlash('error', 'Plus de places disponibles.');
            return $this->redirectToRoute('app_event_show', ['id' => $id]);
        }

        $event->addParticipant($user);
        $em->flush();

        $this->addFlash('success', 'Inscription confirmée !');
        return $this->redirectToRoute('app_event_show', ['id' => $id]);
    }

    #[Route('/', name: 'app_home')]
    public function index(EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findUpcomingPublished();

        return $this->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/{id}', name: 'app_event_show')]
    public function show(int $id, EventRepository $eventRepository): Response
    {
        $event = $eventRepository->find($id);
    
        if (!$event) {
            throw $this->createNotFoundException('Événement introuvable.');
        }
    
        // Vérifie si l'user connecté est déjà inscrit
        $isRegistered = false;
        if ($this->getUser()) {
            $isRegistered = $event->getParticipants()->contains($this->getUser());
        }
    
        return $this->render('event/show.html.twig', [
            'event' => $event,
            'isRegistered' => $isRegistered,
        ]);
    }
}