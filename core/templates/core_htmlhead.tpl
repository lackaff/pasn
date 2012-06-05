<?php
	if (Vars::GET('module') == 'Admin') {
		// load nothing
	} else {
?>
<script type="text/javascript" src="<?=SITE_URL?>/lib/js/jquery.min.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>/lib/js/jquery.form.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>/lib/js/jquery.listen-min.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>/lib/js/jquery.ui.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>/lib/js/jqModal.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>/lib/js/codon.js"></script>
<script type="text/javascript" src="<?=SITE_URL?>/lib/js/pasn.js"></script>
<link rel="stylesheet" href="<?=SITE_URL?>/lib/css/codon.css" type="text/css" />
<link rel="stylesheet" href="<?=SITE_URL?>/lib/css/pasn.css" type="text/css" />
<?php
	}
?>

<?=$MODULE_HEAD_INC;?>