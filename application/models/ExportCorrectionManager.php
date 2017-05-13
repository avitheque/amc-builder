<?php
/**
 * @brief	Classe de gestion d'exportation de la correction du formulaire QCM au format PDF.
 *
 * Cette classe permet de faire un apperçu du questionnaire complet avec les réponses dans l'ordre de création.
 *
 * @li ATTENTION : Le document généré n'est pas au format UTF-8 !!!
 *
 * Étend la classe abstraite DocumentManager.
 * @see			{ROOT_PATH}/libraries/models/DocumentManager.php
 *
 * @name		ExportFormulaireManager
 * @category	Model
 * @package		Main
 * @subpackage	Application
 * @author		durandcedric@avitheque.net
 * @update		$LastChangedBy: durandcedric $
 * @version		$LastChangedRevision: 5 $
 * @since		$LastChangedDate: 2017-03-02 22:16:57 +0100 (jeu., 02 mars 2017) $
 *
 * Copyright (c) 2015-2017 Cédric DURAND (durandcedric@avitheque.net)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 */
class ExportCorrectionManager extends DocumentManager {

	/**
	 * @brief	Constantes du format de création des réponses.
	 *
	 * @var		string
	 */
	const PAGE_HEADER_TITRE					= "CORRECTION QCM";								// Titre de l'entête de la page
	const PAGE_FORMULAIRE_TITRE_DEFAUT		= "Formulaire sans nom";						// Titre du formulaire par défaut
	const PAGE_LIMIT_PERCENT				= 82;											// Pourcentage d'occupation de la page avant passage à la page suivante
	const REPONSE_EMPTY						= "Aucune de ces réponses n'est correcte";		// Aucune réponse n'est valide parmi celles proposées
	const REPONSE_LIBRE						= "Saisie libre";								// Indicateur de réponse libre
	const REPONSE_LIBRE_CORRECTION			= "RÉPONSE ATTENDUE :";							// Titre de la correction de la réponse libre
	const REPONSE_BOX_WIDTH					= 5;											// Largeur de la case à cocher
	const REPONSE_CHECKED_PADDING			= 1;											// Espacement du bord interne de la case à cocher

	/**
	 * @brief	Constantes de messages d'avertissements.
	 *
	 * @var		string
	 */
	const ERROR_ENONCE						= "ATTENTION !\nVeuillez renseigner l'énoncé, sinon cette question ne sera pas prise en compte dans le questionnaire !";
	const ERROR_REPONSE						= "ATTENTION !\nVeuillez renseigner les réponses, sinon cette question ne sera pas prise en compte dans le questionnaire !";

	/**
	 * @brief	Variables de construction du formulaire.
	 *
	 * @var		string|integer|array
	 */
	private $_aQCM							= array();

	/**
	 * @brief	Variables de construction du PDF.
	 *
	 * @var		PDFManager
	 */
	private $nLineWidth						= 0;
	private $nFontSizePt					= 0;

