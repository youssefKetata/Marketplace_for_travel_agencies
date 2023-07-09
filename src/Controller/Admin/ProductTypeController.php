<?php

namespace App\Controller\Admin;


use App\Entity\ProductType;
use App\Form\ProductTypeType;
use App\Repository\ProductTypeRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route('/product/type')]
class ProductTypeController extends AbstractController
{
    private $em;
    protected $flashy;
    protected $translator;

    public function __construct(FlashyNotifier $flashy,
                                TranslatorInterface $translator,
                                private readonly ManagerRegistry $doctrine
    )
    {
        $this->flashy = $flashy;
        $this->translator = $translator;
    }

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
            try {
                $productTypeRepository->save($productType, true);
                $this->flashy->success('The product type has been successfully created.');
            }catch (\Exception $e){
                $this->flashy->error('The product type has not been created.');
            }

            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
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
//    #[Route('/{id}', name: 'app_product_type_show', methods: ['GET'])]
//    public function show(ProductType $productType): Response
//    {
//        return $this->render('product_type/show.html.twig', [
//            'product_type' => $productType,
//        ]);
//    }

    #[Route('/{id}/edit', name: 'app_product_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProductType $productType, ProductTypeRepository $productTypeRepository): Response
    {
        $form = $this->createForm(ProductTypeType::class, $productType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $productTypeRepository->save($productType, true);
                $this->flashy->success('The product type has been successfully updated.');
            }catch (\Exception $e){
                $this->flashy->error('The product type has not been updated.');
            }
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }

            return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product_type/edit.html.twig', [
            'product_type' => $productType,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}/delete', name: 'app_product_type_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, ProductType $productType, ProductTypeRepository $productTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$productType->getId(), $request->request->get('_token'))) {
            $productTypeRepository->remove($productType, true);
            $this->flashy->success("The product type has been successfully deleted.");
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
        }

        return $this->redirectToRoute('app_product_type_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/OfferProductsTypes', name: 'offerProductTypes_ProductType', methods: ['GET'])]
    public function showOfferProductTypes(int $id): Response
    {

        $productType =  $this->doctrine
            ->getRepository(ProductType::class)
            ->find($id);
         //$offerProductTypes = $productType->getProductTypeidProductType();

        if (!$productType) {
            throw $this->createNotFoundException(
                'No productType found for id '.$id
            );
        }

        return $this->render('product_type/show_OffreProductType.html.twig', [
            'productType' => $productType,
          //  'offerProductTypes' => $offerProductTypes

        ]);
    }
}
