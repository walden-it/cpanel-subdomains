<?php
require_once "inc/configure.php";
require_once("inc/dbconfig.php");

function update_user($id_users,$status = "active")
{
	 global $db;
    $sql = "UPDATE users SET status='".$status."' WHERE id_users='".$id_users."' LIMIT 1" ;
	 $db->query($sql);	 
}


function get_users($status = "pending")
{
	 global $db;
   $sql = "SELECT * from users WHERE status='".$status."'" ;
	$dbquery = $db->prepare($sql);
	$dbquery->execute();
	$users = $dbquery->fetchAll();
	return($users);	
}

function copy_skel($user)
{
	
global $domain_root;
global $skel_dir;
$subdomain_dir = $domain_root."/".$user['subdomain'];

shell_exec("rsync -avz ".$skel_dir."/ ".$subdomain_dir."/");

}	
function create_mysql($user){
global $dsn;
global $opt;
global $db_user_root;
global $db_pass_root;
global $domain_root;

$subdomain_dir = $domain_root."/".$user['subdomain'];

$new_password = randomPassword();
$new_user = "dbuser_".$user['subdomain'];
$new_database = "db_".$user['subdomain'];

$db_admin = new PDO($dsn, $db_user_root, $db_pass_root, $opt);
$db_admin->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db_admin->query("CREATE DATABASE `".$new_database."`");
$db_admin->query("GRANT ALL ON `".$new_database."`.* TO `".$new_user."` IDENTIFIED BY '".$new_password."'");

$mycnf  = "[client]\n";
$mycnf .= "user = ".$new_user."\n";
$mycnf .= "password = ".$new_password."\n";
$mycnf .= "database = ".$new_database."\n";
$mycnf .= "host = localhost\n";

file_put_contents($subdomain_dir."/.my.cnf",$mycnf);

}


$users = get_users();
if(!empty($users)) {


$subdomains = array();

foreach($users as $user)
{

	$tmp_filename = "/tmp/cpanel_create_subdomains_".time();
	$subdomains[] = $domain.";".$user['subdomain'];
	$strSubdomains = implode($subdomains,"\n");
	
	/*
	file_put_contents($tmp_filename, $strSubdomains);
	shell_exec("php cpanel_subdomains.php ".$tmp_filename);
	unlink($tmp_filename);
*/

	mkdir($domain_root."/".$user['subdomain']);
	
	copy_skel($user);
			
	create_mysql($user);
		
	update_user($user['id_users']);	

}
	$strSubdomains = implode("\n",$subdomains);	

	
}
else {
	die("no requests to process");

	}
	
?>