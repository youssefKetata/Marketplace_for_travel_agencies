<?php

namespace App\Controller;

use App\Entity\FileData;
use App\Form\UploadImageGaleryType;
use App\Repository\FileDataRepository;
use App\Service\AdminActions;
use App\Service\Helpers;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;


#[Route('/admin/{module}/{entity}')]
class FileController extends AbstractController
{
    #[Route('/{id}/gallery', name: 'app_admin_edit_images', methods: ['POST', 'GET'])]
    public function editGallery( string $module ,Request $request, string $entity, int $id, SluggerInterface $slugger, Helpers $helpers, FileDataRepository $fileDataRepository, AdminActions $adminActions, ManagerRegistry  $em): Response
    {

        $entity_data =$em->getManager()->getRepository( 'App\Entity\\'.ucfirst($entity))->find($id);

        $menu_tab = $adminActions->getMenuTabs()[$entity];
        $directory = $this->getParameter('images_directory') . '/'.$entity;

        $fileData = new FileData();

        $form = $this->createForm(UploadImageGaleryType::class, $fileData);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $files = $form->get('file')->getData();
            try {
                $helpers->insertFile($files, $directory, $entity_data, $fileDataRepository, $slugger);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }

            return $this->redirect('/admin/'.$module.'/'.strtolower($entity).'/' . $entity_data->getId() . '/gallery');
        }

        if ($entity_data->getImages()->getValues()) {
            $images = $entity_data->getImages()->getValues();
            $fileData = $images[0];
        }


        $formImage = $this->createForm(\App\Form\FileDataType::class, $fileData);
        $formImage->handleRequest($request);
        if ($formImage->isSubmitted() && $formImage->isValid()) {
            if ($formImage->get('ordre')->getData() == 1) {
                $imageprincipale = $fileDataRepository->findOneBy(['ordre' => 1]);
                $imageprincipale->setOrdre(0);
                $fileDataRepository->add($imageprincipale, true);
            }
            $fileDataRepository->add($fileData, true);

            return $this->redirect('/admin/'.$module.'/prov_'.strtolower($entity).'/'.strtolower($entity).'/' . $entity_data->getId() . '/gallery');
        }


        return $this->renderForm('admin/'.$module .'/prov_'.strtolower($entity).'/'.strtolower($entity).'/hotel_images/edit.html.twig', [
            'menu' => $menu_tab,
            strtolower($entity) => $entity_data,
            'form' => $form,
            'images' => $entity_data->getImages(),
            'active' => $menu_tab[5]['order'],
            'formImage' => $formImage
        ]);
    }
}
