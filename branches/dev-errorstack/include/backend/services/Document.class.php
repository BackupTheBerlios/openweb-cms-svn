<?php
/**
 * Gestion des documents
 * @package OpenWeb-CMS
 * @author Laurent Jouanneau
 * @author Florian Hatat
 * @copyright Copyright © 2003 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */

require_once(PATH_INC_BACKEND_SERVICE."DocumentType.class.php");
require_once(PATH_INC_BACKEND_SERVICE."ReferenceManager.class.php");
require_once(PATH_INC_BACKEND_SERVICE."DocInfos.class.php");
require_once(PATH_INC_BACKEND_SERVICE."OutputFactory.lib.php");
require_once(PATH_INC_BASECLASS.'FileLock.class.php');
require_once('PEAR/ErrorStack.php');

class Document
{
  /**
   * identifiant du document dans la base de données
   * @var integer
   */
  var $id;

  /**
   * type de document
   * informations sur le type du document, obtenues à partir de la base
   * de données
   * @var object DocumentType
   */
  var $type;

  /**
   * toutes les infos extraites du document ou de la base
   * @var object DocInfos
   */
  var $infos;

  /**
   * état de la publication du document
   * @var integer
   */
  var $etat = -99;

  /**
   * liste des messages d'erreurs survenus pendant les traitements
   * @var PEAR_ErrorStack
   */
  var $errors;

  /** 
   * connexion à la base de données
   * @var object PEAR::DB
   */
  var $db;

  /**
   * Constructeur du document
   * @param object $db connexion à la base de données
   * @param mixed $doc_id id ou nom du document à ouvrir, null pour ne rien ouvrir
   */
  function Document(&$db, $doc_id = null)
  {
    $this->db = &$db;
    $this->ref = new ReferenceManager($this->db);

    $this->id = $this->type = $this->infos = null;
    $this->errors = &PEAR_ErrorStack::singleton('OpenWeb_Backend_Document');

    if($doc_id !== null)
      $this->load($doc_id);
    else
      $this->id = null;
  }

  /**
   * récupère les infos du document à partir d'un fichier DocBook
   * @param string $fichier nom du fichier
   * @return boolean <code>true</code> lorsque tout s'est bien passé
   */
  function setContentFromDocbook($fichier)
  {
    require_once(PATH_INC_BACKEND_SERVICE."DocbookParse.lib.php");

    $errors = &PEAR_ErrorStack::singleton('OpenWeb_Backend_DocbookParse');
    $errors->pushCallback(array(&$this, '_repackageErrorStack'));
    
    $this->infos = docbookGetArticleInfoFromFile($fichier);
    $this->infos->verifyRepertoire();

    $errors->popCallback();

    if($this->errors->hasErrors())
      return false;

    $errors = &PEAR_ErrorStack::singleton('OpenWeb_Backend_DocumentType');
    $errors->pushCallback(array(&$this, '_repackageErrorStack'));
    
    $this->type = new DocumentType($this->infos->type);

    $errors->popCallback();

    if($this->errors->hasErrors())
      return false;

    /* lorsque l'article est tout nouveau, on vérifie en base si le
       nom du répertoire est unique */
    $newdoc = false;

    if(!is_int($this->id))
    {
      $newdoc = true;
      $sql = 'SELECT count(doc_id) as cnt FROM doc_document WHERE lower(doc_repertoire) = '.$this->db->quote(strtolower($this->infos->repertoire));
      $rep = $this->db->getRow($sql);
      if(DB::isError($rep))
      {
        $this->errors->push(17); /*impossible de lire depuis la base de données*/
	return false;
      }

      if(intval($rep['cnt']) > 0)
      {
        $this->errors->push(17); /*"donnez un autre nom de répertoire : ".$this->infos->repertoire." est déjà utilisé";*/
        return false;
      }
    }

    if(in_array($this->type->repertoire.$this->infos->repertoire, $GLOBALS['OW_FORBIDDEN_DIR']))
    {
      $this->errors->push(17);/*"nom de répertoire interdit pas l'admin*/
      return false;
    }

    $destdir = PATH_SITE_ROOT.$this->getDocumentPath();

    // vérification des propriétes
    $this->_validateProperties();
    if($this->errors->hasErrors())
      return false;

    // création du répertoire de l'article dans temp
    umask(0022);

    if(file_exists($destdir))
    {
      if(!is_dir($destdir))
        $this->errors->push(17); /* @todo error code */
    }
    else
      mkdir($destdir, 0755);

    $annexes = $destdir."/annexes";
    if(file_exists($annexes))
    {
      if(!is_dir($annexes))
        $this->errors->push(17); /* @todo error code */
    }
    else
      mkdir($annexes, 0755);

    $dbkfile = $this->getDocumentFileName('docbook');

    /* @todo traiter les erreurs ici */
    $fl = new FileLock();
    ignore_user_abort(true);

    $fp = $fl->Open($dbkfile);
    fwrite($fp, file_get_contents($fichier));
    $fl->Close($fp);

    ignore_user_abort(false);
    chmod($dbkfile, 0644);

    if($this->errors->hasErrors())
      return false;

    // enregistrement en base des différentes infos
    $this->_saveDb();

    if($newdoc)
    {
      $etat = $this->ref->getStatusInfos('OW_STATUS_TEMP');
      $this->changeEtat($etat['id']);
    }

    if($this->type->isintro)
      $this->ref->dumpClassements();

    // conversion du fichier dans les autres formats
    $this->generation();

    return !$this->errors->hasErrors();
  }

