<?php
/**
 * Gestion des tables de références
 * @package Backend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @uses Manager
 */

require_once (PATH_INC_BASECLASS.'Manager.class.php');

class ReferenceManager extends Manager
{
  /**
   * Renvoie la liste des critères de classement
   * @return  array   la liste
   */
  function getCriterionList()
  {
    $sql = 'SELECT cri_name, cri_libelle FROM cri_criteres';
    $res = $this->_getList($sql);

    $liste = array();
    foreach($res as $row)
      $liste[$row['cri_name']] = $row['cri_libelle'];

    return $liste;
  }

  /**
   * Retourne les infos pour un critère
   * @param mixed $crit id (entier) ou nom (chaîne) du critère à rechercher
   * @return array informations sur le critère ou vide
   */
  function getCriterionInfos($crit)
  {
    $sql = 'SELECT * FROM cri_criteres WHERE cri_id = '.intval($crit).'
            OR cri_name = '.$this->db->quote($crit);
    $res = $this->_getRow($sql);
    if(count($res) == 0)
      return array();
    return array('name' => $res['cri_name'],
                 'libelle' => $res['cri_libelle'],
                 'id' => $res['cri_id']);
  }

  /**
   * Ajoute un critère dans la base de données
   * @param string $nom nom du critère à ajouter
   * @param string $libelle libellé du critère
   * @return boolean vrai si l'opération réussit
   */
  function addCriterion($nom, $libelle)
  {
    $res = $this->getCriterionInfos($nom);

    if(count($res) != 0)
      return false; /* On ne remplace pas un critère existant */

    $sql = 'INSERT INTO cri_criteres (cri_name, cri_libelle) VALUES
            ('.$this->db-quote($nom).', '.$this->db->quote($libelle).')';

    $res = $this->db->query($sql);

    if(DB::isError($res))
      return false;

    return true;
  }

  /**
   * Modifie le libellé d'un critère dans la base de données
   * @param string $nom nom du critère à modifier
   * @param string $libelle nouveau libellé
   * @return boolean vrai si l'opération réussit
   */
  function updateCriterion($nom, $libelle)
  {
    $res = $this->getCriterionInfos($nom);

    if(count($res) != 0)
      return false; /* On ne remplace pas un critère existant */

    $sql = 'UPDATE cri_criteres
            SET cri_libelle = '.$this->db->quote($libelle).'
            WHERE cri_name = '.$this->db->quote($nom);

    $res = $this->db->query($sql);

    if(DB::isError($res))
      return false;

    return true;
  }

  /**
   * Supprime un critère de la base de données
   * @param string $nom nom du critère à supprimer
   * @param boolean $force force la suppression même s'il existe des classements liés à ce critère
   * @param boolean $purge indique s'il faut également enlever tous les classements relatifs à ce critère
   * @return boolean vrai si la suppression a eu lieu
   */
  function deleteCriterion($nom, $force = false, $purge = false)
  {
    if(count($cri = $this->getCriterionInfos($nom)) == 0)
      return false; /* On ne supprime pas l'inexistant */

    /* Si l'on n'a pas demandé de passer la tronçonneuse aveuglément on
       vérifie que le bois n'est pas protégé par un traité international */
    if(!$force)
    {
      $sql = 'SELECT count(doc_id) AS num FROM document_criteres
              WHERE cri_id = '.intval($cri['id']);
      $res = $this->_getRow($sql);
      if($res['num'] != 0)
        return false;
    }

    /* Maintenant on a le droit de tout casser */
    if($purge)
    {
      $sql = 'DELETE FROM document_criteres
              WHERE cri_id = '.intval($cri['id']);
      $res = $this->db->query($sql);
      if(DB::isError($res))
        return false;
    }
    
    $sql = 'DELETE FROM cri_criteres WHERE cri_id = '.intval($cri['id']);
    $res = $this->db->query($sql);
    if(DB::isError($res))
      return false;

    return true;
  }

  /**
   * Renvoie la liste des classements possibles pour un critère
   * @param string $criterion le critère
   * @param boolean $only_online précise s'il faut exclure les intros hors-ligne
   * @return array la liste
   */
  function getEntriesList($criterion, $only_online = false)
  {
    $sql = 'SELECT doc_repertoire, doc_titre_mini FROM doc_document d,
        typ_typedocument t, document_criteres l, cri_criteres c
      WHERE d.typ_id = t.typ_id
        AND t.typ_isintro = 1
	AND l.doc_id = d.doc_id
        AND l.cri_id = c.cri_id
        AND c.cri_name = '.$this->db->quote($criterion);
    if($only_online)
      $sql .= ' AND d.doc_etat > 0';

    $res = $this->_getList($sql);

    $liste = array();
    foreach($res as $row)
      $liste[$row['doc_repertoire']] = $row['doc_titre_mini'];

    return $liste;
  }

  /**
   * Renvoie la liste des classements d'un document pour un critère
   * @param string $criterion le critère
   * @param integer $doc_id id du document
   * @return array la liste
   */
  function getEntriesListByDoc($criterion, $doc_id)
  {
    $sql = 'SELECT doc_repertoire, doc_titre_mini FROM doc_document d,
        typ_typedocument t, document_criteres l, cri_criteres c
      WHERE d.typ_id = t.typ_id
        AND t.typ_isintro = 1
        AND l.intro_id = d.doc_id
        AND l.cri_id = c.cri_id
        AND c.cri_name = '.$this->db->quote($criterion).'
	AND l.doc_id = '.intval($doc_id);
    $res = $this->_getList($sql);

    $liste = array();
    foreach($res as $row)
      $liste[$row['doc_repertoire']] = $row['doc_titre_mini'];

    return $liste;
  }

