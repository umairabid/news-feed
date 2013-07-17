<?php
	//Edit this code with your database credentials
	$link = mysql_connect('localhost', 'root', ''); 
	if (!$link) { 
		die('Could not connect: ' . mysql_error()); 
	} 
	mysql_select_db('test', $link); 
?>