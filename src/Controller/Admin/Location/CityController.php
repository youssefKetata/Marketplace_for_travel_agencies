<?php

namespace App\Controller\Admin\Location;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/location/city')]
class CityController extends AbstractController
{
    #[Route('/', name: 'app_admin_location_city_index', methods: ['GET'])]
    public function index(CityRepository $cityRepository, Request $request): Response
    {
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';
        return $this->render('admin/location/city/'.$template, [
            'cities' => $cityRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_location_city_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CityRepository $cityRepository): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cityRepository->add($city, true);

            if ($request->isXmlHttpRequest()) {
                return new Response(null, 204);
            }

            return $this->redirectToRoute('app_admin_location_city_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('admin/location/city/'.$template, [
            'city' => $city,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            )
        );
    }

    #[Route('/{id}', name: 'app_admin_location_city_show', methods: ['GET'])]
    public function show(City $city): Response
    {
        return $this->render('admin/location/city/show.html.twig', [
            'city' => $city,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_location_city_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, City $city, CityRepository $cityRepository): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cityRepository->add($city, true);

            return $this->redirectToRoute('app_admin_location_city_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';

        return $this->renderForm('admin/location/city/'.$template, [
            'city' => $city,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            )
        );
    }

    #[Route('/{id}', name: 'app_admin_location_city_delete', methods: ['POST'])]
    public function delete(Request $request, City $city, CityRepository $cityRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
            $cityRepository->remove($city, true);
        }

        return $this->redirectToRoute('app_admin_location_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
