<?php
namespace App\Security;

class Validator
{
    // Verifie que le CNE respecte le format attendu (lettres majuscules et chiffres)
    public static function cne(string $s): bool
    { 
        return (bool)preg_match('/^[A-Z0-9]{6,20}$/', $s); 
    }

    // Verifie que la longueur d'une chaine ne depasse pas la valeur maximale
    public static function maxLen(string $s, int $max): bool
    { 
        return mb_strlen($s) <= $max; 
    }

    // Verifie que la chaine est un email valide
    public static function email(string $s): bool
    { 
        return (bool)filter_var($s, FILTER_VALIDATE_EMAIL); 
    }
}