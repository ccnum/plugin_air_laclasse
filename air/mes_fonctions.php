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
 * Enregistre en session l'année que le visiteur souhaite afficher.
 * -> cette année ne peut être inférieure à 2021 car cette année est le début de fictions
 *      -> 2021 devient alors l'année choisie
 * -> cette année ne peut pas être supérieur à l'année en cours (on ne peut pas demander l'année 2022 si on est en 2021.
 *      -> 2021 devient l'année choisie
 * @param int $anneeDemandee
 */
function setAnneeActuelle($anneeDemandee=2000){
    if( intval($anneeDemandee)<2021 ){ // On ne peut pas demander de date avant 2021 (fictions n'existait pas)
        $_SESSION['anneeActuelle']=2021;
    } else{
        (date('m')>=9)==true ? $annee_actuelle = date('Y') : $annee_actuelle = date('Y')-1;
        if($anneeDemandee > $annee_actuelle){
            $_SESSION['anneeActuelle']=2021; // On ne peut pas demander de date après l'année actuelle.
        } else{
            $_SESSION['anneeActuelle']=intval($anneeDemandee);
        }
    }
}

/**
 * On renvoie l'année qui est demandée si elle existe. Sinon, on renvoie l'année scolaire en cours
 * (ex : 2021 pour l'année 2021-2022).
 * @return int
 */
function getAnneeActuelle(): int
{
    if( isset($_SESSION['anneeActuelle']) ){
        return $_SESSION['anneeActuelle'];
    } else{
        if (date('m')>=9) $annee_actuelle = date('Y'); else $annee_actuelle = date('Y')-1;
        $_SESSION['anneeActuelle'] = $annee_actuelle;
        return $_SESSION['anneeActuelle'];
    }
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
        "à", "ä",
        "À", "Ä",
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
    //$texteMasqueRegex = preg_replace('[0-9a-zA-Z]', 'X', $texteMasque);
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