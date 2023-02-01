<?php

namespace App\Services\Trick;

use App\Entity\Picture;
use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class NewTrick
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager, private readonly SluggerInterface $slugger,
        private readonly Uploader $uploader
    )
    {
    }

    public function new(Trick $trick): Trick
    {
        $cover = $trick->coverFile;

        $trick->setCover($this->uploader->upload($cover, 'uploads/tricks/cover'));

        /** @var Picture $picture */
        foreach ($trick->getPictures() as $picture) {
            $picture->setPicture($this->uploader->upload($picture->file));
        }

        $trick->setSlug($this->slugger->slug($trick->getName())->lower());
        $this->entityManager->persist($trick);
        $this->entityManager->flush();

        return $trick;
    }
}