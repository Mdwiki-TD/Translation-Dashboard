<?php

function generateListItem($id, $href, $title, $filename, $target = '') {
	$li = "<li class='nav-item col-3 col-lg-auto' id='%s'><a class='linknave' href='$filename?ty=%s'>%s</a></li>";
	$li_blank = "<li class='nav-item col-3 col-lg-auto' id='%s'><a target='_blank' class='linknave' href='%s'>%s</a></li>";

	$template = $target ? $li_blank : $li;
    return sprintf($template, $id, $href, $title);
}

function generateSpan($filename, $text) {
    return <<<HTML
		<span class='d-flex align-items-center pb-1 mb-1 text-decoration-none border-bottom'>
			<a class='nav-link' href='$filename'>
				<span id='Home' class='fs-5 fw-semibold'>$text</span>
			</a>
		</span>
	HTML;
}

function create_side($filename) {

    $mainMenu = [
        'Translations' => [
		['id' => 'last', 'admin' => 0, 'href' => 'last', 'title' => 'Recent'],
		['id' => 'process', 'admin' => 0, 'href' => 'process', 'title' => 'In process'],
		['id' => 'Pending', 'admin' => 0, 'href' => 'Pending', 'title' => 'In process (total)'],
		['id' => 'add', 'admin' => 1, 'href' => 'add', 'title' => 'Add'],
		['id' => 'translate_type', 'admin' => 1, 'href' => 'translate_type', 'title' => 'Translate Type'],
        ],
        'Users' => [
		['id' => 'Emails', 'admin' => 1, 'href' => 'Emails', 'title' => 'Emails'],
		['id' => 'projects', 'admin' => 1, 'href' => 'projects', 'title' => 'Projects'],
        ],
        'Others' => [
		['id' => 'coordinators', 'admin' => 1, 'href' => 'coordinators', 'title' => 'Coordinators'],
		['id' => 'Campaigns', 'admin' => 1, 'href' => 'Campaigns', 'title' => 'Campaigns'],
		['id' => 'stat', 'admin' => 0, 'href' => 'stat', 'title' => 'Status'],
		['id' => 'settings', 'admin' => 1, 'href' => 'settings', 'title' => 'Settings'],
		['id' => 'qidsload', 'admin' => 1, 'href' => 'qids/load', 'title' => 'qids'],
        ],
        'Tools' => [
		['id' => 'wikirefs_options', 'admin' => 1, 'href' => 'wikirefs_options', 'title' => 'Fixwikirefs (options)'],
		['id' => 'fixwikirefs', 'admin' => 0, 'href' => '../fixwikirefs.php', 'title' => 'Fixwikirefs', 'target' => '_blank'],
        ],
	];

    $homeSpan = generateSpan($filename, 'Coordinator Tools');

	$sidebar = <<<HTML
	<nav class="navbar-nav">
		$homeSpan
	HTML;

	foreach ($mainMenu as $key => $items) {
		$lis = '';
        foreach ($items as $item) {
			$target = $item['target'] ?? '';
			$admin = $item['admin'] ?? 0;

			if ($admin == 1 && !user_in_coord) continue;

            $lis .= generateListItem($item['id'], $item['href'], $item['title'], $filename, $target);
		}

		if ($lis !== '') {
			$sidebar .= <<<HTML
				<span class='fs-6 fw-semibold'>$key:</span>
				<ul class='navbar-nav flex-row flex-wrap d-lg-table d-md-table'>
					$lis
				</ul>
			HTML;
		}
	}

    $sidebar .= "</nav>";

	return $sidebar;
}

?>
