<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/wishList', name: 'app_front_')]
class WishList extends AbstractController
{
    #[Route('/', name: 'wishList')]
    public function index(): Response
    {
        return $this->render('front/main/whichList.html.twig');
    }

}