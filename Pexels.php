<?php if(!defined('PLX_ROOT')) exit;
	/**
		* Plugin 			Banque d'image Pexels
		*
		* @CMS required			PluXml 
		*
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
	class Pexels extends plxPlugin {
		
		
		
		const 	BEGIN_CODE = '<?php' . PHP_EOL;
		const 	END_CODE = PHP_EOL . '?>';
		public 	$lang = ''; 
		public 	$mediaFolder;
		public	$headers = [];
		public  $returnedDatas=array();
		public $apikey;
		
		
		public function __construct($default_lang) {
			# appel du constructeur de la classe plxPlugin (obligatoire)
			parent::__construct($default_lang);
			
		
			
			# droits pour accèder à la page config.php du plugin
			$this->setConfigProfil(PROFIL_ADMIN, PROFIL_MANAGER);
			
			
			$this->apikey = $this->getParam('apikey') ==''   ?   'votre clé' : $this->getParam('apikey') ;
			
			
			# Declaration des hooks		
			$this->addHook('AdminTopBottom', 'AdminTopBottom');
			$this->addHook('ThemeEndHead', 'ThemeEndHead');
			$this->addHook('Pexelswidget', 'Pexelswidget');
			$this->addHook('wizard', 'wizard');
			$this->addHook('AdminMediasFoot', 'AdminMediasFoot');
			$this->addHook('AdminPrepend','AdminPrepend');
			
			if(isset($_SESSION['medias'])) $this->mediaFolder=PLX_ROOT.'data/'.plxUtils::strCheck(basename($_SESSION['medias']).'/'.$_SESSION['folder']);
			else $this->mediaFolder=PLX_ROOT.'data/medias/';
		}
		
		# Activation / desactivation
		
		public function OnActivate() {
			# code à executer à l’activation du plugin
			# activation du wizard
			$_SESSION['justactivated'.basename(__DIR__)] = true;
		}
		
		public function OnDeactivate() {
			# code à executer à la désactivation du plugin
		}	
		
		
		public function ThemeEndHead() {
			#gestion multilingue
			if(defined('PLX_MYMULTILINGUE')) {		
				$plxMML = is_array(PLX_MYMULTILINGUE) ? PLX_MYMULTILINGUE : unserialize(PLX_MYMULTILINGUE);
				$langues = empty($plxMML['langs']) ? array() : explode(',', $plxMML['langs']);
				$string = '';
				foreach($langues as $k=>$v)	{
					$url_lang="";
					if($_SESSION['default_lang'] != $v) $url_lang = $v.'/';
					$string .= 'echo "\\t<link rel=\\"alternate\\" hreflang=\\"'.$v.'\\" href=\\"".$plxMotor->urlRewrite("?'.$url_lang.$this->getParam('url').'")."\" />\\n";';
				}
				echo '<?php if($plxMotor->mode=="'.$this->getParam('url').'") { '.$string.'} ?>';
			}
			
			
			// ajouter ici vos propre codes (insertion balises link, script , ou autre)
		}
		
		/**
			* Méthode qui affiche un message si le plugin n'a pas la langue du site dans sa traduction
			* Ajout gestion du wizard si inclus au plugin
			* @return	stdio
			* @author	Stephane F
		**/
		public function AdminTopBottom() {
			
			echo '<?php
			$file = PLX_PLUGINS."'.$this->plug['name'].'/lang/".$plxAdmin->aConf["default_lang"].".php";
			if(!file_exists($file)) {
			echo "<p class=\\"warning\\">'.basename(__DIR__).'<br />".sprintf("'.$this->getLang('L_LANG_UNAVAILABLE').'", $file)."</p>";
			plxMsg::Display();
			}
			?>';
			
			# affichage du wizard à la demande
			if(isset($_GET['wizard'])) {$_SESSION['justactivated'.basename(__DIR__)] = true;}
			# fermeture session wizard
			if (isset($_SESSION['justactivated'.basename(__DIR__)])) {
				unset($_SESSION['justactivated'.basename(__DIR__)]);
				$this->wizard();
			}
			
		}
		
		/**
			* Méthode statique qui affiche le widget
			*
		**/
		public static function Pexelswidget($widget=false) {
			include(PLX_PLUGINS.basename(__DIR__).'/widget.'.basename(__DIR__).'.php');
		}
		
		
		/** 
			* Méthode wizard
			* 
			* Descrition	: Affiche le wizard dans l'administration
			* @author		: G.Cyrille
			* 
		**/
		# insertion du wizard
		public function wizard() {
			# uniquement dans les page d'administration du plugin.
			if(basename(
			$_SERVER['SCRIPT_FILENAME']) 			=='parametres_plugins.php' || 
			basename($_SERVER['SCRIPT_FILENAME']) 	=='parametres_plugin.php' || 
			basename($_SERVER['SCRIPT_FILENAME']) 	=='plugin.php'
			) 	{	
				include_once(PLX_PLUGINS.__CLASS__.'/lang/'.$this->default_lang.'-wizard.php');
			}
		}
		

		
		

		
		
		/** 
			* Méthode AdminPrepend
			* 
			* Descrition	: HOOK
			* @author		: TheCrok
			* 
		**/
		public function AdminPrepend() {

			if(isset($_REQUEST['url']) && basename($_SERVER['PHP_SELF']) == 'medias.php'){

				$url=$_REQUEST['url'];;
				$folder=$this->mediaFolder;
				
				//set "max_execution_time" to 0 so you can copy large files
				ini_set('max_execution_time', 0);
				
				//require the downloadfile class
				//require_once("downloadFile-class.php");
				
				//create an instance of it
				//$save = new downloadFile();
				
				//get the size of the file to be copied
				$filesize = $this->getSize($url);
				if (isset($this->headers["content-disposition"][0]) and preg_match('/"([^"]+)"/', $this->headers["content-disposition"][0], $myFileName)) {
					$name = $myFileName[1];   
				}
				else {$name='';}
				//here is where we actually copy the file
				$returnData = $this->saveFile($url,$this->mediaFolder,$name);
				


			exit;// we got the json response end of the script!
			}

		}
		
		
		/** 
			* Méthode AdminMediasUpload
			* 
			* Descrition	:
			* @author		: TheCrok
			* 
		**/
		public function AdminMediasFoot() {
			# code à executer
			include('widget.Pexels.php');	
		}


		public function saveFile($url,$dir,$name=''){
			$urlB = $url;	
			//remove the query string and get the file name
			if ($url = parse_url($url)) {
				$cleanUrl = $url['scheme'].$url['host'].$url['path'];
			}
			//get the pathinfo() of the url
			$cleanUrl = pathinfo($cleanUrl);
			
			//get the short file name from headers if there
			$name= $name	=='' ? $cleanUrl['basename'] 				: $name;	
			//$name = $cleanUrl['basename'];
			//check if the directory exists and create a new directory if it does not
			if(!file_exists($dir)){
				mkdir($dir);
			}
			//check if the file exists and prepend a timestamp to its name if it does
			if(file_exists($this->mediaFolder.$name)){$name = time()."-".$name;}
			
			//create a new file where its contents will be dumped
			$fp = fopen ($this->mediaFolder.$name, 'w+');
			
			//Here is the file we are downloading, replace spaces with %20
			$ch = curl_init(str_replace(" ","%20",$urlB));
			
			curl_setopt($ch, CURLOPT_TIMEOUT, 50);
			//disable ssl cert verification to allow copying files from HTTPS NB: you can always fix your php 'curl.cainfo' setting so yo dont have to disable this 
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			// write curl response to file
			curl_setopt($ch, CURLOPT_FILE, $fp); 
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
			
			// get curl response
			$exec = curl_exec($ch); 
			
			curl_close($ch);
			fclose($fp);
			error_reporting(E_ERROR | E_PARSE); // remove errors for older PluXMl version not readt php 8.X
			plxUtils::makeThumb($this->mediaFolder.$name, plxUtils::thumbName($this->mediaFolder.$name), '220', '180', 90);
			if($exec == true){
				$returnData[0] = true;
				}else{
				$returnData[0] =false;
			}
			$returnData[1] = $dir;	
			$returnData[2] = $url;
			$returnData[3] = $name;
			$returnData[4] = $dir.$name;
			$returnData[5] = 'data/'.plxUtils::strCheck(basename($_SESSION['medias']).'/'.$_SESSION['folder']).$name;

			echo json_encode($returnData);

		}


		public function getSize($url) {
			// Assume failure.
			$result = -1;
			$curl = curl_init( $url );
			// Issue a HEAD request and follow any redirects.
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt( $curl, CURLOPT_NOBODY, true );
			curl_setopt( $curl, CURLOPT_URL, $url);
			curl_setopt( $curl, CURLOPT_HEADER, true );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			
			// this function is called by curl for each header received
			//  https://stackoverflow.com/a/41135574/2442099
			curl_setopt($curl, CURLOPT_HEADERFUNCTION,
			function($curl, $header) use (&$headers)
			{
				$len = strlen($header);
				$header = explode(':', $header, 2);
				if (count($header) < 2) // ignore invalid headers
				return $len;
				
				$this->headers[strtolower(trim($header[0]))][] = trim($header[1]);
				return $len;				
			}
			
			);			
			curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, true );
			curl_setopt( $curl, CURLOPT_USERAGENT,  $_SERVER['HTTP_USER_AGENT']);

			$data = curl_exec( $curl );
			curl_close( $curl );
			if( $data ) {
				$content_length = "unknown";
				$status = "unknown";
				if( preg_match( "/^HTTP\/1\.[01] (\d\d\d)/", $data, $matches ) ) {
					$status = (int)$matches[1];
				}
				if( preg_match( "/Content-Length: (\d+)/", $data, $matches ) ) {
					$content_length = (int)$matches[1];
				}
				if( $status == 200 || ($status > 300 && $status <= 308) ) {
					$result = $content_length;
				}
			}
			return $result;
		}

	}		