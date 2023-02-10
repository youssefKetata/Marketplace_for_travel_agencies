<?php

namespace App\Controller\Admin;

use App\Form\ParametersTypeCustom;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Sherlockode\ConfigurationBundle\Form\Type\ParametersType;
use Sherlockode\ConfigurationBundle\Manager\ConfigurationManagerInterface;
use Sherlockode\ConfigurationBundle\Manager\FieldTypeManagerInterface;
use Sherlockode\ConfigurationBundle\Manager\ParameterManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingController extends AbstractController
{
    #[Route('/admin/setting', name: 'app_admin_setting')]
    public function index(ParameterManagerInterface $parameterManager, ConfigurationManagerInterface $configurationManager, FieldTypeManagerInterface $fieldTypeManager): Response
    {
        /** @var ParameterManagerInterface $parameterManager */
        $data = $parameterManager->getAll();

        $dataStructure = [];
        $defaultTab = '';
        foreach ($configurationManager->getDefinedParameters() as $definition) {
            $field = $fieldTypeManager->getField($definition->getType());
            $tab = explode('.', $definition->getLabel());

                $reste = '';
                for($i= 2 ; $i< count($tab);$i++ ){
                    if($reste != ''){
                        $reste .= '.';
                    }
                    $reste .= $tab[$i];
                }


            if($defaultTab == ''){
                $defaultTab = $tab[0] . '.' . $tab[1];
            }
            $KeyGroupement = $tab[0] . '.' . $tab[1] ;

            $dataStructure[$KeyGroupement][$reste] = [
                'keyChanged' => str_replace('.','-', $KeyGroupement),
                'label' => $definition->getLabel(),
                'required' => $definition->getOption('required', true),
                'attr' => $definition->getOption('attr', []),
                'row_attr' => $definition->getOption('row_attr', []),
                'label_attr' => $definition->getOption('label_attr', []),
                'translation_domain' => $definition->getTranslationDomain(),
                'value' => isset($data[$definition->getPath()]) ? $data[$definition->getPath()] : null
            ];
            // $childOptions = array_merge($baseOptions, $field->getFormOptions($definition));
        }


        return $this->render('admin/setting/index.html.twig', [
            'dataStructure' => $dataStructure,
            'defaultTab' => $defaultTab
        ]);
    }

    #[Route('/admin/setting/edit', name: 'app_admin_edit_setting')]
    public function edit(Request $request, ParameterManagerInterface $parameterManager, FlashyNotifier $flashy,TranslatorInterface $translator): Response
    {
        // $parameterManager has been injected
        $data = $parameterManager->getAll();
// or using an associative array:
        //$data = ['contact_email' => 'me@example.com', 'max_user_login_attempts' => 5];
//dd($data);
        $form = $this->createForm(ParametersTypeCustom::class, $data);
// handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $params = $form->getData();
            foreach ($params as $path => $value) {
                $parameterManager->set($path, $value);
            }
            // dd($parameterManager);
            $parameterManager->save();
            $flashy->message( $translator->trans('Message.Standard.SuccessSave'));
            return $this->redirectToRoute('app_admin_setting');
        }
        return $this->render('admin/setting/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/admin/setting/editGroup/{group_id}', name: 'app_admin_editGroup_setting')]
    public function editGroup(Request $request, ParameterManagerInterface $parameterManager, FieldTypeManagerInterface $fieldTypeManager, $group_id): Response
    {
        // $parameterManager has been injected
        $data = $parameterManager->getAll();
// or using an associative array:
        //$data = ['contact_email' => 'me@example.com', 'max_user_login_attempts' => 5];
//dd($data);

        $optionsData = [
            'attr' => [
                'KEY_GROUP' => $group_id
            ]
        ];
        $form = $this->createForm( ParametersTypeCustom::class, $data, $optionsData);
// handle form submission
       // dd($parameterManager);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $params = $form->getData();
            foreach ($params as $path => $value) {
                $parameterManager->set($path, $value);
            }
            // dd($parameterManager);
            $parameterManager->save();

            return $this->redirectToRoute('app_admin_setting');
        }
        return $this->render('admin/setting/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
