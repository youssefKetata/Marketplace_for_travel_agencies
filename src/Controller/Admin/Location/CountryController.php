<?php

namespace App\Controller\Admin\Location;

use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/location/country')]
class CountryController extends AbstractController
{
    protected $flashy;
    protected $translator;

    public function __construct(FlashyNotifier $flashy, TranslatorInterface $translator)
    {
        $this->flashy = $flashy;
        $this->translator = $translator;
    }
    #[Route('/', name: 'app_admin_location_country_index', methods: ['GET'])]
    public function index(CountryRepository $countryRepository, Request $request): Response
    {
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';

        return $this->render('admin/location/country/'.$template, [
            'countries' => $countryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_location_country_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CountryRepository $countryRepository): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $countryRepository->add($country, true);
            $this->flashy->message( $this->translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }

            return $this->redirectToRoute('app_admin_location_country_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';
        return $this->renderForm('admin/location/country/'.$template, [
            'country' => $country,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{code}', name: 'app_admin_location_country_show', methods: ['GET'])]
    public function show(Country $country): Response
    {
        return $this->render('admin/location/country/show.html.twig', [
            'country' => $country,
        ]);
    }

    #[Route('/{code}/edit', name: 'app_admin_location_country_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Country $country, CountryRepository $countryRepository): Response
    {
        $form = $this->createForm(CountryType::class, $country);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $countryRepository->add($country, true);
            $this->flashy->message( $this->translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }

            return $this->redirectToRoute('app_admin_location_country_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';
        return $this->renderForm('admin/location/country/edit.html.twig', [
            'country' => $country,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{code}', name: 'app_admin_location_country_delete', methods: ['POST'])]
    public function delete(Request $request, Country $country, CountryRepository $countryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getCode(), $request->request->get('_token'))) {
            $countryRepository->remove($country, true);
            $this->flashy->message( $this->translator->trans('Message.Facility.Delete'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
        }

        return $this->redirectToRoute('app_admin_location_country_index', [], Response::HTTP_SEE_OTHER);
    }
}
