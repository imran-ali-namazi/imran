<?php
DEFINE('TAXONOMY', 'site-taxonomy');

variable(TAXONOMY, [
	'works' => 'Work',
	'collections' => 'Collection',
	'categories' => 'Category',
	'for' => 'Dedication',
]);

class special {
	const All = [
		'for' => [
			['slug' => 'divine', 'in' => ['God', 'MSA']],
			['slug' => 'preceptors', 'in' => ['Uma', 'Shayesteh', 'Vasantha', 'Vichu', 'Mir Momin Ali', 'Asha P', 'Vijaya', 'SKY', 'Raji', 'Jyothi']],
			['slug' => 'girl', 'in' => ['Lace', 'Princess', 'Kiddo']],
			['slug' => 'muses', 'in' => ['Gina', 'Patricia', 'Tracey', 'Kiddo', 'Sandra', 'Lace', 'Dreamy', 'Princess']],
			['slug' => 'good-friends', 'in' => ['Anusha', 'Ashwin', 'Bidisha', 'Bharath M', 'Fehmida', 'Gypsy', 'Mona', 'John R', 'Jyothi', 'Sama', 'Shobi', 'Shyam', 'Srividya', 'Usha S', 'Veena', 'Yogesh']],
			['slug' => 'more-friends', 'in' => ['Abigail S', 'Gary', 'Kavitha', 'Keerthi', 'Lee', 'Meggha', 'Mohan Ram', 'Poonam', 'Prabu S', 'Rahul', 'Ravee', 'Reuben', 'Reza', 'Rubeena', 'Samir', 'Shabana', 'SSD', 'Vidya Shankar', 'Vishnu Vasu']],
			['slug' => 'famous-people', 'in' => ['Ali', 'Gene Roddenberry', 'Louise L Hay', 'Philip Pullman', 'Remzi', 'Richard Bach', 'Robin William', 'Robert Redford', 'Roger Waters', 'Simon', 'Tolkien']],
		],
		'categories' => [
			['slug' => 'harmony', 'in' => ['Daivic', 'Devotional', 'Ethos', 'Gratitude', 'Imagine', 'Mirth', 'One World', 'Religion', 'Spirit']],
			['slug' => 'celebrations', 'in' => ['Birthdays', 'Friends', 'Soulful Dancing']],
			['slug' => 'grieving', 'in' => ['Death', 'Grief', 'Mrityunjay']],
			//['slug' => '', 'in' => ['', ]],
		],
	];

	const BannerName = 'special-banner';
	private const expand = '<i title="Expand All Pieces" class="fa fa-envelope-open-text"></i>';

	static function banner($result = false, $name = false) {
		$all = [];
		$on = $result ? $result['slug'] : false;
		foreach (self::All as $items) {
			foreach ($items as $item)
				$all[] = self::link($item, $on);
		}

		$suffix = '';
		if ($result) {
			if (!$name) $name = humanize(getPageParameterAt());
			$slug = $result['Slug'];
			$items = [variable(TAXONOMY)[$slug] . ': '];
			foreach ($result['in'] as $item) {
				$suffix = $name == $item ? NEWLINE . getLink(self::expand, pageUrl($slug . '/' . urlize($item) . '/?expand=2') . NOFOLLOWSUFFIX, bootstrapAndUX::button(getQueryParameter('expand') ? bootstrapAndUX::danger : bootstrapAndUX::info) . ' m-1') : '';
				$items[] = getLink($item, pageUrl($slug . '/' . urlize($item)), bootstrapAndUX::button($name == $item ? bootstrapAndUX::success : bootstrapAndUX::primary) . ' m-1') . $suffix;
			}
			$suffix = HRTAG . implode(NEWLINE, $items);
		}

		return bootstrapAndUX::colouredDiv('WRITING: ' . implode(NEWLINE, $all) . $suffix, cssUX::concat(bootstrapAndUX::warning, cssUX::CenterContainer, 'rounded-5'));
	}

	private static function link($item, $on = false) {
		if (!$on) $on = getPageParameterAt();
		$result = getLink(humanize($item['slug']), pageUrl('all/' . $item['slug']), bootstrapAndUX::buttonLarge($on == $item['slug'] ? bootstrapAndUX::success : bootstrapAndUX::primary) . ' m-1');
		if ($on == $item['slug'])
			$result .= ' ' . getLink(self::expand, pageUrl('all/' . $item['slug'] . '/?expand=1') . NOFOLLOWSUFFIX, bootstrapAndUX::buttonLarge(getQueryParameter('expand') == '1' ? bootstrapAndUX::danger : bootstrapAndUX::info) . ' m-1');
		return $result;
	}

