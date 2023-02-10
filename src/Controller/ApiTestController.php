<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiTestController extends AbstractController
{
    #[Route('/api/test', name: 'app_api_test')]
    public function index(): Response
    {
        return $this->render('api_test/index.html.twig', [
            'controller_name' => 'ApiTestController',
        ]);
    }
    public function testGetAllPosts(): void
    {
        $client = HttpClient::create();
        /*
         $client = HttpClient::createForBaseUri('https://example.com/', [
    // HTTP Basic authentication (there are multiple ways of configuring it)
    'auth_basic' => ['the-username'],
    'auth_basic' => ['the-username', 'the-password'],
    'auth_basic' => 'the-username:the-password',

    // HTTP Bearer authentication (also called token authentication)
    'auth_bearer' => 'the-bearer-token',

    // Microsoft NTLM authentication (there are multiple ways of configuring it)
    'auth_ntlm' => ['the-username'],
    'auth_ntlm' => ['the-username', 'the-password'],
    'auth_ntlm' => 'the-username:the-password',
]);
         */
        /*
         $client = HttpClient::create([
     'max_redirects' => 7,
]);
        $this->client = $client->withOptions([
    'base_uri' => 'https://...',
    'headers' => ['header-name' => 'header-value']
]);
         */
        $response = $client->request('GET', 'https://api.github.com/repos/symfony/symfony-docs');
        /*
         // you can add request options (or override global ones) using the 3rd argument
$response = $client->request('GET', 'https://...', [
    'headers' => [
        'Accept' => 'application/json',
    ],
]);
         */
        $statusCode = $response->getStatusCode();
        //$statusCode = 200;
        $contentType = $response->getHeaders()['content-type'][0];
        //$contentType ='application/json'
        $content = $response->getContent();
        //$content = '{"id":521583, "name":"symfony-docs", ...}'
        $content = $response->toArray();
        // $content = ['id' => 521583, 'name' => 'symfony-docs', ...]


    }
}
