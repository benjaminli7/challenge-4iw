<?php

namespace App\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Dompdf\Dompdf;

class PdfGenerateController extends AbstractController
{
    #[Route('/pdf/generate/orders', name: 'app_pdf_generate_orders')]
    public function generateOrdersPdf(OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findAll();

        $html = $this->renderView('back/pdf_generate/historic.html.twig', [
            'orders' => $orders,
        ]);

        $pdfGenerator = new Dompdf();
        $pdfGenerator->loadHtml($html);
        $pdfGenerator->setPaper('A4', 'portrait');
        $pdfGenerator->render();
        $pdfOutput = $pdfGenerator->output();

        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="historic_orders.pdf"');

        return $response;
    }

    #[Route('/pdf/generate/employees', name: 'app_pdf_generate_employees')]
    public function generateEmployeesPdf(UserRepository $userRepository): Response
    {
        $employees = $userRepository->findUsersByRole('ROLE_EMPLOYEE');

        $html = $this->renderView('back/pdf_generate/employees.html.twig', [
            'employees' => $employees,
        ]);

        $pdfGenerator = new Dompdf();
        $pdfGenerator->loadHtml($html);
        $pdfGenerator->setPaper('A4', 'portrait');
        $pdfGenerator->render();
        $pdfOutput = $pdfGenerator->output();

        $response = new Response($pdfOutput);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="employees.pdf"');

        return $response;
    }
}
