<?
	require_once("../inc/ad_track.inc");
	require_once("inc/config.inc");
	$seo__page_title = '';
	$seo__meta_kws = '';
	$seo__meta_desc = '';
	$seo__url_fname = '';
	$seo__url_dname = '';
	
	$results = mysql_query("select * from product_category where id = '".$_GET['id']."' and site = '".SITE."' limit 1", $dbh);
	
	if(mysql_num_rows($results)){
		$category = mysql_fetch_assoc($results);
		
		$name = $category['name'];
		$seo__page_title = $category['page_title'];
		$seo__meta_kws = $category['meta_keywords'];
		$seo__meta_desc = $category['meta_description'];
		$seo__url_fname = $category['url_filename'];
		$seo__url_dname = $category['url_foldername'];
	}else{
		header($_SERVER['SERVER_PROTOCOL'] . " 404 Not Found");
		include("404.php");
		exit;
	}
	
	$newURL = DOCUMENT_BASE . '/' . SEO_format_url($_GET['id'], 'product', $name, $seo__url_fname, $seo__url_dname);
	$SEO_self_URL = ((DOCUMENT_BASE == '') ? 'http://www.medical-forms.com' : 'http://www.databusinesssystems.com') . $newURL;
	
	if(strpos($_SERVER['REQUEST_URI'], $newURL) === FALSE)
	{
		header($_SERVER['SERVER_PROTOCOL'] . ' 301 Moved Permanently');
		header('Location: ' . $SEO_self_URL);
		// Output Standard HTML note of redirect.
		print '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN">' . "\n";
		print '<html><head>' . "\n";
		print '<title>301 Moved Permanently</title>' . "\n";
		print '</head><body>' . "\n";
		print '<h1>Moved Permanently</h1>' . "\n";
		print '<p>The document has moved <a href="' . $SEO_self_URL . '">here</a></p>' . "\n";
		print '</body></html>';
		exit();
	}
	unset($newURL);

// ADD CATEGORY NAMES TO TITLES
$tmp = false;
$newTmp = array();
$category_title_names = '';
if($seo__page_title == '' || $seo__page_title == $name)
{
	$tmp = find_menu_parent('product', $_GET['id'], SITE);
	
	while(is_array($tmp) && $tmp['id'] > 0)
	{
		// We know $tmp is valid
		$newTmp[] = $tmp;
		if($tmp['cat_name'] && $tmp['type'] == 'product')
			$tmp['name'] = $tmp['cat_name'];
		else if($tmp['info_name'] && $tmp['type'] == 'info')
			$tmp['name'] = $tmp['info_name'];
		else if($tmp['samp_name'] && $tmp['type'] == 'sample')
			$tmp['name'] = $tmp['samp_name'];
		
		// Build in reverse [root cat, next cat, next cat]
		//$category_title_names = ', ' . $tmp['name'] . $category_title_names;
		// Build in order [last cat, parent cat, parent cat]
		if($tmp['name'] != '')
			$category_title_names .= ', ' . $tmp['name'];
		
		// Figure out if there are more categories
		if($tmp['parent'] > 0)
		{
			$tmp = find_menu_parent($tmp['type'], $tmp['type_id'], SITE, $tmp['parent']);
		}
		else
			$tmp = false;
	}
	$category_title_names = $name . $category_title_names;
}
else
	$category_title_names = $seo__page_title;

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title><?=$category_title_names?> - Medical Forms</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link rel="stylesheet" type="text/css" href="<?=DOCUMENT_BASE?>/style.css">
	<script type="text/javascript" src="<?=DOCUMENT_BASE?>/viewphoto.js"></script>
	<script type="text/javascript" src="<?=DOCUMENT_BASE?>/menu.js"></script>
</head>

<body onLoad="P7_TMopen()">
<?
	include("inc/header.inc");
	include("../inc/product_page.inc");
	include("inc/footer.inc");
?>
</body>
</html>