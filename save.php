<?php
require_once "inc/recaptchalib.php";
require_once "inc/configure.php";
require_once("inc/dbconfig.php");


if(isset($_POST) && ! empty($_POST))
{
	$reCaptcha = new ReCaptcha($captcha_secret);

	$response = $reCaptcha->verifyResponse(
        $_SERVER["REMOTE_ADDR"],
        $_POST["g-recaptcha-response"]
    );
    
    if ($response != null && $response->success) {
    	
    	
    	if(isset($_POST['code']) && !empty($_POST['code']))
    	{
			if(verify_code($_POST['code']))
			{
					if(check_subdomain($_POST['subdomain']))
					{
							save($_POST);
							revoke_code($_POST['code']);
					}
					else {
							header("Location:index.php?error=ERR_SUBDOMAIN_DUP");		
					}			
			}
			else {		
			
			 header("Location:index.php?error=ERR_CODE");
			 exit;
		    
    		} 
    		}else{
			 header("Location:index.php?error=ERR_CODE");
			 exit;
    		
    		}
    		
		}
else {
			header("Location: index.php?error=ERR_CAPTCHA");
			die();
}
}

function revoke_code($code)
{
global $db;
    $sql = "UPDATE codes SET status=0 WHERE code='".$code."' LIMIT 1" ;
	 $db->query($sql);	 
}

function verify_code($code)
{
	 global $db;
    $sql = "SELECT * from codes WHERE status=1 AND code='".$code."'" ;
	$dbquery = $db->prepare($sql);
	$dbquery->execute();
	$dbcode = $dbquery->fetch();

	if(!empty($dbcode) && $dbcode['code'] == $code)
	 return(true);
	else {
		return(false);
	}
	
}


function check_subdomain($subdomain)
{
	 global $db;
    $sql = "SELECT * from users WHERE subdomain='".$subdomain."'" ;
	$dbquery = $db->prepare($sql);
	$dbquery->execute();
	$dbcode = $dbquery->fetch();

	if(!empty($dbcode) && $dbcode['subdomain'] == $subdomain)
	 return(false);
	else {
		return(true);
	}
}

function save($user)
{

	global $db;
	
	$created = time();
   $name = $user['firstname']." ".$user['lastname'];

   $sql = "INSERT INTO users (created,name,business,email,phone,code,subdomain) 
   						VALUES('".$created."'
									,'".$name."'
									,'".$user['company']."'
									,'".$user['email']."'
									,'".$user['phone']."'
									,'".$user['code']."'
									,'".$user['subdomain']."')";
									
	$db->query($sql);
			require_once("head.php");
			 echo ' <div class="alert alert-success" role="alert">
            Thank you!
        </div>';

}
?>