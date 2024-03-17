<?php

namespace App\Controller;
use App\Entity\User;
use App\Service\PaymentHandlerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PremiumController extends AbstractController
{
    #[IsGranted(new Expression("is_granted('ROLE_USER') and !user.isPremium()"))]
    #[Route('/premium', name: 'premiumInfos', methods: ["GET"])]
    public function premium(): Response
    {
        $user = $this->getUser();
        if ($user && !$user->isPremium()) {
            return $this->redirectToRoute('app_home');
        }
        return new Response("User déjà Premium !",  500);
    }

    #[IsGranted(new Expression("is_granted('ROLE_USER') and !user.isPremium()"))]
    #[Route('/premium/checkout', name: 'premiumCheckout', methods: ["GET"])]
    public function premiumCheckout(PaymentHandlerInterface $paymentHandlerInterface): Response {
        /** @var User $user */
        $user = $this->getUser();
        $url = $paymentHandlerInterface->getPremiumCheckoutUrlFor($user);
        return $this->redirect($url);
    }

    #[Route('/premium/checkout/confirm', name: 'premiumCheckoutConfirm', methods: ["GET"])]
    public function premiumCheckoutConfirm(#[MapQueryParameter] string $session_id, PaymentHandlerInterface $paymentHandlerInterface): Response {
        if ($paymentHandlerInterface->checkPaymentStatus($session_id)) {
            $this->addFlash("success", "Paiement confirmé !");
        }
        else {
            $this->addFlash("error", "Une erreur est survenue lors du paiement. Veuillez réessayer !");
        }
        return $this->redirectToRoute('app_home');
    }
}