	/**
	 * @brief	Constructeur de la classe.
	 *
	 * @li	Parcours du formulaire HTML du type :
	 * @code
	 * 	$aQCM = array(
	 * 		// GÉNÉRALITÉS ************************************************************************
	 * 		'formulaire_id'					=> "Identifiant du questionnaire (en BDD)",
	 * 		'formulaire_titre'				=> "Nom du questionnaire",
	 * 		'formulaire_validation'			=> "Mise en validation du questionnaire",
	 * 		'formulaire_presentation'		=> "Présentation du questionnaire",
	 * 		'formulaire_domaine'			=> "Identifiant du domaine du formulaire (en BDD)",
	 * 		'formulaire_sous_domaine'		=> "Identifiant du sous-domaine du formulaire en (BDD)",
	 * 		'formulaire_categorie'			=> "Identifiant de la catégorie du formulaire en (BDD)",
	 * 		'formulaire_sous_categorie'		=> "Identifiant de la sous-catégorie du formulaire (en BDD)",
	 * 		'formulaire_note_finale'		=> "Note du questionnaire, par défaut sur 20 points",
	 * 		'formulaire_penalite'			=> "Facteur de pénalité pour une mauvaise réponse aux questions à choix multiple",
	 * 		'formulaire_nb_max_reponses'	=> "Nombre de réponses maximum par question",
	 * 		'formulaire_nb_total_questions'	=> "Nombre total de questions",
	 *
	 * 		// QUESTIONNAIRE **********************************************************************
	 * 		'question_id'					=> array(
	 * 				0	=> "Identifiant de la première question (en BDD)",
	 * 				1	=> "Identifiant de la deuxième question (en BDD)",
	 * 				...
	 * 				N-1	=> "Identifiant de la Nième question (en BDD)"
	 * 		),
	 * 		'question_titre'				=> array(
	 * 				0	=> "Titre de la première question",
	 * 				1	=> "Titre de la deuxième question",
	 * 				...
	 * 				N-1	=> "Titre de la Nième question"
	 * 		),
	 * 		'question_stricte'				=> array(
	 * 				0	=> "Attente d'une réponse stricte à la première question",
	 * 				1	=> "Attente d'une réponse stricte à la deuxième question",
	 * 				...
	 * 				N-1	=> "Attente d'une réponse stricte à la Nième question"
	 * 		),
	 * 		'question_bareme'				=> array(
	 * 				0	=> "Barème attribué à la première question",
	 * 				1	=> "Barème attribué à la deuxième question",
	 * 				...
	 * 				N-1	=> "Barème attribué à la Nième question"
	 * 		),
	 * 		'question_enonce'				=> array(
	 * 				0	=> "Énoncé de la première question",
	 * 				1	=> "Énoncé de la deuxième question",
	 * 				...
	 * 				N-1	=> "Énoncé de la Nième question"
	 * 		),
	 *
	 * 		// RÉPONSES ***************************************************************************
	 * 		'reponse_id'					=> array(
	 * 				// Réponses concernant la 1ère question
	 * 				0	=> array(
	 * 						0	=>	"Identifiant de la première réponse (en BDD)",
	 * 						1	=>	"Identifiant de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Identifiant de la dernière réponse (en BDD)"
	 * 				),
	 * 				// Réponses concernant la 2ème question
	 * 				1	=> array(
	 * 						0	=>	"Identifiant de la première réponse (en BDD)",
	 * 						1	=>	"Identifiant de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Identifiant de la dernière réponse (en BDD)"
	 * 				),
	 * 				...
	 * 				// Réponses concernant la Nème question
	 * 				N-1	=> array(
	 * 						0	=>	"Identifiant de la première réponse (en BDD)",
	 * 						1	=>	"Identifiant de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Identifiant de la dernière réponse (en BDD)"
	 * 				)
	 * 		),
	 * 		'reponse_texte'					=> array(
	 * 				// Réponses concernant la 1ère question
	 * 				0	=> array(
	 * 						0	=>	"Texte de la première réponse (en BDD)",
	 * 						1	=>	"Texte de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Texte de la dernière réponse (en BDD)"
	 * 				),
	 * 				// Réponses concernant la 2ème question
	 * 				1	=> array(
	 * 						0	=>	"Texte de la première réponse (en BDD)",
	 * 						1	=>	"Texte de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Texte de la dernière réponse (en BDD)"
	 * 				),
	 * 				...
	 * 				// Réponses concernant la Nème question
	 * 				N-1	=> array(
	 * 						0	=>	"Texte de la première réponse (en BDD)",
	 * 						1	=>	"Texte de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Texte de la dernière réponse (en BDD)"
	 * 				)
	 * 		),
	 * 		'reponse_valide'		=> array(
	 * 				// Réponses concernant la 1ère question
	 * 				0	=> array(
	 * 						0	=>	"Validation de la première réponse (en BDD)",
	 * 						1	=>	"Validation de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Validation de la dernière réponse (en BDD)"
	 * 				),
	 * 				// Réponses concernant la 2ème question
	 * 				1	=> array(
	 * 						0	=>	"Validation de la première réponse (en BDD)",
	 * 						1	=>	"Validation de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Validation de la dernière réponse (en BDD)"
	 * 				),
	 * 				...
	 * 				// Réponses concernant la Nème question
	 * 				N-1	=> array(
	 * 						0	=>	"Validation de la première réponse (en BDD)",
	 * 						1	=>	"Validation de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Validation de la dernière réponse (en BDD)"
	 * 				)
	 * 		),
	 * 		'reponse_valeur'		=> array(
	 * 				// Réponses concernant la 1ère question
	 * 				0	=> array(
	 * 						0	=>	"Valeur de la première réponse (en BDD)",
	 * 						1	=>	"Valeur de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Valeur de la dernière réponse (en BDD)"
	 * 				),
	 * 				// Réponses concernant la 2ème question
	 * 				1	=> array(
	 * 						0	=>	"Valeur de la première réponse (en BDD)",
	 * 						1	=>	"Valeur de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Valeur de la dernière réponse (en BDD)"
	 * 				),
	 * 				...
	 * 				// Réponses concernant la Nème question
	 * 				N-1	=> array(
	 * 						0	=>	"Valeur de la première réponse (en BDD)",
	 * 						1	=>	"Valeur de la deuxième réponse (en BDD)",
	 * 						...
	 * 						D-1	=>	"Valeur de la dernière réponse (en BDD)"
	 * 				)
	 * 		)
	 * );
	 * @endcode
	 *
	 * @param	array	$aQCM				: tableau de construction du formulaire QCM.
	 * @return	void
	 */
	public function __construct(array $aQCM = array()) {
		// Initialisation de la variable d'instance
		$this->_aQCM = $aQCM;

		// Récupération du titre du formulaire
		$this->sTitre		= DataHelper::get($this->_aQCM, "formulaire_titre", DataHelper::DATA_TYPE_PDF, self::PAGE_FORMULAIRE_TITRE_DEFAUT, true);

		// Initialisation des paramètres de l'export
		$this->setContentType("application/force-download");
		$this->setExtension("pdf");
		$this->setFilename($this->sTitre);

		// Initialisation du PDF
		$this->_document	= new PDFManager();

		// Construction du bas de page
		$this->nPage		= 1;
		$this->_buildNewPage();

		$this->_document->setTextColor(255, 0, 0);
		$this->_document->title(self::PAGE_HEADER_TITRE, 40);
		$this->_document->addLine();
		$this->_document->setTextColor(0, 0, 0);

		// Récupération des paramètres PDF
		$this->nLineWidth	= $this->_document->getLineWidth();
		$this->nFontSizePt	= $this->_document->getFontSizePt();

		// Initialisation de la position de la première cellule
		$this->_document->setY(25);

		// Création de la cellule
		$this->_document->setFillColor(230, 230, 230);
		$this->_document->setFontSize($this->nFontSizePt * 2);
		$this->_document->setFontSize(20);
		$this->_document->setFontStyle(PDFManager::STYLE_BOLD);
		$this->_document->addCell($this->nLineWidth, $this->nFontSizePt, $this->sTitre, 1, PDFManager::ALIGN_CENTER, true);
		$this->_document->addCell($this->nLineWidth, $this->nFontSizePt, "");
		$this->_document->setFontSize($this->nFontSizePt);
		$this->_document->setFontStyle(PDFManager::STYLE_DEFAULT);

		// Initialisation de la position de la première cellule
		$this->_document->setY(50);

		// Initialisation du numéro de ligne
		$this->nLine		= 0;
		for ($nQuestion = 0 ; $nQuestion < $this->_aQCM['formulaire_nb_total_questions'] ; $nQuestion++) {
			// Ajout d'une cellule vide
			$this->_document->addCell($this->nLineWidth, ($this->nFontSizePt / 2), "");

			// Fonctionnalité réalisée si la question arrive en bas de page
			if (($this->_document->getY() * 100) / $this->_document->getPageHeight() > self::PAGE_LIMIT_PERCENT) {
				// Construction du bas de page
				$this->_buildNewPage();

				// Réinitialisation du numéro de ligne
				$this->nLine = 0;
			}

			// Fonctionnalité réalisée si la ligne n'est pas la première
			if ($this->nLine > 0) {
				// Ajout d'une ligne horizontale entre les questions
				$this->_document->line($this->_document->getLeftMargin(), $this->_document->getY(), $this->_document->getLineWidth() + $this->_document->getRightMargin(), $this->_document->getY());
				// Ajout d'une cellule vide
				$this->_document->addCell($this->nLineWidth, ($this->nFontSizePt / 2), "");
			}

			// Construction du questionnaire
			$this->_buildQuestion($nQuestion);

			// Passage à la ligne suivante
			$this->nLine++;
		}
	}

