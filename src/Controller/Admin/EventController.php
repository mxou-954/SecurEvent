<?php

namespace App\Controller\Admin;

use App\Entity\Event;
use App\Form\EventFormType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/events')]
class EventController extends AbstractController
{
    #[Route('', name: 'app_admin_events')]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('admin/event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_event_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $event = new Event();
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($event);
            $em->flush();
            $this->addFlash('success', 'Événement créé !');
            return $this->redirectToRoute('app_admin_events');
        }

        return $this->render('admin/event/form.html.twig', [
            'form' => $form,
            'title' => 'Créer un événement',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_event_edit')]
    public function edit(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EventFormType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Événement modifié !');
            return $this->redirectToRoute('app_admin_events');
        }

        return $this->render('admin/event/form.html.twig', [
            'form' => $form,
            'title' => 'Modifier l\'événement',
        ]);
    }

    #[Route('/{id}/delete', name: 'app_admin_event_delete', methods: ['POST'])]
    public function delete(Event $event, Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete_event_' . $event->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');
            return $this->redirectToRoute('app_admin_events');
        }

        $em->remove($event);
        $em->flush();
        $this->addFlash('success', 'Événement supprimé !');
        return $this->redirectToRoute('app_admin_events');
    }

    #[Route('/{id}/participants', name: 'app_admin_event_participants')]
    public function participants(Event $event): Response
    {
        return $this->render('admin/event/participants.html.twig', [
            'event' => $event,
        ]);
    }
}