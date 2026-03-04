<?php
namespace App\Security;

class Sanitizer
{
    // Nettoie les chaines dans un tableau en supprimant les espaces inutiles
    public static function trimArray(array $in): array
    { 
        return array_map(function ($v) { 
            return is_string($v) ? trim($v) : $v; 
        }, $in); 
    }

    // Filtre une chaine pour obtenir un email propre
    public static function email(?string $s): string
    { 
        return filter_var((string)$s, FILTER_SANITIZE_EMAIL) ?: ''; 
    }

    // Limite la longueur d'une chaine et supprime les espaces
    public static function string(?string $s, int $max = 255): string
    { 
        $s = trim((string)$s); 
        return mb_substr($s, 0, $max); 
    }

    // Convertit n'importe quelle valeur en entier
    public static function int($v): int 
    { 
        return (int)$v; 
    }
}