<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;

class EmployeesController extends AbstractController
{
    #[Route('/employees', name: 'employees')]
    public function index(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_MANAGER');
        $employees = $userRepository->findUsersByRole('ROLE_EMPLOYEE');
        return $this->render('back/employees/index.html.twig', [
            'employees' => $employees,
        ]);
    }
}
