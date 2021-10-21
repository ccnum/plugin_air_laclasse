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
    else $p->code = 2015;
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