<?php

namespace App\Controller\Admin\Location;

use App\Entity\Country;
use App\Form\CountryType;
use App\Repository\CountryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/location/country')]
class CountryController extends AbstractController
{
    #[Route('/', name: 'app_admin_location_country_index', methods: ['GET'])]
    public function index(CountryRepository $countryRepository): Response
    {
        return $this->render('admin/location/country/index.html.twig', [
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

            return $this->redirectToRoute('app_admin_location_country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/location/country/new.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
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

            return $this->redirectToRoute('app_admin_location_country_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/location/country/edit.html.twig', [
            'country' => $country,
            'form' => $form,
        ]);
    }

    #[Route('/{code}', name: 'app_admin_location_country_delete', methods: ['POST'])]
    public function delete(Request $request, Country $country, CountryRepository $countryRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$country->getCode(), $request->request->get('_token'))) {
            $countryRepository->remove($country, true);
        }

        return $this->redirectToRoute('app_admin_location_country_index', [], Response::HTTP_SEE_OTHER);
    }
}
