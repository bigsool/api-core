<?php

namespace Core\Mailer;

use Core\Context\ApplicationContext;

class Mailer {

    /**
     * @var \Mandrill
     */
    private $mandrill;

    /**
     * @var string
     */
    private $from;

    /**
     * @param ApplicationContext $appCtx
     */
    function __construct (ApplicationContext $appCtx) {

        $config = $appCtx->getConfigManager()->getConfig();
        $APIKey = $config['mailer']['APIKey'];
        $email = $config['mailer']['from'];

        $this->mandrill = new \Mandrill($APIKey);
        $this->from = $email;

    }

    /**
     * @param String $to
     * @param String $subject
     * @param String $message
     *
     * @return Array
     */
    public function send ($to, $subject, $message) {

        $message = [
            'from_email' => $this->from,
            'to'         => [
                [
                    'email' => $to,
                ]
            ],
            'subject'    => $subject,
            'html'       => $message,
        ];

        return $this->mandrill->messages->send($message);

    }

    /**
     * @param String   $templateName
     * @param String   $to
     * @param String[] $params
     * @param String   $subject
     *
     * @return Array
     */
    public function sendFromTemplate ($templateName, $to, array $params, $subject = NULL) {

        $message = [
            'from_email'        => $this->from,
            'to'                => [
                [
                    'email' => $to,
                ]
            ],
            'global_merge_vars' => []
        ];

        if ($subject) {
            $message['subject'] = $subject;
        }

        foreach ($params as $key => $value) {
            $message['global_merge_vars'][] = ['name' => $key, 'content' => $value];
        }

        return $this->mandrill->messages->sendTemplate($templateName, [], $message);

    }

}