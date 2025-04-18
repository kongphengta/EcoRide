<?php

namespace App\Controller;

use App\Form\ProfileFormType;
use App\Form\PasswordUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AccountController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('account/profile.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ProfileFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Handle the form submission and update the user profile
            $entityManager->flush();
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès.');
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('account/edit_profile.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // #[Route('/compte/modifier-mot-de-passe', name: 'app_account_modify_pwd')]
    // public function password(
    //     Request $request,
    //     UserPasswordHasherInterface $passwordHasher,
    //     EntityManagerInterface $entityManager
    // ): Response {

    //     $user = $this->getUser();

    //     $form = $this->createForm(PasswordUserType::class, $user, [
    //         'passwordHasher' => $passwordHasher
    //     ]);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $entityManager->flush();
    //         $this->addFlash('success', 'Votre mot de passe a bien été modifié.');
    //     }

    //     return $this->render('account/password.html.twig', [
    //         'modifyPwd' => $form->createView()
    //     ]);
    // }
}
