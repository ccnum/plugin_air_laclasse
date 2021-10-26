<?php

include_spip('action/editer_objet');
include_spip('base/abstract_sql');

function creer_histoire($id_rub)
{
  $id = objet_inserer('rubrique',$id_rub);
  return $id;
}


function annee_rub($idr){

    $date = sql_getfetsel("maj", "spip_rubriques", "id_rubrique=" . intval($idr));

  if ($date != '')
    {
      $annee_scolaire = intval(substr($date,0,4));
      $mois_scolaire = intval(substr($date,5,2));
      if ($mois_scolaire < 9) $annee_scolaire--;
    }

  return $annee_scolaire;
}

function balise_ANNEE_SCOLAIRE_dist($p) {
    if ((isset($_COOKIE[_cookie_annee_scolaire]))
      &&($_COOKIE[_cookie_annee_scolaire]!=0)
      &&($_COOKIE[_cookie_annee_scolaire]!='')
      &&($_COOKIE[_cookie_annee_scolaire]>2011))
      $p->code = $_COOKIE[_cookie_annee_scolaire];
    else $p->code = 2021;
  if ((isset($_GET['annee_scolaire']))&&($_GET['annee_scolaire']!=0)&&($_GET['annee_scolaire']!=''))
  $p->code = $_GET['annee_scolaire'];
   return $p;
}

function balise_ANNEE_ACTUELLE_dist($p) {
    if (date('m')>=9) $p->code = date('Y'); else $p->code = date('Y')-1;
    return $p;
}

function balise_NOM_AUTEUR_dist($p) {
        $p->code = "'Violaine Schwartz'";
       return $p;
}

// Si balise_FIN_dist = false -> affichage de la grille sur la page d'accueil
// Si balise_FIN_dist = true -> affichage des couvertures et liens pdf sur la page d'accueil

function balise_FIN_dist($p) {
        $p->code = "'false'";
       return $p;
}

// Si balise_LECTURE_dist = false -> les textes sont masqués dans la vue lecture
// Si balise_LECTURE_dist = true -> les textes sont affichés dans la vue lecture

function balise_LECTURE_dist($p) {
        $p->code = "'false'";
       return $p;
}


// FILTRE SOUSTRATION

function balise_SOUSTRACTION_dist($p)
{
  $a = interprete_argument_balise(1, $p);
  $b = interprete_argument_balise(2, $p);

  if ($a == '' || $b == '')
  {
     $p->code = '\'#SOUSTRACTION[Manque argument]\'';
  }
  else
  {
     $p->code = '(' . $a . '-' . $b . ')';
  }

  return $p;
}

// FUNCTION CLEANCUT
function cleanCut($string,$length=200,$cutString = '(...)')
{
  if(strlen($string) <= $length)
  {
    return $string;
  }
  $str = substr($string,strlen($string)-$length-7,strlen($string));
  return $cutString.substr($str,stripos($str,' '));
}

/**
 * La fonction prend la date actuelle et l'année scolaire et tente de déduire quelle option rendre
 * sélectionnée par défaut.
 * @param $annee
 * @param $mois
 * @param $annee_scolaire
 * @return string
 */
function afficher_options_date($annee,$mois,$annee_scolaire)
{
    $texte='';
    if (date('m')>=9) $annee_actuelle = date('Y'); else $annee_actuelle = date('Y')-1;
    if ($mois<9) $annee = $annee--;
    for ($i=$annee_actuelle;$i>=$annee;$i--) {
        $j=$i+1;
        $texte .= "<option style='' value='$i'";
        if ($i==$annee_scolaire) $texte .= " selected ";
        $texte .= ">$i/$j</option>";
    }
    return $texte;
}

/**
 * Cette fonction reçoit une chaîne de caractère (un chapitre complet) et doit en retrancher les X derniers caractères.
 * X étant l'entier reçu en deuxième argument. Puis chaque caractère doit être remplacé par un x.
 *
 * -> si le chapitre contient moins de caractères que le nb de caractères à tronquer, on ne renvoie qu'une chaîne vide.
 *
 * @param string $texteAMasquer
 * @param int $nbDeCaracteresATronquerALaFin
 * @return string
 */