  /**
   * retourne le chemin absolu et complet du répertoire du document, 
   * en fonction de son état ou de l'état indiqué en paramètre
   * @param   integer $etat   état à considérer
   * @return  string  chemin du répertoire du document
   */
  function getDocumentPath($etat = null)
  {
    if($etat === null)
      $etat = $this->etat;

    $dir = $this->ref->getStatusInfos($etat);
    return $dir['dir'].'/'.$this->type->repertoire.'/'.$this->infos->repertoire;
  }

  /**
   * retourne l'url du document, s'il est en ligne
   * @return string URL du document
   */
  function getDocumentUrl()
  {
    if($this->etat > 0)
      return OW_URL.$this->type->repertoire.'/'.$this->infos->repertoire.'/';
    else
      return '';
  }

  /**
   * retourne le nom du fichier contenant le document, en fonction du format demandé
   * @param   string  $type   nom du format demandé
   * @return  string  chemin complet du fichier
   */
  function getDocumentFileName($type = 'docbook')
  {
    global $outputInfos;
    $destdir = PATH_SITE_ROOT.$this->getDocumentPath();
    if($type == 'docbook')
      return $destdir.'/'.'docbook.xml';
    elseif(isset($outputInfos['docbook'][$type]))
      return $destdir.'/'.$outputInfos['docbook'][$type]['file'];
    else
      return null;
  }

  /**
   * retourne la liste des noms de fichiers pour les différents formats du document
   * @return array la liste des formats
   */
  function getDocumentFormats()
  {
    global $outputInfos;
    $filenames = array();

    foreach($outputInfos['docbook'] as $format)
      $filenames[$format['description']] = $format['file'];

    $filenames['Docbook'] = 'docbook.xml';
    
    return $filenames;
  }

  /**
   * change l'état du document
   * @param   integer $next_etat nouvel état du document
   */
  function changeEtat($next_etat)
  {
    $next_etat = intval($next_etat);

    $tmp = $this->ref->getStatusInfos('OW_STATUS_INEXISTANT');
    $inex_etat = intval($tmp['id']);
    unset($tmp);

    if($next_etat != $this->etat || $this->etat == $inex_etat)
    {
      if($next_etat != $inex_etat)
      {
        // mise à jour en base
        $sql = 'UPDATE doc_document SET doc_etat = '.$next_etat.' WHERE doc_id = '.$this->id;
        $this->db->query($sql);

        // changement de répertoire
	$dir1 = $this->ref->getStatusInfos($next_etat); /* PHP sapu ! */
	$dir2 = $this->ref->getStatusInfos($this->etat);
        if($dir1['dir'] != $dir2['dir'])
        {
          $old_dir = PATH_SITE_ROOT.$this->getDocumentPath();
          $new_dir = PATH_SITE_ROOT.$this->getDocumentPath($next_etat);

          delete_dir($new_dir);
          rename($old_dir, $new_dir);
        }
	unset($dir1); unset($dir2);
      }
      else
      {
        // suppression
        $old_dir = PATH_SITE_ROOT.$this->getDocumentPath();
        delete_dir($old_dir);
        $this->db->query('DELETE FROM document_criteres WHERE doc_id = '.$this->id);
        $this->db->query('DELETE FROM doc_document WHERE doc_id = '.$this->id);
      }
      $this->etat = $next_etat;
    }
  }

  /**
   * Génère tous les fichiers du document à partir du DocBook
   */
  function generation()
  {
    global $outputInfos;
    chdir(PATH_SITE_ROOT.$this->getDocumentPath());
    $dbk = $this->getDocumentFileName();
    if($this->id !== null)
      foreach($outputInfos['docbook'] as $out => $inf)
        if(!outputMake($dbk, 'docbook', $out))
          $this->errors->push(17); /* "erreur lors de la génération du format ".$inf['description']." ($out)";*/
  }

