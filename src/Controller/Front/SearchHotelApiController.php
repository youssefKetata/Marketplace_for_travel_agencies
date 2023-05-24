<?php

namespace App\Controller\Front;

use App\Entity\ApiProduct;
use App\Entity\ApiProductClick;
use App\Repository\ApiProductClickRepository;
use App\Repository\ApiProductRepository;
use App\Repository\ProductTypeRepository;
use App\Repository\SellerRepository;
use Doctrine\ORM\NonUniqueResultException;
use GuzzleHttp\Promise\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use GuzzleHttp\Client as GuzzleClient;
use Throwable;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use stdClass;

#[Route('/search', name: 'app_search_')]
class SearchHotelApiController extends AbstractController
{
    public function __construct(private readonly SellerRepository      $sellerRepository,
                                private readonly ProductTypeRepository $productTypeRepository,
                                private readonly ApiProductClickRepository $apiProductClickRepository,
                                private readonly ApiProductRepository $apiProductRepository,
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('search_hotel_api/index.html.twig', [
            'controller_name' => 'SearchHotelApiController',
        ]);
    }

    private function verifyProductType($productTypeName)
    {
        $productType = $this->productTypeRepository->findOneBy([
            'name' => $productTypeName
        ]);
        if ($productType === null) {
            //check if the product type is similar to any product type in the database
            foreach ($this->productTypeRepository->findAll() as $pt) {
                $percentSimilar = similar_text(strtoupper($productTypeName), strtoupper($pt->getName()));
                if ($percentSimilar >= 3) {
                    $productType = $pt;
                    break;
                } else {
                    $productType = null;
                }
            }
        }
        return $productType;
    }


    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws ClientExceptionInterface
     * @throws Throwable
     */
    #[Route('/getFormData', name: 'getFormData', methods: ['POST', 'GET'])]
    public function getFormData(Request $request): JsonResponse
    {
        $factory = new RateLimiterFactory([
            'id' => 'login',
            'policy' => 'token_bucket',
            'limit' => 1,
            'rate' => ['interval' => '15 minutes'],
        ], new InMemoryStorage());

        $limiter = $factory->create();

        // blocks until 1 token is free to use for this process
        $limiter->reserve(1)->wait();
        // ... execute the code

        // only claims 1 token if it's free at this moment (useful if you plan to skip this process)
//        if ($limiter->consume(1)->isAccepted()) {
//            // ... execute the code
//        }


        $data = $request->getContent();
        //convert the data to array
        $formData = json_decode($data, true);

//        foreach ($formData as $key => $value) {
//            if ($value == '' || $value ==null) {
//                return $this->json([
//                    'redirectUrl' => $this->generateUrl('app_main'),
//                ]);
//                break;
//            }
//        }

        $productType = $this->verifyProductType($formData['productType']);//if there is any mistype in product type, it will be corrected
        $result = $this->getValidSellersOffer($productType);
        $sellerInfo = $result['sellerInfo'];//info about valid sellers
        $validSellers = $result['validSellers'];//sellers with valid offers
        $availability = $this->hotelAvailability($validSellers, $formData, $productType, $sellerInfo);
        $response = [
            'sellerInfo' => $sellerInfo,
            'availability' => $availability
        ];
        return $this->json($response);

    }

    private function getValidSellersOffer($productType): ?array
    {
        //get all sellers that have valid offers with specific product type
        $sellers = $this->sellerRepository->findAll();
        $sellerInfo = [];
        $validSellers = [];
        foreach ($sellers as $seller) {
            //seller that have active offers
            if (count($seller->getValidSellerOffers()) > 0) {
                foreach ($seller->getValidSellerOffers() as $sellerOffer) {
                    //check if the seller has an offer with the product type
                    if ($sellerOffer->getOffer()->getOfferProductType($productType) != null) {
                        $sellerInfo[$seller->getId()] = [
                            'name' => $seller->getName(),
                            'logo' => $seller->getBrochureFilename(),
                            'chosenOfferProductType' => $sellerOffer->getOffer()->getOfferProductType($productType)->getMaxItems(),
                        ];
                        $validSellers[$seller->getId()] = $seller;
                    }
                }
            }
        }
        return [
            'sellerInfo' => $sellerInfo,
            'validSellers' => $validSellers,
        ];
    }


