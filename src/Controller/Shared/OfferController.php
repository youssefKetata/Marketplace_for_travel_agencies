<?php

namespace App\Controller\Shared;

use App\Entity\Offer;
use App\Form\OfferType;
use App\Repository\OfferRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;


#[Route('/offer')]
class OfferController extends AbstractController
{
    protected FlashyNotifier $flashy;
    protected TranslatorInterface $translator;

    public function __construct(FlashyNotifier $flashy, TranslatorInterface $translator, private readonly ManagerRegistry $doctrine)
    {
        $this->flashy = $flashy;
        $this->translator = $translator;
    }

    #[Route('/', name: 'app_offer_index', methods: ['GET'])]
    public function index(OfferRepository $offerRepository, Request $request): Response
    {
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';
        return $this->render('offer/'. $template, [
            'offers' => $offerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_offer_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OfferRepository $offerRepository): Response
    {
        $offer = new Offer();
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $offerRepository->save($offer, true);
                $this->flashy->success('The offer has been successfully created.');
            } catch (\Exception $e) {
                $this->flashy->error('An error occurred while trying to create the offer.');
            }

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer/new.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_show', methods: ['GET'])]
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offer $offer, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(OfferType::class, $offer);

        // Store original offerProductTypes for removal check
        $originalOfferProductTypes = new ArrayCollection();
        foreach ($offer->getOfferProductTypes() as $offerProductTypes) {
            $originalOfferProductTypes->add($offerProductTypes);
        }

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Remove offerProductTypes that were removed from the form
            foreach ($originalOfferProductTypes as $offerProductTypes) {
                if (false === $offer->getOfferProductTypes()->contains($offerProductTypes)) {
                    // Check if offer product type has any associated child records
                    if ($offerProductTypes->getProductType()) {
                        // Remove the association with the child record first to avoid foreign key constraint errors
                        $offerProductTypes->getProductType()->removeOfferProductType($offerProductTypes);
                    }
                    $manager->remove($offerProductTypes);
                }
            }

            // Add new offerProductTypes
            foreach ($form->get('offerProductTypes')->getData() as $offerProductTypes) {
                $offerProductTypes->setOffer($offer);
                $manager->persist($offerProductTypes);
            }

            $manager->flush();

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

//    public function edit(Request $request, Offer $offer, OfferRepository $offerRepository): Response
//    {
//        $form = $this->createForm(OfferEditType::class, $offer);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $offerRepository->save($offer, true);
//
//            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('offer/edit.html.twig', [
//            'offer' => $offer,
//            'form' => $form,
//        ]);
//    }
//
    #[Route('/{id}', name: 'app_offer_delete', methods: ['POST'])]
    public function delete(Request $request, Offer $offer, OfferRepository $offerRepository): Response
    {

        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {
            $offerRepository->remove($offer, true);
            $this->flashy->success('The offer has been successfully deleted.');
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
        }

        return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{id}/OfferProductsTypes', name: 'app_offerProductTypes', methods: ['GET'])]
    public function showOfferProductTypes(int $id): Response
    {

        $offer =  $this->doctrine
            ->getRepository(Offer::class)
            ->find($id);
        // $offerProductTypes = $offer->getOfferProductTypes();

        if (!$offer) {
            throw $this->createNotFoundException(
                'No offer found for id '.$id
            );
        }

        return $this->render('offer/show_Offer_Product.html.twig', [
            'offer' => $offer,
            // 'offerProductTypes' => $offerProductTypes

        ]);
    }

}
