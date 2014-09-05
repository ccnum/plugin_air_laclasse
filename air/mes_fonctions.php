<?php

include_spip('action/editer_objet');

function creer_histoire($id_rub)
{
  $id = objet_inserer('rubrique',$id_rub);
  return $id;
}

function balise_ANNEE_SCOLAIRE_dist($p) {
    if ((isset($_COOKIE[_cookie_annee_scolaire]))
      &&($_COOKIE[_cookie_annee_scolaire]!=0)
      &&($_COOKIE[_cookie_annee_scolaire]!='')
      &&($_COOKIE[_cookie_annee_scolaire]>2011))
      $p->code = $_COOKIE[_cookie_annee_scolaire];
    else $p->code = 2013;
   return $p;
}

function balise_ANNEE_ACTUELLE_dist($p) {
    if (date('m')>=9) $p->code = date('Y'); else $p->code = date('Y')-1;
    return $p;
}

function balise_NOM_AUTEUR_dist($p) {
        $p->code = "'L�onora Miano'";
       return $p;
}

// Si balise_FIN_dist = false -> affichage de la grille sur la page d'accueil
// Si balise_FIN_dist = true -> affichage des couvertures et liens pdf sur la page d'accueil

function balise_FIN_dist($p) {
        $p->code = "'true'";
       return $p;
}

// Si balise_LECTURE_dist = false -> les textes sont masqu�s dans la vue lecture
// Si balise_LECTURE_dist = true -> les textes sont affich�s dans la vue lecture

function balise_LECTURE_dist($p) {
        $p->code = "'true'";
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

function afficher_options_date($annee,$mois,$annee_scolaire)
{
  if (date('m')>=9) $annee_actuelle = date('Y'); else $annee_actuelle = date('Y')-1;
  if ($mois<9) $annee = $annee--; 
    for ($i=$annee_actuelle;$i>=$annee;$i--) {
    $j=$i+1;
    $texte .= "<option style='display:inline;' value='$i'";
    if ($i==$annee_scolaire) $texte .= " selected ";
    $texte .= ">$i/$j</option>";
  }
  return $texte;
}

?>