<?php
/**
 * Gestion des types de documents
 * @package OpenWeb-CMS
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright � 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */


class DocumentType
{
  /**
  * connexion � la base de donn�es
  * @var object PEAR::DB
  */
  var $db;
  
  /**
  * identifiant du type
  * @var char
  */
  var $id;

  /**
  * libell�
  * @var string
  */
  var $libelle;

  /**
  * pr�fixe pour l'enregistrement des fichiers de ce type
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
  * vrai si la pr�sence d'une accroche est obligatoire
  * @var bool
  */
  var $accroche;

  /**
  * nombre maximal d'entr�es par crit�re
  * D�finit le nombre maximal d'entr�es autoris�es pour chaque crit�re (profil,
  * techno, th�me). Tableau associatif 'nom' => nombre.
  * D�sactiv� si nombre vaut -1
  * @var array
  */
  var $max;

  /**
  * Nombre minimal d'entr�es par crit�re
  * @var array
  */
  var $min;

  /**
  * somme maximale de toutes les entr�es des crit�res
  * @var integer
  */
  var $total_max;

  /**
  * somme minimale de toutes les entr�es des crit�res
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
    if($typeid)
    	$this->load($typeid);
  }

  /**
   * recup�re les infos du type de document � partir de la base de donn�es
   * @param   string  $type    identifiant ou libell� du type � charger
   * @return  boolean true=tout s'est bien pass�
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
  * v�rifie qu'un objet DocInfos correspond � ce type de document
  * @param object $docinfos
  * @return boolean true si le document correspond au type
  */
  function check($docinfos)
  {
    $res = array();
    if(!$this->id)
      return false;

    /* On v�rifie que l'accroche est correcte */
    if($this->accroche && $docinfos->accroche == '')
      $res[] = "accroche manquante";

    /* Nombre total de classements dans $docinfos */
    $nb = 0;

    /* On compte le nombre d'entr�es dans $docinfos pour chaque crit�re
       qui se trouve dans $this->max ou $this->min. On calcule au passage
       �galement le nombre total d'entr�es. */
    $compte = array();
    foreach(array_unique(array_merge(array_keys($this->max), array_keys($this->min))) as $critere)
      $nb += $compte[$critere] = isset($docinfos->classement[$critere]) ? count($docinfos->classement[$critere]) : 0;

    /* On v�rifie que le nombre de classement pour chaque crit�re est dans
       le bon intervalle */
    foreach(array_keys($this->max) as $critere)
      /* Il n'y a pas de maximum si $this->max[$critere] est n�gatif */
      if($this->max[$critere] >= 0 && $compte[$critere] > $this->max[$critere])
	$res[] = "trop d'entr�es pour $critere (maximum ".$this->max[$critere].")";
    foreach(array_keys($this->min) as $critere)
      if($compte[$critere] < $this->min[$critere])
        $res[] = "pas assez d'entr�es pour $critere (minimum ".$this->min[$critere].")";

    /* Le nombre total de classements est-il compris entre les
       deux valeurs impos�es ? */
    if($nb > $this->total_max)
      $res[] = "trop d'entr�es (maximum ".$this->total_max.")";
    if($nb < $this->total_min)
      $res[] = "pas assez d'entr�es (minimum ".$this->total_min.")";

    return count($res) == 0 ? true : $res;
  }
}
?>
