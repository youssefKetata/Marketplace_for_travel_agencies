<?php

namespace App\Controller\Seller;

use App\Entity\Api;
use App\Entity\Offer;
use App\Entity\SellerOffer;
use App\Form\ApiType;
use App\Form\SellerProfileType;
use App\Repository\ApiProductClickRepository;
use App\Repository\ApiProductRepository;
use App\Repository\ApiRepository;
use App\Repository\MenuItemSellerRepository;
use App\Repository\OfferRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\SellerOfferRepository;
use App\Repository\SellerRepository;
use App\Service\Helpers;
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Exception;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/seller_side', name: 'app_seller_side_')]
class DashboardController extends AbstractController
{
    private FlashyNotifier $flashy;

    public function __construct(private readonly RequestStack             $requestStack,
                                private readonly MenuItemSellerRepository $menuItemSellerRepository,
                                private readonly Security                 $security,
                                private readonly Helpers                  $helpers,
                                FlashyNotifier                            $flashy
    )
    {
        $this->flashy = $flashy;
    }

    #[Route('/my_offers', name: 'my_offers'), IsGranted('ROLE_SELLER')]
    public function myOffers(SellerRepository $sellerRepository, Request $request): Response
    {
        $user = $this->security->getUser();
        $email = $user->getUserIdentifier();
        $seller = $sellerRepository->findOneBy([
            'user' => $user
        ]);

        // Pagination logic
        $page = $request->query->getInt('page', 1); // Get the current page from the request query parameters
        $offersPerPage = 10;
        $totalOffers = $seller->getSellerOffers()->count();
        $totalPages = ceil($totalOffers / $offersPerPage);
        $start = ($page - 1) * $offersPerPage;
        $end = $start + $offersPerPage;
        $slicedOffers = $seller->getSellerOffers()->slice($start, $offersPerPage);

        return $this->render('seller_side/dashboardPartial/myOffers.html.twig', [
            'seller' => $seller,
            'offers' => $slicedOffers,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'offersPerPage' => $offersPerPage,
        ]);
    }


    #[Route('/dashboard', name: 'dashboard'), IsGranted('ROLE_SELLER')]
    public function dashboard(SellerRepository          $sellerRepository,
                              Request                   $request,
                              ApiProductRepository      $apiProductRepository,
                              ApiProductClickRepository $apiProductClickRepository,

    ): Response
    {
        $user = $this->security->getUser();
        $email = $user->getUserIdentifier();
        $seller = $sellerRepository->findOneBy([
            'user' => $user
        ]);
        $session = $this->requestStack->getSession();

        if (!$session->has('menu')) { // Uncomment to get menu from session if exists.
            if ($this->isGranted('ROLE_SELLER')) {
                $menu_object = $this->menuItemSellerRepository->findBy([], ['displayOrder' => 'ASC']);
                $menu = $this->helpers->convert_ObjectArray_to_2DArray($menu_object);
            } else { // ROLE_ADMIN
                $menu = $this->menuItemSellerRepository->find_innerJoin();
            }
            $menu_as_tree = $this->helpers->buildTree($menu);
            if (array_key_exists('ADMIN', $menu_as_tree))
                $session->set('menu', $menu_as_tree['ADMIN']['children']);
        } else {
            $menu = $session->get('menu');
        }

        if ($seller->getApi() === null) {
            $this->addFlash('error', 'Please wait until the admin set your api');
        }
        //active sellerOffers
        $sellerOffers = $seller->getSellerOffers();
        $activeSellerOffers = [];
        foreach ($sellerOffers as $sellerOffer) {
            if ($sellerOffer->getStatus() === 'active') {
                $activeSellerOffers[] = $sellerOffer;
            }
        }

        //clicks count
        $apiProducts = $apiProductRepository->findBy([
            'api' => $seller->getApi()
        ]);

        $yesterdayClicks = 0;
        $lastMonthClicks = 0;
        $today = new DateTime();
        $yesterday = new DateTime();
        $yesterday->modify('-1 day');

        $currentYear = date('Y');
        $lastMonth = date('m') - 1;
        if ($lastMonth < 1) {
            $lastMonth = 12;  // Set to December
            $currentYear--;  // Decrement the year
        }


        foreach ($apiProducts as $apiProduct) {
            $b = $apiProductClickRepository->findByApiProductAndDate($apiProduct, $yesterday);
            $c = $apiProductClickRepository->findByApiProductAndMonth($apiProduct, $lastMonth, $currentYear);
            $yesterdayClicks += count($b);
            $lastMonthClicks += count($c);
        }

        // Pagination logic
        $page = $request->query->getInt('page', 1);
        $offersPerPage = 10;
        $totalOffers = count($activeSellerOffers);
        $totalPages = ceil($totalOffers / $offersPerPage);
        $start = ($page - 1) * $offersPerPage;


        return $this->render('seller_side/dashboardPartial/dashboard.html.twig', [
            'menu' => $menu,
            'seller' => $seller,
            'activeSellerOffers' => $activeSellerOffers,
            'yesterdayClicks' => $yesterdayClicks,
            'lastMonthClicks' => $lastMonthClicks,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'offersPerPage' => $offersPerPage,
        ]);
    }


