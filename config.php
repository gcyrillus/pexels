<?php
	if(!defined('PLX_ROOT')) exit;
	/**
		* Plugin 			Banqued'image
		*
		* @CMS required		PluXml 
		* @page				config.php
		* @version			1.0
		* @date				2024-01-19
		* @author 			
		░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
		░       ░░  ░░░░░░░  ░░░░  ░  ░░░░  ░░      ░░       ░░░      ░░  ░░░░░░░        ░░      ░░░░░   ░░░  ░        ░        ░
		▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒  ▒▒  ▒▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒  ▒▒▒▒▒▒▒▒▒▒    ▒▒  ▒  ▒▒▒▒▒▒▒▒▒▒  ▒▒▒▒
		▓       ▓▓  ▓▓▓▓▓▓▓  ▓▓▓▓  ▓▓▓    ▓▓▓  ▓▓▓▓  ▓       ▓▓  ▓▓▓▓  ▓  ▓▓▓▓▓▓▓▓▓▓  ▓▓▓▓▓      ▓▓▓▓▓  ▓  ▓  ▓      ▓▓▓▓▓▓  ▓▓▓▓
		█  ███████  ███████  ████  ██  ██  ██  ████  █  ███████  ████  █  ██████████  ██████████  ████  ██    █  ██████████  ████
		█  ███████        ██      ██  ████  ██      ██  ████████      ██        █        ██      ██  █  ███   █        ████  ████
		█████████████████████████████████████████████████████████████████████████████████████████████████████████████████████████
	**/	
	# Control du token du formulaire
	plxToken::validateFormToken($_POST);
	
	# Liste des langues disponibles et prises en charge par le plugin
	$aLangs = array($plxAdmin->aConf['default_lang']);	
	
	if(!empty($_POST)) {
		
		$plxPlugin->setParam('apikey', $_POST['apikey'], 'string');
		
		$plxPlugin->saveParams();	
		header("Location: parametres_plugin.php?p=".basename(__DIR__));
		exit;
	}
	
	$var['imgHeight'] = $plxPlugin->getParam('imgHeight')=='' ? 'texte simple': $plxPlugin->getParam('imgHeight');
	# initialisation des variables propres à chaque lanque
	$langs = array();
	foreach($aLangs as $lang) {
		# chargement de chaque fichier de langue
		$langs[$lang] = $plxPlugin->loadLang(PLX_PLUGINS.'Banquedimage/lang/'.$lang.'.php');
		$var[$lang]['mnuName'] =  $plxPlugin->getParam('mnuName_'.$lang)=='' ? $plxPlugin->getLang('L_DEFAULT_MENU_NAME') : $plxPlugin->getParam('mnuName_'.$lang);
	}
	
	
	
	# affichage du wizard à la demande
	if(isset($_GET['wizard'])) {$_SESSION['justactivated'.basename(__DIR__)] = true;}
	# fermeture session wizard
	if (isset($_SESSION['justactivated'.basename(__DIR__)])) {
		unset($_SESSION['justactivated'.basename(__DIR__)]);
		$plxPlugin->wizard();
	}
	
?>
<link rel="stylesheet" href="<?php echo PLX_PLUGINS."Banquedimage/css/tabs.css" ?>" media="all" />
<p>utilise l'api des banques d'images Pexels et pexels pour rechercher, afficher et choisir une image à uploader sur votre PluXml.
La fonction Curl() est requise pour fonctionner.</p>	
<h2><?php $plxPlugin->lang("L_CONFIG") ?></h2>
<a href="parametres_plugin.php?p=<?= basename(__DIR__) ?>&wizard" class="aWizard"><img src="<?= PLX_PLUGINS.basename(__DIR__)?>/img/wizard.png" style="height:2em;vertical-align:middle" alt="Wizard"> Wizard</a>
<div id="tabContainer">
	<form action="parametres_plugin.php?p=<?= basename(__DIR__) ?>" method="post" >
		<div class="tabs">
			<ul>
				<li id="tabHeader_Param">Pexels Key</li>	
			</ul>
		</div>
		<div class="tabscontent">
			<div class="tabpage" id="tabpage_Param">	
				<fieldset><legend><?= $plxPlugin->getLang('L_YOUR_KEY') ?></legend>
					<p>
						<?php plxUtils::printInput('apikey',$plxPlugin->apikey,'text','50-60') ?>		
					</p>	
				</fieldset>
			</div>
			
			<fieldset>
				<p class="in-action-bar">
					<?php echo plxToken::getTokenPostMethod() ?><br>
					<input type="submit" name="submit" value="<?= $plxPlugin->getLang('L_SAVE') ?>"/>
				</p>
			</fieldset>
		</form>
	</div>
<script type="text/javascript" src="<?php echo PLX_PLUGINS."Banquedimage/js/tabs.js" ?>"></script>