<?php
	if(!defined('PLX_ROOT')) exit; 
	/**
		* Plugin 			Banqued'image
		*
		* @CMS required		PluXml 
		* @page				-wizard.php
		* @version			1.0
		* @date				2024-01-19
		* @author 			
	**/		
	
	# pas d'affichage dans un autre plugin !	
	if(isset($_GET['p'])&& $_GET['p'] !== 'Pexels' ) {goto end;}
	
	# on charge la class du plugin pour y accéder
	$plxMotor = plxMotor::getInstance();
	$plxPlugin = $plxMotor->plxPlugins->getInstance( 'Pexels'); 
	
	# On vide la valeur de session qui affiche le Wizard maintenant qu'il est visible.
	if (isset($_SESSION['justactivatedPexels'])) {unset($_SESSION['justactivatedPexels']);}
	
	# initialisation des variables propres à chaque lanque 
	$langs = array();
	
	# initialisation des variables communes à chaque langue	
	$var = array();
	
	$var['imgHeight'] = $plxPlugin->getParam('imgHeight')=='' ? 'texte simple': $plxPlugin->getParam('imgHeight');
	
	#affichage
?>
<link rel="stylesheet" href="<?= PLX_PLUGINS ?>Pexels/css/wizard.css" media="all" />
<input id="closeWizard" type="checkbox">
<div class="wizard">	
	<div class="container">	
		<div class='title-wizard'>
			<h2><?= $plxPlugin->aInfos['title']?><br><?= $plxPlugin->aInfos['version']?></h2>
			<img src="<?php echo PLX_PLUGINS. 'Pexels'?>/icon.png">
			<div><q> Made in France by <?= $plxPlugin->aInfos['author']?> </q></div>
		</div>
		<p></p>
		
		<div id="tab-status">
			<span class="tab active">1</span>
		</div>		
		<form action="parametres_plugin.php?p=<?php echo 'Pexels' ?>"  method="post">
			<div role="tab-list">		
				<div role="tabpanel" id="tab1" class="tabpanel">
					<h2>Bienvenue dans l’extension <br><b style="font-family:cursive;color:crimson;font-variant:small-caps;font-size:2em;vertical-align:-.5rem;display:inline-block;"><?= $plxPlugin->aInfos['title']?></b></h2>
					<p>Cette extension vous permet d'accéder à une banque de plus d'<b  style="font-family:cursive;color:crimson;font-variant:small-caps;">1 million</b> d'images sur Pexels.com.</p>
					<p>La recherche se fait par mots clés, téléchargeable d'un simple clique , 
						le lien de l'image  ou la balise HTML pour l'afficher sont prêts à être copier/coller (en cliquant sur l'icone 📋)
					</p>
					<p><img src='/plugins/Pexels/img/PexelsSearch.png' alt='champ de recherche'></p>
				</div>	
				<div role="tabpanel" id="tab2" class="tabpanel hidden title">
					<h2>Configuration</h2>
				</div>	
				<div role="tabpanel" id="tab3" class="tabpanel hidden">
					<h2>préparation</h2>
					<p style="grid-column:2;grid-row:1/4;max-width:25vw;"><img src='/plugins/Pexels/img/rejoindre.png' alt='champ de recherche'></p>
					<p>Pour utiliser ce plugin, il vous faudra obtenir une clé gratuite depuis Pexels. 
						Cela est obligatoire et permet à Pexels de continuer à fonctionner en evitant
						les abus. Ex. Script ou robots qui tenterait de télécharger en continue le +million d'images disponibles.
						</p>
						<p>Pour obtenir votre clé immediatement, c'est sur Pexels: <a href="https://www.pexels.com/fr-fr/login/">Rejoindre Pexels</a>.</p>
				
				</div>		
				<div role="tabpanel" id="tabEnd" class="tabpanel hidden title">
					<h2>Finaliser</h2>
					<p></p>
					<p>Votre N° de clé: <?php plxUtils::printInput('apikey',$plxPlugin->apikey,'text','50-60') ?></p>
				</div>		
				<div class="pagination">
					<a class="btn hidden" id="prev"><?php $plxPlugin->lang('L_PREVIOUS') ?></a>
					<a class="btn" id="next"><?php $plxPlugin->lang('L_NEXT') ?></a>
					<?php echo plxToken::getTokenPostMethod().PHP_EOL ?>
					<button class="btn btn-submit hidden" id="submit"><?php $plxPlugin->lang('L_SAVE') ?></button>
					</div>
				</div>		
				</form>			
				<p class="idConfig">
				<?php
				if(file_exists(PLX_PLUGINS. 'Pexels/admin.php')) {echo ' 
				<a href="/core/admin/plugin.php?p= Pexels">Page d\'administration '. basename(__DIR__ ).'</a>';}
				if(file_exists(PLX_PLUGINS. 'Pexels/config.php')) {echo ' 	<a href="/core/admin/parametres_plugin.php?p=Pexels">Page de configuration  Banqued\'image</a>';}
				?>
				<label for="closeWizard"> Fermer </label>
				</p>	
				</div>	
				<script src="<?= PLX_PLUGINS ?>Pexels/js/wizard.js"></script>
				</div>
				<?php end: // FIN! ?>				
								