<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>suppleText - Installation : Step 1</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<div id="page-container">

<div id="header">
	<h1><a href="http://www.suppletext.org/" title="suppleText" class="headerlogo"><img src="http://www.suppletext.org/images/logo-medium.gif" alt="suppleText" /></a></h1>
	<div id="title">Step 1: Database Information</div>
</div>

<div id="main">
     <div id="description">
     
		<?php if(!empty($this->validation->error_string)): ?>
			<?php echo $this->validation->error_string; ?>
		<?php endif; ?>
		
     <p>
     Please enter your database connectivity information. If you are unsure of
     what to enter, please contact your host:
     </p>
     <form action="index.php?step1/check" method="post" >
          <table>
               <tr>
                    <th scope="row">Database Type</th> 
                    <td>
                      <select name="dbms">
												<option value="mysql">MySQL</option>
                    	</select>
                    </td> 
                    <td>The type of database suppleText will be using. <em>Currently, only MySQL is supported.</em></td> 
               </tr>
               <tr>
                    <th scope="row">Database Host</th> 
                    <td><input name="dbhost" type="text" size="25" value="<?php if($this->validation->dbhost == '') { echo 'localhost'; } else { echo $this->validation->dbhost; } ?>" /></td> 
                    <td>Location of the database. Most likely localhost.</td> 
               </tr>
               <tr>
                    <th scope="row">Database Name</th> 
                    <td><input name="dbname" type="text" size="25" value="<?php echo $this->validation->dbname; ?>" /></td> 
                    <td>Name of the database that suppleText will be using.</td> 
               </tr>
               <tr>
                    <th scope="row">Database User</th> 
                    <td><input name="dbuser" type="text" size="25" value="<?php echo $this->validation->dbuser; ?>" /></td> 
                    <td>Username that can access the database.</td> 
               </tr>
               <tr>
                    <th scope="row">User Password</th> 
                    <td><input name="dbpass" type="text" size="25" value="<?php echo $this->validation->dbpass; ?>" /></td> 
                    <td>Password of the database user.</td> 
               </tr>
               <tr>
                    <th scope="row">Table Prefix</th> 
                    <td><input name="tblprefix" type="text" size="25" value="<?php if($this->validation->tblprefix == '') { echo 'st_'; } else { echo $this->validation->tblprefix; } ?>" /></td> 
                    <td>If you want to run multiple suppleText installations in a single database, change this.</td> 
               </tr>
          </table>
          <input type="submit" name="submit" value="Check Database Information and Continue &raquo;" class="continuebutton" />
     </form>
     </div>
</div>

</div> <!-- page container -->
</body>
</html>