    /**
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws Throwable
     */
    #[Route('/hotelAvailability', name: 'hotelAvailability', methods: ['POST', 'GET'])]
    public function hotelAvailability($sellers,
                                      $formData,
                                      $productType,
                                      $sellersInfo
    ): array
    {
        //formData is data send by the user
        //apiCredentials array of SellerId and there corresponding api

        $httpClient = new GuzzleClient();
        $httpClient = new $httpClient([
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            'http_errors' => false,
            'connect_timeout' => 10,
            'timeout' => 30,
            'max_connections' => 10,
        ]);

        $requests = [];
        foreach ($sellers as $sellerId => $seller) {
            $maxItems = $sellersInfo[$sellerId]['chosenOfferProductType'];
            $baseUrl = $seller->getApi()->getBaseUrl() . '?product=' . $productType->getName();
            $requests[] = $httpClient->postAsync($baseUrl, [
                'headers' => [
                    'api-key' => $seller->getApi()->getApiKeyValue(),
                    'Login' => $seller->getApi()->getLogin(),
                    'password' => $seller->getApi()->getPassword(),
                ],
                'json' => [
                    'checkIn' => $formData['checkIn'],
                    'checkOut' => $formData['checkOut'],
                    'city' => $formData['location'],
                    'hotelName' => '',
                    'boards' => [],
                    'rating' => [],
                    'occupancies' => $formData['rooms'],
                    'channel' => 'b2c',
                    'language' => 'fr_FR',
                    'onlyAvailableHotels' => false,
                    'marketId' => '1',
                    'customerId' => '7',
                    'backend' => 0,
                    'filtreSearch' => [],
                ],
            ])->then(function ($response) use ($maxItems, $sellerId) {
                $responseArray = [];
                if ($response->getStatusCode() === 200) {
                    $result = json_decode($response->getBody(), true);
                    //guzzle will return response even if the api return error
                    if (isset($result['response'])){
                        $responseArray = array_slice($result['response'], 0, (int)$maxItems);
                    }

                }
                return [$sellerId, $responseArray];
            }, function ($reason) use ($sellerId) {
                return [$sellerId, []];
            });
        }

        $results = Utils::unwrap($requests);
        //if the seller doesn't return any result he will have empty array
        $resultObject = $this->generateHotelObjects($results);
        $popularity = $this->popularity($resultObject);
//        dd($popularity);
        return $popularity;

    }

    function generateHotelObjects(array $responseData): array
    {
        $hotels = [];

        // iterate over each seller's response array
        foreach ($responseData as $sellerData) {
            $sellerId = $sellerData[0];
            $sellerHotels = $sellerData[1];

            // iterate over each hotel in the seller's response
            foreach ($sellerHotels as $hotelData) {
                //generate random random between 1 and 2
                $randomFloat = 1 + (mt_rand() / mt_getrandmax()) * (1.5 - 1);

                $hotelData['lowPrice'] = $hotelData['lowPrice']*$randomFloat;
                $uniqueHotelId = strtoupper($hotelData['hotelName']);//unique id for each hotel

                // if the hotel is not in the $hotels array yet, add it
                if (!isset($hotels[$uniqueHotelId])) {
                    $hotels[$uniqueHotelId] = new stdClass();
                    $hotels[$uniqueHotelId]->hotelId = $uniqueHotelId;
                    $hotels[$uniqueHotelId]->sellers = [];
                }

                // add the seller to the hotel's sellers array
                $sellerObject = new stdClass();
                $sellerObject->sellerId = $sellerId;
                $sellerObject->hotelId = $hotelData['hotelId'];
                $sellerObject->hotelName = $hotelData['hotelName'];
                $sellerObject->category = $hotelData['category'];
                $sellerObject->location = $hotelData['location'];
                $sellerObject->Picture = $hotelData['Picture'];
                $sellerObject->PromoText = $hotelData['PromoText'];
                $sellerObject->lowPrice = $hotelData['lowPrice'];
                $sellerObject->currency =  $hotelData['currency'];
                $sellerObject->detailsLink = $hotelData['detailsLink'];
                $sellerObject->Agency = $hotelData['Agency'];

                $hotels[$uniqueHotelId]->sellers[] = $sellerObject;
            }
        }

        // convert the $hotels array to a sequential array
        return array_values($hotels);
    }



