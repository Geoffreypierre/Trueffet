<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Form\UserUpdateType;
use App\Repository\CategoryRepository;
use App\Repository\UserRepository;
use App\Service\FlashMessageHelperInterface;
use App\Service\UserManagerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends AbstractController
{
    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('/inscription', name: 'app_inscription', methods: ["GET", "POST"])]
    public function inscription(Request $request, EntityManagerInterface $entityManager, UserRepository $utilisateurRepository, FlashMessageHelperInterface $flashMessageHelper, UserManagerInterface $utilisateurManager,
    ): Response
    {
        if($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_home');
        }
        $utilisateur = new User();
        $form = $this->createForm(UserType::class, $utilisateur,
            [
                'method' => 'POST',
                'action' => $this->generateURL('app_inscription')
            ]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            // À ce stade, le formulaire et ses données sont valides
            // L'objet "Exemple" a été mis à jour avec les données, il ne reste plus qu'à le sauvegarder

            $utilisateurManager->proccessNewUser(
                $utilisateur,
                $form->get('plainPassword')->getData(),
                $form->get('fichierPhotoProfil')->getData(),
            );

            $entityManager->persist($utilisateur);
            $entityManager->flush();
            $this->addFlash('success', 'L\'utilisateur a été enregistrée avec succès.');

            //On redirige vers la page suivante
            return $this->redirectToRoute('app_home');
        }

        $flashMessageHelper->addFormErrorsAsFlash($form);

        return $this->render("user/inscription.html.twig",
            [
                "form" => $form,
                "listeCategories" => $this->categoryRepository->findAll()]);
    }

    #[Route('/connexion', name: 'app_connexion', methods: ['GET', 'POST'])]
    public function connexion(CategoryRepository $categoryRepository, AuthenticationUtils $authenticationUtils) : Response {
        if($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/connexion.html.twig', [
            "listeCategories" => $this->categoryRepository->findAll()]);
    }

    #[Route('/deconnexion', name: 'app_deconnexion', methods: ['POST'])]
    public function deconnexion() : never {
        throw new \Exception("Cette route ne doit pas être appelée");
    }

    #[Route('/user/{id}', name: 'app_user_profile', options: ["expose" => true], methods: ['GET'])]
    public function profile(UserRepository $userRepository, int $id) : Response {
        $user = $userRepository->find($id);
        if (!$user) {
            $this->addFlash('warning', 'L\'utilisateur n\'existe pas.');
            return $this->redirectToRoute('app_home');
        }
        else {
            return $this->render("user/profile.html.twig",
                ['user' => $user, "listeCategories" => $this->categoryRepository->findAll()]);
        }
    }

    #[Route('/user/{id}/profile', name: 'app_user_profile_edit', methods: ['POST', 'GET'])]
    public function edit(#[MapEntity] User $user, Request $request, UserManagerInterface $userManager, EntityManagerInterface $entityManager) {
        if (is_null($user)) {
            throw $this->createNotFoundException("Utilisateur inexistant");
        }
        $form = $this->createForm(UserUpdateType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fichierPhotoProfil = $form["fichierPhotoProfil"]->getData();
            $userManager->proccessNewUser($user, $user->getPassword(), $fichierPhotoProfil);
            $entityManager->flush();
            $this->addFlash('success', "Les modifications ont bien été enregistrées");
            return $this->redirectToRoute('app_user_profile_edit', ['id' => $user->getId()]);
        }
        else {
            return $this->render('user/profile.html.twig', [
                'form' => $form->createView(),
                'user' => $user,
                "listeCategories" => $this->categoryRepository->findAll()
            ]);
        }
    }

}
