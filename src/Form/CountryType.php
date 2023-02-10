<?php

namespace App\Form;

use App\Entity\Continent;
use App\Entity\Country;
use App\Entity\Currency;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;

class CountryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('continent', EntityType::class, [
                'class' => Continent::class,
                'attr' => [
                    'class' => 'form-control'
                ]])

            ->add('currency', EntityType::class, [
                'required' => false,
                'class' => Currency::class,
                'attr' => [
                    'class' => 'form-select'
                ]])


            ->add('code', null, [
                'attr' => [
                    'class' => 'form-control'
                ]])

            ->add('name', null, [
                'attr' => [
                    'class' => 'form-control'
                ]])

            ->add('alpha3', null, [
                'attr' => [
                    'class' => 'form-control'
                ]])

            ->add('phone_code', null, [
                'attr' => [
                    'class' => 'form-control'
                ],
            ])


            ->add('capital', null, [
                'attr' => [
                    'class' => 'form-control'
                ]])

            ->add('active', CheckboxType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-check',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Country::class,
        ]);
    }
}
