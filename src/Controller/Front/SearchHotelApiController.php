<?php

namespace App\Controller\Front;

use App\Repository\ProductTypeRepository;
use App\Repository\SellerRepository;
use Doctrine\DBAL\Driver\Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
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

#[Route('/search', name: 'app_search_')]
class SearchHotelApiController extends AbstractController
{
    public function __construct(private readonly SellerRepository      $sellerRepository,
                                private readonly ProductTypeRepository $productTypeRepository,
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

        $productType = $formData['productType'];
        $productType = $this->verifyProductType($productType);
        $result = $this->getValidSellersOffer($productType);
        $sellerInfo = $result['sellerInfo'];
        $apiCredentials = $result['apiCredentials'];

        //send request to each seller's api
        $availability = $this->hotelAvailability($apiCredentials, $formData, $productType);


        //allow local server to send request to this api(react)
//        $response->headers->set('Access-Control-Allow-Origin', 'http://localhost:3000');
//        $response->headers->set('Access-Control-Allow-Methods', 'POST, PUT, GET, DELETE, OPTIONS');
//        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
//        $response->headers->set('Content-Type', 'application/json');
//        $response->setStatusCode(200); // Set HTTP 200 (OK) status code

        $response = [
            'sellerInfo' => $sellerInfo,
            'availability' => $availability
        ];
        return $this->json($response);

        //compare the prices and set the availability
        //return response to react
        //get response from file


//        $template = $this->renderView('search_hotel_api/index.html.twig', [
//            'data' => 'data'
//
//        ]);
//        return new $response($template);
    }

    private function getValidSellersOffer($productType): ?array
    {
        //get all sellers that have valid offers with specific product type
        $sellers = $this->sellerRepository->findAll();
        $sellerInfo = [];
        $apiCredentials = [];
        foreach ($sellers as $seller) {
            if (count($seller->getValidSellerOffers()) > 0) {
                //seller that have active offers
                foreach ($seller->getValidSellerOffers() as $sellerOffer) {
                    //check if the seller has an offer with the product type
                    if ($sellerOffer->getOffer()->getOfferProductType($productType) != null) {
                        //if seller has an offer with the product type
                        $sellerInfo[$seller->getId()] = ['name'=>$seller->getName(), 'logo'=>$seller->getBrochureFilename()];
                        $apiCredentials[$seller->getId()] = $seller->getApi();
                    }
                }
            }
        }
        return [
            'sellerInfo'=>$sellerInfo,
            'apiCredentials'=>$apiCredentials
            ];
    }

    public function transformHotelsArray(array $hotels): array
    {
        //transform the array of hotels to a new array with hotelId as key and
        //the value is another array with the sellerId as key and the value is the hotel
        $result = [];

        foreach ($hotels as $agencyId => $agencyHotels) {
            foreach ($agencyHotels as $hotel) {
                $result[$hotel['hotelId']][$agencyId] = $hotel;
            }
        }

        return $result;
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
    public function hotelAvailability($apiCredentials,
                                      $formData,
                                      $productType
    ): array
    {
        //formData is data send by the user
        //apiCredentials array of SellerId and there corresponding api

        //send wih GuzzleHttp
        $httpClient = new GuzzleClient();
        $promises = [];
        foreach ($apiCredentials as $sellerId => $api) {
            $baseUrl = $api->getBaseUrl() . '?product=' . $productType->getName();
            try {
                $promises[$sellerId] = $httpClient->requestAsync('POST', $baseUrl, [
                    'headers' => [
                        'api-key' => $api->getApiKeyValue(),
                        'Login' => $api->getLogin(),
                        'password' => $api->getPassword(),
                    ],
                    'json' => [
                        'checkIn' => $formData['checkIn'],
                        'checkOut' => $formData['checkOut'],
                        'city' => 'Hammamet,tunisie',
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
                    ]
                ]);
            }catch (\Exception $e){
                $promises[$sellerId] = [];
            }
        }

        $results = Utils::unwrap($promises);

        $responseArray = [];
        foreach ($results as $sellerId => $response) {
            $responseArray[$sellerId] = null;
            if ($response instanceof \Psr\Http\Message\ResponseInterface) {
                try {
                    //$responseArray[$sellerId] = $response->toArray()['response'];
                    $result = json_decode($response->getBody()->getContents(), true);
                    $responseArray[$sellerId] = $result['response'];
                }catch (\Exception){
                    $responseArray[$sellerId] = [];
                }
            }
        }
        return $this->transformHotelsArray($responseArray);
    }

    #[Route('/searchResults', name: 'searchResults', methods: ['POST', 'GET'])]
    public function searchResults(): Response
    {

        // Render the new twig template with the result
        return $this->render('search_hotel_api/index.html.twig',['data'=>'data']);
    }
}
