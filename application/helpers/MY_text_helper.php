<?php

// if ( ! defined('BASEPATH')) exit('No direct script access allowed');


// if ( ! function_exists('first_chars'))
// {
    /**
     * Retourne les n premiers caractères d'une chaine
     * et ajoute des points de suspenssions
     * si la longueur de la chaine est supérieur à n
     */
    function first_chars($str, $n)
    {
        if (count($str) > $n) {
            return substr($str,0, $n).'...';
        }
        else {
            return $str;
        }
    }

// }
