<?php

namespace App\Controller\Seller;

use App\Entity\MarketSubscriptionRequest;
use App\Form\MarketSubscriptionRequestType;
use App\Repository\MarketSubscriptionRequestRepository;
use App\Repository\MenuItemAdminRepository;
use App\Repository\MenuItemSellerRepository;
use App\Service\Helpers;
use App\Service\Mailer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/seller_side', name: 'app_seller_side_')]
class DashboardController extends AbstractController
{
    public function __construct(private readonly RequestStack             $requestStack,
                                private readonly MenuItemSellerRepository $menuItemSellerRepository,
                                private readonly Security                 $security,
                                private readonly Helpers                  $helpers
    ){}


    #[Route('', name: 'seller_side' )]
    public function welcome(): Response
    {
        return $this->render('seller_side/login.html.twig');

    }

    #[Route('/subscription', name: 'subscription' )]
    public function subscription(Request $request,
                                 MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository
    ): Response
    {
        $marketSubscriptionRequest = new MarketSubscriptionRequest();
        $form = $this->createForm(MarketSubscriptionRequestType::class, $marketSubscriptionRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marketSubscriptionRequestRepository->save($marketSubscriptionRequest, true);
            return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('seller_side/subscription.html.twig', [
            'market_subscription_request' => $marketSubscriptionRequest,
            'form' => $form,
        ]);

    }
    #[Route('/dashboard', name: 'dashboard' ) , IsGranted('ROLE_SELLER')]
    public function index(): Response
    {
        $user = $this->security->getUser();
        $session = $this->requestStack->getSession();

        //if(!$session->has('menu')){ // Uncomment to get menu from session if exists.
            if($this->isGranted('ROLE_SELLER')) {
                $menu_object = $this->menuItemSellerRepository->findBy([], ['displayOrder' => 'ASC']);
                $menu = $this->helpers->convert_ObjectArray_to_2DArray($menu_object);
            }else{ // ROLE_ADMIN
                $menu = $this->menuItemSellerRepository->find_innerJoin();
            }
            $menu_as_tree = $this->helpers->buildTree($menu);
            if(array_key_exists('ADMIN', $menu_as_tree))
                $session->set('menu' , $menu_as_tree['ADMIN']['children']);
        //}
        return $this->render('seller_side/base_seller.html.twig', [
            'controller_name' => 'DashboardController',
            'menu' => $menu
        ]);
    }
}
