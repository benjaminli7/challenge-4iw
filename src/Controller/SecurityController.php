<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Cassandra\Type\UserType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use SendinBlue\Client\Configuration as Configuration;
use SendinBlue\Client\Api\TransactionalEmailsApi as TransactionalEmailsApi;
use GuzzleHttp\Client as Client;
use SendinBlue\Client\Model\SendSmtpEmail as SendSmtpEmail;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/register', name: 'register', methods: ['GET', 'POST'])]
    public function create(Request $request, UserRepository $userRepository): Response
    {
        $user = new User();
        $user->setRoles(["ROLE_CLIENT"]);
        $form = $this->createForm(\App\Form\UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            
            try {
                // $apiInstance->sendTransacEmail($sendSmtpEmail);
                $token = bin2hex(random_bytes(32));
                $user->setToken($token);
                $data = $form->getData();
                $email = $data->getEmail();

                $credentials = Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
                $apiInstance = new TransactionalEmailsApi(new Client(),$credentials);

                $link = 'https://' . $_SERVER['HTTP_HOST'];
                $confirm_link = $link . $this->generateUrl('verify_email', ['token' => $token]);
                $sendSmtpEmail = new SendSmtpEmail([
                     'subject' => 'Valider votre mail',
                     'sender' => ['name' => 'Sendinblue', 'email' => 'contact@sendinblue.com'],
                     'replyTo' => ['name' => 'Sendinblue', 'email' => 'contact@sendinblue.com'],
                     'to' => [[ 'name' => 'Max Mustermann', 'email' => $email]],
                    //  'htmlContent' => '<html><body><h1>Hello, here is your confirm link {{params.confirm_link}}</h1></body></html>',
                     'htmlContent' => $this->renderView('emails/verification.html.twig', ['confirm_link' => $confirm_link]),
                ]);

                $apiInstance->sendTransacEmail($sendSmtpEmail);
                $user->setIsVerified(false);
                $userRepository->save($user, true);

            } catch (Exception $e) {
                echo $e->getMessage(),PHP_EOL;
            }

            return $this->redirectToRoute('client_default_index');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/verify_email", name="verify_email")
     */
    #[Route(path: '/verify_email', name: 'verify_email', methods: ['GET', 'POST'])]
    public function verify(Request $request, UserRepository $userRepository) : Response
    {

        $token = $request->query->get('token');
        $user = $userRepository->findOneBy([
            'token' => $token,
        ]);

        
        if ($user) {
            $user->setIsVerified(true);
            $user->setToken("");

            $userRepository->save($user, true);

            return $this->redirectToRoute('verify_success');
        }

        return $this->render('security/verify_fail.html.twig');
    }

    #[Route(path: '/verify_success', name: 'verify_success')]
    public function verify_success()
    {
        return $this->render('security/verify_success.html.twig');
    }


    /**
     * @Route("/forgot-password", name="forgot_password")
     */
    #[Route(path: '/forgot-password', name: 'forgot_password')]
    public function forgot(Request $request, UserRepository $userRepository) : Response
    {
        $form = $this->createForm(\App\Form\ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $email = $data->getEmail();
            $user = $userRepository->findOneBy([
                'email' => $email,
            ]);
            
            if($user) {
                try {

                    $resetToken = bin2hex(random_bytes(16));
                    $user->setResetToken($resetToken);
                    $userRepository->save($user, true);
                    $resetPasswordLink = $this->generateUrl('reset_password', [
                        'token' => $resetToken,
                    ], UrlGeneratorInterface::ABSOLUTE_URL);
    
                    $credentials = Configuration::getDefaultConfiguration()->setApiKey('api-key', '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');
                    $apiInstance = new TransactionalEmailsApi(new Client(),$credentials);
    
                    $sendSmtpEmail = new SendSmtpEmail([
                         'subject' => 'Valider votre mail',
                         'sender' => ['name' => 'Sendinblue', 'email' => 'contact@sendinblue.com'],
                         'replyTo' => ['name' => 'Sendinblue', 'email' => 'contact@sendinblue.com'],
                         'to' => [[ 'name' => 'Max Mustermann', 'email' => $email]],
                        //  'htmlContent' => '<html><body><h1>Hello, here is your confirm link {{params.confirm_link}}</h1></body></html>',
                         'htmlContent' => $this->renderView('emails/reset_password.html.twig', ['reset_password_link' => $resetPasswordLink]),
                    ]);
    
                    $apiInstance->sendTransacEmail($sendSmtpEmail);

                    $this->addFlash('success', 'A reset password link has been sent to your email.');
                    return $this->redirectToRoute('forgot_password');
                } catch (Exception $e) {
                    echo $e->getMessage(),PHP_EOL;
                }
            }
        }

        return $this->render('security/forgot_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset-password", name="reset_password")
     */
    #[Route(path: '/reset-password', name: 'reset_password')]
    public function reset(Request $request, UserRepository $userRepository) : Response
    {
        $token = $request->query->get('token');
        $user = $userRepository->findOneBy([
            'resetToken' => $token,
        ]);

        if ($user) {
            $form = $this->createForm(\App\Form\ResetPasswordType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $user->setResetToken("");

                $userRepository->save($user, true);

                return $this->redirectToRoute('reset_success');
            }

            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
        return $this->render('security/reset_fail.html.twig');
    }

    #[Route(path: '/reset-success', name: 'reset_success')]
    public function reset_success()
    {
        return $this->render('security/reset_success.html.twig');
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
