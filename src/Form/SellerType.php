<?php

namespace App\Form;

use App\Entity\Api;
use App\Entity\City;
use App\Entity\Seller;
use App\Entity\User;
use phpDocumentor\Reflection\Types\String_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SellerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', UserType::class,[
                'label' => false,
                "disabled" => true,
                "empty_data"=>$options['data']->getUser(),
                //'required' => true,

            ])

            ->add('name',null,[
                "disabled" => true,

            ])
            ->add('website',null,[
                "disabled" => true,
            ])
            ->add('address',null,[
                "disabled" => true,
            ])
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => City::class,
                "disabled" => true,

            ]);

        $builder->get('user')
            ->remove('password');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seller::class,
        ]);
    }
}
