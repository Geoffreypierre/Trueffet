<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;


class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', TextType::class, [
                "required" => false
            ])
            ->add('name', TextType::class, [
                "required" => false
            ])
            ->add('firstName', TextType::class, [
                "required" => false
            ])
            ->add('emailAdress', EmailType::class, [
                "required" => false
            ])
            ->add('fichierPhotoProfil', FileType::class, [
                "required" => false,
                "mapped" => false,
                "constraints" => [
                    new File(
                        maxSize: '10M',
                        maxSizeMessage: 'Fichier trop volumineux',
                        extensions: ['jpg', 'png'],
                        extensionsMessage: 'Fichier format invalide',
                    )
                ],
                "attr" => [
                    "accept" => 'image/png, image/jpeg',
                ]
            ])
            ->add('modificationProfil', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}