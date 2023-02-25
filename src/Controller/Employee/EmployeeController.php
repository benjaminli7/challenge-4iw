<?php

namespace App\Controller\Employee;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Service\SmsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class EmployeeController extends AbstractController
{
    private SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    #[Route('/', name: 'default_index', methods: ['GET'])]
    public function orders(OrderRepository $orderRepository): Response
    {
        $ordersOngoing = $orderRepository->findBy(['status' => 'ONGOING'],['date' => 'DESC']);
        $ordersToPickUp = $orderRepository->findBy(['status' => 'TO_PICK_UP'], ['date' => 'DESC']);
        $ordersDoneLastTen = $orderRepository->findBy(['status' => 'DONE'], ['date' => 'DESC'], 10);

        return $this->render('employee/index.html.twig', [
            'ordersOngoing' => $ordersOngoing,
            'ordersToPickUp' => $ordersToPickUp,
            'ordersDoneLastTen' => $ordersDoneLastTen,
        ]);
    }





    #[Route('/order/{id}/status', name: 'update_order_status', methods: ['POST'])]
    public function updateOrderStatus(Request $request, OrderRepository $orderRepository, int $id): Response
    {
        $order = $orderRepository->find($id);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }

        $status = $request->getContent();

        $data = json_decode($status, true);

        if (!in_array($data['status'], ['ONGOING', 'TO_PICK_UP', 'DONE'])) {
            return new JsonResponse(['message' => 'Invalid order status'], Response::HTTP_BAD_REQUEST);
        }


        $previousStatus = $order->getStatus();
        $order->setStatus($data['status']);

        // get order user phone number
        $phoneNumber = $order->getClient()->getPhone();


        $orderRepository->save($order , true);

        if ($order->getStatus() !== $previousStatus) {

            $this->smsService->sendSms($order);
        }

        return new JsonResponse(['message' => 'Order status updated'], Response::HTTP_OK);
    }

    #[Route('/sms', name: 'sms', methods: ['GET'])]
    public function sms(OrderRepository $orderRepository): Void
    {
        $order = $orderRepository->find(2);
        $phoneNumber = $order->getClient()->getPhone();
        $this->smsService->sendSms($order);
    }

}
