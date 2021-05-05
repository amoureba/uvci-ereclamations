<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    const CLAIM_CAPTURE = 'captures';

    private $uploadsPath;

    public function __construct(string $uploadsPath)
    {
        $this->uploadsPath = $uploadsPath;
    }
    
    public function uploadClaimCapture(UploadedFile $uploadedFile): string
    {
        //symfonycasts/screnncast/symfony-uploads/uploader-service#play

        $destination = $this->uploadsPath . '/' . self::CLAIM_CAPTURE;
        //$destination = $this->uploadsPath.'/captures';

        $slugger = new SluggerInterface();

        $originalFilname = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);

        $safeFilename = $slugger->slug($originalFilname);

        $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        $uploadedFile->move($destination, $newFilename);

        return $newFilename;

        
    }
}