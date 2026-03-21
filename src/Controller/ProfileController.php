<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\ProfileFormType;

#[IsGranted('ROLE_USER')]
class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile_dashboard')]
    public function dashboard(): Response
    {
        $user = $this->getUser();

        return $this->render('profile/dashboard.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        /** @var User $user */  // ← on dit à IntelliSense "c'est un User" pour éviter les erreurs de type, c'est un "CAST"
        $user = $this->getUser();
        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $currentPassword = $form->get('currentPassword')->getData();
            $newPassword = $form->get('newPassword')->getData();

            if ($currentPassword && $newPassword) {
                if (!$userPasswordHasher->isPasswordValid($user, $currentPassword)) {
                    $this->addFlash('error', 'Mot de passe actuel incorrect.');
                    return $this->render('profile/edit.html.twig', [
                        'profileForm' => $form,
                    ]);
                }
                $user->setPassword($userPasswordHasher->hashPassword($user, $newPassword));
            }

            $em->flush();
            $this->addFlash('success', 'Profil mis à jour !');
            return $this->redirectToRoute('app_profile_dashboard');
        }

        return $this->render('profile/edit.html.twig', [
            'profileForm' => $form,
        ]);
    }

    #[Route('/profile/mes-evenements', name: 'app_profile_events')]
    public function mesEvenements(): Response
    {
        /** @var User $user */
        $user = $this->getUser();
    
        return $this->render('profile/mes-evenements.html.twig', [
            'events' => $user->getEvents(), // ← récupère les events liés au user
        ]);
    }
}