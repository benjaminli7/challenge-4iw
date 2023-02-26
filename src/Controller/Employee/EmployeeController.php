<?php

namespace App\Controller\Employee;

use App\Repository\OrderRepository;
use App\Service\SmsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        // is granted employee
        $order = $orderRepository->find($id);
        if (!$order) {
            throw $this->createNotFoundException('Order not found');
        }
        switch ($order->getStatus()) {
            case 'ONGOING':
                $nextStatus = 'TO_PICK_UP';
                break;
            case 'TO_PICK_UP':
                $nextStatus = 'DONE';
                break;
            default:
                return new JsonResponse(['message' => 'Order status cannot be updated'], Response::HTTP_BAD_REQUEST);
        }


        $order->setStatus($nextStatus);
        $order->setEmployee(null);

        if($nextStatus === 'DONE'){
            $order->setEmployee($this->getUser());
        }

        $phoneNumber = $order->getClient()->getPhone();
        $orderRepository->save($order , true);

        $this->smsService->sendSms($order);

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