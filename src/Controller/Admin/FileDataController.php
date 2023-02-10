<?php

namespace App\Controller\Admin;

use App\Entity\FileData;
use App\Form\FileDataType;
use App\Repository\FileDataRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/file/data')]
class FileDataController extends AbstractController
{
    #[Route('/', name: 'app_admin_file_data_index', methods: ['GET'])]
    public function index(FileDataRepository $fileDataRepository): Response
    {
        return $this->render('admin/file_data/index.html.twig', [
            'file_datas' => $fileDataRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_file_data_new', methods: ['GET', 'POST'])]
    public function new(Request $request, FileDataRepository $fileDataRepository): Response
    {
        $fileDatum = new FileData();
        $form = $this->createForm(FileDataType::class, $fileDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileDataRepository->add($fileDatum, true);

            return $this->redirectToRoute('app_admin_file_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/file_data/new.html.twig', [
            'file_datum' => $fileDatum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_file_data_show', methods: ['GET'])]
    public function show(FileData $fileDatum): Response
    {
        return $this->render('admin/file_data/show.html.twig', [
            'file_datum' => $fileDatum,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_file_data_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FileData $fileDatum, FileDataRepository $fileDataRepository): Response
    {
        $form = $this->createForm(FileDataType::class, $fileDatum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileDataRepository->add($fileDatum, true);

            return $this->redirectToRoute('app_admin_file_data_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/file_data/edit.html.twig', [
            'file_datum' => $fileDatum,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_file_data_delete', methods: ['POST'])]
    public function delete(Request $request, FileData $fileDatum, FileDataRepository $fileDataRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fileDatum->getId(), $request->request->get('_token'))) {
            try {
                $fileDataRepository->deleteFile($fileDatum);
            } catch (\Exception $e) {
                throw new \Exception($e->getMessage());
            }
            $fileDataRepository->remove($fileDatum, true);
            if($request->isXmlHttpRequest()){
                return new Response( null , 204);
            }
        }
        if( $request ->query->get('ajax') ) {
            $this->render('admin/file_data/_form.html.twig');
        }
        return $this->redirectToRoute('app_admin_file_data_index', [], Response::HTTP_SEE_OTHER);
    }
}
