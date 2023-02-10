<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Country;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('active', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('country', EntityType::class, [
                'required' => false,
                'class' => Country::class,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->setMethod('GET')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => City::class,
        ]);
    }


    public function getBlockPrefix()
    {
        return '';
    }
}
