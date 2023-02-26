<?php

namespace App\Controller\Back;

use App\Entity\User;
use App\Form\EmployeeType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;


#[Route('/employee')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        $employees = $userRepository->findUsersByRole('ROLE_EMPLOYEE');
        return $this->render('back/employee/index.html.twig', [
            'employees' => $employees
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository): Response
    {
        $employee = new User();
        $employee->setRoles(["ROLE_EMPLOYEE"]);
        $employee->setIsVerified(true);
        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($employee, true);

            return $this->redirectToRoute('admin_app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/employee/new.html.twig', [
            'user' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $employee, UserRepository $userRepository): Response
    {

        $form = $this->createForm(EmployeeType::class, $employee);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->save($employee, true);

            return $this->redirectToRoute('admin_app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back/employee/edit.html.twig', [
            'user' => $employee,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $employee, UserRepository $userRepository): Response
    {
        try {
            if ($this->isCsrfTokenValid('delete'.$employee->getId(), $request->request->get('_token'))) {
                $userRepository->remove($employee, true);
            }
        } catch (\Exception $e ) {
            $this->addFlash('danger', $e->getMessage());
        }

        return $this->redirectToRoute('admin_app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
