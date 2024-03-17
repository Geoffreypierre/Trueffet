<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SearchType;
use App\Model\SearchData;
use App\Repository\CategoryRepository;
use App\Repository\VinylRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class VinylController extends AbstractController
{

    private CategoryRepository $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    #[Route('/', name: 'app_home')]
    public function home(VinylRepository $repository, Request $request, SessionInterface $session): Response
    {
        $listeVinyl = $repository->findBy(array("isCollector" => false));
        $panier = $session->get("panier", []);
        $nbVinyl = count($panier);

        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vinylByName = $repository->findBySearch($searchData);
            $allVinyls = $vinylByName;
            if ($allVinyls != null) {
                return $this->render('vinyl/index.html.twig', [
                    "listeVinyl" => $allVinyls,
                    "listeCategories" => $this->categoryRepository->findAll(),
                    "nbVinyl" => $nbVinyl,
                    'form' => $form->createView(),
                    'searched' => true,
                    'vinyls' => ($allVinyls),
                ]);
            } else {
                return $this->render('vinyl/index.html.twig', [
                    "listeVinyl" => $listeVinyl,
                    "listeCategories" => $this->categoryRepository->findAll(),
                    "nbVinyl" => $nbVinyl,
                    'form' => $form->createView(),
                    'searched' => true,
                    'vinyls' => []
                ]);
            }
        }
        return $this->render('vinyl/index.html.twig', [
            "listeVinyl" => $listeVinyl,
            "listeCategories" => $this->categoryRepository->findAll(),
            "nbVinyl" => $nbVinyl,
            'form' => $form->createView(),
            'searched' => false]);

    }

    #[Route('/collectors', name: 'app_collector', methods: ["GET"])]
    public function getCollector(VinylRepository $vinylRepository): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!is_null($user)) {
            if ($user->isPremium()) {
                $listeVinyl = $vinylRepository->findBy(array("isCollector" => true));
                return $this->render('vinyl/collector.html.twig', ["listeVinyl" => $listeVinyl, "listeCategories" => $this->categoryRepository->findAll()]);
            }
            return $this->redirectToRoute('app_home');
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/vinyl/{id}', name: 'app_vinyl', options: ["expose" => true], methods: ["GET"])]
    public function getVinyl(VinylRepository $vinylRepository, int $id): Response
    {
        $vinyl = $vinylRepository->find($id);

        return $this->render('vinyl/single.html.twig', ["vinyl" => $vinyl, "listeCategories" => $this->categoryRepository->findAll()]);

    }

    #[Route('/category/{id}/vinyls', name: 'app_category_vinyls', options: ["expose" => true], methods: ["GET"])]
    public function getVinylByCategory(Request $request, VinylRepository $vinylRepository, CategoryRepository $categoryRepository, int $id): Response
    {
        $listeVinyl = $vinylRepository->findBy(array("isCollector" => false));
        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $vinylByName = $vinylRepository->findBySearch($searchData);
            $allVinyls = $vinylByName;
            if ($allVinyls != null) {
                return $this->render('vinyl/index.html.twig', [
                    "listeVinyl" => $allVinyls,
                    "listeCategories" => $this->categoryRepository->findAll(),
                    'form' => $form->createView(),
                    'searched' => true,
                    'vinyls' => ($allVinyls),
                ]);
            } else {
                return $this->render('vinyl/index.html.twig', [
                    "listeVinyl" => $listeVinyl,
                    "listeCategories" => $this->categoryRepository->findAll(),
                    'form' => $form->createView(),
                    'searched' => true,
                    'vinyls' => []
                ]);
            }
        }
        $category = $categoryRepository->find($id);
        foreach ($category->getVinyls()->getValues() as $vinyl) {
            if ($vinyl->isIsCollector()) {
                $category->removeVinyl($vinyl);
            }
        }
        $listeVinyl = $category->getVinyls();
        return $this->render('vinyl/index.html.twig', [
            "listeVinyl" => $listeVinyl,
            "listeCategories" => $this->categoryRepository->findAll(),
            'form' => $form->createView(),
            'searched' => true
        ]);
    }

    #[Route('/category', name: 'app_category', options: ["expose" => true], methods: ["GET"])]
    public function getCategories(CategoryRepository $categoryRepository): Response
    {
        $listeCategories = $categoryRepository->findAll();
        return $this->isGranted('ROLE_USER') ? $this->json(['genre-cats' => $listeCategories]) : $this->json([]);
    }
}
