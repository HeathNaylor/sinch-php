<?php

namespace HeathNaylor\SinchPhp\Entities;

class Message
{
    const OUTGOING = 'Outgoing';
    
    const INCOMING = 'Incoming';
    
    const UNKNOWN = "Unknown";

    const PENDING = "Pending";

    const SUCCESSFUL = "Successful";

    const FAULTED = "Faulted";

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $status;

    /**
     * @var bool
     */
    private $pending = false;

    /**
     * @var bool
     */
    private $success = false;

    /**
     * @var Participant
     */
    private $recipient;

    /**
     * @var Participant
     */
    private $sender;

    /**
     * @var string
     */
    private $body;

    /**
     * Message constructor.
     *
     * @param int              $id
     * @param string|null      $status
     * @param string           $body
     * @param Participant|null $recipient
     * @param Participant|null $sender
     */
    public function __construct( int $id = null,
                                    string $status = null,
                                    string $body = null,
                                    Participant $recipient = null,
                                    Participant $sender = null )
    {
        $this->id = $id;

        // Set up api of entity based on the status
        switch($status) {
            case self::SUCCESSFUL:
                $this->success = true;
                break;
            case self::PENDING:
                $this->pending = true;
                break;
        }

        $this->status = $status;
        $this->recipient = $recipient;
        $this->sender = $sender;
        $this->body = $body;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return bool
     */
    public function isSuccessful() : bool
    {
        return $this->success;
    }

    /**
     * @return bool
     */
    public function isPending() : bool
    {
        return $this->pending;
    }

    /**
     * @return Participant
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * @return Participant
     */
    public function getSender()
    {
        return $this->sender;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}

