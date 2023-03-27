<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trick;
use App\Form\MessageType;
use App\Form\TrickType;
use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TrickController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager= $entityManager;
    }

    #[Route('/', name: 'app_homepage')]
    public function index(TrickRepository $trickRepository): Response
    {
        $limit = 5;
        $page = 1;
        $tricks = $trickRepository->getPaginatedTricks($page, $limit);

        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks,
            'pages' => ceil($trickRepository->count([]) / $limit)
        ]);
    }

    #[Route('/load-more-tricks/{page}', name: 'app_loadMore', methods: ['GET'])]
    public function loadMore(TrickRepository $trickRepository, $page): JsonResponse
    {
        $limit = 5;
        $tricks = $trickRepository->getPaginatedTricks($page, $limit);

        $response = $this->renderView('trick/_list_tricks.html.twig', [
            'tricks' => $tricks
        ]);

        return new JsonResponse([
            'html' => $response,
            'pages' => ceil($trickRepository->count([]) / $limit)
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TrickRepository $trickRepository): Response
    {
        $trick = new Trick();
        $trick->setCreationDate(new \DateTimeImmutable());
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        $em = $this->entityManager;

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $cover */
            $cover = $trick->coverFile;
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($cover) {
                $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = sprintf("%s.%s", md5(basename($originalFilename)), $cover->guessExtension());

                // Move the file to the directory where brochures are stored
                try {
                    $cover->move('uploads/tricks/cover', $newFilename);

                    $trick->setCover($newFilename);
                } catch (FileException $e) {
                    $e = "L'image n'a pas pu être uploader";
                }
            }

            /** @var UploadedFile $pictures */
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            foreach ($trick->getPictures() as $picture) {
                // this is needed to safely include the file name as part of the URL
                $newFilename = md5(uniqid('', true)).'.'.$picture->file->guessExtension();

                // Move the filewFilename to the directory where brochures are stored
                try {
                    $picture->file->move('uploads/tricks', $newFilename);

                    $picture->setPicture($newFilename);
                } catch (FileException $e) {
                    $e = "L'image n'a pas pu être uploader";
                }
            }

            $videos = $form->get('videos')->getData();

            foreach($videos as $video)
            {
                $video->setTrick($trick);
                $em->persist($video);
            }

            $em->persist($trick);
            $em->flush();

            $this->addFlash('otherx', 'Votre figure a bien été ajouté');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Trick $trick, Request $request, MessageRepository $messageRepository, EntityManagerInterface $entityManager): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $message->setTrick($trick);
            $message->setUser($user);
            $message->setCreationDate(new \DateTime());
            $message->setContent($form->get('content')->getData());

            $em = $entityManager;
            $em->persist($message);
            $em->flush();

            $this->addFlash('success','Votre commentaire a bien été pris en compte');

            return $this->redirectToRoute('app_trick_show', ['id' => $trick->getId()], Response::HTTP_SEE_OTHER);
        }

        $trickId = $trick->getId();

        $limit = 5;
        $page = 1;
        $messages = $messageRepository->getPaginatedMessages($page, $limit, $trickId);
        $pages = ceil($messageRepository->countByTrickId($trickId) / $limit);

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'form' => $form,
            'messages' => $messages,
            'pages' => $pages
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/load-more-messages/{page}/{trickId}', name: 'app_loadMoreMessage', methods: ['GET'])]
    public function loadMoreMessage(MessageRepository $messageRepository, $page, $trickId): JsonResponse
    {
        $limit = 5;
        $messages = $messageRepository->getPaginatedMessages($page, $limit, $trickId);

        $response = $this->renderView('trick/_list_messages.html.twig', [
            'messages' => $messages
        ]);

        $page = ceil($messageRepository->countByTrickId($trickId) / $limit);

        return new JsonResponse([
            'html' => $response,
            'pages' => $page
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $form = $this->createForm(TrickType::class, $trick, [
            'isNew' => false, // Set to false since the trick already exists
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cover = $trick->coverFile;
            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($cover) {
                $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $newFilename = sprintf("%s.%s", md5(basename($originalFilename)), $cover->guessExtension());

                // Move the file to the directory where brochures are stored
                try {
                    $cover->move('uploads/tricks/cover', $newFilename);

                    $trick->setCover($newFilename);
                } catch (FileException $e) {
                    $e = "L'image n'a pas pu être uploader";
                }
            }

            foreach ($form->get('pictures')->getData() as $picture) {
                if ($picture->file !== null) {
                    $file = $picture->file;

                    $newFilename = md5(uniqid('', true)) . '.' . $file->guessExtension();

                    // Move the filewFilename to the directory where brochures are stored
                    try {
                        $picture->file->move('uploads/tricks', $newFilename);

                        $picture->setPicture($newFilename);
                    } catch (FileException $e) {
                        $e = "L'image n'a pas pu être uploader";
                    }
                }
            }

            $trick->setCreationDate(new \DateTime());

            $trickRepository->save($trick, true);

            return $this->redirectToRoute('app_homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_trick_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Trick $trick, EntityManagerInterface $entityManager, Filesystem $filesystem): Response
    {
        foreach ($trick->getPictures() as $picture) {
            $filenamePicture = 'uploads/tricks/'.$picture->getPicture();
            $filesystem->remove($filenamePicture);
            $entityManager->remove($picture);
        }

        foreach ($trick->getVideos() as $video) {
            $entityManager->remove($video);
        }

        $filenameCover = 'uploads/tricks/cover/'.$trick->getCover();
        $filesystem->remove($filenameCover);
        $entityManager->remove($trick);
        $entityManager->flush();

        $this->addflash(
            'success',
            "Le trick <strong>{$trick->getName()}</strong> a été supprimé avec succès !"
        );

        return $this->redirectToRoute('app_homepage');
    }
}
