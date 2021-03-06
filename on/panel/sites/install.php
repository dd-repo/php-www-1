<?php

	if( !defined('PROPER_START') )
	{
		header("HTTP/1.0 403 Forbidden");
		exit;
	}

	$site = api::send('self/site/list', array('id'=> security::encode($_POST['id']) ));
	$site = $site[0];

	$database = api::send('self/database/list');
	$me = api::send('self/whoami', array('quota'=>true))[0];
	
	if( !isset($_POST['sql']) || empty($_POST['sql']) )	
		$_GLOBALS['APP']['PASSWORD'] = random( rand(15, 20) );
	else
		$_GLOBALS['APP']['PASSWORD'] = security::encode( $_POST['sql'] );
		
	if( $_POST['path'] == 1 )
		$_GLOBALS['APP']['PATH'] = '/folder';
	else
		$_GLOBALS['APP']['PATH'] = '';
		
	switch ( $_POST['type'] ) {
		case 'wordpress':			
			$_GLOBALS['APP']['NAME'] = "wordpress";
			$_GLOBALS['APP']['VERSION'] = "4.1";
			$_GLOBALS['REDIRECT']['HTTPS'] = true;
			break;
		case 'joomla':
			$_GLOBALS['APP']['NAME'] = "joomla";
			$_GLOBALS['APP']['VERSION'] = "3.4";
			$_GLOBALS['REDIRECT']['HTTPS'] = false;
			break;
		case 'drupal':
			$_GLOBALS['APP']['NAME'] = "drupal";
			$_GLOBALS['APP']['VERSION'] = "Unknown";
			$_GLOBALS['REDIRECT']['HTTPS'] = false;
			break;
		case 'fluxbb':
			$_GLOBALS['APP']['NAME'] = "fluxbb";
			$_GLOBALS['APP']['VERSION'] = "Unknown";
			$_GLOBALS['REDIRECT']['HTTPS'] = false;
			break;
	}
	
	$_GLOBALS['REDIRECT']['HTTPS'] ? $_GLOBALS['REDIRECT']['HTTPS'] = 'https' : $_GLOBALS['REDIRECT']['HTTPS'] = 'http';
	$_GLOBALS['APP']['SITE'] =  $site;
		
	/* ================ CLEAN UNUSED DATABASES ================ */
	
	foreach( $database as $d )
	{
		if ( ( empty( $d['size'] ) || $d['size']  == 0 ) && ( $d['desc'] == 'wordpress' || $d['desc'] == 'joomla') )
		{
			api::send('self/database/del', array( 'database'=>  $d['name'] ));
			$count++;
		}
	}
	
	if ( $me['quotas'][2]['used'] >= $me['quotas'][2]['max'] )
		if ( $count <= 0 )
			throw new SiteException('Please remove one of your databases ', 400, 'quota reached');

	$install_date = date("Y-m-d");
	$db_description = $_GLOBALS['APP']['NAME']. " - autoinstalled - {$install_date}";
			
	$new = api::send('self/database/add', array('type'=>'mysql', 'desc'=> $db_description, 'pass'=> $_GLOBALS['APP']['PASSWORD'] ));
	$database = api::send( 'self/database/list', array( 'database' => $new['name'] ) )[0];
		
		// write config file on remote directory
		$conf = "
		; This is a configuration file linked to the quick installation
		; It has been automatically generated
		; #### PLEASE DO NOT REMOVE ####
		
		[CONFIG]
		cms = '".$_GLOBALS['APP']['NAME']."'
		version = '".$_GLOBALS['APP']['VERSION']."'
		directory = '".$_GLOBALS['APP']['PATH']."'
		database = '{$database['name']}'
		install_date = '{$install_date}'
		
		";
	
	$unzip = file_get_contents( __DIR__.'/unzip.php' );
	$unzip = str_replace("##PATH##", $_GLOBALS['APP']['PATH'], $unzip);
	$unzip = str_replace("##FILE##", $conf, $unzip);
	
	$_GLOBALS['_FILE']['UNZIP'] = $unzip;
	
	$_push = array ( 'unzip' => $_GLOBALS['_FILE']['UNZIP'],
					 'connect' => $_POST['pass'],
					 'site' => $_GLOBALS['APP']['SITE'],
					 'path' => $_GLOBALS['APP']['PATH'],
					 'type' => $_GLOBALS['APP']['NAME'],
					 'database' => array ( 'name' => $database['name'], 'server' => $database['server'], 'password' => $_GLOBALS['APP']['PASSWORD'] )
					 );
	
	$_push = serialize ( $_push );
	
	$ch = curl_init();
	curl_setopt( $ch, CURLOPT_URL, 'https://on.olympe.in/api.php');
	curl_setopt( $ch, CURLOPT_POST, 1 );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, 'array='.$_push );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true);
	
	$get = curl_exec( $ch );
	curl_close( $ch );
		
	if ( $get == "^_^" )
	{
		sleep( 2 );
		header( "Location: ". $_GLOBALS['REDIRECT']['HTTPS']."://".$site['name'].".olympe.in".$_GLOBALS['APP']['PATH'] );
	}
	else
	{
		$_SESSION['MESSAGE']['TYPE'] = 'error';
		$_SESSION['MESSAGE']['TEXT']= $get;	
		$template->redirect('/panel/sites/config?id=' . security::encode($_POST['id']));
	}
	
	function random($length = 15) 
	{
			$characters = "abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
			$charactersLength = strlen($characters);
			for ($i = 0; $i < $length; $i++) {
				$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
	} 
	
	

?>
