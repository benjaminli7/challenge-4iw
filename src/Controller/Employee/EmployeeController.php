<?php

namespace App\Controller\Employee;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;



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

        $order->setStatus($data['status']);

        $orderRepository->save($order , true);


        return new JsonResponse(['message' => 'Order status updated'], Response::HTTP_OK);
    }


}
