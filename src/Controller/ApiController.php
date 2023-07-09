<?php

namespace App\Controller;

use App\Entity\Api;
use App\Entity\Seller;
use App\Form\ApiType;
use App\Repository\ApiRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/api')]
class ApiController extends AbstractController
{
    protected $flashy;
    protected $translator;

    public function __construct(FlashyNotifier $flashy, TranslatorInterface $translator)
    {
        $this->flashy = $flashy;
        $this->translator = $translator;
    }

    #[Route('/', name: 'app_api_index', methods: ['GET'])]
    public function index(ApiRepository $apiRepository, Request $request): Response
    {
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';
        return $this->render('api/'.$template, [
            'apis' => $apiRepository->findAll(),
        ]);
    }

    #[Route('/new/{seller?null}', name: 'app_api_new', methods: ['GET', 'POST'])]
    public function new(Request $request,
                        ApiRepository $apiRepository,
                        Seller $seller,
    ): Response
    {
        $api = new Api();
        $form = $this->createForm(ApiType::class, $api);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $seller->setApi($api);
                $apiRepository->save($api, true);
                $this->flashy->success('The api has been successfully created.');
            } catch (\Exception $e) {
                $this->flashy->error($e->getMessage());
            }
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }


            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->renderForm('api/'.$template, [
            'api' => $api,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}', name: 'app_api_show', methods: ['GET'])]
    public function show(Api $api): Response
    {
        return $this->render('api/show.html.twig', [
            'api' => $api,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_api_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,
                         Api $api,
                         ApiRepository $apiRepository,
    ): Response
    {
        $form = $this->createForm(ApiType::class, $api);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $apiRepository->save($api, true);
                $this->flashy->success('The api has been successfully updated.');
            } catch (\Exception $e) {
                $this->flashy->error('The api has not been updated.');
            }
            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';

        return $this->renderForm('api/'.$template, [
            'api' => $api,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }



    #[Route('/{id}', name: 'app_api_delete', methods: ['POST'])]
    public function delete(Request $request, Api $api, ApiRepository $apiRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$api->getId(), $request->request->get('_token'))) {
            $apiRepository->remove($api, true);
        }

        return $this->redirectToRoute('app_api_index', [], Response::HTTP_SEE_OTHER);
    }
}
