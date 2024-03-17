<?php

namespace App\Service;

use App\Entity\User;

interface PaymentHandlerInterface
{
    public function getPremiumCheckoutUrlFor(User $utilisateur)  : string;
    public function checkPaymentStatus($sessionId) : bool;
}