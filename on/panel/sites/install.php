<?php

if( !defined('PROPER_START') )
{
	header("HTTP/1.0 403 Forbidden");
	exit;
}

$site = api::send('self/site/list', array('id'=> security::encode($_POST['id']) ));
$site = $site[0];

try
{
	$_GLOBALS['APP']['PASSWORD'] = random(15);
	api::send('self/database/add', array('type'=>'mysql', 'desc'=>'wordpress', 'pass'=> $_GLOBALS['APP']['PASSWORD'] ));
}
catch( Exception $e )
{
	$_SESSION['MESSAGE']['TYPE'] = 'error';
	$_SESSION['MESSAGE']['TEXT']= $lang['error'];
}
	
$content = file_get_contents( 'https://fr.wordpress.org/wordpress-4.1-fr_FR.zip' );
file_put_contents('ftp://'.$site['name'].':'.$_POST['pass'].'@ftp.olympe.in', $content);

if ( file_exists('ftp://'.$site['name'].':'.$_POST['pass'].'@ftp.olympe.in', $content) )
{
	$zip = new ZipArchive;
	$res = $zip->open('ftp://'.$site['name'].':'.$_POST['pass'].'@ftp.olympe.in/file.zip');
	if ($res === TRUE) 
	{
		 // extract it to the path we determined above
		  $zip->extractTo('ftp://'.$site['name'].':'.$_POST['pass'].'@ftp.olympe.in');
		  $zip->close();
		  
		  // delete zip file
		  unlink('ftp://'.$site['name'].':'.$_POST['pass'].'@ftp.olympe.in/file.zip');
		  
		  // redirect
		  header("Location: http://{$site['name']}.olympe.in/wordpress/wp-admin/setup-config.php");
		  return;
	} 
	else 
	  throw new SiteException('Internal Error', 400, 'File can not be extracted');
}
else
	throw new SiteException('Invalid argument', 400, 'File not uploaded');
	
function random($car) 
{
	$chaine = "abcdefghijklmnpqrstuvwxyABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; srand((double)microtime()*1000000);
	for($i=0; $i<$car; $i++) 
		$string .= $chaine[rand()%strlen($chaine)];
		
	return $string;
}

?>