	static function matchesWork($piece) {
		$tax = variable(TAXONOMY);
		foreach (self::All as $thisTax => $items) {
			foreach ($items as $item) {
				$name = $piece[$tax[$thisTax]];
				if (in_array($name, $item['in']))
					return self::enrich($item, $thisTax);
			}
		}
		return false;
	}

	static function setup($node, $on) {
		$specialTax = isset(self::All[$node]);
		$name = humanize($on);

		$result = false;
		$tax = false;

		$allSpecial = self::All;
		if ($node == 'all' || $specialTax) {
			if ($specialTax) $allSpecial = [$node => self::All[$node]];
			foreach ($allSpecial as $thisTax => $items) {
				foreach ($items as $item) {
					if ($node == 'all' && $item['slug'] == $on) {
						$tax = $thisTax;
						$result = $item;
					}

					if ($node != 'all' && $thisTax != $node)
						continue;

					if (in_array($name, $item['in'])) {
						$tax = $thisTax;
						$result = $item;
					}
				}
			}
		}

		if ($result) self::enrich($result, $tax);

		if ($node == 'all' || $result)
			variable(self::BannerName, self::banner($result));

		return $result;
	}

	private static function enrich(&$result, $tax) {
		$result['Slug'] = $tax;
		$result['Field'] = variable(TAXONOMY)[$tax];
		return $result;
	}
}

function renderMedia($piece, $inPiece = false) {
	$items = valueIfSet(($sheet = getSheet('media', 'Piece'))->group, $piece);
	if (!$items) return;

	$url = replaceHtml('%url%') . urlize($piece) . '/';

	if ($inPiece) contentBox('media', cssUX::container);
	h2($inPiece ? getLink('Media', pageUrl('media')) : getLink($piece, $url));

	$useHr = count($items) > 1;

	foreach ($items as $item) {
		if ($useHr) echo HRTAG;

		echo replaceItems('<h3 class="m-0">%date%</h3>%0%<p class="m-0 mb-2">%description%</p>%0%<b>credits</b>:%credits%' . BRNL . BRNL, [NEWLINE,
			'date' => $sheet->getValue($item, 'Date'),
			'description' => $sheet->getValue($item, 'Description'),
			'credits' => $sheet->getValue($item, 'Credits'),
		], WRAPREPLACE);

		if ($media = $sheet->getValue($item, 'Media')) {
			$macro = $sheet->getValue($item, 'Macro');
			echo runAllMacros(replaceItems('[%macro%]%media%[/%macro%]' . NEWLINE, ['macro' => $macro, 'media' => $media], WRAPREPLACE));
		} else {
			echo 'MEDIA NOT FOUND' . NEWLINE;
		}
	}
	if ($inPiece) contentBox('end');
}

