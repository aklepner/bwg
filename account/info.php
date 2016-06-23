<?
	session_start();
	if($_SERVER["HTTPS"] != 'on' || $_SESSION[account_id] < 1){
		header("Location: https://www.databusinesssystems.com/account/");
		exit;
	}
	header("Last-Modified: ".gmdate('D, d M Y H:i:s T', time()));
	header("Pragma: no-cache");
	header("Expires: ".gmdate('D, d M Y H:i:s T', time()-3600));
	header("Cache-Control: no-cache");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
   
<html>
<head>
	<title>Data Business Systems - Change Account Information</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
<?
	require_once("../inc/dbi.inc");
	require_once("../inc/crypt.inc");
	include("../inc/header.inc");
	if(isset($_POST[email])){
		if(!preg_match("/^[a-z0-9\.\-_]+\@[a-z0-9\.\-_]+\.[a-z]+$/",$_POST[email])){
			$error = "Please specify a valid e-mail account.";
		}elseif($_POST[password] != "" && strlen($_POST[password]) < 4 || strlen($_POST[password]) > 16){
			$error = "Password must be between 4 and 16 characters.";
		}elseif($_POST[password] != $_POST[cpassword]){
			$error = "Passwords do not match.";
		}else{
			$db = new dbi();
			$db->query("select * from account where email = '".$_POST[email]."' and id != '$_SESSION[account_id]'");
			if($db->numrows()){
				$error = "Email account already exists.";
			}else{
				$query = "update account set email = '$_POST[email]', password = ";
				if($_POST[password] != ""){
					$query .= "'".base64_encode(encrypto($_POST[password],strtolower(substr($_POST[email],0,2))))."'";
				}else{
					$db->query("select email,password from account where id = '$_SESSION[account_id]'");
					$oldpassword = trim(decrypto(base64_decode($db->result("password")), substr($db->result("email"),0,2)));
					$query .= "'".base64_encode(encrypto($oldpassword,strtolower(substr($_POST[email],0,2))))."'";
				}
				$query .= " where id = '$_SESSION[account_id]'";
				$db->query($query);
				$_SESSION[email] = $_POST[email];
			}
		}
	}
?>
<h1>Change Email/Password</h1>
<form method="post" action="<?=$PHP_SELF?>">
<table width="90%" border="0" cellspacing="0" cellpadding="2" align="center">
<tr><td colspan="2" align="right"><a href="index.php">Back to Your Account</a></td></tr>
<tr><td colspan="2">The following form can be used to update your email address and password.<br><br></td></tr>
<? 	if($error){?>
	<tr><td colspan="2" align="center" class="error"><?=$error?></td></tr>
<?	}elseif(!$error && isset($_POST[email])){?>
	<tr><td colspan="2" align="center" class="success">Your Account Information has been updated successfully!</td></tr>
<?	} ?>
<tr><td align="right" width="40%" style="font-weight:bold;">E-mail</td><td align="left" width="60%"><input type="text" name="email" size="35" value="<?if($error){ print $_POST[email]; }else{ print $_SESSION[email]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Password</td><td align="left" width="60%"><input type="password" name="password" size="25" value="<?if($error){ print $_POST[password]; } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Retype Password</td><td align="left" width="60%"><input type="password" name="cpassword" size="25" value="<?if($error){ print $_POST[cpassword]; } ?>"></td></tr>
<tr><td colspan="2" align="center"><input type="submit" value="Update Information"></td></tr>
</table>
</form>
<?
	include("../inc/footer.inc");
?>
</body>
</html>