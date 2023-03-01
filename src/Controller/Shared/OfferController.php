<?php

namespace App\Controller\Shared;

use App\Entity\Offer;
use App\Form\OfferType;
use App\Repository\OfferRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/offer')]
class OfferController extends AbstractController
{
    protected $flashy;
    protected $translator;

    public function __construct(FlashyNotifier $flashy, TranslatorInterface $translator)
    {
        $this->flashy = $flashy;
        $this->translator = $translator;
    }

    #[Route('/', name: 'app_offer_index', methods: ['GET'])]
    public function index(OfferRepository $offerRepository, Request $request): Response
    {
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';
        return $this->render('offer/'.$template, [
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
            $offerRepository->save($offer, true);
            //$this->flashy->message( $this->translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                //$html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response(null, 204);
            }

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('offer/'.$template, [
            'offer' => $offer,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}', name: 'app_offer_show', methods: ['GET'])]
    public function show(Offer $offer): Response
    {
        return $this->render('offer/show.html.twig', [
            'offer' => $offer,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_offer_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Offer $offer, OfferRepository $offerRepository): Response
    {
        $form = $this->createForm(OfferType::class, $offer);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $offerRepository->save($offer, true);

            return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('offer/edit.html.twig', [
            'offer' => $offer,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_offer_delete', methods: ['POST'])]
    public function delete(Request $request, Offer $offer, OfferRepository $offerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$offer->getId(), $request->request->get('_token'))) {
            $offerRepository->remove($offer, true);
        }

        return $this->redirectToRoute('app_offer_index', [], Response::HTTP_SEE_OTHER);
    }
}
