<?php

namespace App\Controller;

use App\Entity\Traveler;
use App\Form\TravelerType;
use App\Repository\TravelerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/traveler')]
class TravelerController extends AbstractController
{
    #[Route('/', name: 'app_traveler_index', methods: ['GET'])]
    public function index(TravelerRepository $travelerRepository , UserRepository $userRepository ,EntityManagerInterface $entityManager,): Response
    {
        $nonSellers = $userRepository->findNonSellers();
        return $this->render('traveler/index.html.twig', [
            'travelers' => $travelerRepository->findAll(),
//            'nonSellers' => $nonSellers,
        ]);
    }

    #[Route('/new', name: 'app_traveler_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TravelerRepository $travelerRepository): Response
    {
        $traveler = new Traveler();
        $form = $this->createForm(TravelerType::class, $traveler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $travelerRepository->save($traveler, true);

            return $this->redirectToRoute('app_traveler_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('traveler/new.html.twig', [
            'traveler' => $traveler,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_traveler_show', methods: ['GET'])]
    public function show(Traveler $traveler): Response
    {
        return $this->render('traveler/show.html.twig', [
            'traveler' => $traveler,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_traveler_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Traveler $traveler, TravelerRepository $travelerRepository): Response
    {
        $form = $this->createForm(TravelerType::class, $traveler);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $travelerRepository->save($traveler, true);

            return $this->redirectToRoute('app_traveler_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('traveler/edit.html.twig', [
            'traveler' => $traveler,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_traveler_delete', methods: ['POST'])]
    public function delete(Request $request, Traveler $traveler, TravelerRepository $travelerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$traveler->getId(), $request->request->get('_token'))) {
            $travelerRepository->remove($traveler, true);
        }

        return $this->redirectToRoute('app_traveler_index', [], Response::HTTP_SEE_OTHER);
    }

}
