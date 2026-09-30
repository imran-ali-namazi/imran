<?php
$pages = explode(NEWLINE . '---' . NEWLINE, disk_file_get_contents(__DIR__ . '/data/contents.md'));
$printMode = getQueryParameter(VARQueryContent);
$pageClass = 'container my-4' . ($printMode ? ' plain' : '');
$titleClass = ' text-center title-page';
$breakReplaces = !$printMode ? [] : ['<p><!--page-break--></p>' => $breakWith = '&hellip; continued</div><div class="new-page ' . $pageClass . '">', '<!--page-break-->' => $breakWith];

contentBox('', $pageClass . $titleClass);
$settings = [replacer::plainReplaces => $breakReplaces];
foreach ($pages as $ix => $item) {
	$title = contains($item, '<!--title-->');
	if ($ix != 0) echo cbCloseAndOpen($pageClass . ' new-page' . ($title ? $titleClass : ''))
		. ($title ? '' : '<span class="float-right p-3 btn btn-info">Page ' . ($ix + 1) . '</span>');
	renderSet::create()->markdown()->render($item, $settings);
}
contentBox('end');
