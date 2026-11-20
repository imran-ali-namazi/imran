<?php
variables([
	socialBuilder::variableName => socialBuilder::create()
		->addImranPersonal()->addHR()
		->append(socialBuilder::default())
		->getItems(),
	replacer::siteReplaces => [
		'special-links' => special::banner(),
	],
	'SkipSitemap' => ['poems', 'prose'],
]);

setup_cdn();
if (nodeIs(SITEHOME))
	setHtmlVariable(VARWelcomeMessage, getSnippet('welcome'));

function did_site_render_page() {
	if (variable('hasPiece')) {
		renderAny(variable('file'));
		contentBox('end');
	}

	return variable('hasPiece') || variable('hasPieces');
}

function before_file() {
	if (variable('hasPiece')) {
		printSpacer(humanizeThis());
		if ($banner = variable(special::BannerName)) echo $banner;

		printPiece(variable('currentPiece'), 'before');
	} else if (variable('hasPieces')) {
		$pp1 = getPageParameterAt();
		$suffix = $pp1 ? ' &mdash;> ' . humanize($pp1) : '';
		printSpacer(humanizeThis() . $suffix);
		if ($banner = variable(special::BannerName)) echo $banner;

		$count = count(variable('currentPieces'));
		foreach (variable('currentPieces') as $ix => $item)
			printPiece($item, 'during', $ix + 1 . '/' . $count);
	}
}

function after_file() {
	if (variable('hasPiece')) {
		$current = variable('currentPiece');

		$imgStart = '<div class="container text-center after-content p-3 mb-3 rounded-3 img-max-800"><img class="img-fluid img-max-600" src="'; $imgEnd = '" /></div>' . NEWLINE;
		$img = disk_file_exists(SITECDNPATH . ($jpg = 'writing/pieces/' . urlize($current['Name']) . '.jpg')) ? getHtmlVariable('cdn') . $jpg  : false;
		if ($img) echo $imgStart . $img . $imgEnd;

		renderMedia($current['Name'], true);

		$onlyMain = getQueryParameter(VARQueryContent);
		if (!$onlyMain && $item = variable('nextPiece'))
			printPiece($item, 'after', false, 'Next');
		if (!$onlyMain && $item = variable('previousPiece'))
			printPiece($item, 'after', false, 'Previous');

		$md = str_replace('.txt', '.md', $current['File']);
		if (disk_file_exists($md)) {
			echo tagUX::contentBoxClasses('deep-dive', cssUX::container, cssUX::standout);
			printH1InDivider('Deep Dive with Copilot');
			variable(VARNoContentBoxes, true);
			builtinOrRender($md, features::engage);
			clearVariable(VARNoContentBoxes);
			contentBox('end');
		}
	}
}
