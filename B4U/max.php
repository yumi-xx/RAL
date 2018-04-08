<?php
$ROOT = '../';
include $ROOT.'includes/main.php';
include $ROOT.'includes/fetch.php';
include $ROOT.'includes/post.php';

// Which continuity we are reading
$continuity = urldecode($_GET['continuity']);
// Which topic (if any) we are reading
$topic = @$_GET['topic'];
// Whether we are posting or only reading
$postmode = @$_GET['postmode'];

// Posting in a topic
if (isset($_POST['content']) && isset($topic)) {
	$auth = $_COOKIE['auth'];
	$content = $_POST['content'];

	// Strip down the content ; )
	$content = trim($content);
	if (strlen($content) > CONFIG_RAL_POSTMAXLEN
	|| !strlen($content)
	|| !check_robocheck($_POST['robocheckid'], $_POST['robocheckanswer'])
	|| !($post = create_post($continuity, $topic, $auth, $content))) {
		header("HTTP/1.1 303 See Other");
		if (CONFIG_CLEAN_URL)
			$location = CONFIG_WEBROOT . "dariram";
		else
			$location = CONFIG_WEBROOT . "dariram.php";
		header("Location: $location?$_SERVER[QUERY_STRING]");
		die;
	}
	else {
		header("HTTP/1.1 303 See Other");
		if (CONFIG_CLEAN_URL)
			$location = CONFIG_WEBROOT . "r3";
		else
			$location = CONFIG_WEBROOT . "r3.php";
		header("Location: $location?$_SERVER[QUERY_STRING]");
		die;
	}
}
// Posting to the continuity
else if (isset($_POST['content'])) {
	$auth = $_COOKIE['auth'];
	$content = $_POST['content'];

	// Strip down the content ; )
	$content = trim($content);
	$content = stripslashes($content);
	$content = htmlspecialchars($content);
	if (strlen($content) > CONFIG_RAL_POSTMAXLEN
	|| !strlen($content)
	|| !($topic = create_topic($continuity, $auth, $content))) {
		header("HTTP/1.1 303 See Other");
		if (CONFIG_CLEAN_URL)
			$location = CONFIG_WEBROOT . "dariram";
		else
			$location = CONFIG_WEBROOT . "dariram.php";
		header("Location: $location?$_SERVER[QUERY_STRING]");
		die;
	}
	else {
		notify_listeners('POST', $topic);
		header("HTTP/1.1 303 See Other");
		if (CONFIG_CLEAN_URL)
			$location = CONFIG_WEBROOT . "r3";
		else
			$location = CONFIG_WEBROOT . "r3.php";
		header("Location: $location?$_SERVER[QUERY_STRING]");
		die;
	}
}

$continuities = fetch_continuities();

// Timeline parameter extraction and verification
$i = count($continuities);
for ($i = count($continuities) - 1; $i + 1; $i--) {
	if ($continuities[$i]->name == $continuity) break;
}
// 404 continuities which do not exist
if ($i < 0 || !isset($continuity)) {
	http_response_code(404);
	include "{$ROOT}template/404.php";
	die;
}
$continuity = $continuities[$i]->name;
$continuitydesc = $continuities[$i]->description;

if (isset($topic))
	$posts = fetch_posts($continuity, $topic);
else
	$posts = fetch_topics($continuity);
?>
<!DOCTYPE HTML>
<HTML>
<head>
<?php
	if (isset($topic)) {
		$pagetitle = titleize($posts[0]->content);
		$pagedesc = titleize($posts[0]->content, 320);
	}
	else {
		$pagetitle = "$continuity - $continuitydesc";
		$pagedesc = $continuitydesc;
	}
	include "{$ROOT}template/head.php";
?>

	<script src='<?php print CONFIG_WEBROOT?>js/render.js'></script>
</head>
<body><main id=main>
<?php
	$items = $continuities;
	include "../template/toolbar.php";

	// Requires $continuity; $topic is optional
	include "../template/breadcrumb.php";

	if (isset($topic))
		$title = "[$continuity / $topic]";
	else
		$title = "[$continuity]";
	$subtitle = $continuitydesc;
	// Requires $title; $subtitle is optional
	include "../template/header.php";



	// Browsing a topic (reader is in 'expanded' view)
	if (isset($topic)) {
		print <<<HTML
	<div id=reader class=expanded
	data-topic="$topic"
	data-continuity="$continuity">
	<section class="content op">{$posts[0]->toHtml()}</section>
	<hr />

HTML;
		$linkify = false;
		for ($i = 1; $i < count($posts); $i++) {
			$post = $posts[$i];
			include "../template/post.php";
		}
		print
<<<HTML
	</div>

HTML;

	// Browsing a continuity (reader is in 'continuity' view)
	} else {
		print <<<HTML
	<div class=continuity id=reader
	data-continuity="$continuity">

HTML;
		$linkify = true;
		foreach ($posts as $post) {
			include "../template/post.php";
		}
		print
<<<HTML
	</div>

HTML;
	}
	if (isset($postmode))
		// Requires $continuity; $topic optional
		include "../template/postbox.php";
	else
		// Requires $continuity; $topic optional
		include "../template/footer.php";
?>
</main>
<script src='<?php print CONFIG_WEBROOT?>js/remote.js'></script>
<script src='<?php print CONFIG_WEBROOT?>js/esthetic.js'></script>
</body>
</HTML>
