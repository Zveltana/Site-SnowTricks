<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(EmailVerifier $emailVerifier, UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->emailVerifier = $emailVerifier;
        $this->userRepository = $userRepository;
        $this->entityManager= $entityManager;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
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

            /** @var UploadedFile $picture */
            $picture = $form->get('picture')->getData();
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($picture) {
                $originalFilename = pathinfo($picture->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = sprintf("%s.%s", md5(basename($originalFilename)), $picture->guessExtension());

                // Move the file to the directory where brochures are stored
                try {
                    $picture->move('uploads/user', $newFilename);

                    $user->setPicture($newFilename);
                } catch (FileException $e) {
                    $e = "L'image n'a pas pu être uploader";
                }
            }

            $user->setRegistrationToken(Uuid::v4());

            $em = $this->entityManager;
            $em->persist($user);
            $em->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation(
                    'snowtricks@gmail.com',
                    $user->getEmail(),
                    'Activation de votre compte sur le site SnowTricks',
                    'registration/confirmation_email.html.twig',
                    ['user' => $user, 'token' => $user->getRegistrationToken()],
            );
            // do anything else you need here, like send an email

            $this->addFlash('other', 'Confirmer votre email pour pouvoir vous connecter');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verify-email/{token}', name: 'app_verify_email')]
    public function verifyUserEmail($token): Response
    {
        $user = $this->userRepository->findOneBy(['registrationToken' => $token]);
        if ($user) {
            $user->setRegistrationToken(null);
            $user->setIsVerified(true);
            $em = $this->entityManager;
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Votre Email a été correctement vérifié.');

            return $this->redirectToRoute('app_login');
        }

        $this->addFlash("error", "Ce compte a déjà été vérifié");
        return $this->redirectToRoute('app_register');
    }
}
