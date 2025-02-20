<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class RegisterController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $form->getData();
            $entityManager-> persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Votre compte a bien été créé. Vous pouvez vous connecter.');
            return $this->redirectToRoute('app_login');
            
        }
        return $this->render('register/index.html.twig', [
            'registerForm' => $form->createView()
        ]);
    }
}