	/**
	 * @brief	Construction d'une nouvelle page.
	 *
	 * @return	void
	 */
	private function _buildNewPage() {
		// Ajout d'une page
		$this->_document->addPage();

		// Initialisation du style de police
		$this->_document->setFontStyle(PDFManager::STYLE_ITALIC);

		// Modification de la couleur de police
		$this->_document->setTextColor(100, 100, 100);

		// Position en bas de page
		$this->_document->setY($this->_document->getPageHeight() - $this->_document->getTopMargin() - $this->_document->getBottomMargin());

		// Ajout du bas de page
		$this->_document->setX($this->_document->getLeftMargin());
		$this->_document->cell($this->nLineWidth, $this->nFontSizePt, utf8_decode($this->sTitre));

		// Position en bas de page
		$sString = utf8_decode("Page n°" . $this->nPage);
		$this->_document->setX($this->_document->getPageWidth() - strlen($sString) * $this->_document->getFontSize());
		$this->_document->cell(strlen($sString), $this->nFontSizePt, $sString);

		// Rétablissement de la couleur de police par défaut
		$this->_document->setTextColor(0, 0, 0);

		// Rétablissement du style de police par défaut
		$this->_document->setFontStyle(PDFManager::STYLE_DEFAULT);

		// Positionnement en début de page
		$this->_document->setX($this->_document->getLeftMargin());
		$this->_document->setY($this->_document->getTopMargin());
		$this->nPage++;
	}

