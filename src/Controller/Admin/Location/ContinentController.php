<?php

namespace App\Controller\Admin\Location;

use App\Entity\Continent;
use App\Form\ContinentType;
use App\Repository\ContinentRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/location/continent')]
class ContinentController extends AbstractController
{
    protected $flashy;
    protected $translator;

    public function __construct(FlashyNotifier $flashy, TranslatorInterface $translator)
    {
        $this->flashy = $flashy;
        $this->translator = $translator;
    }
    #[Route('/', name: 'app_admin_location_continent_index', methods: ['GET'])]
    public function index(ContinentRepository $continentRepository, Request $request): Response
    {
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';

        return $this->render('admin/location/continent/'.$template, [
            'continents' => $continentRepository->findBy(['active' => true])
        ]);
    }

    #[Route('/new', name: 'app_admin_location_continent_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContinentRepository $continentRepository): Response
    {
        $continent = new Continent();
        $form = $this->createForm(ContinentType::class, $continent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $continent->setActive(true);
            $continentRepository->add($continent, true);
            $this->flashy->message( $this->translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }

            return $this->redirectToRoute('app_admin_location_continent_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('admin/location/continent/'.$template, [
            'continent' => $continent,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            )
        );
    }

    #[Route('/{code}', name: 'app_admin_location_continent_show', methods: ['GET'])]
    public function show(Continent $continent): Response
    {
        return $this->render('admin/location/continent/show.html.twig', [
            'continent' => $continent,
        ]);
    }

    #[Route('/{code}/edit', name: 'app_admin_location_continent_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Continent $continent, ContinentRepository $continentRepository): Response
    {
        $form = $this->createForm(ContinentType::class, $continent);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $continentRepository->add($continent, true);
            $this->flashy->message( $this->translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
            return $this->redirectToRoute('app_admin_location_continent_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';

        return $this->renderForm('admin/location/continent/edit.html.twig', [
            'continent' => $continent,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            )
        );
    }

    #[Route('/{code}', name: 'app_admin_location_continent_delete', methods: ['POST'])]
    public function delete(Request $request, Continent $continent, ContinentRepository $continentRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$continent->getCode(), $request->request->get('_token'))) {
            $continentRepository->remove($continent, true);
        }

        return $this->redirectToRoute('app_admin_location_continent_index', [], Response::HTTP_SEE_OTHER);
    }
}
