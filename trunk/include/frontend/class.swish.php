<?php
/*
    +----------------------------------------------------------------------+
    |    Classe Swish                                                      |
    +----------------------------------------------------------------------+
    |    php4 - 2001                                                       |
    +----------------------------------------------------------------------+
    |  La classe Swish permet d'interroger swish-e                         |
    |  Cette classe supporte le passage des nombreux arguments de          |
    |  swish-e et retourne un tableau de r�sultats ainsi que diverses      |
    |  informations.                                                       |
    +----------------------------------------------------------------------+
    |  Auteur:   Olivier Meunier                                           |
    |  Version:  1.1                                                       |
    +----------------------------------------------------------------------+
*/

class swish
{
    /**
    * D�clarations des prori�t�s
    */
    
    var $str_engine = "swish-e"; //Ligne de commande
    var $str_index_file;
    
    var $str_separator = '@@@@@';
    
    var $words;
    var $get_params;
    var $sort_params;
    var $first_result;
    var $last_result;
    
    var $number_results;
    
    //Les noms des champs titre, url et score sont modifiables
    var $titre_ligne = "TITRE";
    var $url_ligne = "URL";
    var $score_ligne = "SCORE";
    var $err = FALSE;
    
    /**
    * Constructeur
    *
    * @str_index_file    string        Chemin du fichier d'index
    * @str_engine        string        Chemin pour le moteur
    *
    * return            void    
    */
    function swish($str_index_file,$str_engine="")
    {
        $this->str_index_file = $str_index_file;
        
        if ($str_engine != "")
            $this->str_engine = $str_engine;
    }
    
    
    /**
    * M�thode: set_params
    *
    * @words            string        Chaine recherch�e
    * @get_params        array        Tableau des param�tres
    * @sort_params        string        Param�tre de tri
    * @first_result    integer        Indice du premier r�sultat
    * @last_resulte    integer        Nombre max de r�sultats
    *
    * return            void
    */
    function set_params($words, $get_params=array(), $sort_params="", $first_result="", $last_result="")
    {
        $this->words = $words;
        $this->get_params = $get_params;
        $this->sort_params = $sort_params;
        $this->first_result = $first_result;
        $this->last_result = $last_result;
    }
    
    
    /**
    * M�thode: exec_swish
    *
    * return            void
    */
    function exec_swish()
    {
        //Pr�pare la ligne de commande
        $this->words = escapeshellcmd($this->words);
        $this->words = str_replace('\*','*',$this->words);
        
        $cmd =    $this->str_engine." ".
                ' -f '.$this->str_index_file.
                ' -w "'.$this->words.'"'.
                ' -d '.$this->str_separator;
        
        //Ajout du param�tre -p si il y a des param�tres
        if(count($this->get_params) > 0)
        {
            $ligne_params = implode(" ",$this->get_params);
            $cmd .= " -p ".$ligne_params;
        }
        
        //Ajout du param�tre de tri s'il existe
        if($this->sort_params != "") {
            $cmd .= " -s ".$this->sort_params;
        }
        
        //Ajout du param�tre -b pour d�marrer au r�sultat n
        if($this->first_result != "") {
            $cmd .= " -b ".$this->first_result;
        }
        
        //Ajout du param�tre -m pour s'arr�ter � n lignes
        if($this->last_result != "") {
            $cmd .= " -m ".$this->last_result;
        }
        
        //La commande est prete, on l'�x�cute
        $this->cmd = $cmd;
        exec($cmd,$this->arry_swish);
        //Le r�sultat est stock�e dans $this->arry_swish
    }
    
    
    /**
    * Traitement du r�sultat
    *
    * return            void
    */    
    function make_result()
    {
        $i=0;
        
        //On passe en revue chaque ligne du tableau
        foreach($this->arry_swish as $value)
        {
            //Si on trouve une ligne qui commence par "err", on arr�te tout et
            //on initialise la propri�t� $err
            if(ereg("^err",$value))
            {
                $this->err = TRUE;
                break 1;
            }
            
            //Dans les lignes d'info, on r�cup�re le nombre de r�sultats
            if(ereg("^# Number of hits: ([0-9]*)",$value,$Tnb)) {
                $this->number_results = $Tnb[1];
            }
            
            //Ligne de r�sultats
            if(!ereg("^[.#]",$value))
            {
                //On passe en tableau tous les champs
                $arry_tmp = explode($this->str_separator,$value);
                
                //On r�cup�re le score, l'url et le titre
                $arry_int[$this->score_ligne] = $arry_tmp[0];
                $arry_int[$this->url_ligne] = $arry_tmp[1];
                $arry_int[$this->titre_ligne] = $arry_tmp[2];
                $arry_int['DOCSIZE'] = $arry_tmp[3];
                
                //Traitement des propri�t�s
                reset($this->get_params);
                for($j=4; $j<count($arry_tmp); $j++)
                {
                    $arry_int[key($this->get_params)] = $arry_tmp[$j];
                    next($this->get_params);
                }
                $this->arry_res[$i] = $arry_int;
                                
                $i++;                                
            }
        }
    }
    
    
    /**
    * Execution compl�te
    *
    * return            array        Tableau associatif de r�sultats
    */
    function get_result()
    {
        $this->exec_swish();
        $this->make_result();
        return $this->arry_res;
    }

}//Fin de la classe
?>
