<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Trick;
use App\Form\MessageType;
use App\Form\TrickType;
use App\Repository\MessageRepository;
use App\Repository\TrickRepository;
use App\Services\Trick\EditTrick;
use App\Services\Trick\NewTrick;
use App\Services\Trick\RemoveTrick;
use App\Services\Trick\ShowTrick;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrickController extends AbstractController
{
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
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, NewTrick $newTrick): Response
    {
        $trick = new Trick();
        $trick->setCreationDate(new \DateTimeImmutable());
        $trick->setUpdateDate(new \DateTimeImmutable());
        $trick->setUser($this->getUser());
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newTrick->new($trick);

            $this->addFlash('otherx', 'Votre figure a bien été ajouté');
            return $this->redirectToRoute('app_homepage');
        }

        return $this->render('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Trick $trick, Request $request, MessageRepository $messageRepository, ShowTrick $showTrick): Response
    {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $showTrick->Show($trick, $message);

            $this->addFlash('success', 'Votre commentaire a bien été pris en compte');

            return $this->redirectToRoute('app_trick_show', ['slug' => $trick->getSlug()], Response::HTTP_SEE_OTHER);
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

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, EditTrick $editTrick, Trick $trick): Response
    {
        $form = $this->createForm(TrickType::class, $trick, [
            'isNew' => false, // Set to false since the trick already exists
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $editTrick->Edit($trick);
            $this->addFlash('otherx', 'Votre figure a bien été mise à jour');

            return $this->redirectToRoute('app_homepage', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}/delete', name: 'app_trick_delete', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Trick $trick, RemoveTrick $removeTrick): Response
    {
        $removeTrick->Remove($trick);

        $this->addflash(
            'success',
            "Le trick <strong>{$trick->getName()}</strong> a été supprimé avec succès !"
        );

        return $this->redirectToRoute('app_homepage');
    }
}
