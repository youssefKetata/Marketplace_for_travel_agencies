<?php
namespace App\Service;

use Mailjet\Client;
use \Mailjet\Resources;


class Mailjet {

    private $api_key= '73f8755d86bd6aba7cddb3846a4fa4b6';
    private $secret_api= '2460e3d1ae138a4a798ddcd8b77a562d';
    private $Template_ID= '4648324';

//    public function __construct($api_key, $secret_api) {
//        $this->mj = new \Mailjet\Client($api_key, $secret_api, true, ['version' => 'v3.1']);
//    }

    public function sendEmail($api_key, $secret_api ): bool|array
    {
        $mj=new Client($this->api_key,$this->secret_api,true,['version' => 'v3.1']);

        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => 'yusufketata5@gmail.com',
                        'Name' => 'youssef'
                    ],
                    'To' => [
                        [
                            'Email' => 'yusufketata5@gmail.com',
                            'Name' => 'youssef'
                        ]
                    ],
                    'Subject' => "Greetings from Mailjet.",
                    'TextPart' => "My first Mailjet email",
                    'HTMLPart' => "<h3>Dear passenger 1, welcome to <a href='https://www.mailjet.com/'>Mailjet</a>!</h3><br />May the delivery force be with you!",
                    'CustomID' => "AppGettingStartedTest"
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response->success() ? $response->getData() : false;
    }

}

