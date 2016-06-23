<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html>
<head>
	<title>dbs - administration</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
<?
include("header.inc");
if(isset($add) || isset($mod)){
	if(isset($_POST[name])){
		// Process Form
		if($_POST[name] == "")
			$error = "Name must be specified!";
		//else
		//{
			//$_POST[description] = str_replace('\'', '&#039;', $_POST[description]);
		//}
		if(!$error)
		{
			if($_POST['page_title'] != '')
			{
				$_POST['page_title'] = str_replace('"', '&quot;', trim($_POST['page_title']));
			}
			if($_POST['meta_keywords'] != '')
			{
				$_POST['meta_keywords'] = str_replace('"', '&quot;', trim($_POST['meta_keywords']));
			}
			if($_POST['meta_description'] != '')
			{
				$_POST['meta_description'] = str_replace('"', '&quot;', trim($_POST['meta_description']));
			}
			if($_POST['url_filename'] != '')
			{
				$_POST['url_filename'] = cleanforurl2(trim($_POST['url_filename']));
			}
			if($_POST['url_foldername'] != '')
			{
				$_POST['url_foldername'] = cleanforurl2(trim($_POST['url_foldername']));
			}
		}
		
		if(!$error){
			$updb = new dbi();
			if(isset($add)){
				$updb->query("
					INSERT
					INTO sample_category
					(
						site,
						name,
						description,
						page_title,
						meta_keywords,
						meta_description,
						url_filename,
						url_foldername
					) VALUES (
						'".SITE."',
						'".mysql_real_escape_string($_POST['name'])."',
						'".mysql_real_escape_string($_POST['description'])."',
						'".mysql_real_escape_string($_POST['page_title'])."',
						'".mysql_real_escape_string($_POST['meta_keywords'])."',
						'".mysql_real_escape_string($_POST['meta_description'])."',
						'".mysql_real_escape_string($_POST['url_filename'])."',
						'".mysql_real_escape_string($_POST['url_foldername'])."'
					)
				");
			}else{
				$updb->query("
					UPDATE sample_category
					SET
						name = '".mysql_real_escape_string($_POST['name'])."',
						description = '".mysql_real_escape_string($_POST['description'])."',
						page_title = '".mysql_real_escape_string($_POST['page_title'])."',
						meta_keywords = '".mysql_real_escape_string($_POST['meta_keywords'])."',
						meta_description = '".mysql_real_escape_string($_POST['meta_description'])."',
						url_filename = '".mysql_real_escape_string($_POST['url_filename'])."',
						url_foldername = '".mysql_real_escape_string($_POST['url_foldername'])."'
					WHERE id = '$mod'
				");
			}
			print "<div align=\"center\" class=\"success\">Product Category updated successfully!</div><br>";
			$success=1;
		}
	}
	if(isset($mod)){
		$moddb = new dbi();
		$moddb->query("select * from sample_category where site = '".SITE."' and id = '$mod'");
		if(!$moddb->numrows()){
			print "<div align=\"center\">No Sample Category with ID of '$mod'!</div>";
			unset($moddb);
		}
	}
	if(!isset($success)){
		if($error)
			print "<div align=\"center\" class=\"error\">$error</div>"
?>
	<form action="sample_category.php?<? if(isset($add)){ print "add=1"; }elseif(isset($mod)){ print "mod=$mod"; }?>" method="post">
		<table align="center" cellpadding="3" cellspacing="0" border="0">
		<tr bgcolor="#990000"><td style="color:#FFFFFF;">Modify Sample Category</td><td align="right"><a href="sample_category.php" style="color:white;">View All</a></td></tr>
		<tr><td class="field_title">Name</td><td><input type="text" name="name" size="50" value="<? if(isset($error)){ print htmlspecialchars($_POST[name]); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result("name")); }?>"></td></tr>
		<tr><td class="field_title">Description</td><td><textarea cols="50" rows="10" name="description"><? if(isset($error)){ print htmlspecialchars($_POST[description]); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result("description")); }?></textarea></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		<tr><td colspan="2" style="border-bottom: 2px solid #000000; font-weight: bold;">Meta Settings</td></tr>
		<tr><td class="field_title">Title Tag</td><td><input type="text" name="page_title" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['page_title']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('page_title')); }?>'></td></tr>
		<tr><td class="field_title">Meta Keywords</td><td><input type="text" name="meta_keywords" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['meta_keywords']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('meta_keywords')); }?>'></td></tr>
		<tr><td class="field_title">Meta Description</td><td><input type="text" name="meta_description" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['meta_description']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('meta_description')); }?>'></td></tr>
		<tr><td class="field_title">URL Filename</td><td><input type="text" name="url_filename" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['url_filename']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('url_filename')); }?>'>-s<? if(isset($moddb)){ print $moddb->result('id'); }else{ print '#'; } ?>.htm</td></tr>
		<tr><td class="field_title">URL Foldername</td><td><input type="text" name="url_foldername" size="40" value='<? if(isset($error)){ print htmlspecialchars($_POST['url_foldername']); }elseif(isset($moddb)){ print htmlspecialchars($moddb->result('url_foldername')); }?>'></td></tr>
		<tr><td class="field_title">Current URL</td><td><? if(!isset($moddb)){ print 'N/A'; }else{ print '/' . SEO_format_url($moddb->result('id'), 'sample', $moddb->result('name'), $moddb->result('url_filename'), $moddb->result('url_foldername')); } ?></td></tr>
		<tr bgcolor="#990000"><td colspan="2" align="center"><input type="submit" value="Update"></td></tr>
		</table>
	</form>