function masquerTexteChapitre(string $texteAMasquer='', int $nbDeCaracteresATronquerALaFin=325): string
{
    if(strlen($texteAMasquer) < $nbDeCaracteresATronquerALaFin){
        return '';
    }
    $texteTronque = substr($texteAMasquer, 0, strlen($texteAMasquer)-$nbDeCaracteresATronquerALaFin);

    // Remplace tous les caractères sauf les diacritiques.
    // Les RegEx ne semblent pas vouloir fonctionner :/ Je soupçonne un pb d'encofdage iso-latin/utf-8. AU SECOURS !
    $caracteresAMasquer = array(
        "à", "ä", "â",
        "À", "Ä", "Â",
        "ç",
        "Ç",
        "é", "è", "ë", "ê",
        "É", "È", "Ë", "Ê",
        "î", "ï",
        "Î", "Ï",
        "ô", "ö",
        "Ô", "Ö",
        "ù", "û", "ü",
        "Ù", "û", "ü",
        "ŷ", "ÿ",
        "Ŷ", "Ÿ",
        'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z',
        'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z  ',
    );
    return str_replace($caracteresAMasquer, "X", $texteTronque);
}

/**
 * Cette fonction reçoit une chaîne de caractères (un chapitre) et doit n'en retourner que les X derniers caractères. X
 * étant le nombre de caractères finaux que nous désirons. Enfin avant de renvoyer la fin de chaîne ainsi tronquée, on
 * lui concatènera (à son commencement) la chaîne de caractère éventuelle reçue en troisième argument (qui est sensée
 * symboliser le texte manquant)
 *
 * Ex : recupererDernieresLignesChapitres('toto_titi_tutu_tata', 5, '...') renverra ...titi_tutu_tata
 *
 * -> Si le nombre de caractères voulus est supérieur à la taille du texte, on renverra le texte complet sans la chaîne
 * à concaténer au début.
 *
 * @param string $texteChapitre
 * @param int $nbDeDerniersCaracteresAAfficher
 * @return string
 */
function recupererDernieresLignesChapitres($texteChapitre='', $nbDeDerniersCaracteresAAfficher=325, $chaineAConcatenerAuDebut='(...)'){
    if( strlen($texteChapitre) < $nbDeDerniersCaracteresAAfficher ){
        return $texteChapitre;
    }
    return $chaineAConcatenerAuDebut . substr($texteChapitre, strlen($texteChapitre)-$nbDeDerniersCaracteresAAfficher, -1);
}

/**
 * Renvoie l'année scolaire en cours.
 * Ex :
 * - renverra 2021 si on est en décembre 2021
 * - renverra 2021 si on est en mai 2022
 * - renverra 2020 si on est en juin 2021
 * --> Le mois qui permet de basculer d'une année scolaire à une autre est septembre (compris)
 *
 * @return int
 */
function getAnneeScolaireCourante(){
    return (date('m')>=9) ? intval(date('Y')) : intval(date('Y'))-1;
}


/*
 *  BALISES SPIP PERSONNALISÉES
 *
 * Il est possible de créer des balises SPIP personnalisées du type #MA_BALISE. Pour cela, il suffit de déclarer une
 * fonction ici-même.
 *
 * DOCUMENTATION ICI :
 * https://code.spip.net/fr/archives/compilateur/article/creer-des-balises-personnalisees-9
 * https://passingcuriosity.com/2008/creating-custom-tags-spip-static/
 *
 */

/**
 * Cette fonction permet de générer le contenu de la balise #MENU_DEROULANT_ANNEE.
 * @param object $p
 * @return object
 */
function balise_MENU_DEROULANT_ANNEE(object $p): object
{
    // Historiquement, la première année commence en 2012.
    $anne_debut = 2012;
    // La dernière année est l'année scolaire courante.
    $annee_courante = getAnneeScolaireCourante();
    $codeHTML = '<select name="annee_scolaire_voulue">';
    for ($i = $annee_courante; $i >= $anne_debut; $i--) {
        $codeHTML .= '<option value="' . $i . '">' . $i . '-' . ($i+1) . '</option>';
    }
    $codeHTML .= '</select>';

    /*
     * <select onchange="reload_cookie('[(#URL_SITE_SPIP)]','[(#EVAL{_cookie_annee_scolaire})]',document.annee_scolaire.annee_scolaire.value)" name="annee_scolaire">
                        <option value="#" style="">---</option>
                        <BOUCLE_vieux(ARTICLES){par date}{date!=0000-00-00 00:00:00}{0,1}{tout}>
                        [(#DATE|annee|afficher_options_date{[(#DATE|mois)],#EVAL{2021}})]
                        </BOUCLE_vieux>
                        <option style="" value="2011">2011/2012</option>
                        <option style="" value="2010">2010/2011</option>
            		</select>
     */

    $contenu = "<!-- je suis un test de balise dynamique objet -->" . $codeHTML;
    $p->code = '\'' . $contenu . '\'';
    return $p;
}