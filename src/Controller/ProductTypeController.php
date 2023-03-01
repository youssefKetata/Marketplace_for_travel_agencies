<?php

namespace App\Controller;

use App\Entity\ProductType;
use App\Form\ProductTypeType;
use App\Repository\ProductTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product/type')]
class ProductTypeController extends AbstractController
{
    #[Route('/', name: 'app_product_type_index', methods: ['GET'])]
    public function index(ProductTypeRepository $productTypeRepository, Request $request): Response
    {
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';
        return $this->render('product_type/'.$template, [
            'product_types' => $productTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductTypeRepository $productTypeRepository): Response
    {
        $productType = new ProductType();
        $form = $this->createForm(ProductTypeType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productTypeRepository->save($productType, true);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

            return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('product_type/'.$template, [
            'product_type' => $productType,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}', name: 'app_product_type_show', methods: ['GET'])]
    public function show(ProductType $productType): Response
    {
        return $this->render('product_type/show.html.twig', [
            'product_type' => $productType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductType $productType, ProductTypeRepository $productTypeRepository): Response
    {
        $form = $this->createForm(ProductTypeType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $productTypeRepository->save($productType, true);

            return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product_type/edit.html.twig', [
            'product_type' => $productType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_type_delete', methods: ['POST'])]
    public function delete(Request $request, ProductType $productType, ProductTypeRepository $productTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productType->getId(), $request->request->get('_token'))) {
            $productTypeRepository->remove($productType, true);
        }

        return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
