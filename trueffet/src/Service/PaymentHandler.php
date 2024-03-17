<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentHandler implements PaymentHandlerInterface
{

    public function __construct(private RouterInterface                       $router,
                                #[Autowire('%premium_price%')] private string $premium_price,
                                #[Autowire('%secret_key%')] private string    $secret_key,
                                private UserRepository $utilisateurRepository,
                                private EntityManagerInterface $entityManager)
    {
    }

    //Génère et renvoie un lien vers Stripe afin de finaliser l'achat du statut Premium pour l'utilisateur passé en paramètre.
    public function getPremiumCheckoutUrlFor(User $utilisateur)  : string {

        $paymentData = [
            'mode' => 'payment',
            'payment_intent_data' => ['capture_method' => 'manual', 'receipt_email' => $utilisateur->getEmailAdress()],
            'customer_email' => $utilisateur->getEmailAdress(),
            'success_url' => $this->router->generate("premiumCheckoutConfirm",[] , UrlGeneratorInterface::ABSOLUTE_URL).'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => $this->router->generate("premiumInfos",[] , UrlGeneratorInterface::ABSOLUTE_URL),
            "metadata" => ["data" => $utilisateur->getId()],
            "line_items" => [
                [
                    "price_data" => [
                        "currency" => "eur",
                        "product_data" => ["name" => "Premium"],
                        "unit_amount" => $this->premium_price * 100,
                    ],
                    "quantity" => 1
                ],
                []
            ]
        ];
        Stripe::setApiKey($this->secret_key);
        $stripeSession = Session::create($paymentData);
        return $stripeSession->url;

    }

    public function handlePaymentPremium(Session $session) : void {
        $userId = $session["metadata"]["data"];
        $user = $this->utilisateurRepository->findOneBy(['id' => $userId]);

        //L'objet "paymentIntent" permet de capturer (confirmer) ou d'annuler le paiement.
        $paymentIntent = $session["payment_intent"];
        //Pour réaliser ces opérations, on a besoin d'un objet StripeClient initialisé avec notre clé secrète d'API.
        $stripe = new StripeClient($this->secret_key);

        if (is_null($user) || $user->isPremium()) {
            //Pour annuler le paiement
            $stripe->paymentIntents->cancel($paymentIntent);
            throw new \Exception("L'utilisateur n'existe pas ou est déjà premium !");
        }


        //Pour "capturer" et valider le paiement
        $paymentCapture = $stripe->paymentIntents->capture($paymentIntent, []);
        //On peut ensuite vérifier si le paiement a bien été capturé (si oui, on dispose de l'argent sur le compte Stripe, à ce stade).
        if($paymentCapture == null || $paymentCapture["status"] != "succeeded") {
            throw new \Exception("Le paiement n'a pas pu être complété...");
        }
        $user->setPremium(true);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

    }

    //Renvoie true si le paiement lié à la session dont l'identifiant est passé en paramètre a aboutit (a été capturé...) et renvoie false sinon.

    public function checkPaymentStatus($sessionId) : bool {
        //On initialise le client Stripe avec notre clé secrète
        $stripe = new StripeClient($this->secret_key);

        //On récupère les données de la session à partir de l'identifiant de la session
        $session = $stripe->checkout->sessions->retrieve($sessionId);

        //On extraie l'identifiant du paiement depuis la session
        $paymentIntentId = $session->payment_intent;

        //On récupère les données du paiement
        $paymentIntent = $stripe->paymentIntents->retrieve($paymentIntentId);

        //L'état "succeeded" signifie que le paiement a bien été capturé (le client a été débité)
        $status = $paymentIntent->status;

        return $status == 'succeeded';
    }


}