    #[Route('/offers', name: 'offer'), isGranted('ROLE_SELLER')]
    public function offer(offerRepository       $offerRepository,
                          SellerRepository      $sellerRepository,
                          ProductTypeRepository $productTypeRepository
    ): Response
    {
        $user = $this->security->getUser();
        $seller = $sellerRepository->findOneBy(['user' => $user]);
        $offers = $offerRepository->findAll();
        $offer = $offerRepository->findOneBy(['id' => '11139']);
        $productTypes = $productTypeRepository->findAll();
        return $this->render('seller_side/offersList.html.twig', [
            'offers' => $offers,
            'seller' => $seller,
            'productTypes' => $productTypes,
        ]);

    }

    #[Route('/addToCard/{offer}', name: 'addToCard', methods: ['GET', 'POST']), isGranted('ROLE_SELLER')]
    public function addToCard(Offer $offer, SellerRepository $sellerRepository,
    ): Response
    {
        if (!$this->getUser())
            return $this->redirectToRoute('app_main');


        $session = $this->requestStack->getSession();
        $seller = $sellerRepository->findOneBy([
            'user' => $this->getUser()
        ]);

        //check if the user have this offer already adn the offer is not 'expired'

        foreach ($seller->getSellerOffers() as $sellerOffer) {
            if ($sellerOffer->getOffer() === $offer && $sellerOffer->getStatus() !== 'expired') {
                $this->addFlash('error', 'You already have this offer');
                $this->flashy->error('You already have this offer', 'https://symfony.com/doc/current/controller.html');
                return $this->redirectToRoute('app_seller_side_offer');
            }
        }

        if ($session->has('selectedOffers')) {
            //verify if seller already add the offer to the card
            $selectedOffers = $session->get('selectedOffers');
            foreach ($selectedOffers as $selectedOffer) {
                if ($selectedOffer == $offer->getId()) {
                    $this->addFlash('error', 'You already added this offer to the Card');
                    return $this->redirectToRoute('app_seller_side_offer');
                }
            }
        } else {
            $selectedOffers = [];
        }

        $selectedOffers[] = $offer->getId();
        $session->set('selectedOffers', $selectedOffers);
        //add a flash message that tell the user that the offer was added to the card
        $this->addFlash('success', "You added $offer to your card");
        //trigger sql query 'eager' to get all productTypes
        return $this->redirectToRoute('app_seller_side_offer');
    }

    #[Route('/removeFromCard/{id}', name: 'removeFromCard')]
    public function removeFromCard(Offer $offer): Response
    {

        $session = $this->requestStack->getSession();
        $selectedOffers = $session->get('selectedOffers');
        $key = array_search($offer->getId(), $selectedOffers);
        if ($key !== false) {
            unset($selectedOffers[$key]);
            //re-index the array numerically starting from 0
            $selectedOffers = array_values($selectedOffers);
        }
        $session->set('selectedOffers', $selectedOffers);
        return $this->redirectToRoute('app_seller_side_sellerCard');
    }

    #[Route('/sellerCard', name: 'sellerCard')]
    public function sellerCard(Request $request, OfferRepository $offerRepository): Response
    {
        $session = $this->requestStack->getSession();
        $offers = [];
        $forms = [];

        if ($session->has('selectedOffers')) {
            $selectedOffers = $session->get('selectedOffers');
            foreach ($selectedOffers as $offerId) {
                $offer = $offerRepository->findOneBy([
                    'id' => $offerId
                ]);
                $offers[] = $offer;

                //create startDate form for each offer
                $defaultData = ['id' => $offer->getId()];

                $offerForm = $this->createFormBuilder($defaultData)
                    ->add($offer->getId(), DateType::class, [
                        'label' => 'Start date',
                        'data' => new DateTime(),
                        'required' => true,
                        'widget' => 'single_text',
                        'constraints' => [
                            new Assert\GreaterThan([
                                'value' => new DateTime(),
                                'message' => 'The date cannot be earlier than today.',
                            ])
                        ]
                    ])
                    ->remove('_token')
                    ->getForm();
                $offerForm->handleRequest($request);
                $forms[] = $offerForm->createView();
            }
        }
        return $this->render('seller_side/sellerCard.html.twig', [
            'selectedOffers' => $offers,
            'forms' => $forms
        ]);
    }

//    #[Route('/pay', name: 'pay')]
//    public function pay (){
//
//        return $this->render('seller_side/pay.html.twig');
//    }
//


