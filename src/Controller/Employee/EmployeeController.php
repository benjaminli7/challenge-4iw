<?php

namespace App\Controller\Employee;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Rest\Client;

class EmployeeController extends AbstractController
{


    #[Route('/', name: 'default_index', methods: ['GET'])]
    public function orders(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findBy(['status' => ['ONGOING', 'TO_PICK_UP']]);
        return $this->render('employee/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/order/{id}/status', name: 'update_order_status', methods: ['POST'])]
    public function updateOrderStatus(Request $request, OrderRepository $orderRepository, int $id): Response
    {
        $order = $orderRepository->find($id);
        //js console.log($order);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $status = $request->getContent();

        $data = json_decode($status, true);
        // return $date for test
         //return new JsonResponse($data, Response::HTTP_OK);

        if (!in_array($data['status'], ['ONGOING', 'TO_PICK_UP', 'DONE'])) {
            return new JsonResponse(['message' => 'Invalid order status'], Response::HTTP_BAD_REQUEST);
        }
        $previousStatus = $order->getStatus();
        $order->setStatus($data['status']);

        // Send SMS if order status has changed

        $orderRepository->save($order , true);

        if ($order->getStatus() !== $previousStatus) {
            $this->sendSms($order);
        }

        return new JsonResponse(['message' => 'Order status updated'], Response::HTTP_OK);
    }

    #[Route('/test-sms', name: 'test_sms', methods: ['GET'])]
    public function sendTestSMS()
    {
        $accountSid = $this->getParameter('TWILIO_ACCOUNT_SID');
        $authToken = $this->getParameter('TWILIO_AUTH_TOKEN');
        $twilioNumber = $this->getParameter('TWILIO_PHONE_NUMBER');

        // The Twilio phone number and the recipient's phone number from your .env file

        // dd($accountSid, $authToken, $twilioNumber);

        // Replace with your own phone number to receive the test SMS
        $toPhoneNumber = '+33761598898';

        $client = new Client($accountSid, $authToken);

        $message = $client->messages->create(
            $toPhoneNumber,
            array(
                'from' => $twilioNumber,
                'body' => 'This is a test SMS message from your Twilio account!'
            )
        );

        // Log the message SID to verify that the SMS was sent successfully
        error_log('Twilio SMS SID: ' . $message->sid);

        return new Response('Test SMS sent successfully!');
    }

    private function sendSms(Order $order): void
    {
        $accountSid = $this->getParameter('TWILIO_ACCOUNT_SID');
        $authToken = $this->getParameter('TWILIO_AUTH_TOKEN');
        $twilioNumber = $this->getParameter('TWILIO_PHONE_NUMBER');
        //$recipientNumber = $order->getCustomerPhoneNumber();
        $recipientNumber = "+33761598898";

        // Create a new Twilio client with your account SID and auth token
        $client = new Client($accountSid, $authToken);

        // Send the SMS message
        $message = $client->messages->create(
            $recipientNumber,
            [
                'from' => $twilioNumber,
                'body' => sprintf('Your order #%d has been updated to %s.', $order->getId(), $order->getStatus()),
            ]
        );
    }

}
