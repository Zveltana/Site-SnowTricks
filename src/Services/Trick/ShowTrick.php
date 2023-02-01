<?php

namespace App\Services\Trick;

use App\Entity\Message;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShowTrick extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function Show(Trick $trick, Message $message): Message
    {
        $message->setTrick($trick);
        $message->setUser($this->getUser());
        $message->setCreationDate(new \DateTime());
        $message->setContent($message->getContent());

        $this->entityManager->persist($message);
        $this->entityManager->flush();

        return $message;
    }
}