    #[Route('/sellerBoughtOffers', name: 'sellerBoughtOffers')]
    public function sellerBoughtOffers(SellerRepository $sellerRepository): Response
    {
        $seller = $sellerRepository->findOneBy([
            'user' => $this->getUser()
        ]);

        return $this->render('seller_side/sellerBoughtOffer.html.twig', [
            'seller' => $seller,
        ]);
    }


    #[Route('/buyOffer', name: 'buyOffer'), IsGranted('ROLE_SELLER')]
    public function buyOffer(SellerRepository       $sellerRepository,
                             SellerOfferRepository  $sellerOfferRepository,
                             EntityManagerInterface $em,
                             OfferRepository        $offerRepository,
                             Request                $request
    ): JsonResponse
    {
        if (!$this->getUser()) {
            $newPageUrl = $this->generateUrl('app_login');
            return new JsonResponse(['newPageUrl' => $newPageUrl]);
        }

        $session = $this->requestStack->getSession();
        $seller = $sellerRepository->findOneBy([
            'user' => $this->getUser()
        ]);

        //get the start dates
        $formData = $request->request->all();
        if ($formData == null)
            $this->redirectToRoute('app_seller_side_sellerCard');
        // return error if date is not valid
        try {
            $selectedDates = [];
            foreach ($formData["form"] as $id => $date)
                if (is_int($id)) {
                    $specificDate = new DateTime();
                    $dateTime = DateTime::createFromFormat('Y-m-d', $date);
                    if ($dateTime > $specificDate) {
                        $selectedDates[$id] = $dateTime;
                    } else {
                        $this->addFlash('error', 'Invalid date selected: ' . $date);
                        $newPageUrl = $this->generateUrl('app_seller_side_sellerCard');
                        return new JsonResponse(['newPageUrl' => $newPageUrl]);
                    }

                }

        } catch (Exception $e) {
            $this->addFlash('error', 'error occurred: ' . $e->getMessage());
            $newPageUrl = $this->generateUrl('app_seller_side_sellerCard');
            return new JsonResponse(['newPageUrl' => $newPageUrl]);
        }

        $selectedOffers = $session->get('selectedOffers');
        $boughtOffers = [];

        try {
            for ($i = 0; $i < count($selectedOffers); $i++) {
                $selectedOffer = $selectedOffers[$i];
                $offer = $offerRepository->findOneBy([
                    'id' => $selectedOffer
                ]);
                //verify if the seller has the offer and if the offer is not expired
                $renew = false;
                foreach ($seller->getSellerOffers() as $sellerOffer) {
                    if ($sellerOffer->getOffer()->getId() === $offer->getId() && $sellerOffer->getStatus() !=='expired') {
                        $this->addFlash('error', 'You have this offer: ' . $offer->getName() . '. Remove it form the card');
                        $newPageUrl = $this->generateUrl('app_seller_side_sellerCard');
                        return new JsonResponse(['newPageUrl' => $newPageUrl]);
                    }elseif ($sellerOffer->getOffer()->getId() === $offer->getId() && $sellerOffer->getStatus() ==='expired'){
                        $renew = true;
                    }
                }
                //crate a new seller offer if it is not renew and update the existing one if it is renew
                $sellerOffer = null;
                if ($renew){
                    $sellerOffer = $sellerOfferRepository->findOneBy([
                        'seller' => $seller,
                        'offer' => $offer
                    ]);
                }else{
                    $sellerOffer = new SellerOffer();
                }
//                $sellerOffer = new SellerOffer();
                $sellerOffer->setSeller($seller);
                $sellerOffer->setOffer($offer);
                $sellerOffer->setCreationDate(new DateTime());
                $date = $selectedDates[$offer->getId()];
                $sellerOffer->setStartDate($date);
                $sellerOfferRepository->save($sellerOffer, true);
                $boughtOffers[] = $sellerOffer;
            }
        } catch (Exception $e) {
            $this->addFlash('error', 'error occurred2: ' . $e);
            $newPageUrl = $this->generateUrl('app_seller_side_sellerCard');
            return new JsonResponse(['newPageUrl' => $newPageUrl]);
        }
        $session->set('selectedOffers', []);
        $newPageUrl = $this->generateUrl('app_seller_side_sellerBoughtOffers', [
            'boughtOffers' => $boughtOffers
        ]);
        return new JsonResponse(['newPageUrl' => $newPageUrl]);
    }


