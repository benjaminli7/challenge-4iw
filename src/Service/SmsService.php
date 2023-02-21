<?php

namespace App\Service;

use App\Entity\Order;
use Twilio\Rest\Client;

class SmsService
{
    private $client;
    private $phoneNumber;

    public function __construct(string $ssid, string $token, string $phone_number)
    {
        $this->client = new Client($ssid, $token);
        $this->phoneNumber = $phone_number;
    }
    public function sendSms(Order $order): void
    {

        $twilioNumber = getenv('TWILIO_PHONE_NUMBER'); // get Twilio number from .env file

        $recipientNumber = $order->getOrderPhoneNumber();
        // make it this format +33761598898 actual format is 0761598898
        $recipientNumber = '+33' . substr($recipientNumber, 1);

        // Create a new Twilio client with your account SID and auth token
        // Send the SMS message
        $message = $this->client->messages->create(
            $recipientNumber,
            [
                'from' => $this->phoneNumber,
                'body' => sprintf('Your order #%d has been updated to %s.', $order->getId(), $order->getStatus()),
            ]
        );
    }

}
