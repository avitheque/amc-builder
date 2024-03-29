<?php
/**
 * @brief	Classe contenant toutes les constantes de l'application (messages d'erreur).
 *
 * @name		Constantes
 * @package		Constantes
 * @subpackage	Framework
 * @author		durandcedric@avitheque.net
 * @update		$LastChangedBy: durandcedric $
 * @version		$LastChangedRevision: 138 $
 * @since		$LastChangedDate: 2018-07-14 18:16:40 +0200 (Sat, 14 Jul 2018) $
 *
 * Copyright (c) 2015-2017 Cédric DURAND (durandcedric@avitheque.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 */
class Constantes {

    /**
     * Constantes de LOGIN de l'application.
     *
     * @var		string
     */
    const LOGIN_GUEST				= "Utilisateur non authentifié";

	/**
	 * Constantes des messages de l'application.
	 *
	 * @var		string
	 */
	const ERROR_ACL_NORESSOURCE		= 'ACL::Ressource ACL non déclarée';
	const ERROR_ACL_NOROLE			= 'ACL::Rôle ACL non déclaré';
	const ERROR_ACL_UNALLOWED		= 'ACL::Accès non autorisée';
	const ERROR_ACL_UNVALID			= 'ACL::Accès ACL non déterminé';


	const ERROR_AUTH_BAD			= 'AUTH::Authentification non valide !';


	const ERROR_BAD_ACTION			= 'Action impossible::Cette action n\'est pas prévue par l\'application.';
	const ERROR_BAD_CONTROLLER		= 'Action impossible::Cette action n\'est pas prévue par l\'application.';
	const ERROR_BAD_NOCONNECTOR		= 'Connexion inactive sollicitée::Une requête a été envoyée à un connecteur désactivé.';
	const ERROR_BAD_NOCONTROLLER	= 'Pas de contrôleur spécifié::Aucun contrôleur n\'a été spécifié.';
	const ERROR_BAD_TYPE			= 'Paramètre incorrect::Le paramètre transmis n\'a pas le type attendu.';
	const ERROR_BAD_VALUE			= 'Paramètre incorrect::Le paramètre transmis n\'est pas valide.';
	const ERROR_BAD_VIEW			= 'Vue indisponible::Impossible d\'afficher la vue spécifiée par le contrôleur, le fichier est introuvable.';


	const ERROR_DOWNLOAD_FILE		= 'Erreur de téléchargement::Le fichier demandé n\'existe pas !';


	const ERROR_LOG_ACTION			= 'LOG::Problème rencontré lors de l\'enregistrement du LOG';


	const ERROR_SQL_BADFIELD		= 'SQL::Un des champs de la table n\'a pas été trouvé';
	const ERROR_SQL_BADDATA			= 'SQL::Une des valeurs saisies n\'est pas correcte';
	const ERROR_SQL_BADQUERY		= 'SQL::La requête contient des erreurs';
	const ERROR_SQL_CASCADE			= 'SQL::Les contraintes de la base empêchent la suppression de l\'enregistrement...';
	const ERROR_SQL_NODELETE		= 'SQL::Suppression impossible !';
	const ERROR_SQL_NOQUERY			= 'SQL::La requête spécifiée est introuvable';
	const ERROR_SQL_NOSAVE			= 'SQL::Enregistrement impossible !';
	const ERROR_SQL_NOWHERE			= 'SQL::Le type de clause WHERE n\'est pas défini';


	const ERROR_UNDEFINED			= 'Exception non-définie::Une erreur est survenue lors du fonctionement de l\'application.';


	const ERROR_TRIGGERED			= 'Exception déclenchée : ';

}
