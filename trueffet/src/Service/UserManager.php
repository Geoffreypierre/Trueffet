<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserManager implements UserManagerInterface
{

    public function __construct(
        #[Autowire('%dossier_photo_profil%')] private string $dossierPhotoProfil,
        private readonly UserPasswordHasherInterface         $userPasswordHasher,
    )
    {
    }

    /**
     * Chiffre le mot de passe puis l'affecte au champ correspondant dans la classe de l'user
     */
    private function chiffrerMotDePasse(User $user, ?string $plainPassword): void
    {
        $password = $this->userPasswordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
    }

    /**
     * Sauvegarde l'image de profil dans le dossier de destination puis affecte son nom au champ correspondant dans la classe de l'user
     */
    private function sauvegarderPhotoProfil(User $user, ?UploadedFile $fichierPhotoProfil): void
    {
        if ($fichierPhotoProfil != null) {
            $fileName = uniqid() . '.' . $fichierPhotoProfil->guessExtension();
            $fichierPhotoProfil->move($this->dossierPhotoProfil, $fileName);
            $user->setNameProfilImage($fileName);
        }
    }

    /**
     * Réalise toutes les opérations nécessaires avant l'enregistrement en base d'un nouvel user, après soumissions du formulaire (hachage du mot de passe, sauvegarde de la photo de profil...)
     */
    public function proccessNewUser(User $user, ?string $plainPassword, ?UploadedFile $fichierPhotoProfil): void
    {
        $this->chiffrerMotDePasse($user, $plainPassword);
        $this->sauvegarderPhotoProfil($user, $fichierPhotoProfil);
    }

}