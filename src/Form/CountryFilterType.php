<?php

namespace App\Form;

use App\Entity\Continent;
use App\Entity\Country;
use App\Entity\Currency;
use App\Repository\CountryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountryFilterType extends AbstractType
{

    public function __construct(private CountryRepository $countryRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('continent', EntityType::class, [
                'class' => Continent::class,
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add('currency_code', EntityType::class,
            [
                'class' => Currency::class,
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])
            ->add(('active'), ChoiceType::class, [
                'choices' => ['Oui' => 'true', 'Non'=>'false'],
                'required' => false,
            ])
            ->setMethod('GET')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
