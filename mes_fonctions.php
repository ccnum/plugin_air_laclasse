<?php

/*                                      CONFIGURATION DE LA CCN                                                       */

/**
 * Les CCN de type « cadavre exquis » ont deux modes de fonctionnements :
 * - par année dans des collèges
 * - par session/jeu dans des musées/évènements
 *
 * Comment savoir dans quel mode on se trouve ? Impossible de le prédire assurément : il s'agit d'une confiuration à
 * faire ici.
 * @return string
 */
function quelModeDeCCN(): string
{
    $jeu = array('petitfablab');
    $morceaux = explode('.', parse_url($_SERVER['HTTP_HOST']));
    var_dump($morceaux[0]);
    return $morceaux[0];
    /*
    if ( in_array(extraireSousDomaine($_SERVER['HTTP_HOST']), $jeu) ){
        return 'jeu';
    }
    return 'college';*/
}


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
 * Renvoie le sous-domaine d'une url reçue.
 * Ex :
 * - extraireSousDomaine(petitfablab.laclasse.com) -> petitfablab
 * - extraireSousDomaine(https://petitfablab.laclasse.com) -> petitfablab
 * - extraireSousDomaine(http://www.petitfablab.laclasse.com) -> petitfablab
 * @param string $url_site
 * @return string
 */
function extraireSousDomaine(string $url_site=''): string
{
    $urlParsee = parse_url($url_site);
    $domaines = explode('.', $urlParsee['host']);
    return $domaines[0];
}