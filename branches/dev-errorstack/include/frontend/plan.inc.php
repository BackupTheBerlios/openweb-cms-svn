<?php

require_once('init.inc.php');
require_once(PATH_INC_FRONTEND.'FrontService.class.php');

function getOWPlan()
{
  global $db;
  $plan = array();
  $fs = new FrontService($db);

  $sql = 'SELECT * FROM typ_typedocument WHERE typ_ordre IS NOT NULL ORDER BY typ_ordre';
  $types = $db->getAll($sql);
  if(DB::isError($types))
  {
    trigger_error(E_USER_ERROR, "yo");
    return array();
  }
  foreach($types as $type)
  {
    $entree = array();
    $entree['libelle'] = $type['typ_description'];
    if($type['typ_isintro'] == 1)
    {
      $sql = 'SELECT d.doc_repertoire AS dir, d.doc_titre AS titre,
        c.cri_name AS crit, c.cri_libelle as critlib
        FROM doc_document d, typ_typedocument t, cri_criteres c,
        document_criteres l
        WHERE d.doc_etat > 0 AND d.typ_id = t.typ_id AND t.typ_ordre IS NOT NULL
        AND d.doc_id = l.doc_id AND l.cri_id = c.cri_id
        AND t.typ_id = '.$db->quote($type['typ_id']);
      $docs = $db->getAll($sql);
      if(DB::isError($docs))
  {
    trigger_error(E_USER_ERROR, "yo");
    return array();
  }
      foreach($docs as $doc)
      {
        $entree['crit'][$doc['crit']]['libelle'] = $doc['critlib'];
        $entree['crit'][$doc['crit']]['docs'][$fs->getDocumentPath($doc['dir'])] = $doc['titre'];
      }
    }
    else
    {
      $sql = 'SELECT d.doc_repertoire as dir, d.doc_titre as titre
        FROM doc_document d, typ_typedocument t
        WHERE d.doc_etat > 0 AND d.typ_id = t.typ_id AND t.typ_ordre IS NOT NULL
        AND t.typ_id = '.$db->quote($type['typ_id']);
      $docs = $db->getAll($sql);
      if(DB::isError($docs))
  {
    trigger_error(E_USER_ERROR, "yo");
    return array();
  }
      foreach($docs as $doc)
        $entree['docs'][$fs->getDocumentPath($doc['dir'])] = $doc['titre'];
    }
    $plan[] = $entree;
  }
  return $plan;
}

?>
