<?php
/**
 * Gestion des tables de r�f�rences
 * @package Backend
 * @subpackage Services
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright � 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 * @uses Manager
 */

require_once (PATH_INC_BASECLASS.'Manager.class.php');

class ReferenceManager extends Manager
{
  /**
   * Renvoie la liste des crit�res de classement
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
   * Retourne les infos pour un crit�re
   * @param mixed $crit id (entier) ou nom (cha�ne) du crit�re � rechercher
   * @return array informations sur le crit�re ou vide
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
   * Ajoute un crit�re dans la base de donn�es
   * @param string $nom nom du crit�re � ajouter
   * @param string $libelle libell� du crit�re
   * @return boolean vrai si l'op�ration r�ussit
   */
  function addCriterion($nom, $libelle)
  {
    $res = $this->getCriterionInfos($nom);

    if(count($res) != 0)
      return false; /* On ne remplace pas un crit�re existant */

    $sql = 'INSERT INTO cri_criteres (cri_name, cri_libelle) VALUES
            ('.$this->db-quote($nom).', '.$this->db->quote($libelle).')';

    $res = $this->db->query($sql);

    if(DB::isError($res))
      return false;

    return true;
  }

  /**
   * Modifie le libell� d'un crit�re dans la base de donn�es
   * @param string $nom nom du crit�re � modifier
   * @param string $libelle nouveau libell�
   * @return boolean vrai si l'op�ration r�ussit
   */
  function updateCriterion($nom, $libelle)
  {
    $res = $this->getCriterionInfos($nom);

    if(count($res) != 0)
      return false; /* On ne remplace pas un crit�re existant */

    $sql = 'UPDATE cri_criteres
            SET cri_libelle = '.$this->db->quote($libelle).'
            WHERE cri_name = '.$this->db->quote($nom);

    $res = $this->db->query($sql);

    if(DB::isError($res))
      return false;

    return true;
  }

  /**
   * Supprime un crit�re de la base de donn�es
   * @param string $nom nom du crit�re � supprimer
   * @param boolean $force force la suppression m�me s'il existe des classements li�s � ce crit�re
   * @param boolean $purge indique s'il faut �galement enlever tous les classements relatifs � ce crit�re
   * @return boolean vrai si la suppression a eu lieu
   */
  function deleteCriterion($nom, $force = false, $purge = false)
  {
    if(count($cri = $this->getCriterionInfos($nom)) == 0)
      return false; /* On ne supprime pas l'inexistant */

    /* Si l'on n'a pas demand� de passer la tron�onneuse aveugl�ment on
       v�rifie que le bois n'est pas prot�g� par un trait� international */
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
   * Renvoie la liste des classements possibles pour un crit�re
   * @param string $criterion le crit�re
   * @return array la liste
   */
  function getEntriesList($criterion)
  {
    $sql = 'SELECT doc_repertoire, doc_titre_mini FROM doc_document d,
        typ_typedocument t, document_criteres l, cri_criteres c
      WHERE d.typ_id = t.typ_id
        AND t.typ_isintro = 1
	AND l.doc_id = d.doc_id
        AND l.cri_id = c.cri_id
        AND c.cri_name = '.$this->db->quote($criterion);
    $res = $this->_getList($sql);

    $liste = array();
    foreach($res as $row)
      $liste[$row['doc_repertoire']] = $row['doc_titre_mini'];

    return $liste;
  }

  /**
   * Renvoie la liste des classements d'un document pour un crit�re
   * @param string $criterion le crit�re
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
   * Renvoie la liste des �tats possibles pour un document
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
   * Renvoie les informations sur un �tat
   * @param mixed $eta id ou nom de l'�tat
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
   * @param integer $doc_id l'id du document concern�
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
   * V�rifie que les classements pass�s en param�tre existent dans la base
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
   * Copie les classements en base vers une repr�sentation XML
   * @todo s�curiser l'�criture du fichier
   * @todo dumper �galement le chemin du fichier
   */
  function dumpClassements()
  {
    require_once(PATH_INC_BACKEND_SERVICE.'Document.class.php');

    $doc = new Document($this->db);

    $res = '<?xml version="1.0" encoding="iso-8859-1"?>'."\n<criteres>\n";
    foreach($this->getCriterionList() as $critnam => $critlib)
    {
      $res .= '  <critere name="'.$critnam.'" libelle="'.$critlib.'">'."\n  <classements>\n";
      foreach($this->getEntriesList($critnam) as $entrnam => $entrlib)
      {
        $entrloc = $doc->load($entrnam) ? $doc->getDocumentPath() : '';
        $res .= '    <entry><name>'.$entrnam.'</name><libelle>'.$entrlib.'</libelle><location>'.$entrloc.'</location></entry>'."\n";
      }
      $res .= "  </classements>\n</critere>\n";
    }
    $res .= "</criteres>\n";
    ignore_user_abort(true);
    $fp = fopen(PATH_INCLUDE."xslt/inc/criteres.xml", "w");
    fwrite($fp, $res);
    fclose($fp);
    ignore_user_abort(false);
  }
}

?>
