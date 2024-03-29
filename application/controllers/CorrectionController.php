<?php
/**
 * @brief	Classe contrôleur de correction d'une épreuve QCM.
 *
 * Étend la classe abstraite AbstractFormulaireQCMController.
 * @see			{ROOT_PATH}/libraries/controllers/AbstractFormulaireQCMController.php
 *
 * @name		CorrectionController
 * @category	Controller
 * @package		Main
 * @subpackage	Application
 * @author		durandcedric@avitheque.net
 * @update		$LastChangedBy: durandcedric $
 * @version		$LastChangedRevision: 78 $
 * @since		$LastChangedDate: 2017-08-29 18:14:10 +0200 (Tue, 29 Aug 2017) $
 *
 * Copyright (c) 2015-2017 Cédric DURAND (durandcedric@avitheque.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 */
class CorrectionController extends AbstractFormulaireQCMController {

	/**
	 * @var		integer
	 */
	protected	$_idEpreuve		= null;

	/**
	 * @var		FormulaireManager
	 */
	protected	$_oFormulaireManager;

	/**
	 * @brief	Constructeur de la classe.
	 *
   	 * @overload	AbstractFormulaireQCMController::construct()
	 *
	 * @li Initialisation du tableau des données du formulaire.
	 */
	public function __construct() {
		// Initialisation du contôleur parent
		parent::__construct(__CLASS__, 'CORRECTION');

		// Récupération de l'identifiant du formulaire en session
		$this->_idEpreuve 		= $this->getDataFromSession('ID_EPREUVE');
	}

	/**
	 * @brief	Action du contrôleur réalisée par défaut.
	 *
	 * Si l'identifiant du formulaire est connu, le document est créé, sinon une liste complète est affichée.
	 */
	public function indexAction() {
		// Récupération de l'identifiant du formulaire
		$nIdEpreuve = $this->getParam("id_epreuve");

		// Fonctionnalité réalisée si le formulaire est valide
		if ($nIdEpreuve) {
			// Stockage de l'identifiant de l'édition en session
			$this->sendDataToSession($nIdEpreuve, "ID_EPREUVE");

			// Redirection afin d'effacer les éléments présents en GET
			$this->redirect('correction/epreuve');
		}
	}
	
	/**
	 * @brief	Action du contrôleur réalisée pour lister les controles d'une épreuve.
	 *
	 * Si l'identifiant du formulaire est connu, le document est créé, sinon une liste complète est affichée.
	 */
	public function epreuveAction() {
		if ($this->_idEpreuve) {
			
		} else {
			// Redirection à la page d'accueil
			$this->redirect('correction');
		}
	}
	
	/**
	 * @brief	Action du contrôleur réalisée pour visualiser le controle d'un candidat.
	 *
	 * Si l'identifiant du formulaire est connu, le document est créé, sinon une liste complète est affichée.
	 */
	public function controleAction() {
		
	}
	
	/**
	 * @brief	Action du contrôleur réalisée pour valider une correction.
	 *
	 * Si l'identifiant du formulaire est connu, le document est créé, sinon une liste complète est affichée.
	 */
	public function validerAction() {
		
	}

}
