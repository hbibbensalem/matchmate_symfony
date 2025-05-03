<?php

namespace App\Service;

class ReferenceGenerator
{
    public function generateReference(string $productName): string
    {
        // 1. Prendre les 3 premières lettres du nom en majuscules
        $letters = substr(strtoupper($productName), 0, 3);
        
        // 2. Compléter avec 'X' si le nom est trop court
        $letters = str_pad($letters, 3, 'X');
        
        // 3. Générer deux séries de 4 chiffres aléatoires
        $numbers1 = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        $numbers2 = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        
        // 4. Combiner le tout : XXXX-YYY-XXXX
        return sprintf('%s-%s-%s', $numbers1, $letters, $numbers2);
    }
}
