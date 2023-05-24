<?php

namespace App\Controller\Admin;

use App\Repository\ApiProductClickRepository;
use App\Repository\ApiProductRepository;
use App\Repository\SellerRepository;
use http\Client\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StatisticsController extends AbstractController
{
    #[Route('/admin/statistics', name: 'app_admin_statistics' ) , IsGranted('ROLE_ADMIN')]
    public function index(ApiProductRepository $apiProductRepository,
                          SellerRepository $sellerRepository,
                          ApiProductClickRepository $apiProductClickRepository
    ): \Symfony\Component\HttpFoundation\Response
    {
        $sellers = $sellerRepository->findAll();
        $array = [[]];
        foreach ($sellers as $seller) {
            $apiProduct = $apiProductRepository->findBy(['api' => $seller->getApi()]);
            if ($apiProduct){
                $array[] = [
                    0 => $seller,
                    1 => $apiProduct,
                    2 => $apiProductClickRepository->findBy(['apiProduct' => $apiProduct]),
                ];
            }
        }


        return $this->render('admin/statistics/index.html.twig', [
            'sellers' => $sellers,
            'array' => $array,
        ]);
    }

}