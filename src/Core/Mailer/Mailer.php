<?php

namespace Core\Mailer;

use Core\Context\ApplicationContext;

class Mailer {

    private $mandrill;
    private $from;

    function __construct (ApplicationContext $appCtx) {

        $config = $appCtx->getConfigManager()->getConfig();
        $APIKey = $config['mailer']['APITestKey'];
        $email = $config['mailer']['from'];

        $this->mandrill = new \Mandrill($APIKey);
        $this->from = $email;

    }

    public function send ($to, $subject, $message) {

        $message = [
            'from_email' => $this->from,
            'to' => [
                [
                    'email' => $to,
                ]
            ],
            'subject' => $subject,
            'text' => $message,
        ];

        $this->mandrill->messages->send($message);

    }

    public function sendFromTemplate ($templateName, $to, $subject, $params) {

        $message = [
            'from_email' => $this->from,
            'to' => [
                [
                    'email' => $to,
                ]
            ],
            'subject' => $subject,
            'global_merge_vars' => []
        ];

        foreach ($params as $key => $value) {
            $message['global_merge_vars'][] = ['name' => $key, 'content' => $value];
        }

        $this->mandrill->messages->sendTemplate($templateName, [], $message);

    }

}