<?php

namespace App\Controller;

use App\Entity\OfferProductType;
//use App\Entity\ProductType;
use App\Form\OfferProdType;
use App\Repository\OfferProductTypeRepository;
//use App\Repository\ProductTypeRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/offer/product')]
class OfferProductTypeController extends AbstractController
{

    protected $flashy;
    protected $translator;

    public function __construct(FlashyNotifier $flashy, TranslatorInterface $translator)
    {
        $this->flashy = $flashy;
        $this->translator = $translator;
    }

    #[Route('/', name: 'app_offer_product_index', methods: ['GET'])]
    public function index(OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        return $this->render('offer_product/index.html.twig', [
            'offer_product_types' => $offerProductTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_offer_product_new', methods: ['GET', 'POST'])]
    //,ProductTypeRepository $ProductTypeRepository
    public function new(Request $request, OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        $offerProductType = new OfferProductType();
        $form = $this->createForm(OfferProdType::class, $offerProductType );
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid() )  {
            try{
                $offerProductTypeRepository->save($offerProductType, true);
                $this->flashy->success("The offer product type has been successfully created.");
            }catch (\Exception $e){
                $this->flashy->error("The offer product type has not been created.");
            }

            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
            return $this->redirectToRoute('app_offer_product_index', [], Response::HTTP_SEE_OTHER);
        }
        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->renderForm('offer_product/'.$template, [
            'offer_product_type' => $offerProductType,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}', name: 'app_offer_product_show', methods: ['GET'])]
    public function show(OfferProductType $offerProductType): Response
    {
        return $this->render('offer_product/show.html.twig', [
            'offer_product_type' => $offerProductType,
        ]);
    }
    #[Route('/{id}/productDetail', name: 'offer_product_seller', methods: ['GET'])]
    public function showOfferSeller(OfferProductType $offerProductType): Response
    {
        return $this->render('seller/dashbord/detail_product.html.twig', [
            'offerProductType' => $offerProductType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offer_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OfferProductType $offerProductType, OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        $form = $this->createForm(OfferProdType::class, $offerProductType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerProductTypeRepository->save($offerProductType, true);

            return $this->redirectToRoute('app_offer_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer_product/edit.html.twig', [
            'offer_product_type' => $offerProductType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_product_delete', methods: ['POST'])]
    public function delete(Request $request, OfferProductType $offerProductType, OfferProductTypeRepository $offerProductTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offerProductType->getId(), $request->request->get('_token'))) {
            $offerProductTypeRepository->remove($offerProductType, true);
        }

        return $this->redirectToRoute('app_offer_product_index', [], Response::HTTP_SEE_OTHER);
    }
}
