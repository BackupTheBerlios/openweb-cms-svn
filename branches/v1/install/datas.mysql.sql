-- phpMyAdmin SQL Dump
-- version 2.6.3
-- http://www.phpmyadmin.net
-- 
-- Serveur: sql4.apinc.org
-- Généré le : Vendredi 09 Septembre 2005 à 22:25
-- Version du serveur: 4.1.11
-- Version de PHP: 4.4.0
-- 
-- Base de données: `nitot_openweb2`
-- 

-- 
-- Contenu de la table `eta_etat`
-- 

INSERT INTO `eta_etat` (`eta_name`, `eta_id`, `eta_libelle`, `eta_dir`) VALUES ('OW_STATUS_ONLINE', 1, 'en ligne', 'www');
INSERT INTO `eta_etat` (`eta_name`, `eta_id`, `eta_libelle`, `eta_dir`) VALUES ('OW_STATUS_OFFLINE', -1, 'hors ligne', 'offline');
INSERT INTO `eta_etat` (`eta_name`, `eta_id`, `eta_libelle`, `eta_dir`) VALUES ('OW_STATUS_TEMP', -10, 'temporaire', 'temp');
INSERT INTO `eta_etat` (`eta_name`, `eta_id`, `eta_libelle`, `eta_dir`) VALUES ('OW_STATUS_INEXISTANT', -99, 'inexistant', 'temp');

-- 
-- Contenu de la table `prm_permsbackend`
-- 

INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (1, 0, 'ACT_ROOT', 'RCA', 'Backend Openweb', '');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (2, 1, 'ACT_ACCUEIL', 'RCA', 'Accueil', 'index.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (3, 1, 'ACT_DOCUMENTS', 'RCA', 'Documents', 'documents.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (4, 1, 'ACT_OUTILS', 'RCA', 'Outils', 'outils.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (5, 1, 'ACT_UTI', 'RCA', 'PrÃ©fÃ©rences', 'utilisateur.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (6, 1, 'ACT_ADMIN', 'AW', 'Administration', 'admin.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (7, 1, 'ACT_ACTUALITES', 'RC', 'ActualitÃ©s', 'actualite.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (8, 3, 'ACT_DOCAJOUT', 'RC', 'Ajouter un document', 'document_upload.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (10, 3, 'ACT_DOCREGEN', 'C', 'RegÃ©nÃ©rer tous les documents', 'document_regenere.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (11, 3, 'ACT_DOCCLASSE', 'C', 'Classer les documents', 'doc_classement.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (12, 6, 'ACT_ADMIN_ADD', 'AW', 'Ajout d''un utilisateur', 'admin_adduser.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (13, 6, 'ACT_ADMIN_DELETE', 'AW', 'Suppression d''un utilisateur', 'admin_deleteuser.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (14, 6, 'ACT_ADMIN_EDIT', 'AW', 'Modification d''un utilisateur', 'admin_edituser.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (25, 4, 'ACT_ACRO_LISTE', 'RCA', 'Liste des acronymes', 'acronymes_liste.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (16, 3, 'ACT_DOC_DETAILS', 'RCA', 'DÃ©tails d''un document', 'document_details.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (21, 16, 'ACT_DOC_MAJ', 'RC', 'Mettre Ã  jour', '');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (17, 16, 'ACT_DOC_DEL', 'RC', 'Supprimer', '');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (19, 16, 'ACT_DOC_OFFLINE', 'RC', 'Mettre hors-ligne', '');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (20, 16, 'ACT_DOC_ONLINE', 'RC', 'Mettre en ligne', '');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (22, 16, 'ACT_DOC_ANNEXES', 'RC', 'Mettre Ã  jour les annexes', 'document_annexes.php');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (18, 16, 'ACT_DOC_TEMP', 'RC', 'Repasser en temporaire', '');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (24, 1, 'ACT_DECONNEXION', 'RCA', 'Se dÃ©connecter', 'index.php?logon');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (23, 16, 'ACT_DOC_VIEW', 'RCA', 'Voir le document', '');
INSERT INTO `prm_permsbackend` (`act_id`, `act_parent`, `act_name`, `uti_type`, `act_libelle`, `act_param`) VALUES (26, 4, 'ACT_RECHERCHE', 'CA', 'Moteur de recherche', 'recherche.php');

-- 
-- Contenu de la table `uti_types`
-- 

INSERT INTO `uti_types` (`uti_type`, `uti_libelle`) VALUES ('A', 'Administrateur');
INSERT INTO `uti_types` (`uti_type`, `uti_libelle`) VALUES ('C', 'RÃ©dacteur en chef');
INSERT INTO `uti_types` (`uti_type`, `uti_libelle`) VALUES ('R', 'RÃ©dacteur');
INSERT INTO `uti_types` (`uti_type`, `uti_libelle`) VALUES ('W', 'Administrateur Wiki');

-- 
-- Contenu de la table `wkf_workflow`
-- 

INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_ANNEXES', 'R', -10, -10, 1);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_ANNEXES', 'C', -1, -1, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_ANNEXES', 'C', -10, -10, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_ANNEXES', 'C', 1, 1, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_DEL', 'C', -10, -99, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_DEL', 'R', -10, -99, 1);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_MAJ', 'R', -10, -10, 1);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_MAJ', 'C', -10, -10, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_MAJ', 'C', -1, -1, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_MAJ', 'C', 1, 1, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_OFFLINE', 'R', -10, -1, 1);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_OFFLINE', 'C', 1, -1, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_OFFLINE', 'C', -10, -1, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_ONLINE', 'C', -10, 1, 1);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_TEMP', 'C', -1, -10, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_VIEW', 'R', -10, -10, 1);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_VIEW', 'RC', -1, -1, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_VIEW', 'RC', 1, 1, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_VIEW', 'C', -10, -10, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_DEL', 'C', -1, -99, 0);
INSERT INTO `wkf_workflow` (`act_name`, `uti_type`, `doc_etat_in`, `doc_etat_out`, `only_author`) VALUES ('ACT_DOC_ONLINE', 'C', -1, 1, 0);
