<?php
/*
    +----------------------------------------------------------------------+
    |    Classe Swish                                                      |
    +----------------------------------------------------------------------+
    |    php4 - 2001                                                       |
    +----------------------------------------------------------------------+
    |  La classe Swish permet d'interroger swish-e                         |
    |  Cette classe supporte le passage des nombreux arguments de          |
    |  swish-e et retourne un tableau de résultats ainsi que diverses      |
    |  informations.                                                       |
    +----------------------------------------------------------------------+
    |  Auteur:   Olivier Meunier                                           |
    |  Version:  1.1                                                       |
    +----------------------------------------------------------------------+
*/

class swish
{
    /**
    * Déclarations des proriétés
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
    * Méthode: set_params
    *
    * @words            string        Chaine recherchée
    * @get_params        array        Tableau des paramètres
    * @sort_params        string        Paramètre de tri
    * @first_result    integer        Indice du premier résultat
    * @last_resulte    integer        Nombre max de résultats
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
    * Méthode: exec_swish
    *
    * return            void
    */
    function exec_swish()
    {
        //Prépare la ligne de commande
        $this->words = escapeshellcmd($this->words);
        $this->words = str_replace('\*','*',$this->words);
        
        $cmd =    $this->str_engine." ".
                ' -f '.$this->str_index_file.
                ' -w "'.$this->words.'"'.
                ' -d '.$this->str_separator;
        
        //Ajout du paramètre -p si il y a des paramètres
        if(count($this->get_params) > 0)
        {
            $ligne_params = implode(" ",$this->get_params);
            $cmd .= " -p ".$ligne_params;
        }
        
        //Ajout du paramètre de tri s'il existe
        if($this->sort_params != "") {
            $cmd .= " -s ".$this->sort_params;
        }
        
        //Ajout du paramètre -b pour démarrer au résultat n
        if($this->first_result != "") {
            $cmd .= " -b ".$this->first_result;
        }
        
        //Ajout du paramètre -m pour s'arrêter à n lignes
        if($this->last_result != "") {
            $cmd .= " -m ".$this->last_result;
        }
        
        //La commande est prete, on l'éxécute
        $this->cmd = $cmd;
        exec($cmd,$this->arry_swish);
        //Le résultat est stockée dans $this->arry_swish
    }
    
    
    /**
    * Traitement du résultat
    *
    * return            void
    */    
    function make_result()
    {
        $i=0;
        
        //On passe en revue chaque ligne du tableau
        foreach($this->arry_swish as $value)
        {
            //Si on trouve une ligne qui commence par "err", on arrête tout et
            //on initialise la propriété $err
            if(ereg("^err",$value))
            {
                $this->err = TRUE;
                break 1;
            }
            
            //Dans les lignes d'info, on récupère le nombre de résultats
            if(ereg("^# Number of hits: ([0-9]*)",$value,$Tnb)) {
                $this->number_results = $Tnb[1];
            }
            
            //Ligne de résultats
            if(!ereg("^[.#]",$value))
            {
                //On passe en tableau tous les champs
                $arry_tmp = explode($this->str_separator,$value);
                
                //On récupère le score, l'url et le titre
                $arry_int[$this->score_ligne] = $arry_tmp[0];
                $arry_int[$this->url_ligne] = $arry_tmp[1];
                $arry_int[$this->titre_ligne] = $arry_tmp[2];
                $arry_int['DOCSIZE'] = $arry_tmp[3];
                
                //Traitement des propriétés
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
    * Execution complète
    *
    * return            array        Tableau associatif de résultats
    */
    function get_result()
    {
        $this->exec_swish();
        $this->make_result();
        return $this->arry_res;
    }

}//Fin de la classe
?>
