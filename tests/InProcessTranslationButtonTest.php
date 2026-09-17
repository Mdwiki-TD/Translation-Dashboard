<?php

declare(strict_types=1);

namespace MyLibrary\Tests;

use PHPUnit\Framework\TestCase;

use function Results\Helps\make_translate_urls;
use function Results\GetResults2026\Rows\make_one_row_new_inprocess;
use function Results\ResultsTableHtml\make_table_start;
use function Leaderboard\Subs\LeadHelp\make_td_fo_user;

class InProcessTranslationButtonTest extends TestCase
{
    public function testMakeTranslateUrlsWhenInProcessAndButtonDisabledForNonTranslator(): void
    {
        // When inprocess is true, in_progress_translation_button is 0, and user is NOT translator/coordinator
        [$tab, $translate_url, $full_translate_url] = make_translate_urls(
            'Cardiovascular disease',
            'lead',
            100,
            'als',
            'RTT',
            'Occupational Health',
            true, // $inprocess
            '0',  // $in_progress_translation_button
            'Mr. Ibrahem',
            false,
            false, // $login_user_is_the_translator
            'https://mdwikicx.toolforge.org/w/index.php'
        );

        $this::assertSame('', $tab);
        $this::assertStringContainsString('mdwiki.org', $translate_url);
    }

    public function testMakeTranslateUrlsWhenInProcessAndUserIsTranslatorEvenIfButtonDisabled(): void
    {
        // When inprocess is true, in_progress_translation_button is 0, BUT user IS translator/coordinator
        [$tab, $translate_url, $full_translate_url] = make_translate_urls(
            'Cardiovascular disease',
            'lead',
            100,
            'als',
            'RTT',
            'Occupational Health',
            true, // $inprocess
            '0',  // $in_progress_translation_button
            'Mr. Ibrahem',
            false,
            true,  // $login_user_is_the_translator
            'https://mdwikicx.toolforge.org/w/index.php'
        );

        $this::assertStringContainsString('Translate', $tab);
        $this::assertStringContainsString('class=\'btn btn-outline-primary btn-sm\'', $tab);
        $this::assertStringContainsString('mdwikicx.toolforge.org', $translate_url);
    }

    public function testMakeOneRowNewInprocessShowsTranslateButtonAndTypeForCoordinator(): void
    {
        $inprocessTable = [
            'user' => 'Mr. Ibrahem',
            'date' => '2026-09-17',
            'translate_type' => 'lead'
        ];

        $titleData = [
            'w_lead_words' => 361,
            'r_lead_refs' => 12,
            'importance' => 'Unknown',
            'en_views' => 22158,
            'qid' => 'Q389735'
        ];

        // User is coordinator ($user_coord = true) and in_progress_translation_button = '0'
        $html = make_one_row_new_inprocess(
            'Cardiovascular disease',
            'lead',
            1,
            'als',
            'RTT',
            'Occupational Health',
            $inprocessTable,
            '0', // $in_progress_translation_button setting disabled
            false,
            false,
            'Mr. Ibrahem', // $global_username
            $titleData,
            'https://mdwikicx.toolforge.org/w/index.php',
            true // $user_coord
        );

        $this::assertStringContainsString('Translate', $html);
        $this::assertStringContainsString('Mr. Ibrahem', $html);
        $this::assertStringContainsString('lead', $html);
    }

    public function testMakeOneRowNewInprocessHidesTranslateButtonForLoggedOutUser(): void
    {
        $inprocessTable = [
            'user' => 'Mr. Ibrahem',
            'date' => '2026-09-17',
            'translate_type' => 'lead'
        ];

        $titleData = [
            'w_lead_words' => 361,
            'r_lead_refs' => 12,
            'importance' => 'Unknown',
            'en_views' => 22158,
            'qid' => 'Q389735'
        ];

        // Non-logged-in user ($global_username = '')
        $html = make_one_row_new_inprocess(
            'Cardiovascular disease',
            'lead',
            1,
            'als',
            'RTT',
            'Occupational Health',
            $inprocessTable,
            '1', // even if setting is 1
            false,
            false,
            '', // empty $global_username
            $titleData,
            'https://mdwikicx.toolforge.org/w/index.php',
            false
        );

        $this::assertStringNotContainsString('Translate', $html);
        $this::assertStringContainsString('lead', $html);
    }

