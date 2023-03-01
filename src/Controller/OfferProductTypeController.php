<?php

namespace App\Controller;

use App\Entity\OfferProductType;
use App\Form\OfferProductTypeType;
use App\Repository\OfferProductTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/offer/product/type')]
class OfferProductTypeController extends AbstractController
{
    #[Route('/', name: 'app_offer_product_type_index', methods: ['GET'])]
    public function index(OfferProductTypeRepository $offerProductTypeRepository,
                          Request $request,
    ): Response
    {
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';
        return $this->render('offer_product_type/'.$template, [
            'offer_product_types' => $offerProductTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_offer_product_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        $offerProductType = new OfferProductType();
        $form = $this->createForm(OfferProductTypeType::class, $offerProductType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerProductTypeRepository->save($offerProductType, true);
            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }
            return $this->redirectToRoute('app_offer_product_type_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('offer_product_type/'.$template, [
            'offer_product_type' => $offerProductType,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}', name: 'app_offer_product_type_show', methods: ['GET'])]
    public function show(OfferProductType $offerProductType): Response
    {
        return $this->render('offer_product_type/show.html.twig', [
            'offer_product_type' => $offerProductType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offer_product_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OfferProductType $offerProductType, OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        $form = $this->createForm(OfferProductTypeType::class, $offerProductType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerProductTypeRepository->save($offerProductType, true);

            return $this->redirectToRoute('app_offer_product_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer_product_type/edit.html.twig', [
            'offer_product_type' => $offerProductType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_product_type_delete', methods: ['POST'])]
    public function delete(Request $request, OfferProductType $offerProductType, OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offerProductType->getId(), $request->request->get('_token'))) {
            $offerProductTypeRepository->remove($offerProductType, true);
        }

        return $this->redirectToRoute('app_offer_product_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