function renderMetaPage($slug) {
	$name = getQueryParameter(VARQueryName);
	$headings = getQueryParameter(VARQueryHeadings);
	$wantsImg = in_array($slug, ['works', 'collections']);

	echo tagUX::selfClosetag(tagUX::HorizontalRule, cssUX::m2);
	$suffix = $name ? ' &mdash;> ' . humanize($name) : '';
	printSpacer(humanizeThis() . $suffix);

	contentBox($slug, 'container');

	$tax = variable(TAXONOMY);
	if (!isset($tax[$slug])) showDebugging('cms.php', 'TAXONOMY for ' . $slug . ' not defined', true);
	$groupBy = $tax[$slug];

	$sheet = sitemapTsv::read($groupBy);
	variable('sheet', $sheet);

	$op = [];
	$imgStart = '<div class="text-center after-content p-3 rounded-3"><img class="img-fluid img-max-400" src="'; $imgEnd = '" /></div>' . NEWLINE;

	if ($name || $headings) echo getLink(' == ALL == ', pageUrl($slug), 'btn btn-primary my-3');
	if (!$headings) echo getLink(' == ONLY HEADINGS == ', pageUrl($slug) . '?headings=1', 'btn btn-success my-3');

	foreach ($sheet->group as $key => $rows) {
		$text = humanize($key);
		$url = urlize($key);

		if ($name && $name != $url) continue;

		$count = '<span class="float-right">Count: ' . count($rows) . '</span>';
		$title = getLink($count . $text, pageUrl($slug . '/' . $url));
		$onlyMe = getLink('**', pageUrl($slug . '/?name=' . $url), 'btn btn-outline-info');
		$img = $wantsImg && disk_file_exists(SITECDNPATH . ($jpg = 'writing/' . $slug . '-' . $url . '.jpg')) ? $imgStart . getHtmlVariable('cdn') . $jpg . $imgEnd : '';
		$res = $img . h2($title . ' &mdash; ' . $onlyMe, '', true);

		if (!$headings) $res .= NEWLINE . implode(NEWLINE, array_map(function($piece) {
			$sheet = variable('sheet');
			return getLink($sheet->getSNo($piece) . ' ' . $sheet->getName($piece),
				$link = pageUrl(urlize($sheet->getName($piece))), 'btn btn-outline-info m-2 ms-0')
				. ' ' . getLinkWithCustomAttr('**', $link . '?content=1', ' data-lightbox="iframe" class="btn btn-outline-info"')
				. ' ' . $sheet->getDescription($piece) . BRTAG;
		}, $rows));

		$res .= cbCloseAndOpen('container');
		$op[$key] = $res;
	}

	ksort($op);
	echo implode(NEWLINES2, $op);

	contentBox('end');
}

//retain .txt and use .md for the deep dives

function printPiece($item, $where, $xofy = false, $relative = '') {
	if ($where != 'embed') contentBox('', 'container');
	if ($relative) echo '<span class="right-button">' . $relative . '</span>';

	$heading = '<!--noop-->' . $item['SNo'] . '. ' . $item['Name'];
	if ($where != 'before') $heading = getLink($heading, urlFromSlugs($item['Name']));

	if ($xofy) echo '<span style="float: right">' . $xofy . '</span>';
	h2($heading, 'm-0 p-0');

	echo BRNL . '<p class="mb-3 p-3 content-box after-content">' . $item['Description'] . '</p>';

	echo '<div class="large-list with-labels"><ul class="p-0 mb-0"><li>' . NEWLINE . implode('</li>' . NEWLINE . '	<li>', [
		'<label>Date: </label> '       . $item['Date'],
		'<label>Category: </label> '   . getLink(_getTaxonomyText($item['Category'], 'category'), urlFromSlugs('categories', $item['Category'])),
		'<label>Dedication: </label> ' . getLink($item['Dedication'], urlFromSlugs('for', $item['Dedication'])),
		'<label>Collection: </label> ' . getLink(_getTaxonomyText($item['Collection'], 'collection'), urlFromSlugs('collections', $item['Collection'])),
		'<label>Work: </label> ' . getLink(_getTaxonomyText($item['Work'], 'work'), urlFromSlugs('works', $item['Work'])),
		'<label>Rhymes: </label> ' . $item['RhymeScheme'],
	]) . NEWLINE . '</li></div>' . BRNL;

	if (getQueryParameter('expand')) renderAny($item['File']);

	if ($where != 'before' && $where != 'embed')
		contentBox('end');

	if ($where == 'embed') renderAny($item['File']);
}

function _getTaxonomyText($val, $type) {
	$sheet = getSheet('taxonomy', 'type');
	$groupKey = 'types_' . $type;
	if (!($group = variable($groupKey))) {
		$group = arrayGroupBy($sheet->group[$type], $sheet->columns['slug'], true);
		variable($groupKey, $group);
	}

	$key = urlize($val);
	if (!isset($group[$key])) return $val;

	$item = $group[$key][0];
	return $sheet->getValue($item, 'text');
}

//sets inner node for more/with-ai/
function site_before_render() {
	autosetPageMenu([VARDontOverwriteLogo => true, VARLinkToNodeHome => true, VARLinkToSubnodeHome => true]);

	if (getQueryParameter(VARQueryContent)) {
		add_body_class(cssUX::pt4);
		setSubTheme(VARSubthemeContentOnly);
	}
}

//================================================

class sitemapTsv extends sheet {
	const expected = 'EXPECTED!!';

	static function read($groupBy, $urlize = false) {
		return new sitemapTsv('sitemap', $groupBy, $urlize);
	}

