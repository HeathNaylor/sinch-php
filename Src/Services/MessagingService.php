<?php

namespace HeathNaylor\SinchPhp\Services;

use GuzzleHttp\ClientInterface;
use HeathNaylor\SinchPhp\Entities\Message;
use HeathNaylor\SinchPhp\Entities\Participant;

class MessagingService {

    const API_BASE_URL = "https://messagingapi.sinch.com";

    const API_VERSION  = 1;

    // Error codes
    const PARAMETER_VALIDATION         = 40001;

    const MISSING_PARAMETER            = 40002;

    const INVALID_REQUEST              = 40003;

    const ILLEGAL_AUTHORIZATION_HEADER = 40100;

    const INSUFFICIENT_FUNDS           = 40200;

    const FORBIDDEN_REQUEST            = 40300;

    const INVALID_AUTHORIZATION_SCHEME = 40301;

    const NO_VERIFIED_PHONE_NUMBER     = 40303;

    const FULL_SMS_NOT_ENABLED         = 40303;

    const INTERNAL_ERROR               = 500000;

    /**
     * @var string
     */
    private $apiUrl;

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * MessagingService constructor.
     *
     * @param ClientInterface $httpClient
     * @param string          $apiBaseUrl
     * @param int             $apiVersion
     */
    public function __construct( ClientInterface $httpClient, string $apiBaseUrl = self::API_BASE_URL, int $apiVersion = self::API_VERSION )
    {
        $this->httpClient = $httpClient;
        $this->apiUrl = "{$apiBaseUrl}/v{$apiVersion}";
    }

    /**
     * Send an SMS message
     *
     * @param string $message
     * @param int    $to
     * @param string $from
     *
     * @return Message
     */
    public function send( string $message, int $to, string $from ) : Message
    {
        $response = $this->httpClient->request(
            'POST',
            "{$this->apiUrl}/sms/{$to}",
            [
                "from" => $from,
                "message" => $message,
            ]
        );

        // Validate the response
        $contents = json_decode($response->getBody());
        if(!$contents->messageId) {
            // @todo Throw bad response contents exception
        }

        $to = new Participant('number', $to);
        $from = new Participant('number', $from);

        return new Message($contents->messageId, Message::OUTGOING, $message, $to, $from);
    }

    /**
     * @param int $messageId
     *
     * @return Message
     */
    public function checkStatus( int $messageId ) : Message
    {
        $response = $this->httpClient->request(
            "GET",
            "{$this->apiUrl}/sms/{$messageId}"
        );

        // Validate the response
        $contents = json_decode($response->getBody());
        if(!$contents->status) {
            // @todo Throw bad response contents exception
        }

        return new Message($messageId, $contents->status);
    }

    public function receive( string $json ) : Message
    {
        $contents = json_decode($json);
        // @todo add json guard
        $to = new Participant($contents->to->type, $contents->to->endpoint);
        $from = new Participant($contents->from->type, $contents->from->endpoint);
        return new Message(null, Message::INCOMING, $contents->message, $to, $from);
    }
}
