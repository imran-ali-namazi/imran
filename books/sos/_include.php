<?php
if (getPageParameterAt() == 'read' && getQueryParameter(VARQueryContent))
	addStyle('print', assetManager::core);

if (getPageParameterAt() == 'go' && $to = getPageParameterAt(2)) {
	$links = getSheet(__DIR__ . '/data/links.tsv', false);
	foreach ($links->rows as $item) {
		$no = $links->getValue($item, 'sno');
		if ($to == $no) {
			$url = $links->getValue($item, 'link');
			$url = replaceHtml($url);
			header('Location: ' . $url);
			exit;
		}
	}
}
