<?php
/**
* @package	JPack
* @version	1.0
* @Authors	Jouanneau Laurent <jouanneau@netcourrier.com
* @Copyright 2002-2003 Jouanneau Laurent
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU General Public License
* as published by the Free Software Foundation; either
* version 2.1 of the License, or (at your option) any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
* General Public License for more details.
*
* You should have received a copy of the GNU General Public
* License along with this library; if not, write to the Free Software
* Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*
* For questions, help, comments, discussion, etc., please send a e-mail to jouanneau@netcourrier.com
*
*/


class JUrl {

	var $scriptName;

	var $params=array();

	/**
	 * @param   string  $scriptname
	 * @param   array   $params             list of params to be set to url
	 * @param   array   $authorizedParams   list of params that we keep
	 */
	function JUrl($scriptname, $params, $authorizedParams=null){
		$this->params = $params;
		$this->scriptName = $scriptname;

		if($authorizedParams != null ){
	        if(is_array($authorizedParams)){
                foreach($authorizedParams as $name){
				    if(isset($params[$name]))
					    $this->params[$name]=$params[$name];
		            else
					    $this->params[$name]='';
                }
			}else{
				if(isset($params[$authorizedParams]))
					$this->params[$authorizedParams]=$params[$authorizedParams];
				else
					$this->params[$authorizedParams]='';
            }

		}else{
    		$this->params = $params;

		}
    }

	function set($name, $value){
		$this->params[$name]=$value;
	}

	function del($name){
		unset($this->params[$name]);
	}

	function clear(){
		$this->params=array();
	}

	function getUrl($xhtml=true){

		if(count($this->params)>0){
			$url='';
			foreach($this->params as $k=>$v){
				if($url=='')
					$url=$k.'='.$v;
				else
					$url.=($xhtml?'&amp;':'&').$k.'='.$v;
			}
			$url=$this->scriptName.'?'.$url;
		}else
			$url=$this->scriptName;

		return $url;
	}

}

?>
