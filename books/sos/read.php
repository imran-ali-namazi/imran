<?php
$pages = explode(NEWLINE . '---' . NEWLINE, disk_file_get_contents(__DIR__ . '/data/contents.md'));
$links = getSheet(__DIR__ . '/data/links.tsv', false);

$printMode = getQueryParameter(VARQueryContent);
$linkReplaces = [];
$pageClass = 'container my-4' . ($printMode ? ' plain' : '');
$titleClass = ' text-center title-page';
$breakReplaces = !$printMode ? [] : ['<p><!--page-break--></p>' => $breakWith = '&hellip; continued</div><div class="new-page ' . $pageClass . '">', '<!--page-break-->' => $breakWith];

foreach ($links->rows as $item) {
	$no = $links->getValue($item, 'sno');
	$url = $links->getValue($item, 'link');
	if ($links->getBoolValue($item, 'embed')) {
		$sheet = sitemapTsv::read('Name');
		if (!isset($sheet->group[$url]))
			return false;

		$item = $sheet->enrichedPiece($sheet->group[$url][0]);
		doToBuffering(1);
		printPiece($item, 'embed');
		$html = replaceItems(doToBuffering(2), $breakReplaces, NOWRAPREPLACE);
		doToBuffering(3);

		$url = pageurl(urlize($item['Name']));
		$linkReplaces['<!--embed/' . $no . '/embed-->']
			= 'image#todo-qrcode-' . $no . BRNL . $html;
		continue;
	}

	$url_r = str_replace($links->values['prefix'], '', $url);
	$url = replaceHtml($url);
	$link = markdown('[' . $url_r . '](' . $url . '~~TARGETNEW)');
	$go = markdown('[GO: ' . $no . '](' . pageUrl(nodeValue() . '/go/' . $no) . '~~TARGETNEW)');
	$linkReplaces['<!--link/' . $no . '/link-->']
		= $link . BRNL . 'image#todo-qrcode-' . $no . BRNL . $go;
}

contentBox('', $pageClass . $titleClass);
$settings = [replacer::plainReplaces => $breakReplaces, replacer::secondPassPlain => $linkReplaces];
foreach ($pages as $ix => $item) {
	$title = contains($item, '<!--title-->');
	if ($ix != 0) echo cbCloseAndOpen($pageClass . ' new-page' . ($title ? $titleClass : ''))
		. ($title ? '' : '<span class="float-right p-3 btn btn-info">Page ' . ($ix + 1) . '</span>');
	renderSet::create()->markdown()->render($item, $settings);
}
contentBox('end');
