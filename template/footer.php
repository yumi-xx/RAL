<?php if (!isset($timeline)) {
		print "Improper template usage!";
		die;
} ?>
<footer>
<?php
	if (isset($topic)) {
		$q = $_GET;
		if (CONFIG_CLEAN_URL) {
			unset($q['timeline']); unset($q['topic']);
		}
		$q = http_build_query($q);
		if (empty($q)) $a = "?postmode";
		else $a = "?postmode&$q";
		print
<<<HTML
	<span class=hoverbox>
		<a href='$a'>Reply to Topic</a>
	</span>

HTML;
		$q = $_GET;
		unset($q['topic']);
		if (CONFIG_CLEAN_URL) {
			$q = http_build_query($q);
			if (empty($q)) $a =  CONFIG_WEBROOT
			. "max/$timeline";
			else $a = CONFIG_WEBROOT
			. "max/$timeline?$q";
		}
		else {
			$q = http_build_query($q);
			$a = "?$q";
		}

		print
<<<HTML
	<span class=hoverbox>
		<a href=$a>Return</a>
	</span>
HTML;
	} else {
		$q = $_GET;
		if (CONFIG_CLEAN_URL) {
			unset($q['timeline']); unset($q['topic']);
		}
		$q = http_build_query($q);
		if (empty($q)) $a = "?postmode";
		else $a = "?postmode&$q";
		print
<<<HTML
		<span class=hoverbox>
			<a href=$a>Create a Topic</a>
		</span>

HTML;
	}
	print
<<<HTML
	</footer>

HTML;
?>