<?php

namespace App\Services\Trick;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

class Uploader
{
    public function Upload($cover, $dir = 'uploads/tricks/'): ?string
    {
        if ($cover) {
            $originalFilename = pathinfo($cover->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $newFilename = sprintf("%s.%s", md5(basename($originalFilename)), $cover->guessExtension());

            try {
                $cover->move($dir, $newFilename);
            } catch (FileException $e) {
                $e = "L'image n'a pas pu être uploader";
            }

            return $newFilename;
        }

        return null;
    }
}