	function enrichedPiece($item) {
		$result = $this->asObject($item);
		$type = $result['Type'];
		$workFol = $type == 'prose' ? '/' . $this->getWork($item, true) : '';
		$result['File'] = concatArgsWithSlash(
			SITEPATH, $type . $workFol,
			$this->getCollection($item),
			$this->getName($item, true) . '.txt',
		);
		return $result;
	}

	function getWork($item, $urlize = false) {
		return $this->getValue($item, 'Work', self::expected, $urlize);
	}

	function getCollection($item) {
		return $this->getValue($item, 'Collection');
	}

	function getName($item, $urlize = false) {
		return $this->getValue($item, 'Name', self::expected, $urlize);
	}

	function getDescription($item) {
		return $this->getValue($item, 'Description');
	}

	function getSNo($item) {
		return $this->getValue($item, 'SNo');
	}
}

//1 - piece checking happens here
function beforeSectionSet() {
	$node = nodeValue();

	$tax = variable(TAXONOMY);
	$isHelper = startsWith($node, '_');
	$isPseudo = in_array($node, ['all', 'poems', 'prose']);
	$isTax = in_array($node, array_keys($tax));

	$byWork = sitemapTsv::read('Work');

	if (!$isPseudo && !$isTax && !$isHelper) {
		$sheet = sitemapTsv::read('Name', true);

		if (!isset($sheet->group[$node]))
			return false;

		$item = $sheet->group[$node][0];

		$ofSameWork = $byWork->group[$sheet->getWork($item)];
		$indicesByName = [];
		foreach ($ofSameWork as $ix => $row) $indicesByName[$byWork->getName($row)] = $ix;

		$currentIndex = $indicesByName[$sheet->getName($item)];
		$current = $sheet->enrichedPiece($item);

		$match = special::matchesWork($current);
		if ($match) variable(special::BannerName, special::banner($match, $current[$match['Field']]));

		$previous = $currentIndex > 0 ? $sheet->enrichedPiece($ofSameWork[$currentIndex - 1]) : false;
		$next = $currentIndex < count($ofSameWork) -1 ? $sheet->enrichedPiece($ofSameWork[$currentIndex + 1]) : false;

		variables([
			'file' => $current['File'],
			'hasPiece' => true,
			'currentPiece' => $current,
			'previousPiece' => $previous,
			'nextPiece' => $next,
		]);

		afterSectionSet();
		return true;
	}

	$on = getPageParameterAt();
	$special = special::setup($node, $on);

	$isSpecial = $special != false;
	$doSpecial = $isSpecial && $node == 'all';

	if ($doSpecial)
		$sheet = sitemapTsv::read(false);
	else if ($node == 'works')
		$sheet = $byWork;
	else if (array_key_exists($node, $tax))
		$sheet = sitemapTsv::read($tax[$node], true);
	else if ($isPseudo || $isHelper)
		$sheet = sitemapTsv::read(false);

	if ($isTax) {
		if (!$isSpecial && !isset($sheet->group[$on]))
			return false;
		if ($on)
			variable('skip-content-render', true);
	}

	$items = $isPseudo || $isHelper || $doSpecial ? $sheet->rows : $sheet->group[$on];
	$pieces = [];
	foreach ($items as $item) {
		$obj = $sheet->enrichedPiece($item);
		if ($isHelper) continue;
		$specialMatch = $isSpecial && in_array($obj[$special['Field']], $special['in']);
		if ($isSpecial && !$specialMatch) continue;
		if ($specialMatch && $node != 'all') $specialMatch = false;
		if (!$specialMatch && $isPseudo && $node != 'all' && $node != $obj['Type']) continue;
		$pieces[] = $obj;
	}

	variables([
		'hasPieces' => true,
		'currentPieces' => $pieces,
	]);

	afterSectionSet();
	return true;
}

function getParentSlug($sectionFor) {
	if (getPageParameterAt(1)) return '';
	$piece = in_array($sectionFor, ['poems', 'prose', 'essays']);
	if ($piece) return $sectionFor;
	
	return '';
}

function getParentSlugForMenuItem($sectionFor, $item) {
	$piece = in_array($sectionFor, ['poems', 'prose', 'essays']);
	if ($piece) return 'collections/';

	$alias = in_array($sectionFor, ['works']);
	if ($alias) return 'works';
	
	return '';
};
