<?php

namespace App\Controller\Front;


use App\Entity\MarketSubscriptionRequest;
use App\Form\MarketSubscriptionRequestType;
use App\Repository\MarketSubscriptionRequestRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MainController extends AbstractController
{
    public function __construct(
        private Security $security,
        private RequestStack $requestStack)
    {
    }

    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        $session = $this->requestStack->getSession();


        if($this->isGranted('ROLE_ADMIN')){
            if($this->isGranted('ROLE_SUPER_ADMIN')) {
                $auth_agencies = [];// $this->agencyRepository->findAll();
            }
            else { // ROLE_ADMIN
                $auth_agencies = [];
            }
            $session->set('auth_agencies' , $auth_agencies);
            return $this->redirectToRoute('app_admin_dashboard');
        }

        elseif($this->isGranted('ROLE_SELLER')){
            return $this->redirectToRoute('app_seller_side_dashboard');
        }

        //if user

        /*if($user) {
            $roles = $user->getRoles();
            //dd($user);
            switch ($roles[0]){
                case "ROLE_SUPER_ADMIN": // 3T User
              //      dd('here');
                case "ROLE_ADMIN": // Agency Administrator or Agency Head (Chef)


                case "ROLE_B2B":
                case "ROLE_B2B_SUB":
                case "ROLE_B2CORP":
                case "ROLE_B2CORP_ADH":
                case "ROLE_B2C":
                return $this->render('front/main/index.html.twig', [
                    'controller_name' => $user->getUserIdentifier(),
                ]);
            }
        }*/
        return $this->render('front/main/index.html.twig');
    }

    #[Route('/aboutUs', name: 'aboutUs')]
    public function aboutUs(): Response
    {
        return $this->render('front/main/aboutUs.html.twig');

    }

    #[Route('/contact', name: 'contact')]
    public function contact(): Response
    {
        return $this->render('front/main/contact.html.twig');

    }

    #[Route('/subscription', name: 'subscription')]
    public function subscription(Request                             $request,
                                 MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository,

    ): Response
    {

        $marketSubscriptionRequest = new MarketSubscriptionRequest();
        $form = $this->createForm(MarketSubscriptionRequestType::class, $marketSubscriptionRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $marketSubscriptionRequestRepository->save($marketSubscriptionRequest, true);
                return $this->render('front/main/requestSubmitted.html.twig', [
                    'marketSubscriptionRequest' => $marketSubscriptionRequest]);

            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
                return $this->RedirectToRoute('app_seller_side_subscription');

            }
        }
        return $this->renderForm('front/main/subscription.html.twig', [
            'market_subscription_request' => $marketSubscriptionRequest,
            'form' => $form
        ]);

    }
}
