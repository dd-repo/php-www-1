<?php

	if( !defined('PROPER_START') )
	{
		header("HTTP/1.0 403 Forbidden");
		exit;
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
	
	/* recreate banned website */
	$new_password = random( rand(15, 20) );
	$banned_sites_user_id = '119354';
	$site_name = $_POST['sitename'];
	
	api::send('site/del', array('user'=>$_POST['user'], 'site'=>$_POST['site']));
	
	
	/* display banned page */
	$htaccess = __DIR__.'/404/.htaccess';
	$font = __DIR__.'/404/BebasNeue Regular.ttf';
	
	/*
	$index = file_get_contents( __DIR__.'/404/index.html' );
	$index = str_replace("**DATE**", date("F j, Y, g:i a"), $index);
	$index = str_replace("**EXPLAIN**", empty($_POST['explain'])?'No reason provided':htmlentities($_POST['explain']), $index);
	*/
	
	sleep(2);
	api::send('site/insert', array('site'=>$site_name, 'user'=>$banned_sites_user_id, 'pass'=>$new_password));
	sleep(10);
	
	$connection = ssh2_connect('ftp.olympe.in', 22);
	ssh2_auth_password( $connection, $site_name, $new_password );
	ssh2_scp_send($connection, $htaccess, '/.htaccess', 0644);
	ssh2_scp_send($connection, $font, '/BebasNeue Regular.ttf', 0644);
	/*ssh2_scp_send($connection, $index, '/index.html', 0644);*/
	
	
	if( isset($_GET['redirect']) )
		template::redirect($_GET['redirect']);
	else
		template::redirect('/admin/users/detail?id='.$_POST['user'].'#sites');


?>