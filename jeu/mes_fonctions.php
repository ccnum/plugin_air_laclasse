<?php

###################
# Ouverture Wiki  #
###################

/*define('SECTEURS_WIKI', '36');

function autoriser_article_modifier($faire, $type, $id, $qui, $opt) {
    return true;

  // Si on est deja autorise en standard, dire 'OK'
  if (autoriser_article_modifier_dist($faire, $type, $id, $qui, $opt))
    return true;

  // Sinon, verifier si l'article est dans un secteur wiki
  $s = spip_query("SELECT id_secteur FROM spip_articles WHERE id_article="._q($id));
  if ($t = spip_fetch_array($s)
  AND in_array($t['id_secteur'], explode(',', SECTEURS_WIKI))
  )
    return true;

  // par defaut, NIET
  return false;
}

function autoriser_rubrique_publierdans($faire, $type, $id, $qui, $opt) {
  return true;

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
*/

#########################
# Fonctions générales   #
#########################

function cookie($nom,$valeur)
{
  setcookie($nom,$valeur,time()+10*24*3600);
}

function creer_histoire($id)
{
  include_spip('action/editer_objet');
  include_spip('inc/autoriser');
  $id_prologue = sql_getfetsel("id_article", "spip_articles", "`titre` LIKE '%prologue%' AND `statut` LIKE 'publie' AND id_rubrique = ".$id,"","RAND()");

  //Rubrique
    $id_rub = objet_inserer('rubrique',$id);
    autoriser_exception('modifier', 'rubrique', $id_rub);
    objet_modifier('rubrique', $id_rub, array("titre"=>"Histoire ".$id_rub,"descriptif"=>$id_prologue));
    sql_update('spip_rubriques', array("`statut`"=>"'publie'"), 'id_rubrique=' . intval($id_rub));
  
  //Prologue
    /*
    $art = objet_inserer('article',$id_rub);
    autoriser_exception('modifier', 'article', $art);
    objet_modifier('article', $art,  array('titre'=>'Prologue','texte'=> $texte));
    sql_update('spip_articles', array("`statut`"=>"'publie'"), "id_article=".$art);
    autoriser_exception('modifier', 'article', $art, false);
    */

  //Cloture exceptions
    autoriser_exception('modifier', 'rubrique', $id_rub,false);

  //Cookie
    cookie(rub_hist,$id_rub);

  return $id_rub;

}

function valider_chapitre($id_article,$id_rubrique){
      include_spip('action/editer_objet');
      include_spip('inc/autoriser');

      //Publication
        autoriser_exception('modifier', 'article', $id_article);
        sql_update('spip_articles', array("`statut`"=>"'publie'"), 'id_article=' . intval($id_article));
        autoriser_exception('modifier', 'article', $id_article, false);

      //mail
         $to = sql_getfetsel("soustitre", "spip_articles", "id_article = ".$id_article);
         $subject = 'Votre chapitre AIR !';
         $message = "Bonjour,\r\n\r\nFélicitations pour votre participation au cadavre exquis #air2014.\r\n Accédez dès maintenant à votre chapitre en ligne : http://air.laclasse.com/spip.php?scenario=jeu&page=lecture&id_rubrique=".$id_rubrique.". Vous serez prévenus par un prochain mail lorsque votre histoire écrite à 5 mains sera disponible.\r\n\r\nA très bientôt\r\n\r\nL'équipe d'Erasme et de la Villa Gillet.";
         $headers = "From: thematiques@laclasse.com" . "\r\n" .
         "Reply-To: thematiques@laclasse.com" . "\r\n" .
         "Bcc: pvincent@erasme.org, kcharnay@villetassinlademilune.fr, evalette@villetassinlademilune.fr" . "\r\n" .
         "Content-Type: text/plain; charset='utf-8'" . "\r\n" .
         "X-Mailer: PHP/" . phpversion();
         if (isset($to)&&($to != "")&&(filter_var($to, FILTER_VALIDATE_EMAIL))) mail($to, $subject, $message,$headers);
      
      //Si 5ème chapitre
        if ($res = sql_select("titre", "spip_articles", "`statut` LIKE 'publie' AND id_rubrique=".$id_rubrique)) 
        $n = sql_count($res);
        
        if ($n>=5) {
          $id_parent = sql_getfetsel("id_parent", "spip_rubriques", "id_rubrique=" . intval($id_rubrique));
          $rub_hist = creer_histoire($id_parent);
          $to = '';
          if ($resultats = sql_select("soustitre", "spip_articles", "id_rubrique = ". intval($id_rubrique))) {
            // boucler sur les resultats
              while ($res = sql_fetch($resultats)) {
                if (filter_var($res['soustitre'], FILTER_VALIDATE_EMAIL)) $to .= $res['soustitre'].",";
              }
          }

          $subject = 'Votre histoire AIR !';
          $message = "Bonjour à tous,\r\n\r\nFélicitations votre cadavre exquis est terminé.\r\n Discutez de l'édition de votre histoire avec vos co-auteurs par retour de mail : http://air.laclasse.com/spip.php?scenario=jeu&page=lecture&id_rubrique=".$id_rubrique."\r\n\r\nA très bientôt\r\n\r\nL'équipe d'Erasme et de la Villa Gillet.";
          $headers = "From: thematiques@laclasse.com" . "\r\n" .
          "Reply-To: thematiques@laclasse.com" . "\r\n" .
          "Bcc: pvincent@erasme.org, kcharnay@villetassinlademilune.fr, evalette@villetassinlademilune.fr" . "\r\n" .
          "Content-Type: text/plain; charset='utf-8'" . "\r\n" .
          "X-Mailer: PHP/" . phpversion();
          //$to = "pvincent@erasme.org";
          if ((isset($to))&&($to != "")) mail($to, $subject, $message,$headers);
        }

      //return if last chapitre
        if (isset($rub_hist)) return $rub_hist; 
  
}

function balise_ANNEE_SCOLAIRE_dist($p) {
    if ((isset($_COOKIE[_cookie_annee_scolaire]))
      &&($_COOKIE[_cookie_annee_scolaire]!=0)
      &&($_COOKIE[_cookie_annee_scolaire]!='')
      &&($_COOKIE[_cookie_annee_scolaire]>2011))
      $p->code = $_COOKIE[_cookie_annee_scolaire];
    else $p->code = 2013;
   if ((isset($_GET['annee_scolaire']))&&($_GET['annee_scolaire']!=0)&&($_GET['annee_scolaire']!=''))
  $p->code = $_GET['annee_scolaire'];   
   return $p;
}

function balise_ANNEE_ACTUELLE_dist($p) {
    if (date('m')>=9) $p->code = date('Y'); else $p->code = date('Y')-1;
    return $p;
}

function balise_NOM_AUTEUR_dist($p) {
        $p->code = "'Joy Surman'";
       return $p;
}

// Si balise_FIN_dist = false -> affichage de la grille sur la page d'accueil
// Si balise_FIN_dist = true -> affichage des couvertures et liens pdf sur la page d'accueil

function balise_FIN_dist($p) {
        $p->code = "'true'";
       return $p;
}

// Si balise_LECTURE_dist = false -> les textes sont masqués dans la vue lecture
// Si balise_LECTURE_dist = true -> les textes sont affichés dans la vue lecture

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