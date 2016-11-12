<?php

namespace HeathNaylor\SinchPhp\Tests\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use HeathNaylor\SinchPhp\Entities\Message;
use HeathNaylor\SinchPhp\Entities\Participant;
use HeathNaylor\SinchPhp\Services\MessagingService;
use HeathNaylor\SinchPhp\Tests\BaseTest;

class MessagingServiceTest extends BaseTest {
    public function setUp(  )
    {
        
    }

    public function testSendingSmsMessage(  )
    {
        $messagingService = new MessagingService($this->getMockHttpClient());
        $response = $messagingService->send("Testing", 1111111111, "Test");

        $to = new Participant('number', 1111111111);
        $from = new Participant('number', "Test");
        $expectedMessage = new Message(123, Message::OUTGOING, "Testing", $to, $from);
        $this->assertEquals($expectedMessage, $response);
        $this->assertEquals(Message::OUTGOING, $response->getStatus());
        $this->assertEquals("1111111111", $response->getRecipient()->getEndpoint());
        $this->assertEquals("number", $response->getRecipient()->getType());
    }

    public function testCheckingSuccessfulSmsMessageStatus(  )
    {
        $messageService = new MessagingService($this->getMockHttpClient());
        $messageService->send("Testing", 1, "Test");
        $response = $messageService->checkStatus(123);

        $expectedMessage = new Message(123, "Successful");
        $this->assertEquals($expectedMessage, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isPending());
    }

    public function testCheckingPendingSmsMessageStatus(  )
    {
        $messageService = new MessagingService($this->getMockHttpClient());
        $messageService->send("Testing", 1, "Test");
        $messageService->checkStatus(123);
        $response = $messageService->checkStatus(123);

        $expectedMessage = new Message(123, "Pending");
        $this->assertEquals($expectedMessage, $response);
        $this->assertTrue($response->isPending());
        $this->assertFalse($response->isSuccessful());
        $this->assertEquals("Pending", $response->getStatus());
        $this->assertEquals(123, $response->getId());
    }

    public function testRecieveSmsMessage(  )
    {
        $messageService = new MessagingService($this->getMockHttpClient());
        /** @var Message $message */
        $message = $messageService->receive( $this->getMockReceiveMessage());

        $this->assertInstanceOf(Message::class, $message);
        $this->assertNull($message->getId());
        $this->assertEquals("Test", $message->getBody());
        $this->assertEquals("1111111111", $message->getRecipient()->getEndpoint());
        $this->assertEquals("2222222222", $message->getSender()->getEndpoint());
    }

    private function getMockHttpClient(  ) : Client
    {
        $mock = new MockHandler(
            [
                new Response(
                    200, [ ], '{"messageId":123}'
                ),
                new Response(
                    200, [ ], '{"status":"Successful"}'
                ),
                new Response(
                    200, [ ], '{"status":"Pending"}'
                )
            ]
        );

        $handler = HandlerStack::create($mock);

        return new Client(['handler' => $handler]);
    }

    private function getMockReceiveMessage(  ) : string
    {
        $payload = [
            "event"     => "incomingSms",
            "to"        => [
                "type"     => "number",
                "endpoint" => "1111111111",
            ],
            "from"      => [
                "type"     => "number",
                "endpoint" => "2222222222",
            ],
            "message"   => "Test",
            "timestamp" => "2014-12-01T12:00:00Z",
            "version"   => "1",
        ];
        
        return json_encode($payload);
    }
}