	/**
	 * @brief	Construction de la réponse.
	 *
	 * @li La construction est annulée si aucune réponse n'est rédigée.
	 *
	 * @param	boolean	$bChecked			: case cochée ou non.
	 * @param	string	$sTexte				: texte de la réponse.
	 * @return	void
	 */
	private function _buildReponse($bChecked = false, $sTexte = '') {
		// Fonctionnalité réalisé si le texte est renseigné correctement
		if (! empty($sTexte)) {
			// Le texte de la réponse est renseigné
			$this->_document->setX(20);

			// Dessin de la case à cocher
			$this->_document->Line($this->_document->getX(), $this->_document->getY(), $this->_document->getX() + self::REPONSE_BOX_WIDTH, $this->_document->getY());
			$this->_document->Line($this->_document->getX(), $this->_document->getY(), $this->_document->getX(), $this->_document->getY() + $this->_document->getFontSizePt() / 2);
			$this->_document->Line($this->_document->getX(), $this->_document->getY() + $this->_document->getFontSizePt() / 2, $this->_document->getX() + self::REPONSE_BOX_WIDTH, $this->_document->getY() + $this->_document->getFontSizePt() / 2);
			$this->_document->Line($this->_document->getX() + self::REPONSE_BOX_WIDTH, $this->_document->getY(), $this->_document->getX() + self::REPONSE_BOX_WIDTH, $this->_document->getY() + $this->_document->getFontSizePt() / 2);

			// Fonctionnalité réalisée si la réponse est valide
			if ($bChecked) {
				// Initialisation de la couleur du dessin
				$this->_document->setDrawColor(0, 0, 255);
				// Coloration de l'intérieur de la case
				for ($i = 0 ; $i < ($this->_document->getFontSizePt() / 2 - (self::REPONSE_CHECKED_PADDING * 2)) ; $i += 0.1) {
					// Ligne colorée à l'intérieur de la case
					$this->_document->Line($this->_document->getX() + self::REPONSE_CHECKED_PADDING, $this->_document->getY() + self::REPONSE_CHECKED_PADDING + $i, $this->_document->getX() + self::REPONSE_BOX_WIDTH - self::REPONSE_CHECKED_PADDING, $this->_document->getY() + $i + self::REPONSE_CHECKED_PADDING);
				}
				// Réinitialisation de la couleur de dessin
				$this->_document->setDrawColor(0, 0, 0);
			}

			// Ajout du texte de la réponse
			$this->_document->setX(20 + self::REPONSE_BOX_WIDTH);
			$this->_document->addCell($this->nLineWidth - $this->_document->getRightMargin(), ($this->nFontSizePt / 2), $sTexte, 0, PDFManager::ALIGN_JUSTIFY);
		}
	}