  /**
   * charge les données du document à partir de la base de données
   * @param mixed $doc_id id ou nom du document à charger
   * @return boolean vrai si les informations ont été lues sans erreurs
   */
  function load($doc_id)
  {
    $sql = 'SELECT doc_id, doc_document.typ_id as typ_id, typ_libelle,
            uti_id_soumis, doc_auteurs, doc_titre, doc_titre_mini,
            doc_accroche, doc_repertoire, doc_lang, doc_etat,
            doc_date_publication, doc_date_modification,
            doc_date_enregistrement, eta_libelle
            FROM doc_document, eta_etat, typ_typedocument
            WHERE doc_etat = eta_id AND doc_document.typ_id =
            typ_typedocument.typ_id ';
    if(is_numeric($doc_id))
      $sql .= 'AND doc_id = '.intval($doc_id);
    elseif(is_string($doc_id))
      $sql .= 'AND doc_repertoire = '.$this->db->quote($doc_id);
    else
    {
      $this->errors->push(17); /*'le document doit être désigné par son id ou le nom de son répertoire';*/
      return false;
    }

    $res = $this->db->getRow($sql);
    if(DB::isError($res))
    {
      $this->errors->push(17);/*'impossible d\'accéder à la base de données';*/
      return false;
    }

    if(count($res) == 0)
    {
      $this->errors->push(17);/*'le document n\'existe pas dans la base de données';*/
      return false;
    }

    $this->id = intval($res['doc_id']);

    $this->infos = new DocInfos();
    $this->infos->type = $res['typ_libelle'];
    $this->infos->auteurs = $res['doc_auteurs'];
    $this->infos->titre = $res['doc_titre'];
    $this->infos->titremini = $res['doc_titre_mini'];
    $this->infos->repertoire = $res['doc_repertoire'];
    $this->infos->pubdate = $res['doc_date_publication'];
    $this->infos->update = $res['doc_date_modification'];
    $this->infos->accroche = $res['doc_accroche'];
    $this->infos->lang = $res['doc_lang'];

    $this->type = new DocumentType($res['typ_id']);
    $this->etat = $res['doc_etat'];

    $this->infos->classement = $this->ref->getCriterionList();
    foreach($this->infos->classement as $cri => $val)
      $this->infos->classement[$cri] = $this->ref->getEntriesListByDoc($cri, $doc_id);

    return true;
  }

  /**
   * liste des annexes
   * @return array    liste des noms de fichiers annexes
   */
  function listeAnnexe()
  {
    $file_list = array();
    if($dir = opendir(PATH_SITE_ROOT.$this->getDocumentPath().'/annexes'))
    {
      while($file = readdir($dir))
      {
        if($file != '.' && $file != '..')
          $file_list[] = $file;
      }
      closedir($dir);
    }
    return $file_list;
  }

  /**
   * ajout d'une annexe à un document
   * @param   string  $fichier_temp   nom du fichier source (temporaire dans le cas d'un upload)
   * @param   string  $nom_fichier   nom du fichier cible
   * @todo voir s'il faut verrouiller le fichier pendant l'écriture
   * @todo renvoyer les erreurs s'il s'en produit, et s'assurer qu'on ne fait rien si par exemple le répertoire n'est pas accessible en écriture
   */
  function ajoutAnnexe($fichier_temp, $nom_fichier)
  {
    $dir_name = PATH_SITE_ROOT.$this->getDocumentPath().'/annexes/';
    copy($fichier_temp, $dir_name.$nom_fichier);

    chmod($dir_name.$nom_fichier, 0644);
  }

   /**
   * suppression d'un fichier annexe
   * @param   string  $fichier   nom du fichier à supprimer
   * @todo renvoyer des erreurs !
   */
  function supprimerAnnexe($fichier)
  {
    $dir_name = PATH_SITE_ROOT.$this->getDocumentPath().'/annexes/';
    if(is_array($fichier))
    {
      foreach($fichier as $fic)
      {
        $fic = basename($fic); // securité
        if(file_exists($dir_name.$fic))
          unlink($dir_name.$fic);
      }
    }
    else
    {
      $fichier = basename($fichier); // securité
      if(file_exists($dir_name.$fichier))
        unlink($dir_name.$fichier);
    }
  }


/*** Méthodes privées ***/

  /**
   * méthode principale pour l'enregistrement en base
   * selon l'id, met à jour ou ajoute les informations du document en base
   * @access private
   */
  function _saveDB()
  {
    if($this->id !== null)
      $this->_updateDB();
    else
      $this->_createDB();
  }

