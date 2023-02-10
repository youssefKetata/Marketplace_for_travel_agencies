<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class UploadImageGaleryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder
            ->add('file', FileType::class, [
                /*       'row_attr' => [
                         'class' => 'fallback'
                       ],*/
                'label_attr' => [
                    'hidden' => true,
                    'class' => 'col-sm-3'
                ],
                'mapped' => false,
                'multiple' => true,
                'attr' => [
                    'data-plugins' => "dropify",
                    'data-height' => "300"
                ],
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new notBlank(['message' => "please add a photo"]),
                    new All ([
                        'constraints' => [
                            new File([
                                'maxSize' => '1024k',
                                'mimeTypes' => [
                                    'image/jpeg',
                                    'image/png',
                                    'image/svg+xml',
                                    'image/tiff',
                                ],
                                'mimeTypesMessage' => 'Please upload a valid Image with only jpeg or png or png or svg or tiff extension',
                            ])
                        ]
                    ])
                ],
            ])
            ->add('save', SubmitType::class, [
                'row_attr' => [
                    'class' => ' mt-2 text-end'
                ],

                'attr' => [
                    'class' => ' btn btn-primary'
                ],
                'label_html' => true,
                'label' => '<i class = "fe-upload" > </i>  upload',
            ])
            ->setAction($options['action']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            "csrf_protection" => "true",
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
