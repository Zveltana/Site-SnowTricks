<?php

namespace App\Services\Trick;

use App\Entity\Trick;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Filesystem\Filesystem;

class RemoveTrick
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly Filesystem $filesystem)
    {
    }

    public function Remove(Trick $trick): Trick
    {
        foreach ($trick->getPictures() as $picture) {
            $filenamePicture = 'uploads/tricks/' . $picture->getPicture();
            $this->filesystem->remove($filenamePicture);
            $this->entityManager->remove($picture);
        }

        foreach ($trick->getVideos() as $video) {
            $this->entityManager->remove($video);
        }

        $filenameCover = 'uploads/tricks/cover/' . $trick->getCover();
        $this->filesystem->remove($filenameCover);

        $this->entityManager->remove($trick);
        $this->entityManager->flush();

        return $trick;
    }
}