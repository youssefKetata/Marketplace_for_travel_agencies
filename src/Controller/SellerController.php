<?php

namespace App\Controller;

use App\Entity\MarketSubscriptionRequest;
use App\Entity\Seller;
use App\Entity\User;
use App\Form\SellerType;
use App\Repository\SellerRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Helpers;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

#[Route('/seller') , IsGranted('ROLE_SUPER_ADMIN')]
class SellerController extends AbstractController
{

    protected $flashy;
    protected $translator;

    public function __construct(FlashyNotifier $flashy, TranslatorInterface $translator)
    {
        $this->flashy = $flashy;
        $this->translator = $translator;
    }

    #[Route('/', name: 'app_seller_index', methods: ['GET'])]
    public function index(SellerRepository $sellerRepository, Request $request): Response
    {
        $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';
        return $this->render('seller/'.$template, [
            'sellers' => $sellerRepository->findAll(),
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/sendMail', name: 'app_seller_sendMail', methods: ['GET', 'POST'])]
    public function sendMail(MailerInterface $mailer): string
    {
//        require 'vendor/autoload.php';
        $email = new \SendGrid\Mail\Mail();
        $email->setFrom("yusufketata5@gmail.com", "Example User");
        $email->setSubject("Sending with SendGrid is Fun");
        $email->addTo("yusufketata5@gmail.com", "Example User");
        $email->addContent("text/plain", "and easy to do anywhere, even with PHP");
        $email->addContent(
            "text/html", "<strong>and easy to do anywhere, even with PHP</strong>"
        );
        $sendgrid = new \SendGrid('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');
        try {
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
            return 'Email sent successfully';
        } catch (Exception $e) {
            return 'Caught exception: '. $e->getMessage() ."\n";
        }
//        $email = (new Email())
//            ->from('yusufketata5@gmail.com')
//            ->to('yusufketata5@gmail.com')
//            ->subject('Hello from Symfony!')
//            ->text('This is a test email sent from Symfony.')
//            ->html('<p>See Twig integration for better HTML integration!</p>');
//
//        $sendMail = $mailer->send($email);
//        //check if the mail was sent successfully
//        dd($sendMail);
//        if ($sendMail) {
//            //if yes do something
//            return 'Email sent successfully';
//        } else {
//            //if no do something else
//            return ('Something went wrong!');
//        }

    }

    #[Route('/new/{idM?null}', name: 'app_seller_new', methods: ['GET', 'POST'])]
    public function new(Request $request,
                        SellerRepository $sellerRepository,
                        UserRepository $userRepository,
                        Helpers $helpers,
                        EventDispatcherInterface $dispatcher,
                        UserPasswordHasherInterface $passwordHasher,
                        MarketSubscriptionRequest $idM,
                        EntityManagerInterface $entityManager


    ): Response
    {

        if(!is_null($idM) && strcmp($idM->getStatus(),"validated")==0){
            return $this->redirectToRoute('app_market_subscription_request_index');
        }
        $user = new User();
        $seller = new Seller();
        $seller->setUser($user);
        //set default values
        $marketSubscriptionRequest=$idM;
        $password = $helpers->generateRandomPassword();
        $user->setEmail($marketSubscriptionRequest->getEmail());
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $password
            )
        );
        $user->setDisplayName($marketSubscriptionRequest->getName());
        $name = str_replace(' ', '_', $marketSubscriptionRequest->getName());
        $user->setUsername($name);
        $user->setRoles((array)'ROLE_SELLER');
        $user->setActive(true);
        $user->setIsVerified(true);
        $seller->setName($marketSubscriptionRequest->getName());
        $seller->setWebsite($marketSubscriptionRequest->getWebsite());
        $seller->setAddress($marketSubscriptionRequest->getAddress());
        $seller->setCity($marketSubscriptionRequest->getCity());
        $seller->setBrochureFilename(null);
//            if seller has an api
            //$seller->setApi();

        $form = $this->createForm(SellerType::class, $seller);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $testUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if ($testUser) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }

            try {
                $marketSubscriptionRequest->setStatus('validated');
                $userRepository->add($user, true);
                $sellerRepository->save($seller, true);
                //send mail using service/mailer
            }catch (UniqueConstraintViolationException $e){
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
            $this->flashy->message( $this->translator->trans('Message.Standard.SuccessSave'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }

            return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);

            //return $this->redirectToRoute('app_market_subscription_request_index', [], Response::HTTP_SEE_OTHER);
        }

        $template = $request->isXmlHttpRequest() ? '_form.html.twig' : 'new.html.twig';

        return $this->renderForm('seller/'.$template, [
            'seller' => $seller,
            'form' => $form,
        ],
            new Response(
                null,
                $form->isSubmitted() && !$form->isValid() ? 422 : 200,
            ));
    }

    #[Route('/{id}', name: 'app_seller_show', methods: ['GET'])]
    public function show(Seller $seller): Response
    {
        return $this->render('seller/show.html.twig', [
            'seller' => $seller,
        ]);
    }

//    #[Route('/{id}/edit', name: 'app_seller_edit', methods: ['GET', 'POST']), IsGranted('ROLE_SELLER')]
//    public function edit(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
//    {
//        $form = $this->createForm(SellerType::class, $seller);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $sellerRepository->save($seller, true);
//
//            return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm('seller/edit.html.twig', [
//            'seller' => $seller,
//            'form' => $form,
//        ]);
//    }

    #[Route('/{id}', name: 'app_seller_delete', methods: ['POST'])]
    public function delete(Request $request, Seller $seller, SellerRepository $sellerRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seller->getId(), $request->request->get('_token'))) {
            $sellerRepository->remove($seller, true);
            $this->flashy->message( $this->translator->trans('Message.Facility.Delete'));
            if ($request->isXmlHttpRequest()) {
                $html = $this->render('@MercurySeriesFlashy/flashy.html.twig');
                return new Response($html->getContent(), Response::HTTP_OK);
            }
        }

        return $this->redirectToRoute('app_seller_index', [], Response::HTTP_SEE_OTHER);
    }


}
