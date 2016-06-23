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
	<title>Data Business Systems - Change Address Information</title>
	<link rel="stylesheet" type="text/css" href="../style.css">
</head>

<body>
<?
	require_once("../inc/dbi.inc");
	include("../inc/header.inc");
?>
<h1>Change Address Information</h1>
<div style="margin:0 25px 5px 0;" align="right"><a href="index.php">Back to Your Account</a></div>
<?
	$db = new dbi();
	if(isset($_GET['delete'])){
		if($db->query("update order_address set disable = 'y' where id = '".$_GET['delete']."' and account = '$_SESSION[account_id]'")){
			print "<div align=\"center\" class=\"success\">Address deleted!</div>";
		}else{
			print "<div align=\"center\" class=\"error\">No such address!</div>";
		}
	}
	if(isset($_GET['modify'])){
		$db->query("select * from order_address where account = '".$_SESSION['account_id']."' and id = '".$_GET['modify']."' and disable = 'n' limit 1");
		if($db->numrows()){ 
			if(sizeof($_POST)){
				if($_POST['name'] == "")
					$error = "Please specify a Full Name!";
				elseif($_POST['address1'] == "")
					$error = "Please specify an Address!";
				elseif($_POST['city'] == "")
					$error = "Please specify a City!";
				elseif($_POST['state'] == "")
					$error = "Please specify a State!";
				elseif($_POST['zip_code'] == "")
					$error = "Please specify a Zip Code!";
				elseif($_POST['phone'] == "")
					$error = "Please specify a Phone Number!";
					
				if(!$error){
					mysql_query("update order_address set name = '".addslashes($_POST['name'])."', company = '".addslashes($_POST['company'])."', address1 = '".addslashes($_POST['address1'])."', address2 = '".addslashes($_POST['address2'])."', city = '".addslashes($_POST['city'])."', state = '".addslashes($_POST['state'])."', zip_code = '".addslashes($_POST['zip_code'])."', phone = '".addslashes($_POST['phone'])."', fax = '".addslashes($_POST['fax'])."' where account = '".$_SESSION['account_id']."' and id = '".$_GET['modify']."'", $dbh);
					print "<div align=\"center\" class=\"success\">Address updated successfully!</div>";
				}
			}
			if(isset($error) || !sizeof($_POST)){
				if($error)
					print "<div align=\"center\" class=\"error\">$error</div><br>";
		?>
			<form action="<?=$_SERVER['PHP_SELF']?>?modify=<?=$db->result("id")?>" method="post">
			<table width="95%" border="0" cellspacing="1" cellpadding="4" align="center">
			<tr><td class="bar" align="left" colspan="2">Modify Address</td></tr>
			<tr><td align="right" width="40%" style="font-weight:bold;">Full Name</td><td align="left" width="60%"><input type="text" name="name" size="35" value="<? if($error){ print $_POST['name']; }else{ print $db->result("name"); } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Company</td><td align="left" width="60%"><input type="text" name="company" size="35" value="<? if($error){ print $_POST['company']; }else{ print $db->result("company"); } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Address 1</td><td align="left" width="60%"><input type="text" name="address1" size="35" value="<? if($error){ print $_POST['address1']; }else{ print $db->result("address1"); } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Address 2</td><td align="left" width="60%"><input type="text" name="address2" size="35" value="<? if($error){ print $_POST['address2']; }else{ print $db->result("address2"); } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">City</td><td align="left" width="60%"><input type="text" name="city" size="35" value="<? if($error){ print $_POST['city']; }else{ print $db->result("city"); } ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">State</td><td align="left" width="60%"><select name="state"><option value=""></option><?
			
			$results = mysql_query("select * from states order by name asc", $dbh);
			while($row = mysql_fetch_assoc($results)){
				print "<option value=\"".$row['abbr']."\"";
				if((isset($error) && $_POST['state'] == $row['abbr']) || (!isset($error) && $db->result("state") == $row['abbr']))
					print " selected=\"selected\"";
				print ">".$row['name']."</option>";
			}
		?></select></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Zip Code</td><td align="left" width="60%"><input type="text" name="zip_code" size="15" value="<? if($error){ print $_POST['zip_code']; }else{ print $db->result("zip_code"); }  ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Phone Number</td><td align="left" width="60%"><input type="text" name="phone" size="20" value="<? if($error){ print $_POST['phone']; }else{ print $db->result("phone"); }  ?>"></td></tr>
<tr><td align="right" width="40%" style="font-weight:bold;">Fax Number</td><td align="left" width="60%"><input type="text" name="fax" size="20" value="<? if($error){ print $_POST['fax']; }else{ print $db->result("fax"); }  ?>"></td></tr>
			<tr><td class="bar" align="center" colspan="2"><input type="submit" value="Submit"></td></tr>
			</table>
			</form>
		<?
			}
		}else{
			print "<div align=\"center\" class=\"error\">No Such Address</div>";
		}
	}
	if(!isset($_GET['modify']) || (sizeof($_POST) && !isset($error))){
	$db->query("select * from order_address where account = '$_SESSION[account_id]' and disable = 'n' order by type");
	if($db->numrows()){
?>
<table width="95%" border="0" cellspacing="1" cellpadding="4" align="center">
<?
		$p = 1;
		$type = "";
		while($db->loop()){
			if($type != $db->result("type")){
				if(!($p%2))
					print "<td>&nbsp;</td></tr>";
				$type = $db->result("type");
				$p = 1;
?>
<tr><td class="bar" align="left" colspan="2"><? if($type == 'bill'){ print "Billing Address"; }else{ print "Shipping Address"; } ?></td></tr>
<?
			}
			if($p%2)
				print "<tr>";
			print "<td align=\"left\">";
			if($db->result("company") != "")
				print $db->result("company")."<br>";
			print $db->result("name")."<br>".$db->result("address1")." ".$db->result("address2")."<br>".$db->result("city").", ".$db->result("state")." ".$db->result("zip_code")."<br>Phone: ".$db->result("phone")."<br>Fax: ".$db->result("fax")."<br>";
			print "<a href=\"".$_SERVER['PHP_SELF']."?modify=".$db->result("id")."\">Modify</a>  <a href=\"".$_SERVER['PHP_SELF']."?delete=".$db->result("id")."\" onclick=\"if(confirm('Are you sure you wish to delete this address?')){ return true; }else{ return false; }\">Delete?</a>";
			print "</td>";
			if(!($p%2))
				print "</tr>";
			$p++;
		}
		if(!($p%2))
			print "<td>&nbsp;</td></tr>";
	?>
</table>
<?	}else{ ?>
<table width="90%" border="0" cellspacing="0" cellpadding="2" align="center"><tr><td align="left">No Address Information available for this account.  Addresses will be available after you place your first order.</td></tr></table>
<?
	}
	}
?>
<br>
<div align="center" style="font-weight:bold;">Please note that any modification to BILL TO and SHIP TO information can be made during the checkout process.</div>
<?
	include("../inc/footer.inc");
?>
</body>
</html>