	/**
	 * @brief	Construction de la question.
	 *
	 * @li	Si aucune réponse n'est valide, une nouvelle case est automatiquement créée.
	 *
	 * @param	integer	$nQuestion			: occurrence de la question.
	 * @return	void
	 */
	private function _buildQuestion($nQuestion) {
		// Ajout de la réponse attendue
		$this->_document->setX($this->_document->getLeftMargin());

		// Identifiant de la question
		$idQuestion		= sprintf(LatexFormManager::DOCUMENT_QUESTIONS_ID, intval($nQuestion + 1));

		// Titre de la question
		$sTitre			= DataHelper::get($this->_aQCM['question_titre'],		$nQuestion, DataHelper::DATA_TYPE_PDF);
		// Énoncé de la question
		$sEnonce		= DataHelper::get($this->_aQCM['question_enonce'],		$nQuestion, DataHelper::DATA_TYPE_PDF);
		// Texte de la réponse
		$sCorrection	= DataHelper::get($this->_aQCM['question_correction'],	$nQuestion,	DataHelper::DATA_TYPE_PDF);
		// Attente d'une réponse stricte à la question
		$bStricte		= DataHelper::get($this->_aQCM['question_stricte'],		$nQuestion, DataHelper::DATA_TYPE_BOOL);
		// Attente d'une réponse libre à la question
		$bLibre			= DataHelper::get($this->_aQCM['question_libre'],		$nQuestion, DataHelper::DATA_TYPE_BOOL);
		// Barème de la question
		$fBareme		= DataHelper::get($this->_aQCM['question_bareme'],		$nQuestion,	DataHelper::DATA_TYPE_PDF);

		// Ajout du barème à la question
		$idQuestion		.= sprintf('%10s (%s pt)', " ", $fBareme);

		// Fonctionnalité réalisée si la saisie est libre
		if ($bLibre) {
			// Ajout d'un indicateur de saisie libre
			$idQuestion	.= " - " . self::REPONSE_LIBRE;
		}

		// Fonctionnalité réalisée si l'énoncé est VIDE
		if (empty($sEnonce)) {
			// Affichage d'un message d'avertissement afin de préciser que l'énoncé est vide
			$this->_document->setTextColor(255, 0, 0);
			$this->_document->addCell($this->nLineWidth, ($this->nFontSizePt / 2), utf8_decode(self::ERROR_ENONCE), 0, PDFManager::ALIGN_CENTER);
			$this->_document->setTextColor(0, 0, 0);

			// STOP !
			return false;
		}

		// Création de la cellule
		$this->_document->setFontStyle(PDFManager::STYLE_BOLD_ITALIC);
		$this->_document->addCell($this->nLineWidth, ($this->nFontSizePt / 2), $idQuestion);
		$this->_document->setFontStyle(PDFManager::STYLE_DEFAULT);

		// Extraction des lignes de l'énoncé
		$aEnonce		= explode("\n", $sEnonce);
		// Parcours de chaque ligne de l'énoncé
		foreach ($aEnonce as $sLigneEnonce) {
			// Ajout de la ligne à l'énoncé
			$this->_document->addCell($this->nLineWidth, ($this->nFontSizePt / 2), trim($sLigneEnonce), 0, PDFManager::ALIGN_JUSTIFY);
		}

		// Fonctionnalité réalisée si la question est à réponse LIBRE
		if ($bLibre) {
			// Ajout de la réponse attendue
			$this->_document->setTextColor(255, 0, 0);
			$this->_document->addCell($this->nLineWidth, ($this->nFontSizePt / 2));
			$this->_document->setX($this->_document->getLeftMargin() * 2);
			$this->_document->setFontStyle(PDFManager::STYLE_BOLD_UNDERLINE);
			$this->_document->addCell($this->nLineWidth, ($this->nFontSizePt / 2), self::REPONSE_LIBRE_CORRECTION);
			$this->_document->setFontStyle(PDFManager::STYLE_ITALIC);
			$this->_document->setX($this->_document->getLeftMargin() * 2);

			// Respect des lignes
			$aCorrection = explode("\n", $sCorrection);
			foreach ($aCorrection as $sCorrection) {
				// Suppression des caractères [espace] superflus
				$sCorrection = trim($sCorrection);
				if (!empty($sCorrection)) {
					$this->_document->setX($this->_document->getLeftMargin() * 2);
					$this->_document->addCell($this->nLineWidth, ($this->nFontSizePt / 2), sprintf("%10s %s", " ", $sCorrection));
				}
			}

			$this->_document->setTextColor(0, 0, 0);
			$this->_document->setFontStyle(PDFManager::STYLE_DEFAULT);
		} else {
			// Boucle de parcours des réponses
			$nCount = 0;
			$nChoice = 0;
			for ($nReponse	= 0 ; $nReponse < $this->_aQCM['formulaire_nb_max_reponses'] ; $nReponse++) {
				// Ajout de la réponse attendue
				$this->_document->setX($this->_document->getLeftMargin() * 2);

				// Initialisation du status de la réponse avec un caractère d'indicateur de `bonne` ou `mauvaise` réponse
				$bChecked				= empty($this->_aQCM['reponse_valide'][$nQuestion][$nReponse])	?	false	: true;
				$nChoice				+= $bChecked													?	1		: 0;

				// Texte de la réponse
				$sTexte					= DataHelper::get($this->_aQCM['reponse_texte'][$nQuestion],		$nReponse,	DataHelper::DATA_TYPE_PDF,	null);

				if (!empty($sTexte)) {
					$nCount++;
					// Construction de la réponse
					$this->_buildReponse($bChecked, $sTexte);
				}
			}

			// Fonctionnalité réalisée si aucun texte de réponse n'est trouvé
			if (empty($nChoice) && empty($nCount)) {
				// Affichage d'un message d'avertissement afin de préciser qu'aucun choix de réponse n'est proposé
				$this->_document->setTextColor(255, 0, 0);
				$this->_document->addCell($this->nLineWidth, ($this->nFontSizePt / 2), self::ERROR_REPONSE, 0, PDFManager::ALIGN_CENTER);
				$this->_document->setTextColor(0, 0, 0);

				// STOP !
				return false;
			}

			// Fonctionnalité réalisée si aucune réponse n'est valide
			if (empty($nChoice)) {
				$this->_buildReponse(true, self::REPONSE_EMPTY);
			}
		}
	}

}