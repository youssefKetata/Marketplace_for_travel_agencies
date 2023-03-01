<?php

namespace App\Form;

use App\Entity\Offer;
use App\Entity\OfferProductType;
use App\Entity\ProductType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OfferProductTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('maxItems')
            ->add('price')
            ->add('offer', EntityType::class, [
                'required' => false,
                'class'=> Offer::class
            ])
            ->add('productType', EntityType::class, [
                'required' => false,
                'class'=> ProductType::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => OfferProductType::class,
        ]);
    }
}
