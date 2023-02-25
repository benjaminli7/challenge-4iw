<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'app_order')]
    public function index(OrderRepository $orderRepository): Response
    {
        $ordersDone = $orderRepository->findBy(['status' => 'DONE']);
        return $this->render('back/order/index.html.twig', [
            'ordersDone' => $ordersDone,
        ]);
    }
}
