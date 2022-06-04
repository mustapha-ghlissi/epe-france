<?php

namespace App\Controller\Manager;

use App\Entity\User;
use App\Form\ForgotPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UserRepository;
use Carbon\Carbon;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordController extends AbstractController
{
    /**
     * @param Request $request
     * @param UserRepository $userRepository
     * @param MailerInterface $mailer
     * @return RedirectResponse|Response
     * @throws TransportExceptionInterface
     * @Route("/manager/password/forgot", name="manager_password_forgot")
     */
    public function forgot(Request $request, UserRepository $userRepository, MailerInterface $mailer)
    {
        $form = $this->createForm(ForgotPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();
            $manager = $userRepository->findOneByEmail($form->get('email')->getData());

            if(!$manager instanceof User) {
                $this->addFlash('danger','Adresse email introuvable.');
                return $this->redirectToRoute('manager_password_forgot');
            }

            $token = md5(uniqid());
            $url = $this->generateUrl('manager_password_reset', [
                'email' => $manager->getEmail(),
                'token' => $token
            ], UrlGeneratorInterface::ABSOLUTE_URL);


            $body = "Bonjour,<br/>";
            $body .= "<p>Afin de réinitialiser votre mot de passe veuillez cliquer sur le lien suivant: <p/>";
            $body .= "<a href='{$url}' target='_blank'>{$url}</a>";
            $body .= "<br/><br/>Cordialement<br/>ENE France.";

            $email = (new Email())
                ->from($this->getParameter('mailer.email'))
                ->to($manager->getEmail()) // Set email here
                ->subject("Réinitialiser votre mot de passe")
                ->html($body);
            $mailer->send($email);

            $manager->setResetToken($token)
                ->setResetTokenRequestAt(Carbon::now());
            $em->flush();
            $this->addFlash('success', 'Demande de réinisialisation de mot de passe envoyée avec succès.');
            return $this->redirectToRoute('manager_login');
        }

        return $this->render('manager/password/forgot.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/manager/password/reset/{email}/{token}", name="manager_password_reset")
     */
    public function reset(string $email, string $token, Request $request, UserRepository $userRepository, UserPasswordEncoderInterface $passwordEncoder)
    {
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em = $this->getDoctrine()->getManager();

            $manager = $userRepository->findOneByEmail($email);

            if(!$manager instanceof User) {
                throw new NotFoundHttpException('Page introuvable');
            }
            elseif($manager->getResetToken() !== $token) {
                $this->addFlash('danger', 'Demande de réinitialisation invalide');
                return $this->redirectToRoute('manager_password_forgot');
            }
            elseif(Carbon::now()->diffInHours(Carbon::instance($manager->getResetTokenRequestAt())) > 2) {
                $this->addFlash('danger', 'Demande de réinitialisation expirée. Veuillez demander de nouveau la réinitilisation de votre mot de passe.');
                return $this->redirectToRoute('manager_password_forgot');
            }

            $encodedPassword = $passwordEncoder->encodePassword($manager, $form->get('password')->getData());
            $manager->setPassword($encodedPassword);

            $em->flush();
            $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
            return $this->redirectToRoute('manager_login');
        }

        return $this->render('manager/password/forgot.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
