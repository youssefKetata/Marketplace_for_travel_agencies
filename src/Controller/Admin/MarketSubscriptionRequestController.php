<?php

namespace App\Controller\Admin;

use App\Entity\MarketSubscriptionRequest;
use App\Entity\User;
use App\Form\MarketSubscriptionRequestType;
use App\Repository\MarketSubscriptionRequestRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/market/subscription/request')]
class MarketSubscriptionRequestController extends AbstractController
{
    #[Route('/', name: 'app_market_subscription_request_index', methods: ['GET'])]
    public function index(MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository,
                          Request $request,
                          UserRepository $userRepository
    ): Response

    {

        return $this->render('market_subscription_request/index.html.twig', [
            'market_subscription_requests' => $marketSubscriptionRequestRepository->findAll(),
            'users' => $userRepository->findByRole("ROLE_SELLER")

        ]);
    }

    #[Route('/new', name: 'app_market_subscription_request_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository ,Mailer $mailer): Response
    {
        $marketSubscriptionRequest = new MarketSubscriptionRequest();
        $form = $this->createForm(MarketSubscriptionRequestType::class, $marketSubscriptionRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailermessage = $marketSubscriptionRequest->getName().' '.$marketSubscriptionRequest->getEmail().' ';
            $mailer->sendEmail(content: $mailermessage);
            $marketSubscriptionRequestRepository->save($marketSubscriptionRequest, true);
//            $user = new User();
//            $now = new \DateTime();
//            $user->setCreatedAt($now->format('Y-m-d H:i:s'));
            return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('market_subscription_request/'.$template, [
            'market_subscription_request' => $marketSubscriptionRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_subscription_request_show', methods: ['GET'])]
    public function show(MarketSubscriptionRequest $marketSubscriptionRequest): Response
    {
        return $this->render('market_subscription_request/show.html.twig', [
            'market_subscription_request' => $marketSubscriptionRequest,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_market_subscription_request_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MarketSubscriptionRequest $marketSubscriptionRequest, MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository): Response
    {
        $form = $this->createForm(MarketSubscriptionRequestType::class, $marketSubscriptionRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marketSubscriptionRequestRepository->save($marketSubscriptionRequest, true);

            return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('market_subscription_request/edit.html.twig', [
            'market_subscription_request' => $marketSubscriptionRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_market_subscription_request_delete', methods: ['POST'])]
    public function delete(Request $request, MarketSubscriptionRequest $marketSubscriptionRequest, MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$marketSubscriptionRequest->getId(), $request->request->get('_token'))) {
            $marketSubscriptionRequestRepository->remove($marketSubscriptionRequest, true);
        }

        return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/rejected/{id}', name: 'app_market_subscription_request_rejected', methods: ['GET','POST'])]
    public function rejected (Request $request, MarketSubscriptionRequest $marketSubscriptionRequest,MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository): Response
    {

        if ($marketSubscriptionRequest->getStatus() === "pending") {
            $marketSubscriptionRequest->setStatus("rejected");
            $marketSubscriptionRequestRepository->save($marketSubscriptionRequest, true);
        }

        return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
    }

}
