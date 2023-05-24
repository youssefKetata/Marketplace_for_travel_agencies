<?php

namespace App\Controller\Admin\Location;

use App\Entity\City;
use App\Form\CityType;
use App\Repository\CityRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/admin/location/city')]
class CityController extends AbstractController
{
    protected $flashy;
    protected $translator;

    public function __construct(FlashyNotifier $flashy,
                                TranslatorInterface $translator,
                                private SerializerInterface $serializer
    )
    {
        $this->flashy = $flashy;
        $this->translator = $translator;
    }
    #[Route('/', name: 'app_admin_location_city_index', methods: ['GET'])]
    public function index(CityRepository $cityRepository): Response
    {
        return $this->render('admin/location/city/index.html.twig', [
            'cities' => $cityRepository->findAll(),
        ]);
    }

    #[Route('/getCities', name: 'app_admin_location_cities', methods: ['GET', 'POST'])]
    public function getCities(CityRepository $cityRepository): Response
    {
        $cities = $cityRepository->findBy(['active' => true]);

        $c = [];
        foreach ($cities as $city) {
            $c[$city->getId()] = $city->getName();
        }
        return new JsonResponse($c, Response::HTTP_OK, []);
    }


    #[Route('/new', name: 'app_admin_location_city_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CityRepository $cityRepository): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cityRepository->add($city, true);
            $this->flashy->message( $this->translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
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
            $this->flashy->message( $this->translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
            return $this->redirectToRoute('app_admin_location_city_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'edit.html.twig';

        return $this->renderForm('admin/location/city/edit.html.twig', [
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
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
        }
        return $this->redirectToRoute('app_admin_location_city_index', [], Response::HTTP_SEE_OTHER);
    }
}