  /**
   * met à jour les infos du document en base de données
   * @access private
   */
  function _updateDB()
  {
    $sql = 'UPDATE doc_document SET '.
        'doc_repertoire = '.$this->db->quote($this->infos->repertoire).', '.
	'doc_lang = '.$this->db->quote($this->infos->lang).', '.
        'typ_id = '.$this->db->quote($this->type->id).', '.
	'doc_auteurs = '.$this->db->quote($this->infos->auteurs).', '.
        'doc_titre = '.$this->db->quote($this->infos->titre).', '.
        'doc_titre_mini = '.$this->db->quote($this->infos->titremini).', '.
        'doc_accroche = '.$this->db->quote($this->infos->accroche).', '.
        'doc_date_modification = '.$this->db->quote($this->infos->update).' '.
        'WHERE doc_id = '.$this->id;

    $res = $this->db->query($sql);
    if(DB::isError($res))
      trigger_error($res->userinfo.' ('.get_class($this).'::_updateDB)', E_USER_ERROR);

    $this->ref->setEntriesListByDoc($this->infos->classement, $this->id);
  }

  /**
   * ajoute le document dans la base de données
   * @access private
   */
  function _createDB()
  {
    $pubdate = $this->infos->pubdate;
    $dtupdate = $this->infos->update;

    $sql = 'INSERT INTO doc_document
            (typ_id, uti_id_soumis, doc_auteurs,
             doc_titre, doc_titre_mini, doc_accroche, doc_repertoire, doc_lang,
             doc_etat, doc_date_publication, doc_date_modification,
             doc_date_enregistrement)
            VALUES
            ('.$this->db->quote($this->type->id).', '.
              $_SESSION['utilisateur']['uti_id'].', '.
              $this->db->quote($this->infos->auteurs).', '.
              $this->db->quote($this->infos->titre).' , '.
              $this->db->quote($this->infos->titremini).' , '.
              $this->db->quote($this->infos->accroche).' , '.
              $this->db->quote($this->infos->repertoire).', '.
              $this->db->quote($this->infos->lang).', '.
              $this->etat.', '.
              $this->db->quote($this->infos->pubdate).', '.
              $this->db->quote($this->infos->update).', now() )';

    $res = $this->db->query($sql);
    if(DB::isError($res))
    {
      $this->errors->push(17); /* erreur sql, et ajouter $res->userinfo */
      return;
    }

    $res = $this->db->getRow('SELECT doc_id FROM doc_document WHERE doc_repertoire = '.$this->db->quote($this->infos->repertoire));
    $this->id = $res['doc_id'];

    $this->ref->setEntriesListByDoc($this->infos->classement, $this->id);
  }

  /**
   * vérifie les propriétes du document, leur présence et leur validité
   * à surcharger eventuellement dans les classes descendantes, pour tenir compte des spécifités de chaque type de document
   * @return boolean  true= tout est ok
   * @access private
   */
  function _validateProperties()
  {
    $errors = &PEAR_ErrorStack::singleton('OpenWeb_Backend_DocbookParse');
    $errors->getErrors(true);
    $errors->pushCallback(array(&$this, '_repackageErrorStack'));
    $res = $this->infos->verify();
    $errors->popCallback();

    if($this->errors->hasErrors())
      return false;

    $classement = &$this->infos->classement;

    if($this->type->isintro)
      foreach($classement as $crit => $entries)
        foreach($entries as $nom => $rub)
          if($nom == $this->infos->repertoire)
            unset($classement[$crit][$nom]);

    $errors = &PEAR_ErrorStack::singleton('OpenWeb_Backend_ReferenceManager');
    $errors->pushCallback(array(&$this, '_repackageErrorStack'));
    $this->ref->checkClassements($classement);
    $errors->popCallback();

    if($this->errors->hasErrors())
      return false;

    $errors = &PEAR_ErrorStack::singleton('OpenWeb_Backend_DocumentType');
    $errors->pushCallback(array(&$this, '_repackageErrorStack'));
    $this->type->check($this->infos);
    $errors->popCallback();

    return $this->errors->hasErrors();
  }

  function _repackageErrorStack($err)
  {
    $this->errors->push(17, 'error', array(), false, $err);
    return PEAR_ERRORSTACK_IGNORE;
  }
}

/**
 * efface un fichier ou le contenu d'un répertoire
 * @param   string  $file   nom du fichier à supprimer
 */
function delete_dir($file)
{
  if (file_exists($file))
  {
    chmod($file, 0777);
    if(is_dir($file))
    {
      $handle = opendir($file);
      while($filename = readdir($handle))
        if($filename != "." && $filename != "..")
          delete_dir($file."/".$filename);
      closedir($handle);
      rmdir($file);
    }
    else
      unlink($file);
  }
}

?>
