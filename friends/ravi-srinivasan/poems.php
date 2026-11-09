<?php
contentBox(nodeValue(), cssUX::container);

h2(humanize(nodeValue()) . ' Media');
echo 'Here are pieces that my friend Ravi S helped bring to life as media.';

$sheet = getSheet('media', false);
$lastPiece = false;

foreach ($sheet->rows as $item) {
	$name = $sheet->getValue($item, 'Piece');
	if ($lastPiece == $name) continue;

	echo cbCloseAndOpen(cssUX::container);
	if (contains($sheet->getValue($item, 'Credits'), 'Ravi S'))
		renderMedia($name);

	$lastPiece = $name;
}

contentBox('end');
