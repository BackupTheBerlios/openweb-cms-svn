<?php

class FileLock
{
  var $locked = array();
  var $tmp = "/tmp";

  function FileLock($tmp = "/tmp")
  {
    $this->tmp = $tmp;
  }

  function Open($filename)
  {
    $abort = ignore_user_abort(true);
    $timestamp = time();

    $tmpfile = tempnam($this->tmp, basename($filename));
    if(($fp = @fopen($tmpfile, "w")) === false)
      return false;
    if(!flock($fp, LOCK_EX|LOCK_NB))
    {
      fclose($fp);
      unlink($tmpfile);
      return false;
    }

    $infos = array("timestamp" => $timestamp,
      "filename" => $filename,
      "temp" => $tmpfile,
      "handle" => $fp);

    if(!$this->_doUpdate($infos, true))
      return false;

    $this->locked[] = $infos;

    ignore_user_abort($abort);
    return $fp;
  }

  function Close($fp)
  {
    $abort = ignore_user_abort(true);

    $infos = null;
    $locked = array();

    foreach($this->locked as $cur)
      if($cur['handle'] == $fp)
        $infos = $cur;
      else
        $locked[] = $cur;

    if($infos === null)
      return false;

    $this->locked = $locked;
    fclose($fp);

    $ret = $this->_doUpdate($infos, false);

    ignore_user_abort($abort);
    return $ret;
  }

  function _doUpdate($me, $lockonly)
  {
    if(($lp = @fopen($me['filename'].".locks", "a+")) === false)
      return false;
    if(!flock($lp, LOCK_EX))
    {
      fclose($lp);
      return false;
    }

    $othersinfos = array();
    $writeallowed = false;

    while(!feof($lp))
    {
      $cur = array();
      list($cur['timestamp'], $cur['temp']) = fscanf($lp, "%d\t%s\n");
      if($cur['timestamp'] > $me['timestamp'])
        $othersinfos[] = $cur;
      if($cur['timestamp'] == $me['timestamp'] && $cur['temp'] == $me['temp'])
        $writeallowed = true;
    }
    if($lockonly)
      $othersinfos[] = $me;
    
    ftruncate($lp, 0);
    foreach($othersinfos as $cur)
      fprintf($lp, "%d\t%s\n", $cur['timestamp'], $cur['temp']);

    if(!$lockonly && $writeallowed)
      rename($me['temp'], $me['filename']);

    /* @todo je pense que l'on peut faire l'unlink sans pb, mais à vérifier quand même */
    $stats = fstat($lp);
    if($stats['size'] == 0)
      unlink($me['filename'].".locks");

    flock($lp, LOCK_UN);
    fclose($lp);
    return true;
  }
}

?>
