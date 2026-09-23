<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('password')->getData();

            // On chiffre le mot de passe avant de le stocker dans la base de données
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // On définit la date de création de l'utilisateur
            $user->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            // return $this->redirectToRoute('app_main');

            // CONNEXION AUTOMATIQUE IMMEDIATE
            // Le service s'occupe de créer la session, les cookies et d'authentifier l'objet $user.
            // On lui passe l'entité du user et le nom de la route de redirection cible.
            // $user : C'est l'entité fraîchement créée et enregistrée en base de données.
            // 'form_login' : C'est l'authentificateur défini dans security.yaml sous la clé form_login.
            //   Cela indique à Symfony quel mécanisme utiliser pour l'enregistrement de la session.
            // 'main' : C'est le nom du pare-feu (firewall) défini dans security.yaml.
            // la méthode $security->login() renvoie directement une réponse de redirection
            // vers la page d'accueil par défaut défini dans le firewall de security.yaml
            return $security->login($user, 'form_login', 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
