<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\FormLoginAuthenticator;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use function Symfony\Component\Clock\now;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request                      $request,
        UserPasswordHasherInterface  $userPasswordHasher,
        EntityManagerInterface       $entityManager
//        , UserAuthenticatorInterface $userAuthenticator, FormLoginAuthenticator $formLoginAuthenticator
        , VerifyEmailHelperInterface $verifyEmailHelper
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email


            /**
             * authenticate user and automatically redirect to authenticator provided redirect link
             * return $userAuthenticator->authenticateUser(
             * $user,
             * $formLoginAuthenticator,
             * $request
             * );
             */

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );

            // TODO: in a real app, send email with signatureComponent

            $this->addFlash('success', 'Confirm your email at: ' . $signatureComponents->getSignedUrl());

            return $this->redirectToRoute('questions.index');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify', name: "app_verify_email")]
    public function verifyUserEmail(Request $request, VerifyEmailHelperInterface $verifyEmailHelper, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        // http://127.0.0.1:8000/verify?expires=1698515497&id=51&signature=H%2FLx1zl8PFYoh3HE3mRjJ2ZIgaykZD%2FrwL0W%2FqoJoXE%3D&token=ILksjnrnt6zzVA7zSEcs5cnJuU085HzfBC4Q2ndzFJc%3D

        $user = $userRepository->find($request->query->get('id'));

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );

        } catch (VerifyEmailExceptionInterface $e) {
            $this->addFlash('error', $e->getReason());

            return $this->redirectToRoute('app_register');
        }

        $user->setVerifiedAt(now());
        $entityManager->flush();

        $this->addFlash('success', 'Account Verified! You can log in!');

        return $this->redirectToRoute('app_login');
    }
}
