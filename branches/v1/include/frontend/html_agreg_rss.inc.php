<?php
/**
 * Crée une liste de documents en XHTML
 * @package Frontend
 * @subpackage Présentation
 * @author Laurent Jouanneau
 * @copyright Copyright © 2005 OpenWeb.eu.org
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
require_once(PATH_SITE_ROOT.'libs/magpierss/rss_fetch.inc');
define('MAGPIE_CACHE_ON',true);
define('MAGPIE_CACHE_DIR', PATH_SITE_ROOT.'temp/magpie/');
define('MAGPIE_CACHE_AGE', 60*60*12);
define('MAGPIE_OUTPUT_ENCODING', 'UTF-8');

require_once (PATH_INC_BASECLASS.'Manager.class.php');

class rssManager extends Manager
{

   function getUrlList(){

       $sql = 'SELECT uti_nom, uti_prenom, uti_rss FROM uti_utilisateur';
        $sql .= ' WHERE uti_valide = 1 AND uti_rss <> \'\'';

    return $this->_getList($sql);
   }

}



function cmpBillets($item1, $item2)
{
   $t1 = $item1['date_timestamp'];
   $t2 = $item2['date_timestamp'];

   if ($t1 == $t2) {
       return 0;
   }
   return ($t1 > $t2) ? -1 : 1;
}


function OW_liste_rss ($maxbillets = 10)
{
  global $db;

  $rssman = new rssManager ($db);

  $urls = $rssman->getUrlList();

  $billets = array();

    foreach($urls as $url){
        $rss = fetch_rss( $url['uti_rss'] );
        if($rss){
            foreach($rss->items as $item){
                $item['channel_title'] =  $rss->channel['title'];
                $item['channel_link'] =  $rss->channel['link'];
                $item['ow_auteur'] = $url['uti_prenom'].' '.$url['uti_nom'];
                $billets[] = $item;
         }
      }
   }

   if(count($billets)){

        usort($billets, "cmpBillets");


        echo '<ul>';
        $i=0;
        foreach($billets as $billet){
            echo '<li><a href="',$billet['link'],'">',htmlspecialchars($billet['title']),'</a> ';
            echo 'sur ', htmlspecialchars($billet['channel_title']);
            echo ' le ', date('d-m-Y H:i:s',$billet['date_timestamp']);
            echo '</li>';

            if(++$i > $maxbillets)
                break;
        }
        echo '</ul>';
    }else{
        echo '<p>Pas de billets pour le moment.</p>';
    }
}

?>