<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>suppleText - Installation : Step 3</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<div id="page-container">

<div id="header">
	<h1><a href="http://www.suppletext.org/" title="suppleText" class="headerlogo"><img src="http://www.suppletext.org/images/logo-medium.gif" alt="suppleText" /></a></h1>
	<div id="title">Step 3: User Information</div>
</div>

<div id="main">
     <div id="description">
     
		<?php if(!empty($this->validation->error_string)): ?>
			<?php echo $this->validation->error_string; ?>
		<?php endif; ?>
		
     <p>
     An administrative user must be created before you can use the administration
     panel. Please provide the following information:
     </p>
     <form action="index.php?step3/check" method="post" >
          <table>
               <tr>
                    <th scope="row">Username</th> 
                    <td><input name="adminuser" type="text" size="25" value="<?php if($this->validation->adminuser == '') { echo 'admin'; } else { echo $this->validation->adminuser; } ?>" /></td> 
                    <td>The login name of the administrative user. The username is limited 
										    to alphanumeric characters and the underscore (ie. <em>joe_cool</em>).</td> 
               </tr>
               <tr>
                    <th scope="row">Password</th> 
                    <td><input name="adminpassword" type="password" size="25" value="" /></td> 
                    <td></td> 
               </tr>
               <tr>
                    <th scope="row">Password Again</th> 
                    <td><input name="adminpassword2" type="password" size="25" value="" /></td> 
                    <td>Of course, passwords must match.</td> 
               </tr>
               <tr>
                    <th scope="row">Email Address</th> 
                    <td><input name="adminemail" type="text" size="25" value="<?php echo $this->validation->adminemail; ?>" /></td> 
                    <td></td> 
               </tr>
          </table>
          <input type="submit" name="submit" value="Create New User and Continue &gt;" class="continuebutton" />
     </form>
     </div>
</div>

</div> <!-- page container -->
</body>
</html>
