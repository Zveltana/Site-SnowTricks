<?php

namespace App\Services\Trick;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\String\Slugger\SluggerInterface;

class EditTrick
{
    public function __construct(
        private readonly Uploader $uploader, private readonly Filesystem $filesystem,
        private readonly TrickRepository $trickRepository, private readonly SluggerInterface $slugger
    )
    {
    }

    public function Edit(Trick $trick): Trick
    {
        $cover = $trick->coverFile;

        if ($cover) {
            $filenameCover = 'uploads/tricks/cover/' . $trick->getCover();
            $this->filesystem->remove($filenameCover);
            $trick->setCover($this->uploader->upload($cover, 'uploads/tricks/cover'));
        }

        foreach ($trick->getPictures() as $picture) {
            if ($picture->file !== null) {
                $filenamePicture = 'uploads/tricks/' . $picture->getPicture();
                $this->filesystem->remove($filenamePicture);

                $picture->setPicture($this->uploader->upload($picture->file));
            }
        }

        $trick->setUpdateDate(new \DateTime());
        $trick->setSlug($this->slugger->slug($trick->getName())->lower());

        $this->trickRepository->save($trick, true);

        return $trick;
    }
}