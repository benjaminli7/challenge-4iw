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
        $orders = $orderRepository->findBy(['status' => ['ONGOING', 'TO_PICK_UP' , 'DONE']],['date' => 'DESC']);

        $doneOrders = array_filter($orders, function($order) {
            return $order->getStatus() == 'DONE';
        });

        $firstTenDoneOrders = array_slice($doneOrders, 0, 10);

        $finalOrders = array_merge($firstTenDoneOrders, array_filter($orders, function($order) {
            return $order->getStatus() != 'DONE';
        }));

        return $this->render('employee/index.html.twig', [
            'orders' => $finalOrders,
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



        $orderRepository->save($order , true);

        if ($order->getStatus() !== $previousStatus) {
            //$this->smsService->sendSms($order);
        }

        return new JsonResponse(['message' => 'Order status updated'], Response::HTTP_OK);
    }

    // test for sms
    #[Route('/sms', name: 'sms', methods: ['GET'])]
    public function sms(OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find(6);
        dd($order->getClient());
        $this->smsService->sendSms($order);
        return $this->render('employee/index.html.twig');
    }

}
