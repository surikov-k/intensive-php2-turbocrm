<?php


namespace common\services;


use common\services\containers\GmailMessage;

class GmailClient implements MailClient
{

    /**
     * @var AuthClient
     */
    protected $authClient;

    protected $serviceGmail;

    protected $user = 'me';

    public function __construct(AuthClient $authClient)
    {
        $this->authClient = $authClient;
        $this->serviceGmail = new \Google_Service_Gmail($authClient->getVendorClient());
    }

    /**
     * @inheritDoc
     */
    public function getMessages($max_count = 10)
    {
        $result = [];
        $response = $this->serviceGmail->users_messages->listUsersMessages($this->user, ['maxResults' => $max_count]);

        /**
         * @var $messages \Google_Service_Gmail_Message[]
         */
        $messages = $response->getMessages();

        foreach ($messages as $message) {
            $msg = $this->serviceGmail->users_messages->get($this->user, $message->getId());

            $result[] = new GmailMessage($msg);
        }

        return $result;
    }
}
