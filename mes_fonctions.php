<?php

  error_reporting(error_reporting() & (-1 ^ E_DEPRECATED));


function autoriser_article_modifier($faire, $type, $id, $qui, $opt) {
  // Si on est deja autorise en standard, dire 'OK'
  if (autoriser_article_modifier_dist($faire, $type, $id, $qui, $opt))
    return true;

  // Sinon, verifier si l'article est dans un secteur wiki
  $s = spip_query("SELECT id_secteur FROM spip_articles WHERE id_article="._q($id));
  if ($t = spip_fetch_array($s)
  AND in_array($t['id_secteur'], explode(',', SECTEURS_WIKI))
# AND in_array($qui['statut'], array('0minirezo', '1comite'))
  )
    return true;

  // par defaut, NIET
  return false;
}

function autoriser_rubrique_publierdans($faire, $type, $id, $qui, $opt) {
  // Si on est deja autorise en standard, dire 'OK'
  if (autoriser_rubrique_publierdans_dist($faire, $type, $id, $qui, $opt))
    return true;

  // Sinon, verifier si la rubrique est dans un secteur gribouille
  // et si on est bien redacteur
  if (
  in_array($qui['statut'], array('0minirezo', '1comite'))

  AND
  (in_array($id, array(36))
  OR (
    $s = spip_query("SELECT id_secteur FROM spip_rubriques WHERE id_rubrique="._q($id))
    AND $t = spip_fetch_array($s)
    AND in_array($t['id_secteur'], explode(',', SECTEURS_WIKI))
  ))
  )
    return true;

  // par defaut, NIET
  return false;
}

function analyse_droits_rapide() {
  return true;
}

###################
# Fonctions générales  #
###################

function balise_ANNEE_SCOLAIRE_dist($p) {
    if ((isset($_COOKIE[_cookie_annee_scolaire]))
      &&($_COOKIE[_cookie_annee_scolaire]!=0)
      &&($_COOKIE[_cookie_annee_scolaire]!='')
      &&($_COOKIE[_cookie_annee_scolaire]>2011))
      $p->code = $_COOKIE[_cookie_annee_scolaire];
    else $p->code = 2014;
  if ((isset($_GET['annee_scolaire']))&&($_GET['annee_scolaire']!=0)&&($_GET['annee_scolaire']!=''))
  $p->code = $_GET['annee_scolaire'];
   return $p;
}

function balise_ANNEE_ACTUELLE_dist($p) {
    if (date('m')>=9) $p->code = date('Y'); else $p->code = date('Y')-1;
    return $p;
}

function balise_NOM_AUTEUR_dist($p) {
        $p->code = "'Joe Sorman'";
       return $p;
}

// Si balise_FIN_dist = false -> affichage de la grille sur la page d'accueil (début d'année)
// Si balise_FIN_dist = true -> affichage des couvertures et liens pdf sur la page d'accueil (fin d'année)

function balise_FIN_dist($p) {
        $p->code = "'false'";
       return $p;
}

// Si balise_LECTURE_dist = false -> les textes sont masqués dans la vue lecture (début d'année)
// Si balise_LECTURE_dist = true -> les textes sont affichés dans la vue lecture (fin d'année)

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

function afficher_options_date($annee,$mois,$annee_scolaire)
{
  if (date('m')>=8) { // La nouvelle année scolaire apparaîtra dans le menu au mois d'août.
      $annee_actuelle = date('Y');
  }
  else{
      $annee_actuelle = date('Y')-1;
  }
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