<?php

namespace HeathNaylor\SinchPhp\Entities;

class Participant
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $endpoint;

    public function __construct( string $type, string $endpoint )
    {
        $this->type = $type;
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getEndpoint() : string
    {
        return $this->endpoint;
    }

}
