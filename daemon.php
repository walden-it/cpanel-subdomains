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

function create_cron($user)
{
	global $domain_root;
	global $skel_dir;
	
	$user_dir = $domain_root."/".$user['subdomain'];	
	$cron_location = "/etc/cron.d/cron_".$user['subdomain'];
	$cron_template = file_get_contents($skel_dir."/cron");
	$cron_data = str_replace("%%USER_DIR%%", $user_dir, $cron_template);
	file_put_contents($cron_location, $cron_data);	
	
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
global $skel_dir;
global $domain;

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

$config_file = file_get_contents($skel_dir."/admin/config.php");
$config_file = str_replace("%%DB_USER%%", $new_user, $config_file, &$count = null);
$config_file = str_replace("%%DB_PASS%%", $new_pass, $config_file, &$count = null);
$config_file = str_replace("%%DB_NAME%%", $new_database, $config_file, &$count = null);
$config_file = str_replace("%%FQDN%%", $user['subdomain'].$domain, $config_file, &$count = null);
$config_file = str_replace("%%DOMAIN_ROOT%%", $domain_root, $config_file, &$count = null);
$config_file = str_replace("%%SUBDOMAIN%%", $user['subdomain'], $config_file, &$count = null);

$admin_config_file = file_get_contents($skel_dir."/admin-config.php");
$admin_config_file = str_replace("%%DB_USER%%", $new_user, $admin_config_file, &$count = null);
$admin_config_file = str_replace("%%DB_PASS%%", $new_pass, $admin_config_file, &$count = null);
$admin_config_file = str_replace("%%DB_NAME%%", $new_database, $admin_config_file, &$count = null);
$admin_config_file = str_replace("%%FQDN%%", $user['subdomain'].$domain, $admin_config_file, &$count = null);
$admin_config_file = str_replace("%%DOMAIN_ROOT%%", $domain_root, $admin_config_file, &$count = null);
$admin_config_file = str_replace("%%SUBDOMAIN%%", $user['subdomain'], $admin_config_file, &$count = null);
unlink($subdomain_dir."/admin/config.php");

file_put_contents($subdomain_dir."/admin/config.php",$config_file);

shell("cat ".$skel_dir."/dump.sql |  mysql -u".$new_user." -p".$new_password." ".$new_database);


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
	
	create_cron($user);
		

}
	$strSubdomains = implode("\n",$subdomains);	

	
}
else {
	die("no requests to process");

	}
	
?>