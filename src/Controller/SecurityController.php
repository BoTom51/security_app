<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    ////////// GESTION INSCRIPTION UTILISATEUR //////////
    #[Route('/register', name: 'security_register')]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // CREATION nouveau USER
        $user = new User();
        // CREATION nouveau FORM
        $form = $this->createForm(UserType::class, $user);

        // gestion du SUBMIT
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);

            // HASHAGE PASSWORD
            $hash = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            // UPDATE BDD
            $manager->persist($user);
            $manager->flush();

            // REDIRECTION si inscription valide vers la page de modif de compte
            return $this->redirectToRoute('security_login');
        }

        /*  Faire ici aussi la gestion des données (account) avec une deuxieme route.
            Les données d'entrées en inscription sont les memes qui pourront etre modif
            avec quelque ajout (préférence du site ?), creation = mise a jour
            seul la route et la vue differe, mais le traitement par controleur est presque le
            meme.
        */

        // Envoi du FORM vers TWIG
        return $this->render('security/register.html.twig', [
            'form' => $form,
        ]);
    }

    ////////// GESTION DONNEES PERSONNELLES UTILISATEUR //////////
    #[Route('/account', name: 'security_account')]
    public function account(): Response
    {

        // Envoi du FORM vers TWIG
        return $this->render('security/account.html.twig', []);
    }

    ////////// GESTION DE LA CONNEXION & DECONNEXION //////////
    #[Route('/login', name: 'security_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }
}
