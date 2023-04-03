<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\MarketSubscriptionRequest;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;

class MarketSubscriptionRequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('website')
            ->add('address')
            ->add('city', EntityType::class, [
                'required' => true,
                'class' => City::class,
                'attr' => [
                    'class' => 'form-select'
                ]
            ])
            ->add('terms', CheckboxType::class, [
                'mapped' => false,
                'label' => 'I have read and accept the Terms and conditions and Privacy policy',
                'label_html' => true,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Please accept the terms and conditions.',
                    ])
                ]
            ])
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MarketSubscriptionRequest::class,
        ]);
    }
}
