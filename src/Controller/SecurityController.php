<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
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
                $user = $form->getData();
                $email = $user->getEmail();
                $firstName = $user->getFirstname();
                $lastName = $user->getLastname();

                $credentials = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->getParameter('SENDINBLUE_SECRET'));
                $apiInstance = new TransactionalEmailsApi(new Client(), $credentials);

                $link = 'https://' . $_SERVER['HTTP_HOST'];
                $confirm_link = $link . $this->generateUrl('verify_email', ['token' => $token]);
                $sendSmtpEmail = new SendSmtpEmail([
                    'subject' => 'B+B - Valider votre mail',
                    'sender' => ['name' => 'B+B', 'email' => 'contact@sendinblue.com'],
                    'replyTo' => ['name' => 'B+B', 'email' => 'contact@sendinblue.com'],
                    'to' => [['name' => "$firstName $lastName", 'email' => $email]],
                    //  'htmlContent' => '<html><body><h1>Hello, here is your confirm link {{params.confirm_link}}</h1></body></html>',
                    'htmlContent' => $this->renderView('emails/verification.html.twig', ['confirm_link' => $confirm_link, 'firstName' => $firstName, 'lastName' => $lastName]),
                ]);

                $apiInstance->sendTransacEmail($sendSmtpEmail);
                $user->setIsVerified(false);
                $userRepository->save($user, true);
                $this->addFlash('success', 'Un email de confirmation vous a été envoyé');
            } catch (Exception $e) {
                echo $e->getMessage(), PHP_EOL;
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/verify_email", name="verify_email")
     */
    #[Route(path: '/verify_email', name: 'verify_email', methods: ['GET', 'POST'])]
    public function verify(Request $request, UserRepository $userRepository): Response
    {
        $token = $request->query->get('token');
        $user = $userRepository->findOneBy([
            'token' => $token,
        ]);

        if ($user) {
            $user->setIsVerified(true);
            $user->setToken("");

            $userRepository->save($user, true);
            $this->addFlash('success', 'Vous pouvez maintenant vous connecter!');
            return $this->redirectToRoute('app_login');
        }

        $this->addFlash('danger', 'Token invalide');
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/forgot-password", name="forgot_password")
     */
    #[Route(path: '/forgot-password', name: 'forgot_password')]
    public function forgot(Request $request, UserRepository $userRepository): Response
    {
        $form = $this->createForm(\App\Form\ForgotPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();
            $email = $data["email"];
            $user = $userRepository->findOneBy([
                'email' => $email,
            ]);

            if ($user) {
                try {
                    $firstName = $user->getFirstname();
                    $lastName = $user->getLastname();
                    $resetToken = bin2hex(random_bytes(16));
                    $user->setResetToken($resetToken);
                    $userRepository->save($user, true);
                    $resetPasswordLink = $this->generateUrl('reset_password', [
                        'token' => $resetToken,
                    ], UrlGeneratorInterface::ABSOLUTE_URL);

                    $credentials = Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->getParameter('SENDINBLUE_SECRET'));
                    $apiInstance = new TransactionalEmailsApi(new Client(), $credentials);

                    $sendSmtpEmail = new SendSmtpEmail([
                        'subject' => 'B+B - Réinitialisation de mot de passe',
                        'sender' => ['name' => 'B+B', 'email' => 'contact@sendinblue.com'],
                        'replyTo' => ['name' => 'B+B', 'email' => 'contact@sendinblue.com'],
                        'to' => [['name' => "$firstName $lastName", 'email' => $email]],
                        //  'htmlContent' => '<html><body><h1>Hello, here is your confirm link {{params.confirm_link}}</h1></body></html>',
                        'htmlContent' => $this->renderView('emails/reset_password.html.twig', ['reset_password_link' => $resetPasswordLink,'firstName' => $firstName, 'lastName' => $lastName]),
                    ]);

                    $apiInstance->sendTransacEmail($sendSmtpEmail);

                    $this->addFlash('success', 'Un email de réinitialisation de mot de passe vous a été envoyé.');
                    return $this->redirectToRoute('forgot_password');
                } catch (Exception $e) {
                    echo $e->getMessage(), PHP_EOL;
                }
            } else {
                $this->addFlash('danger', 'Cette adresse email n\'existe pas.');
                return $this->redirectToRoute('forgot_password');
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
    public function reset(Request $request, UserRepository $userRepository): Response
    {

        $token = $request->query->get('token');
        if ($token == null) {
            return $this->redirectToRoute('app_login');
        }
        $user = $userRepository->findOneBy([
            'resetToken' => $token,
        ]);

        if ($user) {
            $form = $this->createForm(\App\Form\ResetPasswordType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                $user->setResetToken("");

                $userRepository->save($user, true);
                $this->addFlash('success', 'Votre mot de passe a bien été modifié!');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
        return $this->redirectToRoute('app_login');
    }

    #[Route('/profile', name: 'profile', methods: ['GET', 'POST'])]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(\App\Form\UserType::class, $this->getUser());

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('client_default_index');
        }

        return $this->render('security/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
