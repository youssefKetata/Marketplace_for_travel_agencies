<?php

namespace App\Form;

use Sherlockode\ConfigurationBundle\Manager\ConfigurationManagerInterface;
use Sherlockode\ConfigurationBundle\Manager\FieldTypeManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Class ParametersType
 */
class ParametersTypeCustom extends AbstractType
{

    /**
     * @var ConfigurationManagerInterface
     */
    private $configurationManager;

    /**
     * @var FieldTypeManagerInterface
     */
    private $fieldTypeManager;

    public function __construct(  ConfigurationManagerInterface $configurationManager, FieldTypeManagerInterface $fieldTypeManager)
    {
        $this->fieldTypeManager = $fieldTypeManager;
        $this->configurationManager = $configurationManager;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
// get custom parameters
        $CUSTOM_OPTIONS = $options['attr'];
        $group_id = $CUSTOM_OPTIONS['KEY_GROUP'];
        foreach ($this->configurationManager->getDefinedParameters() as $definition) {
            $field = $this->fieldTypeManager->getField($definition->getType());
            $allow_add = true;
            if($group_id != null){
                $tab = explode('.', $definition->getLabel());
                $KeyGroupement = $tab[0] . '.' . $tab[1] ;
                $allow_add = ($KeyGroupement == $group_id);
            }

            if($allow_add){
                $baseOptions = [
                    'label' => $definition->getLabel(),
                    'required' => $definition->getOption('required', true),
                    'attr' => $definition->getOption('attr', ['class' => 'form-control']),
                    'row_attr' => $definition->getOption('row_attr', ['class' => 'col-md-6']),
                    'label_attr' => $definition->getOption('label_attr', []),
                    'translation_domain' => $definition->getTranslationDomain(),
                ];
                $childOptions = array_merge($baseOptions, $field->getFormOptions($definition));

                $builder
                    ->add($definition->getPath(), $field->getFormType($definition), $childOptions)
                ;
            }


        }
    }
}
