<?php


/*                                      FONCTIONS TEXTUELLES                                                          */

/**
 * Cette fonction absurde sert à supprimer les premiers et derniers caractères d'une chaîne si ce sont des parenthèses.
 * Elle a été créée car je ne parviens pas, sur SPIP, à passer en argument du texte avec des virgules sans les
 * envelopper dans des parenthèses...
 * @param string $texte
 * @return string
 */
function enleverParenthesesTexte(string $texte=''): string
{
    if (strlen($texte) > 1) {
        if ($texte[0] === '(') {
            $texte = substr($texte, 1, strlen($texte) - 1);
        }
        if (strlen($texte) > 1 && $texte[-1] === ')') {
            $texte = substr($texte, 0, -1);
        }
    }
    return $texte;
}


/*                                      FONCTIONS USUELLES                                                            */

/**
 * @param string $url_site
 * @return string
 */
function extraireSousDomaine(string $url_site=''): string
{
    return 'toto';
}