    /**
     * @throws NonUniqueResultException
     */
    #[Route('/searchClick/{data}', name: 'searchClick', methods: ['POST', 'GET'])]
    public function searchClick ($data): JsonResponse
    {
        $decodeData = json_decode($data, true);
        //check if there is any missed data (some users don't have location)
        if (!isset($decodeData['sellerId']) || !isset($decodeData['hotelId']) ||
            !isset($decodeData['hotelName']) || !isset($decodeData['productType']))
            return new JsonResponse(['error' => 'missing data'], Response::HTTP_BAD_REQUEST);


        //prepare data
        $seller = $this->sellerRepository->findOneBy(['id' => $decodeData['sellerId']]);
        $hotelId = $decodeData['hotelId'];
        $hotelName = strtoupper($decodeData['hotelName']) ;
        $productType = $this->productTypeRepository->findOneBy(['name' => $decodeData['productType']]);
        $userIP = $decodeData['userIP'] ?? null;
        $userContinent = $decodeData['userContinent'] ?? null;
        $userCountry = $decodeData['userCountry'] ?? null;
        $userCity = $decodeData['userCity'] ?? null;
        $currentDate = new \DateTime();

        //check if this seller has the apiProduct already
        //apiProduct can be identified by the api and the idProductFromApi
        $apiProduct = $this->apiProductRepository->findOneByTwoFields(['api' => $seller->getApi()], ['idProductFromApi' => $hotelId]);
        if (!$apiProduct){
            //create new instance of apiProduct
            $apiProduct = new ApiProduct();
            $apiProduct->setName($hotelName);
            $apiProduct->setProductType($productType);
            $apiProduct->setApi($seller->getApi());
            $apiProduct->setIdProductFromApi($hotelId);
            try {
                $this->apiProductRepository->save($apiProduct, true);
            }catch (\Exception){
                return new JsonResponse(['error' => 'error while saving apiProduct'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        }

        $apiProductClick = new ApiProductClick();
        $apiProductClick->setDate($currentDate);
        $apiProductClick->setIpTraveler($userIP);
        $location = ($userContinent ?? '') . ($userCountry ? ' - ' . $userCountry : '') . ($userCity ? ' - ' . $userCity : '');
        $location = empty($location) ? null : $location;
        $apiProductClick->setIpLocation($location);
//        $apiProductClick->setTraveler($traveler);
        $apiProductClick->setApiProduct($apiProduct);
        try{
            $this->apiProductClickRepository->save($apiProductClick, true);
        }catch (\Exception){
            return new JsonResponse(['error' => 'error while saving apiProductClick'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse('success', Response::HTTP_OK, []);
    }

    public function popularity($resultObject): array{
        //iterate throw each hotel and add a popularity score wish is the sum of the click of each seller
//        dd($resultObject);
        foreach ($resultObject as $hotel) {
            $popularity = 0;
            $apiProduct = $this->apiProductRepository->findOneBy(['name' => strtoupper($hotel->hotelId)]);
            if ($apiProduct){
                $currentMonth = date('m');
                $currentYear = date('Y');

                $startDate = new \DateTime("{$currentYear}-{$currentMonth}-01");
                $endDate = new \DateTime("{$currentYear}-{$currentMonth}-31");
                $apiProductClicks = $this->apiProductClickRepository->createQueryBuilder('apc')
                    ->andWhere('apc.apiProduct = :apiProduct')
                    ->andWhere('apc.date >= :startDate')
                    ->andWhere('apc.date <= :endDate')
                    ->setParameter('apiProduct', $apiProduct)
                    ->setParameter('startDate', $startDate)
                    ->setParameter('endDate', $endDate)
                    ->getQuery()
                    ->getResult();

                foreach ($apiProductClicks as $apiProductClick){
                    $popularity += 1;
                }
            }
            $hotel->popularity = $popularity;
        }
        return $resultObject;




    }
}
