<?php

namespace App\Controller;

use App\Entity\MarketSubscriptionRequest;
use App\Entity\Offer;
use App\Entity\Seller;
use App\Entity\User;
use App\Events\SellerCreatedEvent;
use App\Form\SellerType;
use App\Repository\SellerRepository;
use App\Repository\UserRepository;
use App\Service\Mailer;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Helpers;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;


#[Route('/seller') , IsGranted('ROLE_SUPER_ADMIN')]
class SellerController extends AbstractController
{
    #[Route('/', name: 'app_seller_index', methods: ['GET'])]
    public function index(SellerRepository $sellerRepository): Response
    {
        return $this->render('seller/index.html.twig', [
            'sellers' => $sellerRepository->findAll(),
        ]);
    }

    #[Route('/new/{idM?null}', name: 'app_seller_new', methods: ['GET', 'POST'])]
    public function new(Request $request,
                        SellerRepository $sellerRepository,
                        UserRepository $userRepository,
                        Helpers $helpers,
                        EventDispatcherInterface $dispatcher,
                        UserPasswordHasherInterface $passwordHasher,
                        MarketSubscriptionRequest $idM,
                        EntityManagerInterface $entityManager


    ): Response
    {

        if(!is_null($idM) && strcmp($idM->getStatus(),"validated")==0){
            return $this->redirectToRoute('app_market_subscription_request_index');
        }
        $user = new User();
        $seller = new Seller();
        $seller->setUser($user);
        //set default values
        $marketSubscriptionRequest=$idM;
        $password = $helpers->generateRandomPassword();
        dump($password);
        $user->setEmail($marketSubscriptionRequest->getEmail());
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setDisplayName($marketSubscriptionRequest->getName());
        $name = str_replace(' ', '_', $marketSubscriptionRequest->getName());
        $user->setUsername($name);
        $user->setRoles((array)'ROLE_SELLER');
        $user->setActive(true);
        $user->setIsVerified(true);
        $seller->setName($marketSubscriptionRequest->getName());
        $seller->setWebsite($marketSubscriptionRequest->getWebsite());
        $seller->setAddress($marketSubscriptionRequest->getAddress());
        $seller->setCity($marketSubscriptionRequest->getCity());
//            if seller has an api
            //$seller->setApi();

        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
//            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
//            if($user !=null){
//
//            }
            try {
                $marketSubscriptionRequest->setStatus('validated');
                $userRepository->add($user, true);
                $sellerRepository->save($seller, true);
                dd($password);
            }catch (UniqueConstraintViolationException $e){

            }
            return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('seller/'.$template, [
            'seller' => $seller,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}', name: 'app_seller_show', methods: ['GET'])]
    public function show(Seller $seller): Response
    {
        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_seller_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
    {
        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sellerRepository->save($seller, true);

            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller/edit.html.twig', [
            'seller' => $seller,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_seller_delete', methods: ['POST'])]
    public function delete(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seller->getId(), $request->request->get('_token'))) {
            $sellerRepository->remove($seller, true);
        }

        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
    }


}