    public function testMakeOneRowNewInprocessShowsTranslateButtonForSameTranslator(): void
    {
        $inprocessTable = [
            'user' => 'Mr. Ibrahem',
            'date' => '2026-09-17',
            'translate_type' => 'lead'
        ];

        $titleData = [
            'w_lead_words' => 361,
            'r_lead_refs' => 12,
            'importance' => 'Unknown',
            'en_views' => 22158,
            'qid' => 'Q389735'
        ];

        // User is same as translator ($_user_ == $global_username), not coord, setting = '0'
        $html = make_one_row_new_inprocess(
            'Cardiovascular disease',
            'lead',
            1,
            'als',
            'RTT',
            'Occupational Health',
            $inprocessTable,
            '0',
            false,
            false,
            'Mr. Ibrahem',
            $titleData,
            'https://mdwikicx.toolforge.org/w/index.php',
            false // $user_coord
        );

        $this::assertStringContainsString('Translate', $html);
        $this::assertStringContainsString('Mr. Ibrahem', $html);
        $this::assertStringContainsString('lead', $html);
    }

    public function testMakeOneRowNewInprocessHidesTranslateButtonForOtherUserWhenDisabled(): void
    {
        $inprocessTable = [
            'user' => 'OtherUser',
            'date' => '2026-09-17',
            'translate_type' => 'lead'
        ];

        $titleData = [
            'w_lead_words' => 361,
            'r_lead_refs' => 12,
            'importance' => 'Unknown',
            'en_views' => 22158,
            'qid' => 'Q389735'
        ];

        // Logged in as Mr. Ibrahem, item is assigned to OtherUser, not coord, setting = '0'
        $html = make_one_row_new_inprocess(
            'Cardiovascular disease',
            'lead',
            1,
            'als',
            'RTT',
            'Occupational Health',
            $inprocessTable,
            '0',
            false,
            false,
            'Mr. Ibrahem',
            $titleData,
            'https://mdwikicx.toolforge.org/w/index.php',
            false
        );

        $this::assertStringNotContainsString('Translate', $html);
        $this::assertStringContainsString('OtherUser', $html);
    }

    public function testMakeTableStartIncludesTranslateAndTypeHeaderForInProcess(): void
    {
        $headerHtml = make_table_start(true, '0');
        $this::assertStringContainsString('<th><span>Translate</span></th>', $headerHtml);
        $this::assertStringContainsString('>Type</th>', $headerHtml);
    }

    public function testMakeTdFoUserIncludesTypeCell(): void
    {
        $tabb = [
            'title' => 'Cardiovascular disease',
            'user' => 'Mr. Ibrahem',
            'lang' => 'ar',
            'cat' => 'RTT',
            'translate_type' => 'lead',
            'word' => 361,
            'date' => '2026-09-17',
            'target' => 'أمراض القلب'
        ];

        $rowHtml = make_td_fo_user(
            $tabb,
            1,
            100,
            361,
            'users',
            'pending',
            true,
            [],
            'https://mdwikicx.toolforge.org/w/index.php'
        );

        $this::assertStringContainsString('data-content="Type" data-filter="lead"', $rowHtml);
        $this::assertStringContainsString('lead', $rowHtml);
    }

    public function testSettingsArrayColumnLookup(): void
    {
        $rawSettings = [
            ['id' => 1, 'title' => 'translation_button_in_progress_table', 'value' => '1'],
            ['id' => 2, 'title' => 'allow_type_of_translate', 'value' => '0'],
        ];

        $settings = array_column($rawSettings, 'value', 'title');

        $in_progress_translation_button = $settings['translation_button_in_progress_table'] ?? '0';
        $this::assertSame('1', $in_progress_translation_button);
    }
}
