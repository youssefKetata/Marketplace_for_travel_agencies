<?php

namespace App\Form;

use App\Entity\Api;
use App\Entity\City;
use App\Entity\Seller;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SellerProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class,[

                "empty_data"=>$options['data']->getUser(),
                //'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],

            ])

            ->add('name',null,[])
            ->add('website',null,[])
            ->add('address',null,[])
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => City::class,
            ])
            ->add('api', EntityType::class, [
                'required' => false,
                'class'=> Api::class
            ])
            ->add('oldPassword',PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'invalid_message' => 'The password fields must match.',
        ])
            ->remove('api')

        ;

        $builder->get('user')
            ->add('password',PasswordType::class,[
                'required' => false,
                'label' => 'New password'
            ])
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
