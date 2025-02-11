<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        // si le formulaire est soumis et valide.
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupère les données du formulaire.
            // $user = $form->getData();
            // on enregistre les données du formulaire dans en base de données.
            $entityManager->persist($user);
            $entityManager->flush();
            // on envoie un message de confirmation à l'utilisateur.
            $this->addFlash('success', 'Votre compte a bien été créé.');
            // on redirige l'utilisateur vers la page login.
            return $this->redirectToRoute('app_login');
        }

        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView(),
        ]);
    }
} 
