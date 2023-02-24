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

        $status = $order->getStatus();

        // Set the message based on the status
        switch ($status) {
            case 'ONGOING':
                $messageBody = sprintf('Your order #%d is now being prepared.', $order->getId());
                break;
            case 'TO_PICK_UP':
                $messageBody = sprintf('Your order #%d is ready for pick up!', $order->getId());
                break;
            case 'DONE':
                $messageBody = sprintf('Your order #%d has been delivered. Enjoy your meal!', $order->getId());
                break;
            default:
                $messageBody = sprintf('Your order #%d has been updated to %s.', $order->getId(), $status);
                break;
        }
        // Send the SMS message
        $message = $this->client->messages->create(
            $recipientNumber,
            [
                'from' => $this->phoneNumber,
                'body' => $messageBody,
            ]
        );
    }
}
