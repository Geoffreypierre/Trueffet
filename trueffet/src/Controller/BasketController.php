<?php
namespace App\Controller;

use App\Entity\User;
use App\Entity\Vinyl;
use App\Form\UserUpdateType;
use App\Repository\CategoryRepository;
use App\Repository\VinylRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class BasketController extends AbstractController
{

    /*public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }*/
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    #[Route('/basket/index', name: 'app_basket_index' ,options: ["expose" => true],  methods: ["GET"])]
    public function index(SessionInterface $session, VinylRepository $vinylsRepository)
    {
        if($this->isGranted('ROLE_USER')) {

            $panier = $session->get("panier", []);

            $dataPanier = [];
            $total = 0;

            foreach ($panier as $id => $quantite) {
                $vinyl = $vinylsRepository->find($id);
                $dataPanier[] = ["vinyl" => $vinyl];
                $total += $vinyl->getPrice();
            }
            return $this->render('user/basket.html.twig',
                ["dataPanier" => $dataPanier, "total" => $total, "listeCategories" => $this->categoryRepository->findAll()]);
        }
        else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/basket/add/{id}', name: 'app_basket_add' ,options: ["expose" => true],  methods: ["GET"])]
    public function add(Vinyl $vinyl, SessionInterface $session)
    {

        $id = $vinyl->getId();
        // On récupère le panier actuel
        $panier = $session->get('panier', []);

        if (!isset($panier[$id])) {
            $panier[$id] = 1;
            // On sauvegarde dans la session
            $session->set("panier", $panier);
            $this->addFlash('success', 'Article ajouté au panier');
        }
        else {
            $this->addFlash('info', 'Article déjà au panier');

        }

        return $this->redirectToRoute("app_home");
    }

    #[Route('/basket/remove', name: 'app_basket_remove' ,options: ["expose" => true],  methods: ["GET"])]
    public function remove(Vinyl $vinyl, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $vinyl->getId();

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }else{
                unset($panier[$id]);
            }
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);

        return $this->redirectToRoute("app_home");
    }

    #[Route('/basket/delete/{id}', name: 'app_basket_delete' ,options: ["expose" => true],  methods: ["GET"])]
    public function delete(Vinyl $vinyl, SessionInterface $session)
    {
        // On récupère le panier actuel
        $panier = $session->get("panier", []);
        $id = $vinyl->getId();

        if(!empty($panier[$id])){
            unset($panier[$id]);
        }

        // On sauvegarde dans la session
        $session->set("panier", $panier);
        $this->addFlash('success', 'Article retiré');
        return $this->redirectToRoute("app_basket_index");
    }

    #[Route('/basket/deleteAll', name: 'app_basket_delete_all' ,options: ["expose" => true],  methods: ["GET"])]
    public function deleteAll(SessionInterface $session)
    {
        $session->remove("panier");
        $this->addFlash('success', 'Tous les article ont été retirés');
        return $this->redirectToRoute("app_basket_index");
    }

    #[Route('/basket/pay', name: 'app_basket_payment' ,options: ["expose" => true],  methods: ["GET"])]
    public function payment(EntityManagerInterface $entityManager, SessionInterface $session, VinylRepository $vinylRepository) {
        $panier = $session->get("panier", []);
        /** @var User $user */
        $user = $this->getUser();
        foreach ($panier as $id => $v) {
            $vinyl = $vinylRepository->find($id);
            $user->addVinyl($vinyl);
        }
        $entityManager->flush();
        $this->addFlash('success', 'Paiement Validé');
        $session->remove("panier");
        return $this->redirectToRoute("app_user_profile_edit", ['id' => $user->getId()]);
    }


    //API
    #[Route('/basket/api', name: 'app_basket_api' ,options: ["expose" => true],  methods: ["GET"])]
    public function api(SessionInterface $session, VinylRepository $vinylsRepository)
    {
        $panier = $session->get("panier", []);

        if ($this->isGranted('ROLE_USER')) {
            return $this->json(
                [
                    'basketCountDiv' => count($panier),
                ],
            );
        }
        else {
            return $this->json(
                []
            );
        }

    }

}
