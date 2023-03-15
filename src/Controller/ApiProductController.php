<?php

namespace App\Controller;

use App\Entity\ApiProduct;
use App\Form\ApiProductType;
use App\Repository\ApiProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/product')]
class ApiProductController extends AbstractController
{
    #[Route('/', name: 'app_api_product_index', methods: ['GET'])]
    public function index(ApiProductRepository $apiProductRepository): Response
    {
        return $this->render('api_product/index.html.twig', [
            'api_products' => $apiProductRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_api_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ApiProductRepository $apiProductRepository): Response
    {
        $apiProduct = new ApiProduct();
        $form = $this->createForm(ApiProductType::class, $apiProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiProductRepository->save($apiProduct, true);

            return $this->redirectToRoute('app_api_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('api_product/new.html.twig', [
            'api_product' => $apiProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_api_product_show', methods: ['GET'])]
    public function show(ApiProduct $apiProduct): Response
    {
        return $this->render('api_product/show.html.twig', [
            'api_product' => $apiProduct,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_api_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ApiProduct $apiProduct, ApiProductRepository $apiProductRepository): Response
    {
        $form = $this->createForm(ApiProductType::class, $apiProduct);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $apiProductRepository->save($apiProduct, true);

            return $this->redirectToRoute('app_api_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('api_product/edit.html.twig', [
            'api_product' => $apiProduct,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_api_product_delete', methods: ['POST'])]
    public function delete(Request $request, ApiProduct $apiProduct, ApiProductRepository $apiProductRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$apiProduct->getId(), $request->request->get('_token'))) {
            $apiProductRepository->remove($apiProduct, true);
        }

        return $this->redirectToRoute('app_api_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
