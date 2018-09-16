<?php
$ROOT = '../';
include "{$ROOT}includes/main.php";
include "{$ROOT}includes/ContinuityIterator.php";
include "{$ROOT}includes/News.php";
include "{$ROOT}includes/Renderer.php";

$Renderer = new RAL\Renderer();
$Renderer->themeFromCookie($_COOKIE);
$RM = new RAL\ResourceManager();
$iterator = new RAL\ContinuityIterator($RM);
?>
<!DOCTYPE HTML>
<HTML>
<head>
<?php
	$Renderer->Title = "BBCode Help";
	$Renderer->putHead();
?>
</head>
<body>
<header>
	<h1>Using BBCode on RAL</h1>
	<span>or, making your text look pretty on-line</span><br />
</header>
<?php include "{$ROOT}template/Feelies.php" ?><hr />
<?php include "{$ROOT}info/BBCode.txt" ?><hr />
<footer>
<?php include "{$ROOT}template/Footer.php"; ?>
</footer>
</body>
</HTML>