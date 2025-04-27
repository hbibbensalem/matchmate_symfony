<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function uploadBase64Image($base64Image, $prefix = 'face')
    {
        // Extraire les données base64
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64Image));
        
        // Générer un nom de fichier unique
        $fileName = $prefix.'_'.uniqid().'.jpg';
        $filePath = $this->getTargetDirectory().'/'.$fileName;
        
        // Sauvegarder le fichier
        if (file_put_contents($filePath, $imageData) === false) {
            throw new FileException("Impossible d'enregistrer le fichier");
        }
        
        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}