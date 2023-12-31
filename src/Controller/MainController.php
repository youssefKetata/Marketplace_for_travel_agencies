<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/change-locale/{locale}', name: 'change_locale')]
    public function changeLocale($locale, Request $request): Response
    {

       // echo 'test' . $locale;exit();
        // on stocke la langue demandée dans la session
        $request->getSession()->set('_locale', $locale);
       // $request->setLocale($locale);
        // on revient sur la page précédente
        return $this->redirect($request->headers->get('referer'));
    }
}
