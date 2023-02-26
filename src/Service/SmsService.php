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
                $messageBody = sprintf('Votre commande #%d est en cours de préparation.', $order->getId());
                break;
            case 'TO_PICK_UP':
                $messageBody = sprintf('Votre commande #%d est prête à étre récupérée!', $order->getId());
                break;
            case 'DONE':
                $messageBody = sprintf('Votre commande #%d a bien été délivrée. Bon appétit!', $order->getId());
                break;
            default:
                $messageBody = sprintf('Votre commande #%d a été mise à jour à %s.', $order->getId(), $status);
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
