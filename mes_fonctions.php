<?php

/*                                      CONFIGURATION DE LA CCN                                                       */

/**
 * Les CCN de type « cadavre exquis » ont deux modes de fonctionnements :
 * - par année dans des collèges                -> renverra 'college'
 * - par session/jeu dans des musées/évènements -> renverra 'jeu'
 *
 * Comment savoir dans quel mode on se trouve ? Impossible de le prédire assurément : il s'agit d'une configuration à
 * faire ici.
 * @return string
 */
function quelModeDeCCN(): string
{
    $listeDesCCNEnModeJeu = array('petitfablab');

    $urlParsee = parse_url($_SERVER['HTTP_HOST']);
    if ( isset($urlParsee['path']) ){
        $morceaux = explode('.', $urlParsee['path']);
        if (sizeof($morceaux)>0 && in_array($morceaux[0], $listeDesCCNEnModeJeu) ){
            return 'jeu';
        }
    }
    return 'college';
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

function masquerTexte($texteAMasquer='', $derniersCaracteresAAfficher=200): string
{
    return 'toto';
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

/**
 * Renvoie la valeur du paramètre 'session' se trouvant dans l'url. Si ce paramètre n'existe pas, renverra 0.
 * Ex :
 * - getValeurIdSessionCCN(petitfablab.laclasse.com?session=6) -> 6
 * - getValeurIdSessionCCN(petitfablab.laclasse.com?var_mode=recalcul) -> 0
 * - getValeurIdSessionCCN(petitfablab.laclasse.com?var_mode=recalcul&sessions=4) -> 4
 * - getValeurIdSessionCCN(petitfablab.laclasse.com?var_mode=recalcul&sessions=toto) -> 0
 * @return int
 */
function getValeurIdSessionCCN(): int
{
    if ( isset($_GET['session']) ){
        return intval($_GET['session']);
    }
    return 0;
}