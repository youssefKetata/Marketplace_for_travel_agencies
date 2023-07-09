<?php

namespace App\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WhishList extends AbstractController
{
    public function index()
    {
        return $this->render('front/main/whichList.html.twig');
    }

}