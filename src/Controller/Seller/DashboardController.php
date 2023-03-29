<?php

namespace App\Controller\Seller;

use App\Entity\MarketSubscriptionRequest;
use App\Entity\Offer;
use App\Entity\Seller;
use App\Entity\SellerOffer;
use App\Form\MarketSubscriptionRequestType;
use App\Form\SellerProfileType;
use App\Repository\MarketSubscriptionRequestRepository;
use App\Repository\MenuItemSellerRepository;
use App\Repository\OfferRepository;
use App\Repository\SellerOfferRepository;
use App\Repository\SellerRepository;
use App\Service\Helpers;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use DoctrineExtensions\Query\Mysql\Date;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\NotBlank;

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
            'form' => $form
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
        return $this->render('seller_side/dashboardPartial/dashboard.html.twig', [
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
    #[Route('/offers', name: 'offer' ), isGranted('ROLE_SELLER')]
    public function offer(offerRepository $offerRepository, SellerRepository $sellerRepository): Response
    {
        $user = $this->security->getUser();
        $seller = $sellerRepository->findOneBy(['user' => $user]);
        $offer = $offerRepository->findAll();
        return $this->render('seller_side/offer.html.twig',[
            'offers' => $offer,
            'seller' => $seller
        ]);

    }

    #[Route('/addToCard/{offer}', name: 'addToCard', methods: ['GET', 'POST']), isGranted('ROLE_SELLER')]
    public function addToCard(Offer $offer, SellerRepository $sellerRepository ): Response
    {
        if(!$this->getUser())
            return $this->redirectToRoute('app_seller_side_login');


        $session = $this->requestStack->getSession();
        $user = $this->getUser();
        $seller = $sellerRepository->findOneBy([
            'user' => $user
        ]);

        //check if the user have this offer already
        foreach($seller->getSellerOffers() as $sellerOffer){
            if($sellerOffer->getOffer()===$offer){
                $this->addFlash('error', 'You already have this offer');
                return $this->redirectToRoute('app_seller_side_offer');
            }
        }

        if($session->has('selectedOffers')){
            //verify if seller already add the offer to the card
            $selectedOffers = $session->get('selectedOffers');
            foreach($selectedOffers as $selectedOffer){
                if ($selectedOffer==$offer->getId()){
                    $this->addFlash('error','You already added this offer to the Card');
                    return $this->redirectToRoute('app_seller_side_offer');
                }
            }
        }else{
            $selectedOffers = [];
        }

        $selectedOffers[] = $offer->getId();
        $session->set('selectedOffers', $selectedOffers);
        $this->addFlash('success', 'offer added to your card');
        //trigger sql query 'eager' to get all productTypes
        return $this->redirectToRoute('app_seller_side_offer');
    }

    #[Route('/removeFromCard/{id}', name: 'removeFromCard' )]
    public function removeFromCard(Offer $offer): Response
    {
        $session = $this->requestStack->getSession();
        $selectedOffers = $session->get('selectedOffers');
        //offer should be removed form session and the variable selectedOffers
        $selectedOffers = $session->get('selectedOffers');
        $key = array_search($offer->getId(), $selectedOffers);
        if($key!== false)
            unset($selectedOffers[$key]);
        $session->set('selectedOffers', $selectedOffers);
        return $this->redirectToRoute('app_seller_side_sellerCard');
    }

    #[Route('/sellerCard', name: 'sellerCard' )]
    public function sellerCard(Request $request, OfferRepository $offerRepository): Response
    {
        $forms = [];

        $session = $this->requestStack->getSession();
        $offers = [];
        if ($session->has('selectedOffers')){
            $selectedOffers = $session->get('selectedOffers');
            foreach ($selectedOffers as $offerId){
                $offer = $offerRepository->findOneBy([
                    'id' => $offerId
                ]);
                $offers[] = $offer;

                //create form for each offer
                $defaultData = ['id' => $offer->getId()];
                $offerForm= $this->createFormBuilder($defaultData)
                    ->add('startDate_'.$offer->getId(), DateType::class,[
                        'label' => $offer->getName(),
                    ])
                    ->add('save', SubmitType::class)
                    ->getForm();
                $offerForm->handleRequest($request);
                $forms[] = $offerForm->createView();

                if ($offerForm->isSubmitted() && $offerForm->isValid()){
                    dd($offerForm->getData());
                }


                if($offerForm->isSubmitted() && $offerForm->isValid()){
                    dd('submited');

                }
            }
        }
        return $this->render('seller_side/dashboardPartial/sellerCard.html.twig',[
            'selectedOffers' => $offers,
            'forms' => $forms
        ]);
    }

    #[Route('/sellerValidOffers', name: 'sellerValidOffers' )]
    public function sellerValidOffers(SellerRepository $sellerRepository): Response
    {
        $session = $this->requestStack->getSession();
        $user = $this->getUser();
        $seller = $sellerRepository->findOneBy([
            'user' => $user
        ]);
        return $this->render('seller_side/dashboardPartial/sellerValidOffer.html.twig',[
            'seller' => $seller
        ]);
    }


    #[Route('/buyOffer', name: 'buyOffer' )]
    public function buyOffer(SellerRepository $sellerRepository,
                             SellerOfferRepository $sellerOfferRepository,
                             EntityManagerInterface $em,
                             OfferRepository $offerRepository
    ): Response
    {
        if(!$this->getUser())
            return $this->redirectToRoute('app_seller_side_login');


        $session = $this->requestStack->getSession();
        $user = $this->getUser();
        $seller = $sellerRepository->findOneBy([
            'user' => $user
        ]);

        $selectedOffers = $session->get('selectedOffers');

        try {
            foreach($selectedOffers as $selectedOffer){
                $sellerOffer = new SellerOffer();
                $sellerOffer->setSeller($seller);
                $offer = $offerRepository->findOneBy([
                    'id' => $selectedOffer
                ]);
                $sellerOffer->setOffer($offer);
                $sellerOffer->setCreationDate(new \DateTime());
                $sellerOffer->setStartDate(new \DateTime());
                $sellerOfferRepository->save($sellerOffer, true);
            }
            $session->set('selectedOffers', []);

        }catch (Exception $e){

            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('app_seller_side_sellerCard');

        }

        $session->set('selectedOffer',[]);
        $this->addFlash('success','you purchase was completed successfully');
        return $this->redirectToRoute('app_seller_side_sellerValidOffers');
    }


    #[Route('/{id}/edit', name: 'profile', methods: ['GET', 'POST']), IsGranted('ROLE_SELLER')]
    public function edit(Request $request,
                         Seller $seller,
                         SellerRepository $sellerRepository,
                         UserPasswordHasherInterface $passwordHasher,
    ): Response
    {
        $oldPassword = $seller->getUser()->getPassword();
        $form = $this->createForm(SellerProfileType::class,$seller);
        //$form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('oldPassword')->getData()!=0 and $form->get('user')->get('password')->getData()!=0){
                $testPassword = $form->get('oldPassword')->getData();
                $newPassword = $form->get('user')->get('password')->getData();
                $seller->getUser()->setPassword($oldPassword);
                if($passwordHasher->isPasswordValid($seller->getUser(), $testPassword)){
                    $seller->getUser()->setPassword(
                        $passwordHasher->hashPassword($seller->getUser(),$newPassword)
                    );
                }
            }
            $sellerRepository->save($seller, true);
            return $this->redirectToRoute('app_seller_side_profile', ['id'=>$seller->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller_side/dashboardPartial/profile.html.twig', [
            'seller' => $seller,
            'form' => $form
        ]);
    }




}
