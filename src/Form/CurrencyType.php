<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Currency;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrencyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            /*   ->add('name' , EntityType::class  , [
                   'mapped' => false,
                   'class' => Country::class,
                   'choice_label' => 'name',
                   'placeholder' => 'Choose a Country',
                   'label' => 'Countries',
                   'choice_value' => function (Country $entity = null) {
                       return $entity ? $entity->getName() : '';
                   },
                   'query_builder' => fn (PaysRepository $payRepository) =>
                   $payRepository->findAllCountriesOrderedByAscNameQueryBuilder(),
               ])*/
            ->add('name', EntityType::class, [
                    'mapped' => false,
                    'class' => Currency::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Choose a Currency',
                    'label' => 'name',
                    'choice_value' => function (Currency $entity = null) {
                        return $entity ? $entity->getName() : '';
                    },
                ]
            )
            ->add('symbol');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Currency::class,
        ]);
    }
}
