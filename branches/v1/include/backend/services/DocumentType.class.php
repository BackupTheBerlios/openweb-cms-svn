<?php
/**
 * Gestion des types de documents
 * @package OpenWeb-CMS
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

require_once('PEAR/ErrorStack.php');

class DocumentType
{
  /**
  * connexion à la base de données
  * @var object PEAR::DB
  */
  var $db;
  
  /**
   * liste des messages d'erreurs survenus pendant les traitements
   * @var PEAR_ErrorStack
   */
  var $errors;

  /**
  * identifiant du type
  * @var char
  */
  var $id;

  /**
  * libellé
  * @var string
  */
  var $libelle;

  /**
  * préfixe pour l'enregistrement des fichiers de ce type
  * @var string
  */
  var $repertoire;

  /**
  * liste d'articles
  * vrai si le document doit contenir une liste d'autres documents
  * qu'il introduit
  * @var bool
  */
  var $isintro;

  /**
  * accroche obligatoire
  * vrai si la présence d'une accroche est obligatoire
  * @var bool
  */
  var $accroche;

  /**
  * nombre maximal d'entrées par critère
  * Définit le nombre maximal d'entrées autorisées pour chaque critère (profil,
  * techno, thème). Tableau associatif 'nom' => nombre.
  * Désactivé si nombre vaut -1
  * @var array
  */
  var $max;

  /**
  * Nombre minimal d'entrées par critère
  * @var array
  */
  var $min;

  /**
  * somme maximale de toutes les entrées des critères
  * @var integer
  */
  var $total_max;

  /**
  * somme minimale de toutes les entrées des critères
  * @var integer
  */
  var $total_min;

  function DocumentType($typeid = null)
  {
    $this->db = &$GLOBALS['db'];
    $this->id = null;
    $this->libelle = '';
    $this->repertoire = '';
    $this->isintro = false;
    $this->accroche = false;
    $this->max = array();
    $this->min = array();
    $this->total_max = -1;
    $this->total_min = 0;
    $this->errors = &PEAR_ErrorStack::singleton('OpenWeb_Backend_DocumentType');
    if($typeid)
      $this->load($typeid);
  }

  /**
   * récupère les infos du type de document à partir de la base de données
   * @param   string  $type    identifiant ou libellé du type à charger
   * @return  boolean true=tout s'est bien passé
   */
  function load($type)
  {
    $type = $this->db->quote(trim(strtolower($type)));
    $sql = "SELECT typ_id, typ_libelle, typ_repertoire, typ_isintro,
            typ_accroche, typ_nbmax, typ_nbmin
            FROM typ_typedocument
            WHERE typ_id = $type OR typ_libelle = $type;";
    $res = $this->db->getRow($sql);
    if(DB::isError($res))
      return false;

    $this->id = $res["typ_id"];
    $this->libelle = $res["typ_libelle"];
    $this->repertoire = $res["typ_repertoire"];
    $this->isintro = $res["typ_isintro"] ? true : false;
    $this->accroche = $res["typ_accroche"] ? true : false;
    $this->total_max = $res["typ_nbmax"];
    $this->total_min = $res["typ_nbmin"];

    $sql = "SELECT cri_name, nb_min, nb_max FROM cri_criteres c, type_criteres t
            WHERE t.typ_id = ".intval($this->id)." AND c.cri_id = t.cri_id";
    $res = $this->db->getAll($sql);
    if(DB::isError($res))
      return false;

    foreach($res as $critere)
    {
      $this->min[$critere['cri_name']] = $critere["nb_min"];
      $this->max[$critere['cri_name']] = $critere["nb_max"];
    }
  }

  /**
  * vérifie qu'un objet DocInfos correspond à ce type de document
  * @param object $docinfos
  * @return boolean true si le document correspond au type
  */
  function check($docinfos)
  {
    $res = array();
    if(!$this->id)
      return false;

    /* On vérifie que l'accroche est correcte */
    if($this->accroche && $docinfos->accroche == '')
      $res[] = "accroche manquante";

    /* Nombre total de classements dans $docinfos */
    $nb = 0;

    /* On compte le nombre d'entrées dans $docinfos pour chaque critère
       qui se trouve dans $this->max ou $this->min. On calcule au passage
       également le nombre total d'entrées. */
    $compte = array();
    foreach(array_unique(array_merge(array_keys($this->max), array_keys($this->min))) as $critere)
      $nb += $compte[$critere] = isset($docinfos->classement[$critere]) ? count($docinfos->classement[$critere]) : 0;

    /* On vérifie que le nombre de classement pour chaque critère est dans
       le bon intervalle */
    foreach(array_keys($this->max) as $critere)
      /* Il n'y a pas de maximum si $this->max[$critere] est négatif */
      if($this->max[$critere] >= 0 && $compte[$critere] > $this->max[$critere])
	$res[] = "trop d'entrées pour $critere (maximum ".$this->max[$critere].")";
    foreach(array_keys($this->min) as $critere)
      if($compte[$critere] < $this->min[$critere])
        $res[] = "pas assez d'entrées pour $critere (minimum ".$this->min[$critere].")";

    /* Le nombre total de classements est-il compris entre les
       deux valeurs imposées ? */
    if($nb > $this->total_max)
      $res[] = "trop d'entrées (maximum ".$this->total_max.")";
    if($nb < $this->total_min)
      $res[] = "pas assez d'entrées (minimum ".$this->total_min.")";

    return count($res) == 0 ? true : $res;
  }
}
?>
