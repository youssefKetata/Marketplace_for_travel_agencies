<?php

namespace App\Controller\Seller;

use App\Entity\Api;
use App\Entity\MarketSubscriptionRequest;
use App\Entity\Offer;
use App\Entity\SellerOffer;
use App\Form\ApiType;
use App\Form\MarketSubscriptionRequestType;
use App\Form\SellerProfileType;
use App\Repository\ApiRepository;
use App\Repository\MarketSubscriptionRequestRepository;
use App\Repository\MenuItemSellerRepository;
use App\Repository\OfferRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\SellerOfferRepository;
use App\Repository\SellerRepository;
use App\Service\Helpers;
use DateTime;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\Validator\Constraints\Length;

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


    #[Route('/dashboard', name: 'dashboard'), IsGranted('ROLE_SELLER')]
    public function dashboard(SellerRepository $sellerRepository, Request $request): Response
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
        //build pagination system
        return $this->render('seller_side/dashboardPartial/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'menu' => $menu,
            'seller' => $seller,
        ]);
    }


    #[Route('/offers', name: 'offer'), isGranted('ROLE_SELLER')]
    public function offer(offerRepository $offerRepository,
                          SellerRepository $sellerRepository,
                          ProductTypeRepository $productTypeRepository
    ): Response
    {
        $user = $this->security->getUser();
        $seller = $sellerRepository->findOneBy(['user' => $user]);
        $offers = $offerRepository->findAll();
        $productTypes = $productTypeRepository->findAll();
        $top_offers = array_splice($offers, 0, 3);
        return $this->render('seller_side/offer.html.twig', [
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

        //check if the user have this offer already
        foreach ($seller->getSellerOffers() as $sellerOffer) {
            if ($sellerOffer->getOffer() === $offer) {
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
                        'help' => 'The products will start appear in marketplace at this date',
                        'data' => new DateTime(),
                        'label' => "Starting date",
                        'required' => true,
                        'widget' => 'single_text',
                        'constraints' => [
                            new Length(['min' => 3]),
                            new Assert\GreaterThan([
                                'value' => new DateTime(),
                                'message' => 'The date cannot be earlier than today.',
                            ])
                        ]
                    ])
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

    #[Route('/sellerBoughtOffers', name: 'sellerBoughtOffers')]
    public function sellerBoughtOffers(SellerRepository $sellerRepository): Response
    {
        $seller = $sellerRepository->findOneBy([
            'user' => $this->getUser()
        ]);

        return $this->render('seller_side/sellerValidOffer.html.twig', [
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
        // simplify fromData
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
                //verify if the seller has the offer
                foreach ($seller->getSellerOffers() as $sellerOffer) {
                    if ($sellerOffer->getOffer()->getId() === $offer->getId()) {
                        $this->addFlash('error', 'You have this offer: ' . $offer->getName() . '. Remove it form the card');
                        $newPageUrl = $this->generateUrl('app_seller_side_sellerCard');
                        return new JsonResponse(['newPageUrl' => $newPageUrl]);
                    }
                }

                $sellerOffer = new SellerOffer();
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
                dump('dump');
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    dd($e->getMessage());
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
