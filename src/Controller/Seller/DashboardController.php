<?php

namespace App\Controller\Seller;

use App\Entity\MarketSubscriptionRequest;
use App\Form\MarketSubscriptionRequestType;
use App\Repository\MarketSubscriptionRequestRepository;
use App\Repository\MenuItemAdminRepository;
use App\Repository\MenuItemSellerRepository;
use App\Repository\SellerRepository;
use App\Repository\UserRepository;
use App\Service\Helpers;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/seller_side', name: 'app_seller_side_')]
class DashboardController extends AbstractController
{
    public function __construct(private readonly RequestStack             $requestStack,
                                private readonly MenuItemSellerRepository $menuItemSellerRepository,
                                private readonly Security                 $security,
                                private readonly Helpers                  $helpers
    ){}


    #[Route('', name: 'login' )]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();
        // $view = "shared/login/login_".$type.".html.twig";
        return $this->render('seller_side/login.html.twig', [
            'controller_name' => 'LoginController',
            'last_username' => $lastUsername,
            'error' => $error
        ]);

    }

    #[Route('/home', name: 'home' )]
    public function home(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('seller_side/home.html.twig');
    }

    #[Route('/subscription', name: 'subscription' )]
    public function subscription(Request $request,
                                 MarketSubscriptionRequestRepository $marketSubscriptionRequestRepository,

    ): Response
    {

        $marketSubscriptionRequest = new MarketSubscriptionRequest();
        $form = $this->createForm(MarketSubscriptionRequestType::class, $marketSubscriptionRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $marketSubscriptionRequestRepository->save($marketSubscriptionRequest, true);
                return $this->render('seller_side/requestSubmitted.html.twig',[
                    'marketSubscriptionRequest'=>$marketSubscriptionRequest]);

            }catch (UniqueConstraintViolationException $e){
                $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
                return $this->RedirectToRoute('app_seller_side_subscription');

            }
        }
        return $this->renderForm('seller_side/subscription.html.twig', [
            'market_subscription_request' => $marketSubscriptionRequest,
            'form' => $form,
        ]);

    }
    #[Route('/dashboard', name: 'dashboard' ) , IsGranted('ROLE_SELLER')]
    public function index(SellerRepository $sellerRepository): Response
    {
        $user = $this->security->getUser();
        $email = $user->getUserIdentifier();
        $seller = $sellerRepository->findOneBy([
            'user' => $user
        ]);
        $session = $this->requestStack->getSession();

        if(!$session->has('menu')){ // Uncomment to get menu from session if exists.
            if($this->isGranted('ROLE_SELLER')) {
                $menu_object = $this->menuItemSellerRepository->findBy([], ['displayOrder' => 'ASC']);
                $menu = $this->helpers->convert_ObjectArray_to_2DArray($menu_object);
            }else{ // ROLE_ADMIN
                $menu = $this->menuItemSellerRepository->find_innerJoin();
            }
            $menu_as_tree = $this->helpers->buildTree($menu);
            if(array_key_exists('ADMIN', $menu_as_tree))
                $session->set('menu' , $menu_as_tree['ADMIN']['children']);
        }
        else{
            $menu = $session->get('menu');
        }
        return $this->render('seller_side/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'menu' => $menu,
            'seller' => $seller
        ]);
    }
    #[Route('/aboutUs', name: 'aboutUs' )]
    public function aboutUs(): Response
    {
        return $this->render('seller_side/partial/aboutUs.html.twig');

    }
    #[Route('/contact', name: 'contact' )]
    public function contact(): Response
    {
        return $this->render('seller_side/partial/contact.html.twig');

    }


}
