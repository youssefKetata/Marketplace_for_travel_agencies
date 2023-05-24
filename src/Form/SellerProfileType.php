<?php

namespace App\Form;

use App\Entity\Api;
use App\Entity\City;
use App\Entity\Seller;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SellerProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class,[

                "empty_data"=>$options['data']->getUser(),
                'required' => false,
                'error_bubbling' => false,
                'constraints' => [
                    new NotBlank(),
                ],

            ])

            ->add('name',TextType::class)
            ->add('website',UrlType::class)
            ->add('address',TextType::class)
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => City::class,
            ])
            ->add('oldPassword',PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'invalid_message' => 'The password fields must match.',
                'attr' => [
                    'placeholder' => 'Old password'
                ],
                'error_bubbling' => false, // Prevent errors from bubbling up to the parent form
        ])
            ->add('brochure', FileType::class, [
                'label' => 'Logo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/png',
                            'image/jpeg',
                            'image/jpg'
                        ],
                        'mimeTypesMessage' => "Please upload a valid image type(.png, .jpeg, .jpg)"
                    ])
                ],
                'help' => '.png, .jpeg, jpg'
            ])
            ->add('newPassword', PasswordType::class,[
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'New password'
                ],
                'constraints' => [
                    new Length(
                        min: 5,minMessage: 'password should at least contain 5 characters'
                    )
                ]
            ])

        ;

        $builder->get('user')
            ->add('email', EmailType::class, [
                'error_bubbling' => false,
                'constraints' => [
                    new Email(),
                    new NotBlank()
                ]
            ])
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->remove('password')
            ->remove('active')
            ->remove('isVerified')
            ->remove('display_name');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}
