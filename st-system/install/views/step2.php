<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>suppleText - Installation : Step 2</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<div id="page-container">

<div id="header">
	<h1><a href="http://www.suppletext.org/" title="suppleText" class="headerlogo"><img src="http://www.suppletext.org/images/logo-medium.gif" alt="suppleText" /></a></h1>
	<div id="title">Step 2: Site Information</div>
</div>

<div id="main">
     <div id="description">
     
		<?php if(!empty($this->validation->error_string)): ?>
			<?php echo $this->validation->error_string; ?>
		<?php endif; ?>
		
     <p>
     We would like to know a few things about your new suppleText installation:
     </p>
     <form action="index.php?step2/check" method="post">
          <table>
               <tr>
                    <th scope="row">Site/Wiki Name</th> 
										<td><input name="wikiname" type="text" size="30" value="<?php if($this->validation->wikiname == '') { echo 'My suppleText Wiki'; } else { echo $this->validation->wikiname; } ?>" /></td>
                    <td>The name of your new suppleText wiki (ie. <em>My Cool Wiki</em>). You can always change this later.</td> 
               </tr>
               <tr>
                    <th scope="row">Site/Wiki URL Path</th> 
                    <td><input name="wikipath" type="text" size="30" value="<?php if($this->validation->wikipath == '') { echo 'http://www.mysite.com/wiki/'; } else { echo $this->validation->wikipath; } ?>" /></td> 
                    <td>The full URL to this suppleText wiki with the trailing slash (ie. <em>http://www.mysite.com/wiki/</em>). We've tried to detect the URL for you. Double check this!</td> 
               </tr>
          </table>
          <input type="submit" name="submit" value="Apply Wiki Settings and Continue &raquo;" class="continuebutton" />
     </form>
     </div>
</div>

</div> <!-- page container -->
</body>
</html>
