<!DOCTYPE html>
<?php include("inc/errors.php");?>
<?php include("inc/configure.php");?>
<?php include("head.php");?>
<?php if(isset($_GET['error']) && ! empty($_GET['error'])) { ?>
 <div class="alert alert-danger" role="alert">
 				<?php switch($_GET['error']) { 
				case 'ERR_CODE':
				case 'ERR_SUBDOMAIN_DUP': 				
				case 'ERR_CAPTCHA':
				$err_msg = $errors[$_GET['error']]; 				
 				?>
            <ul><li><?php echo $err_msg;?></li></ul>
				<?php 
				break;
				} ?>        
        </div>
        
<?php } ?>

        <form class='form-horizontal' id="formSubdomain" role='form' method="post" action="save.php">

          <div class='form-group'>
            <label class='control-label col-md-2 col-md-offset-2' for='id_title'>Name</label>
            <div class='col-md-8'>
              <div class='col-md-3 indent-small' style='margin-right:5px;'>
                <div class='form-group internal'>
                  <input class='form-control' id='name' name="firstname" placeholder='First Name' type='text'>
                </div>
              </div>
              <div class='col-md-3 indent-small'>
                <div class='form-group internal'>
                  <input class='form-control' id='id_last_name' name="lastname" placeholder='Last Name' type='text'>
                </div>
              </div>
            </div>
          </div>
			<div class='form-group'>
            <label class='control-label col-md-2 col-md-offset-2' for='id_title'>Business Name</label>
            <div class='col-md-8'>
              <div class='col-md-3 indent-small'>
                <div class='form-group internal'>
                  <input class='form-control' id='id_company' name="company" placeholder='Company' type='text'>
                </div>
              </div>
            </div>
          </div>

          <div class='form-group'>
            <label class='control-label col-md-2 col-md-offset-2' for='id_email'>Contact</label>
            <div class='col-md-6'>
              <div class='form-group'>
                <div class='col-md-11'>
                  <input class='form-control' id='email' name="email" placeholder='E-mail (*)' type='text'>
                </div>
              </div>
              <div class='form-group internal'>
                <div class='col-md-11'>
                  <input class='form-control' id='id_phone' name="phone" placeholder='Phone: (xxx) - xxx xxxx' type='text'>
                </div>
              </div>
            </div>
          </div>
<div class='form-group'>
            <label class='control-label col-md-2 col-md-offset-2' for='id_title'>Subdomain (*)</label>
            <div class='col-md-8'>
              <div class='col-md-3 indent-small' style='margin-right:5px;'>
                <div class='form-group internal'>
                  <input class='form-control' id='subdomain' name="subdomain" placeholder='subdomain' type='text'>.
                </div>
              </div>
              <div class='col-md-3 indent-small'>
                <div class='form-group internal'>
					<?php echo ".".$domain;?>
              </div>
            </div>
                </div>
          </div>
                  <div class='form-group'>
            <label class='control-label col-md-2 col-md-offset-2' for='id_title'>Verification Code (*)</label>
            <div class='col-md-8'>
              <div class='col-md-3 indent-small'>
                <div class='form-group internal'>
                  <input class='form-control' id='code' name="code" placeholder='Code' type='text'>
                </div>
              </div>
            </div>
          </div>

          <div class='form-group' >
            <label class='control-label col-md-2 col-md-offset-2' for='id_comments'>Human Verification (*)
            </label>            
            <div class='col-md-6'>
            <?php if( isset($_GET['error']) && ! empty($_GET['error']) && $_GET['error'] == "captcha") { ?>
<div style='background-color:red;color:white;' >CAPTCHA VERIFICATION ERROR: You need to verify you are human</div>
<?php } ?>            
          <div class="g-recaptcha" data-sitekey="6LekzQoUAAAAAGu4_OU5SOwaev4x4tY2ur-CsWWa"></div>
            </div>
          </div>  

          <div class='form-group'>
            <div class='col-md-offset-4 col-md-3'>
              <button class='btn-lg btn-primary' id="submit" type='submit'>Request Account</button>
            </div>
            <div class='col-md-3'>
              <button class='btn-lg btn-danger' style='float:right' type='submit'>Cancel</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
<script>
var error = "";

$('#formSubdomain').submit(function () {

	
   if($('#email').val() == ''){
      error = error + 'Email is mandatory';
   }

   if($('#code').val() == ''){
      error = error + '\nVerification Code is mandatory';
   }

   if($('#subdomain').val() == ''){
      error = error + '\nSubdomain is mandatory';
   }
   
	if(error)
	{
		alert(error);
		error = "";	
	}
	else {
		return(true);
	}

});


</script>
