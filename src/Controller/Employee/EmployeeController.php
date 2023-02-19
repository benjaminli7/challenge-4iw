<?php

namespace App\Controller\Employee;

use App\Entity\Order;
use App\Entity\User;
use App\Form\EmployeeType;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


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

}
