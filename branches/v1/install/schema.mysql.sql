-- phpMyAdmin SQL Dump
-- version 2.6.3
-- http://www.phpmyadmin.net
-- 
-- Serveur: sql4.apinc.org
-- Généré le : Vendredi 09 Septembre 2005 à 22:21
-- Version du serveur: 4.1.11
-- Version de PHP: 4.4.0
-- 
-- Base de données: `nitot_openweb2`
-- 

-- --------------------------------------------------------

-- 
-- Structure de la table `cri_criteres`
-- 

DROP TABLE IF EXISTS `cri_criteres`;
CREATE TABLE `cri_criteres` (
  `cri_id` tinyint(4) NOT NULL auto_increment,
  `cri_name` varchar(15) NOT NULL default '',
  `cri_libelle` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`cri_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `doc_document`
-- 

DROP TABLE IF EXISTS `doc_document`;
CREATE TABLE `doc_document` (
  `doc_id` int(11) NOT NULL auto_increment,
  `doc_repertoire` varchar(40) NOT NULL default '',
  `doc_lang` char(2) NOT NULL default '',
  `typ_id` char(1) NOT NULL default '',
  `uti_id_soumis` int(11) NOT NULL default '0',
  `doc_auteurs` varchar(100) NOT NULL default '',
  `doc_titre` varchar(255) NOT NULL default '',
  `doc_titre_mini` text,
  `doc_accroche` text,
  `doc_etat` tinyint(4) NOT NULL default '0',
  `doc_date_publication` date NOT NULL default '0000-00-00',
  `doc_date_modification` date NOT NULL default '0000-00-00',
  `doc_date_enregistrement` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`doc_repertoire`,`doc_lang`),
  UNIQUE KEY `doc_repertoire` (`doc_repertoire`),
  UNIQUE KEY `doc_id` (`doc_id`),
  KEY `usr_id_soumis` (`uti_id_soumis`),
  KEY `doc_type` (`typ_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=130 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `document_criteres`
-- 

DROP TABLE IF EXISTS `document_criteres`;
CREATE TABLE `document_criteres` (
  `doc_id` int(11) NOT NULL default '0',
  `cri_id` tinyint(4) NOT NULL default '0',
  `intro_id` int(11) default '0',
  `ordre` mediumint(9) NOT NULL default '0',
  UNIQUE KEY `unique` (`doc_id`,`cri_id`,`intro_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `eta_etat`
-- 

DROP TABLE IF EXISTS `eta_etat`;
CREATE TABLE `eta_etat` (
  `eta_name` varchar(20) NOT NULL default '',
  `eta_id` tinyint(4) NOT NULL default '0',
  `eta_libelle` varchar(25) NOT NULL default '',
  `eta_dir` varchar(25) NOT NULL default '',
  PRIMARY KEY  (`eta_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `prm_permsbackend`
-- 

DROP TABLE IF EXISTS `prm_permsbackend`;
CREATE TABLE `prm_permsbackend` (
  `act_id` tinyint(4) NOT NULL auto_increment,
  `act_parent` tinyint(4) NOT NULL default '0',
  `act_name` varchar(20) NOT NULL default '',
  `uti_type` varchar(4) NOT NULL default '',
  `act_libelle` varchar(30) NOT NULL default '',
  `act_param` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`act_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=27 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `typ_typedocument`
-- 

DROP TABLE IF EXISTS `typ_typedocument`;
CREATE TABLE `typ_typedocument` (
  `typ_id` char(1) NOT NULL default '',
  `typ_libelle` varchar(30) NOT NULL default '',
  `typ_repertoire` varchar(30) NOT NULL default '',
  `typ_isintro` tinyint(1) NOT NULL default '0',
  `typ_accroche` tinyint(1) NOT NULL default '0',
  `typ_nbmax` smallint(6) default '0',
  `typ_nbmin` smallint(6) NOT NULL default '0',
  `typ_description` varchar(30) NOT NULL default '',
  `typ_ordre` int(11) default '0',
  PRIMARY KEY  (`typ_id`),
  UNIQUE KEY `typ_ordre` (`typ_ordre`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `type_criteres`
-- 

DROP TABLE IF EXISTS `type_criteres`;
CREATE TABLE `type_criteres` (
  `typ_id` char(1) NOT NULL default '',
  `cri_id` tinyint(4) NOT NULL default '0',
  `nb_min` smallint(6) NOT NULL default '0',
  `nb_max` smallint(6) default '0',
  PRIMARY KEY  (`typ_id`,`cri_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `uti_types`
-- 

DROP TABLE IF EXISTS `uti_types`;
CREATE TABLE `uti_types` (
  `uti_type` char(1) NOT NULL default '',
  `uti_libelle` varchar(20) NOT NULL default '',
  UNIQUE KEY `uti_type` (`uti_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

-- 
-- Structure de la table `uti_utilisateur`
-- 

DROP TABLE IF EXISTS `uti_utilisateur`;
CREATE TABLE `uti_utilisateur` (
  `uti_id` int(11) NOT NULL auto_increment,
  `uti_login` varchar(30) NOT NULL default '',
  `uti_password` varchar(32) NOT NULL default '',
  `uti_nom` varchar(50) NOT NULL default '',
  `uti_prenom` varchar(50) NOT NULL default '',
  `uti_type` char(2) NOT NULL default '',
  `uti_valide` tinyint(4) NOT NULL default '0',
  `uti_charset` varchar(11) NOT NULL default 'ISO-8859-1',
  `uti_lang` varchar(4) NOT NULL default 'fr',
  `uti_rss` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`uti_id`),
  UNIQUE KEY `uti_login` (`uti_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

-- 
-- Structure de la table `wkf_workflow`
-- 

DROP TABLE IF EXISTS `wkf_workflow`;
CREATE TABLE `wkf_workflow` (
  `act_name` varchar(20) NOT NULL default '',
  `uti_type` char(3) NOT NULL default '',
  `doc_etat_in` tinyint(4) NOT NULL default '0',
  `doc_etat_out` tinyint(4) NOT NULL default '0',
  `only_author` tinyint(4) NOT NULL default '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
