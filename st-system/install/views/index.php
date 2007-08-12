<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>suppleText - Installation</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<link rel="stylesheet" href="style.css" type="text/css" />
</head>
<body>
<div id="page-container">

<div id="header">
	<h1><a href="http://www.suppletext.org/" title="suppleText" class="headerlogo"><img src="http://www.suppletext.org/images/logo-medium.gif" alt="suppleText" /></a></h1>
	<div id="title">Installation</div>
</div>

<div id="main">
     <div id="description">
     <p>
     Welcome to the installation of suppleText! Before we begin, please make sure
     you have completed the following tasks:
     </p>
     <ol>
     			<li>
     			<strong>Make sure cookies are enabled in your browser</strong>! Otherwise,
     			you will get error messages saying that steps were not completed.
     			</li>
          <li>
          Make sure that this script has the permissions to write to the
          directory <code>/st-external</code>. If you 
          are running Xcomic on a Linux server, this means that you must 
          <code>chmod 777 st-external</code>.
          Windows users should have write permissions by default.
          </li>
          <li>
          Obtain the following information to setup the database that suppleText 
          will be using:
               <ul>
                    <li>Database name</li>
                    <li>Database username</li>
                    <li>Database password</li>
                    <li>Database host</li>
                    <li>Table prefix (if you want to run more than one suppleText installation in a single database)</li>
               </ul>
          
          </li>
     </ol>
     <form action="index.php?step1" method="post">
          <input type="submit" name="submit" value="Continue &raquo;" class="continuebutton" />
     </form>
     </div>
</div>

</div> <!-- page container -->
</body>
</html>
