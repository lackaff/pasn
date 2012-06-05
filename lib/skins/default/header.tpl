<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo Config::Get('SITE_NAME'); ?></title>

<link rel="stylesheet" href="<?=SITE_URL?>/lib/skins/default/styles.css" type="text/css" />

<?php
Template::Show('core_htmlhead.tpl');
?>

<style type="text/css">
</style>
</head>

<body>
<?php
	Template::Show('core_htmlreq.tpl');
?>
<div align="center" id="mainbody">