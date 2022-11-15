<?php

function enleverParenthesesTexte($texte=''): string
{
    if (strlen($texte) > 1) {
        if ($texte[0] === '(') {
            $texte = substr($texte, 1, strlen($texte) - 1);
        }
    }
    return $texte;
}