<?
	}
}
if((!isset($add) && !isset($mod)) || isset($success)){
	if(isset($delete)){
		$deldb = new dbi();
		$deldb->query("select * from sample_category where site = '".SITE."' and type = 'sample' and type_id = '$delete'");
		if($deldb->numrows()){
			print "<div align=\"center\" class=\"error\">Sample Category attached to Menu!  Please remove from Menu first.</div><br>";
		}else{
			if(isset($confirm) && $confirm == 'y'){
				$deldb->query("delete from sample_category where site = '".SITE."' and id = '$delete'");
				print "<div align=\"center\" class=\"success\">Sample Category Item deleted!</div><br>";
			}else{
				print "<div align=\"center\" class=\"error\">Are you sure you want to delete this sample category? <a class=\"error\" href=\"sample_category.php?delete=$delete&confirm=y\">Yes</a>  <a class=\"error\" href=\"sample_category.php\">No</a></div><br>";
			}
		}
	}
	$db = new dbi();
	$db->query("select * from sample_category where site = '".SITE."' order by name asc");
	print "<table align=\"center\" cellpadding=\"2\" cellspacing=\"0\">";
	print "<tr bgcolor=\"#990000\"><td style=\"color:#FFFFFF;\">Sample Categories</td><td colspan=\"3\"><a href=\"sample_category.php?add=1\" style=\"color:#FFFFFF;\">Add a Sample Category</a></td></tr>";
	if($db->numrows()){
		print "<tr><td class=\"bold\" align=\"left\">Name</td><td class=\"bold\" align=\"left\">View</td><td class=\"bold\" align=\"left\">Samples</td><td class=\"bold\" align=\"left\">Delete?</td></tr>";
		while($db->loop()){
			print "<tr><td><a href=\"sample_category.php?mod=".$db->result("id")."\">".$db->result("name")."</a></td><td align=\"center\"><a href=\"".DOCUMENT_BASE."/sample/".$db->result("id")."\" target=\"_blank\">View</a></td><td align=\"center\"><a href=\"sample.php?category=".$db->result("id")."\">Edit</a></td><td align=\"right\"><a href=\"sample_category.php?delete=".$db->result("id")."\">Delete?</a></td></tr>";
		}
	}else{
		print "<tr><td colspan=\"4\" align=\"center\">No Sample Categories!</td></tr>";
	}
	print "</table>";
}
include("footer.inc");
?>
</body>
</html>
