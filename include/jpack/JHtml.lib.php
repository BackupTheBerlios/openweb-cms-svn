<?
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
* For questions, help, comments, discussion, etc., please send an e-mail to jouanneau@netcourrier.com
*
*/




function htmlTextArea($name,$value, $cols='',$rows='', $attribut=''){
	$value= str_replace("\\'", "'",$value);

	$html="<textarea name=\"$name\"";
	if($cols!='') $html.=" cols=\"$cols\"";
	if($rows!='') $html.=" rows=\"$rows\"";
	$html.=" $attribut>$value</textarea>";
	echo $html;
}


function htmlInputText($name, $value, $size='', $maxlength='', $attribut=''){
	$value= str_replace("\\'", "'",$value);
	$value= htmlentities( $value );
	$html="<input type=\"text\" name=\"$name\" id=\"$name\" value=\"$value\"";
	if($size!='') $html.=" size=\"$size\"";
	if($maxlength!='') $html.=" maxlength=\"$maxlength\"";
	$html.=" $attribut />";
	echo $html;
}


function htmlInputHidden($name, $value){
	$value= str_replace("\\'", "'",$value);
	$value= htmlentities( $value );
	$html="<input type=\"hidden\" name=\"$name\" id=\"$name\" value=\"$value\" />";
	echo $html;
}

function htmlCheckBox($name, $checkedvalue, $attribut=''){
	$checked='';
	if($checkedvalue!=0)
		$checked=' checked="checked" ';
  	$html="<input type=\"checkbox\" name=\"$name\" id=\"$name\" $checked $attribut />";
	echo $html;
}

function htmlRadio($name, $value, $checkedvalue, $attribut=''){
	$checked='';
	if($value==$checkedvalue)
		$checked=' checked="checked" ';
  	$html="<input type=\"radio\" name=\"$name\" id=\"$name\" value=\"$value\" $checked $attribut />";
	echo $html;
}

function htmlSelect($name, $options, $selected, $size=1, $multiple=false, $option_all_id='', $option_all_value=''){
    $html="<select name=\"$name\" id=\"$name\" size=\"$size\"".($multiple?' multiple="multiple"':'')." >\n";
	if($option_all_id != '' || $option_all_value !='') {

	    if($option_all_id == $selected)
	        $html.="<option value=\"$option_all_id\" selected=\"selected\">$option_all_value</option>\n";
	    else
	    $html.="<option value=\"$option_all_id\">$option_all_value</option>\n";

	}

	foreach($options as $k=>$v){
	    if(strval($k) == strval($selected))
	        $html.="<option value=\"$k\" selected=\"selected\">$v</option>\n";
	    else
		    $html.="<option value=\"$k\">$v</option>\n";
	}
    echo $html, '</select>';
}



function htmlSelectDB($name, $options, $selected, $idkey, $valuekey, $size=1, $multiple=false, $option_all_id='', $option_all_value=''){

    $html="<select name=\"$name\" id=\"$name\" size=\"$size\"".($multiple?' multiple="multiple"':'')." >\n";
	if($option_all_id != '' || $option_all_value !='') {
	    if($option_all_id == $selected)
	        $html.="<option value=\"$option_all_id\" selected=\"selected\">$option_all_value</option>\n";
	    else
		    $html.="<option value=\"$option_all_id\">$option_all_value</option>\n";
	}
	
	foreach($options as $v){
	    if(is_array($valuekey)){
		    $libelle='';
            foreach($valuekey as $key){
                $libelle.=' '.$v[$key];
			}
		}else
		    $libelle=$v[$valuekey];


	    if($v[$idkey] == $selected)
	        $html.='<option value="'.$v[$idkey].'" selected="selected">'.$libelle."</option>\n";
	    else
	        $html.='<option value="'.$v[$idkey].'">'.$libelle."</option>\n";
	}
    echo $html, '</select>';
}


?>