  /**
   * Renvoie la liste des états possibles pour un document
   * @return  array   la liste
   */
  function getStatusList()
  {
    $sql = 'SELECT eta_id, eta_libelle FROM eta_etat ORDER BY eta_id DESC';
    $res = $this->_getList($sql);

    $liste = array();
    foreach($res as $row)
      $liste[intval($row['eta_id'])] = $row['eta_libelle'];

    return $liste;
  }

  /**
   * Renvoie les informations sur un état
   * @param mixed $eta id ou nom de l'état
   * @return array les informations
   */
  function getStatusInfos($eta)
  {
    $sql = 'SELECT eta_id as id, eta_name as name, eta_libelle as libelle, eta_dir as dir FROM eta_etat WHERE eta_name = '.$this->db->quote($eta).' OR eta_id = '.intval($eta);
    return $this->_getRow($sql);
  }

  /**
   * Renvoie la liste des types d'articles
   * @return  array   la liste
   */
  function getTypeList()
  {
    $sql = 'SELECT typ_id, typ_libelle FROM typ_typedocument ORDER BY typ_id ASC';
    $res = $this->_getList($sql);

    $liste = array();
    foreach($res as $row)
      $liste[$row['typ_id']] = $row['typ_libelle'];

    return $liste;
  }

  /**
   * Modifie le classement d'un article
   * @param array $crit nouveux classements
   * @param integer $doc_id l'id du document concerné
   */
  function setEntriesListByDoc($crit, $doc_id)
  {
    $doc_id = intval($doc_id);
    $plus = $moins = $this->getCriterionList();
    foreach($plus as $cri => $entries)
    {
      $classement = array_keys($this->getEntriesListByDoc($cri, $doc_id));
      $plus[$cri] = isset($crit[$cri]) ? array_diff($crit[$cri], $classement) : array();
      $moins[$cri] = isset($crit[$cri]) ? array_diff($classement, $crit[$cri]) : array();
    }

    foreach($plus as $cri => $entries)
    {
      $cri_id = $this->getCriterionInfos($cri);
      $cri_id = $cri_id['id'];

      foreach($entries as $intro)
      {
        $sql = 'SELECT doc_id FROM doc_document WHERE doc_repertoire = '.$this->db->quote($intro);
	$res = $this->_getRow($sql);
	$intro_id = intval($res['doc_id']);

        $sql = "INSERT INTO document_criteres (doc_id, intro_id, cri_id, ordre) VALUES ($doc_id, $intro_id, $cri_id, 0);";
	$res = $this->db->query($sql);
      }
    }

    foreach($moins as $cri => $entries)
    {
      $cri_id = $this->getCriterionInfos($cri);
      $cri_id = $cri_id['id'];

      foreach($entries as $intro)
      {
        $sql = 'SELECT doc_id FROM doc_document WHERE doc_repertoire = '.$this->db->quote($intro);
	$res = $this->_getRow($sql);
	$intro_id = intval($res['doc_id']);

        $sql = "DELETE FROM document_criteres WHERE doc_id = $doc_id AND intro_id = $intro_id AND cri_id = $cri_id;";
	$res = $this->db->query($sql);
      }
    }
  }

  /**
   * Vérifie que les classements passés en paramètre existent dans la base
   * @param array $criteres
   * @return boolean vrai les tous les classements sont valides
   */
  function checkClassements($criteres)
  {
    $crit = $this->getCriterionList();
    foreach($crit as $cri => $val)
      $crit[$cri] = $this->getEntriesList($cri);

    $res = true;

    foreach($criteres as $cri => $ent)
      foreach($ent as $rub => $nom)
        $res = $res && isset($crit[$cri][$nom]);

    return $res;
  }

  /**
   * Copie les classements en base vers une représentation XML
   * @todo sécuriser l'écriture du fichier (collision possible entre deux écritures en parallèle)
   */
  function dumpClassements()
  {
    $res = '<?xml version="1.0" encoding="utf-8"?>'."\n<criteres>\n";
    foreach($this->getCriterionList() as $critnam => $critlib)
    {
      $res .= '  <critere name="'.$critnam.'" libelle="'.$critlib.'">'."\n  <classements>\n";
      foreach($this->getEntriesList($critnam, true) as $entrnam => $entrlib)
      {
        $sql = 'SELECT CONCAT("/", typ_repertoire, "/", doc_repertoire)
                AS repertoire
                FROM doc_document NATURAL JOIN typ_typedocument
                WHERE doc_repertoire = '.$this->db->quote($entrnam);
        $entrloc = $this->_getRow($sql);
        $entrloc = preg_replace('/\/+/', '/', '/'.$entrloc["repertoire"].'/');
        $res .= '    <entry><name>'.$entrnam.'</name><libelle>'.$entrlib.'</libelle><location>'.$entrloc.'</location></entry>'."\n";
      }
      $res .= "  </classements>\n</critere>\n";
    }
    $res .= "</criteres>\n";

    ignore_user_abort(true);
    if(($fp = fopen(PATH_INCLUDE."xslt/inc/criteres.xml", "w")) !== false)
    {
      fwrite($fp, $res);
      fclose($fp);
    }
    ignore_user_abort(false);
  }
}

?>