    #[Route('/statistics', name: 'statistics'), IsGranted('ROLE_SELLER')]
    public function statistics(SellerRepository $sellerRepository, ApiProductRepository $apiProductRepository): Response
    {
        $seller = $sellerRepository->findOneBy([
            'user' => $this->getUser()
        ]);

        $apiProducts = $apiProductRepository->findBy(['api' => $seller->getApi()]);
        $clicksCountHotels = [];
//        $apiProductLocation = [[]];
        //count number of clicks for each product
        foreach ($apiProducts as $apiProduct) {
            if ($apiProduct->getProductType()->getName() === 'hotels') {
                $clicksCountHotels[$apiProduct->getId()] = 0;
//                $apiProductLocation[$apiProduct->getName()] = [];
                foreach ($apiProduct->getApiProductClicks() as $click) {
                    $clicksCountHotels[$apiProduct->getId()]++;
//                    if(!array_key_exists($click->getIpLocation(), $apiProductLocation[$apiProduct->getName()]))
//                        $apiProductLocation[$apiProduct->getName()][$click->getIpLocation()] = 1;
//                    else
//                        $apiProductLocation[$apiProduct->getName()][$click->getIpLocation()]++;
                }
            }
            //other products
            //if ProductType === 'flights'
        }
        // Sort the array based on the number of clicks (in descending order)
        $keys = array_keys($clicksCountHotels);
        $values = array_values($clicksCountHotels);
        // Sort the values array while maintaining key-value association
        usort($values, function ($a, $b) {
            return $b - $a; // Sort in ascending order, use $b - $a for descending order
        });

        // Combine the sorted values with the original keys
        $sortedArray = array_combine($keys, $values);


        return $this->render('seller_side/dashboardPartial/statistics.html.twig', [
            'seller' => $seller,
            'apiProducts' => $apiProducts,
            'clicksCount' => $sortedArray,
//            'apiProductLocation' => $apiProductLocation,
        ]);
    }


    #[Route('/profile', name: 'profile', methods: ['GET', 'POST']), IsGranted('ROLE_SELLER')]
    public function profile(Request                     $request,
                            SellerRepository            $sellerRepository,
                            UserPasswordHasherInterface $passwordHasher,
                            SluggerInterface            $slugger,
    ): Response
    {
        $seller = $sellerRepository->findOneBy([
            'user' => $this->getUser()
        ]);

        $oldPassword = $seller->getUser()->getPassword();

        $form = $this->createForm(SellerProfileType::class, $seller);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $brochureFile = $form->get('brochure')->getData();
            if ($brochureFile) {
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $brochureFile->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                    $this->addFlash('success', 'Your logo has been updated successfully');
                } catch (FileException $e) {
                    $this->addFlash('error', 'Error occurred while updating your logo');
                }

                // updates the 'brochureFilename' property to store the image file name
                // instead of its contents
                $seller->setBrochureFilename($newFilename);
            }

            if ($form->get('oldPassword')->getData() != 0 and $form->get('newPassword')->getData() != 0) {
                //dd($oldPassword, $form->get('oldPassword')->getData(), $form->get('user')->get('password')->getData());
                $testPassword = $form->get('oldPassword')->getData();
                $newPassword = $form->get('newPassword')->getData();
                $seller->getUser()->setPassword($oldPassword);
                if ($passwordHasher->isPasswordValid($seller->getUser(), $testPassword)) {
                    $seller->getUser()->setPassword(
                        $passwordHasher->hashPassword($seller->getUser(), $newPassword)
                    );
                    $this->addFlash('success', 'Your password has been updated successfully');
                } else {
                    $form->get('oldPassword')->addError(new FormError('invalid password.'));
                    return $this->renderForm('seller_side/dashboardPartial/profile.html.twig', [
                        'form' => $form,
                        'seller' => $seller
                    ]);
                }
            }
            try {
                $sellerRepository->save($seller, true);
                $this->addFlash('success', 'Your profile has been updated successfully');
            } catch (\PHPUnit\Exception $e) {
                $form->addError(new FormError('error'));
                return $this->render('profile/index.html.twig');
            }

            return $this->redirectToRoute('app_seller_side_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller_side/dashboardPartial/profile.html.twig', [
            'form' => $form,
            'seller' => $seller
        ]);
    }

    #[Route('/apiForm', name: 'api_form', methods: ['GET', 'POST']), IsGranted('ROLE_SELLER')]
    public function apiForm(Request          $request,
                            SellerRepository $sellerRepository,
                            ApiRepository    $apiRepository,
    ): Response
    {
        $seller = $sellerRepository->findOneBy([
            'user' => $this->getUser()
        ]);
        $seller->getApi() ? $api = $seller->getApi() : $api = new Api();
        $form = $this->createForm(ApiType::class, $api);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $apiRepository->save($api, true);
            $seller->setApi($api);
            $sellerRepository->save($seller, true);
            //$api->setSeller($seller);
            return $this->redirectToRoute('app_seller_side_api_form', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seller_side/dashboardPartial/api.html.twig', [
            'api' => $api,
            'form' => $form,
            'seller' => $seller,
        ]);
    }
}
