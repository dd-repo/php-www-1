<?php

if( !defined('PROPER_START') )
{
	header("HTTP/1.0 403 Forbidden");
	exit;
}

api::send('account/del', array('user'=>$_POST['user'], 'domain'=>$_POST['domain'], 'account'=>$_POST['account']));
if( isset($_GET['redirect']) )
	template::redirect($_GET['redirect']);
else
	template::redirect('/admin/users/detail?id='.$_POST['user'].'#accounts');

?>