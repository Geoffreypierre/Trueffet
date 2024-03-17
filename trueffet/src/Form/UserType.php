<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('login', TextType::class)
            ->add('name', TextType::class, [
                "constraints" => [
                    new NotBlank(),
                    new NotNull()]
            ])
            ->add('firstName', TextType::class, [
                "constraints" => [
                    new NotBlank(),
                    new NotNull()]
            ])
            ->add('emailAdress', EmailType::class)
            ->add('plainPassword', PasswordType::class, [
                "mapped" => false,
                "constraints" => [
                    new NotBlank(),
                    new NotNull(),
                    new Length(
                        min: 8,
                        max: 20
                    ),
                    new Regex(
                        pattern: '#^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)[a-zA-Z\\d]{8,30}$#',
                        message: 'Mot de passe invalide'
                    )
                ]
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
            ->add('inscription', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
