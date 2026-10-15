<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Ce contrôleur est responsable de la gestion de l'inscription des utilisateurs.
 * Il permet aux utilisateurs de créer un compte et de se connecter automatiquement après l'inscription.
 */
class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        EntityManagerInterface $entityManager,
        Security $security,
        #[Autowire(service: 'monolog.logger.web')] LoggerInterface $webLogger
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // Vérification de la soumission et de la validité du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('password')->getData();

            // On chiffre le mot de passe avant de le stocker dans la base de données
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // On définit la date de création de l'utilisateur
            $user->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

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
        // Gestion des erreurs de validation du formulaire
        elseif ($form->isSubmitted()) {
            $validationErrors = [];

            // Parcours des erreurs de validation du formulaire pour les enregistrer dans le journal
            foreach ($form->getErrors(true) as $error) {
                $validationErrors[] = [
                    'field' => $error->getOrigin()?->getName(),
                    'message' => $error->getMessage(),
                ];
            }

            // Enregistrement des erreurs de validation dans le journal pour le débogage
            $webLogger->warning('Échec de validation du formulaire d’inscription.', [
                'route' => 'app_register',
                'errors' => $validationErrors,
            ]);
        }

        // Rendu du formulaire d'inscription avec les erreurs éventuelles
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
