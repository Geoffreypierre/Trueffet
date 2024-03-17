<?php

namespace App\Controller;

use App\Service\PaymentHandler;
use App\Service\PaymentHandlerInterface;
use Stripe\Webhook;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{

    public function __construct(#[Autowire('%secret_webhook%')] private string $secret_webhook,
                                private PaymentHandlerInterface $paymentHandler) {

    }
    #[Route('/webhook/stripe', name: 'stripeWebhook', methods: ["POST"])]
    public function index(): Response
    {
        //On extrait le contenu de la requête (format imposé par Stripe, on utilise pas les outils de Symfony dans ce cas)
        $payload = @file_get_contents('php://input');

        //On extrait la signature de la requête
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        try {
            $event = Webhook::constructEvent($payload, $sig_header, $this->secret_webhook);

            if ($event->type == 'checkout.session.completed') {
                $session = $event->data->object;
                //On imagine que $service est un service contenant une méthode permettant de traiter la suite de la requête.
                //Si on arrive là, tout s'est bien passé, on renvoie un code de succès à Stripe.
                $this->paymentHandler->handlePaymentPremium($session);
                return new Response("IT'S OK !", 200);
            }
            else {
                //Si on arrive là, c'est qu'on ne gère pas l'événement déclenché, on renvoie alors un code d'erreur à Stripe.
                return new Response("Erreur de session !", 400);
            }
        } catch(\Exception $e) {
            return new Response("Erreur de requête !", 400);
        }
    }
}