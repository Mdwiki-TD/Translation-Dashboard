<?php

namespace App\Leaderboard\Helpers\Filters;

use App\ApiClients\WikiApi;
use App\Utils\HtmlUrls;
use App\Utils\TrLink;
use App\Leaderboard\Helpers\Camps\Camps;

class LeadHelp
{
	public static function make_key(array $Taab): string
	{
		$dat = '';

		foreach (['pupdate', 'date', 'add_date'] as $key) {
			if (!empty($Taab[$key])) {
				$dat = $Taab[$key];
				break;
			}
		}

		// if $_date_ has : then split before first space
		if (strpos($dat, ':') !== false) {
			$dat = explode(' ', $dat)[0];
		}

		$urt = '';

		if (!empty($dat)) {
			$urt = str_replace('-', '', $dat) . ':';
		}

		return $urt . $Taab['lang'] . ':' . $Taab['title'];
	}

	public static function make_td_fo_user(
		array $tabb,
		int $number,
		int|float $view_number,
		int|float|string $word,
		string $page_type,
		string $tab_ty,
		bool $user_is_global_username,
		array $new_camps,
	): string {

		// $page_type = 'users' or 'langs' only
		if ($page_type != 'users' && $page_type != 'langs') {
			$page_type = 'users';
		}

		$mdtitle  = trim($tabb['title']);
		$user	 = $tabb['user'] ?? "";
		$lang	 = $tabb['lang'] ?? "";
		$cat	  = $tabb['cat'] ?? "";
		$deleted  = $tabb['deleted'] ?? "";
		$pupdate  = $tabb['pupdate'] ?? "";
		$campaign = $tabb['campaign'] ?? "";

		$date	= $tabb['date'] ?? $tabb['add_date'] ?? "";

		// if $_date_ has : then split before first space
		if (strpos($date, ':') !== false) {
			$date = explode(' ', $date)[0];
		}

		$word = number_format((float)$word);

		$mdwiki_url = HtmlUrls::make_mdwiki_article_url_blank($mdtitle);

		$cat_or_camp_link = HtmlUrls::make_mdwiki_cat_url($cat);

		$campaign_data = $campaign;

		// 2023-08-22
		if (count($new_camps) > 0) {
			$campaign_data = "";
			$cat_or_camp_link = "";
			foreach ($new_camps as $campaign) {
				$cat_or_camp_link .= "<a href='leaderboard.php?camp=$campaign' style='white-space: nowrap;'>$campaign</a><br>";
				$campaign_data .= "$campaign, ";
			}
			// remove last <br>
			$cat_or_camp_link = substr($cat_or_camp_link, 0, -4);
		} else {
			// echo "No campaigns for $mdtitle<br>";
			if (!empty($campaign)) {
				$cat_or_camp_link = "<a href='leaderboard.php?camp=$campaign'>$campaign</a>";
			}
		}

		$tran_type = $tabb['translate_type'] ?? '';

		$usr_or_lang = ($page_type == 'users') ? "Lang" : "User";

		$urll_data = '';

		if ($page_type == 'users') {
			$urll = "<a href='leaderboard.php?get=langs&langcode=$lang'><span style='white-space: nowrap;'>$lang</span></a>";
			$urll_data = $lang;
		} else {
			$use = rawurlencode($user);
			$use = str_replace('+', '_', $use);

			$urll = "<a href='leaderboard.php?get=users&user=$use'><span style='white-space: nowrap;'>$user</span></a>";
			$urll_data = $user;
		}

		$udate = $pupdate;
		$complete   = '';

		$target = "";

		if ($tab_ty == 'pending') {
			$udate = $date;
			$target_link = 'Pending';
			$td_views = '';

			$tralink = TrLink::makeContentTranslationUrl(
				$mdtitle,
				$lang,
				$cat,
				$campaign,
				$tran_type,
			);
			$complete   = ($user_is_global_username) ? "<td data-content='complete'><a target='_blank' href='$tralink'>complete</a></td>" : '';
		} else {
			$target  = trim($tabb['target']);

			$view = "-";
			if ($deleted == 0) {
				$view = WikiApi::make_view_by_number($target, $view_number, $lang, $pupdate);
			}

			$target_link = HtmlUrls::make_wikipedia_url_blank($target, $lang, "", $deleted);

			$td_views = "<td data-content='Views' data-sort='$view_number' data-filter='$view_number'>$view</td>";
		}

		$year = substr($udate, 0, 4);

		$laly = <<<HTML
			<!-- <tr class='filterDiv show2 $year'> -->
			<tr>
				<th data-content="#">
					$number
				</th>
				<td data-content="$usr_or_lang" data-filter="$urll_data">
					$urll
				</td>
				<td data-content="Title" data-filter="$mdtitle">
					$mdwiki_url
				</td>
				<td data-content="Campaign" data-filter="$campaign_data">
					$cat_or_camp_link
				</td>
				<td data-content="Type" data-filter="$tran_type">
					$tran_type
				</td>
				<td data-content="Words" data-filter="$word">
					$word
				</td>
				<td data-content="Translated" data-filter="$target">
					$target_link
				</td>
				<td data-content="Date" class='spannowrap' data-filter="$year">
					$udate
				</td>
				$td_views
				$complete
			</tr>
			HTML;

		return $laly;
	}

