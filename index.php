<?php
function sigml_to_html($sig) {
	ob_start();
	foreach ($sig->p as $p) {
?>
<p><?php echo preg_replace('/(?<=\s)\s/', '&nbsp;', $p); ?></p>
<?php
	}
	return trim(ob_get_clean());
}

function sigml_to_text($sig) {
	$out = [];
	foreach ($sig->p as $p)
		$out[] = "$p";
	return join("\n", $out);
}

function sigml_to_json($sig) {
	return json_encode([ 'signature' => sigml_to_text($sig) ]);
}

$xml = file_get_contents("sigs.sigml");
$sigs = simplexml_load_string($xml)->signature;
$selected = random_int(0, count($sigs)-1);

$type = pathinfo($_SERVER['REQUEST_URI'])['filename'];
switch ($type) {
case 'json':
header('Content-Type: application/json; charset=utf-8');
	echo sigml_to_json($sigs[$selected]);
	exit;
case 'text':
header('Content-Type: text/plain; charset=utf-8');
	echo sigml_to_text($sigs[$selected]);
	exit;
}

$self = (empty($_SERVER['HTTPS']) ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'];
header('Content-Type: text/html; charset=utf8');
?>
<!DOCTYPE html>
<html>
<head>
<style>
blockquote {
	border-left: lightgray solid 10px;
	padding-left: 1.5em;
}
p {
	margin-bottom: 0;
	margin-top: .5em;
	font-size: 120%;
}
.usage {
	font-family: sans;
}
</style>
</head>
<body>
<?php if ($type != 'html'): ?>
<h1>Random Quote</h1>
<?php endif; ?>
<blockquote><?php echo sigml_to_html($sigs[$selected]);?></blockquote>
<?php if ($type != 'html'): ?>
<div class="usage">
<p>
Get a random quote in one of the following formats:
</p>
<ul>
<li><a href="<?php echo $self;?>/html"><?php echo $self;?>/html</a> : Just the quote in HTML (without this usage section).</li>
<li><a href="<?php echo $self;?>/text"><?php echo $self;?>/text</a> : Simple text (without HTML formatting).</li>
<li><a href="<?php echo $self;?>/json"><?php echo $self;?>/json</a> : JSON object format.</li>
</ul>
</div>
<?php endif; ?>
</body>
</html>