	public static function make_table_lead(
		array $missingItems,
		string $tab_type,
		string $page_type,
		bool $user_is_global_username,
	): array {
		$total_words = 0;
		$total_views = 0;

		$user_or_lang = ($page_type == 'users') ? 'Lang.' : 'User';

		$tab_views  = ($tab_type == 'pending') ? '' : '<th>Views</th>';
		$th_Date    = ($tab_type == 'pending') ? 'Start date' : 'Date';
		$complete   = ($tab_type == 'pending' && $user_is_global_username) ? '<th>complete!</th>' : '';

		$leadtable = ($tab_type == 'pending') ? 'leadtable2' : 'leadtable';

		// table-mobile-responsive
		$table2 = <<<HTML
			<table class='table table-striped compact table_text_left table_responsive' id='$leadtable'>
				<thead>
					<tr>
						<th>#</th>
						<th data-priority="1">$user_or_lang</th>
						<th data-priority="2">Title</th>
						<th>Campaign</th>
						<th>Type</th>
						<th>Words</th>
						<th data-priority="3">Translated</th>
						<th>$th_Date</th>
						$tab_views
						$complete
					</tr>
				</thead>
				<tbody>
			HTML;

		$total_articles = count($missingItems);
		$noo = 0;

		$articlesto_camps = Camps::get_articles_to_camps();

		foreach ($missingItems as $tat => $tabe) {

			$noo += 1;

			$deleted = $tabe['deleted'] ?? 0;

			$view_number  = $tabe['views'] ?? 0;

			if ($deleted == 1) {
				$view_number = 0;
			}

			$total_views += $view_number;

			$mdtitle = $tabe['title'] ?? "";

			$word = $tabe['word'] ?? 0;

			$total_words += $word;

			$new_camps = $articlesto_camps[trim($mdtitle)] ?? [];

			$table2 .= self::make_td_fo_user(
				$tabe,
				$noo,
				$view_number,
				$word,
				$page_type,
				$tab_type,
				$user_is_global_username,
				$new_camps,
			);
		}

		$table2 .= <<<HTML
			</tbody>
			<tfoot>
			</tfoot>
		</table>
		HTML;

		$table1 = ['total_articles' => $total_articles, 'total_words' => $total_words, 'total_views' => $total_views];

		return [$table1, $table2];
	}

	public static function make_users_lead(
		array $tab,
		string $tab_type,
		bool $user_is_global_username,
	): array {
		[$_, $table_pnd] = self::make_table_lead(
			$tab,
			$tab_type,
			'users',
			$user_is_global_username,
		);

		return [$_, $table_pnd];
	}

	public static function make_langs_lead(
		array $tab,
		string $tab_type,
	): array {
		[$_, $table_pnd] = self::make_table_lead(
			$tab,
			$tab_type,
			'langs',
			false,
		);

		return [$_, $table_pnd];
	}
}
