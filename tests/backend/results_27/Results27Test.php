<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Results\GetResults27\Data\ResultsFetcher;
use App\Results\GetResults27\Helpers\CardRenderer;
use App\Results\GetResults27\Helpers\TranslateTypeLoader;
use App\Results\GetResults27\Rows\ExistsRowBuilder;
use App\Results\GetResults27\Rows\InProcessRowBuilder;
use App\Results\GetResults27\Rows\MissingRowBuilder;
use App\Results\GetResults27\Tables\ExistsTable;
use App\Results\GetResults27\Tables\InProcessTable;
use App\Results\GetResults27\Tables\MissingTable;
use App\Results\GetResults27\ResultsLoader;

use function App\Results\GetResults27\results_loader_27;

class Results27Test extends TestCase
{
    /**
     * Test CardRenderer renders HTML correctly with title, body, and extra header.
     */
    public function testCardRendererRender(): void
    {
        $title = "Test Card Title";
        $body = "<p>Test Card Body</p>";
        $extraHeader = "<span class='badge'>Extra</span>";

        $html = CardRenderer::render($title, $body, $extraHeader);

        $this->assertStringContainsString('<span class="card-title h5">Test Card Title</span>', $html);
        $this->assertStringContainsString('<p>Test Card Body</p>', $html);
        $this->assertStringContainsString("<span class='badge'>Extra</span>", $html);
        $this->assertStringContainsString('data-card-widget="collapse"', $html);
    }

    /**
     * Test MissingRowBuilder build for logged out vs logged in vs fullTrUser vs video.
     */
    public function testMissingRowBuilderBuild(): void
    {
        $rowBuilder = new MissingRowBuilder();

        $titleData = [
            'w_lead_words' => 120,
            'r_lead_refs' => 5,
            'w_all_words' => 500,
            'r_all_refs' => 20,
            'importance' => 'High',
            'en_views' => 1500,
            'qid' => 'Q12345'
        ];

        // 1. Logged out user -> shows Login button
        $htmlLoggedOut = $rowBuilder->build(
            'Asthma',
            'lead',
            1,
            'ar',
            'RTT',
            'Campaign1',
            false,
            false,
            null, // empty globalUsername
            $titleData
        );
        $this->assertStringContainsString('Login', $htmlLoggedOut);
        $this->assertStringContainsString('/auth/login.php', $htmlLoggedOut);

        // 2. Logged in standard user -> shows Translate button
        $htmlLoggedIn = $rowBuilder->build(
            'Asthma',
            'lead',
            1,
            'ar',
            'RTT',
            'Campaign1',
            false,
            false,
            'User123',
            $titleData
        );
        $this->assertStringContainsString('Translate', $htmlLoggedIn);
        $this->assertStringNotContainsString('Login', $htmlLoggedIn);

        // 3. Logged in fullTrUser non-video -> shows Lead and Full buttons
        $htmlFullTr = $rowBuilder->build(
            'Asthma',
            'lead',
            1,
            'ar',
            'RTT',
            'Campaign1',
            false,
            true, // fullTrUser
            'User123',
            $titleData
        );
        $this->assertStringContainsString('>Lead<', $htmlFullTr);
        $this->assertStringContainsString('>Full<', $htmlFullTr);

        // 4. Video title -> sets traType='all' and shows single Translate button even for fullTrUser
        $htmlVideo = $rowBuilder->build(
            'Video:Asthma_treatment',
            'lead',
            1,
            'ar',
            'RTT',
            'Campaign1',
            false,
            true,
            'User123',
            $titleData
        );
        $this->assertStringContainsString('Translate', $htmlVideo);
        $this->assertStringNotContainsString('>Lead<', $htmlVideo);

        // 5. Full row display counter formatting
        $htmlFullRow = $rowBuilder->build(
            'Asthma',
            'all',
            2,
            'ar',
            'RTT',
            'Campaign1',
            true, // isFullRow
            false,
            'User123',
            $titleData
        );
        $this->assertStringContainsString('2.Full', $htmlFullRow);
    }

    /**
     * Test ExistsRowBuilder build.
     */
    public function testExistsRowBuilderBuild(): void
    {
        $rowBuilder = new ExistsRowBuilder();

        $titleDataTd = [
            'importance' => 'Top',
            'qid' => 'Q54321',
            'target' => 'ربو',
            'via' => 'td'
        ];

        $htmlTd = $rowBuilder->build(
            'Asthma',
            1,
            'ar',
            'RTT',
            'Campaign1',
            $titleDataTd,
            'CoordinatorUser',
            true, // userCoord
            'https://mdwikicx.toolforge.org/w/index.php'
        );

        $this->assertStringContainsString('Asthma', $htmlTd);
        $this->assertStringContainsString('ربو', $htmlTd);
        $this->assertStringContainsString('Translate', $htmlTd);
        $this->assertStringContainsString('Q54321', $htmlTd);

        $titleDataBefore = [
            'importance' => 'Top',
            'qid' => 'Q54321',
            'target' => 'ربو_سابق',
            'via' => 'before'
        ];

        // Non-coordinator logged in user -> no Translate button
        $htmlBefore = $rowBuilder->build(
            'Asthma',
            2,
            'ar',
            'RTT',
            'Campaign1',
            $titleDataBefore,
            'StandardUser',
            false, // non-coordinator
            'https://mdwikicx.toolforge.org/w/index.php'
        );

        $this->assertStringNotContainsString('Translate', $htmlBefore);
        $this->assertStringContainsString('ربو_سابق', $htmlBefore);
    }

    /**
     * Test InProcessRowBuilder build.
     */
    public function testInProcessRowBuilderBuild(): void
    {
        $rowBuilder = new InProcessRowBuilder();

        $inProcessData = [
            'user' => 'TranslatorUser',
            'date' => '2026-09-17 12:00:00',
            'translate_type' => 'lead'
        ];

        $titleData = [
            'w_lead_words' => 200,
            'r_lead_refs' => 8,
            'importance' => 'Medium',
            'en_views' => 800,
            'qid' => 'Q999'
        ];

        // Same logged-in translator
        $html = $rowBuilder->build(
            'Diabetes',
            'lead',
            1,
            'es',
            'RTT',
            'Campaign1',
            $inProcessData,
            false, // inProgressButton
            false, // isFullRow
            false, // fullTrUser
            'TranslatorUser', // globalUsername
            $titleData,
            'https://mdwikicx.toolforge.org/w/index.php',
            false // userCoord
        );

        $this->assertStringContainsString('Diabetes', $html);
        $this->assertStringContainsString('TranslatorUser', $html);
        $this->assertStringContainsString('2026-09-17', $html);
        $this->assertStringContainsString('Translate', $html);

        // Video in-process item displays 1.Full counter check disabled if video
        $htmlVideo = $rowBuilder->build(
            'Video:Diabetes_overview',
            'all',
            1,
            'es',
            'RTT',
            'Campaign1',
            $inProcessData,
            false,
            true, // isFullRow
            false,
            'TranslatorUser',
            $titleData,
            'https://mdwikicx.toolforge.org/w/index.php',
            false
        );
        $this->assertStringNotContainsString('1.Full', $htmlVideo);
        $this->assertStringContainsString('scope="row">1</th>', $htmlVideo);
    }

    /**
     * Test MissingTable render sorting and filtering.
     */
    public function testMissingTableRender(): void
    {
        $items = [
            [
                'title' => 'Page_B',
                'en_views' => 100,
                'w_lead_words' => 50,
                'r_lead_refs' => 2,
                'importance' => 'Low',
                'qid' => 'Q2'
            ],
            [
                'title' => 'Page_A',
                'en_views' => 500,
                'w_lead_words' => 150,
                'r_lead_refs' => 5,
                'importance' => 'High',
                'qid' => 'Q1'
            ],
            [
                'title' => '', // should be skipped
                'en_views' => 0
            ]
        ];

        $table = new MissingTable(
            'ar',
            'RTT',
            'Campaign1',
            'lead',
            false,
            'User1',
            ['NoLeadPage'],
            ['FullPage']
        );

        $html = $table->render($items);

        $this->assertStringContainsString('Page A', $html);
        $this->assertStringContainsString('Page B', $html);

        // Verify Page_A appears before Page_B due to higher en_views
        $posA = strpos($html, 'Page A');
        $posB = strpos($html, 'Page B');
        $this->assertNotFalse($posA);
        $this->assertNotFalse($posB);
        $this->assertLessThan($posB, $posA);
    }

    /**
     * Test ExistsTable render.
     */
    public function testExistsTableRender(): void
    {
        $items = [
            'Asthma' => [
                'target' => 'ربو',
                'via' => 'td',
                'qid' => 'Q123'
            ],
            'Diabetes' => [
                'target' => 'سكري',
                'via' => 'before',
                'qid' => 'Q456'
            ],
            '' => [] // empty title skipped
        ];

        $table = new ExistsTable(
            'ar',
            'RTT',
            'Campaign1',
            'CoordUser',
            true,
            'https://mdwikicx.toolforge.org/w/index.php'
        );

        $html = $table->render($items);

        $this->assertStringContainsString('Translated (1)', $html);
        $this->assertStringContainsString('Translated before (1)', $html);
        $this->assertStringContainsString('Asthma', $html);
        $this->assertStringContainsString('Diabetes', $html);
    }

    /**
     * Test InProcessTable render.
     */
    public function testInProcessTableRender(): void
    {
        $items = [
            'Asthma' => [
                'user' => 'User1',
                'date' => '2026-01-01',
                'translate_type' => 'lead'
            ],
            'Video:Hypertension' => [
                'user' => 'User2',
                'date' => '2026-01-02',
                'translate_type' => 'all'
            ],
            '' => [] // empty title skipped
        ];

        $titlesInfos = [
            'Asthma' => [
                'w_lead_words' => 100,
                'r_lead_refs' => 3,
                'importance' => 'High',
                'en_views' => 1000,
                'qid' => 'Q111'
            ],
            'Video:Hypertension' => [
                'w_all_words' => 300,
                'r_all_refs' => 10,
                'importance' => 'Medium',
                'en_views' => 2000,
                'qid' => 'Q222'
            ]
        ];

        $table = new InProcessTable(
            'ar',
            'RTT',
            'Campaign1',
            true, // inProgressButton
            false,
            'User1',
            $titlesInfos,
            'https://mdwikicx.toolforge.org/w/index.php',
            false
        );

        $html = $table->render($items);

        $this->assertStringContainsString('Asthma', $html);
        $this->assertStringContainsString('Video:Hypertension', $html);
        $this->assertStringContainsString('User1', $html);
        $this->assertStringContainsString('User2', $html);
    }

    /**
     * Test ResultsFetcher get method.
     */
    public function testResultsFetcher(): void
    {
        $fetcher = new ResultsFetcher(false);
        $results = $fetcher->get('RTT', 'ar');

        $this->assertArrayHasKey('ix', $results);
        $this->assertArrayHasKey('inprocess', $results);
        $this->assertArrayHasKey('exists', $results);
        $this->assertArrayHasKey('missing', $results);
    }

    /**
     * Test ResultsLoader and wrapper functions.
     */
    public function testResultsLoaderAndWrappers(): void
    {
        // 2. ResultsLoader class & results_loader_27 function
        $loader = new ResultsLoader();
        $inputData = [
            'camp' => 'TestCamp',
            'code' => 'ar',
            'cat' => 'RTT',
            'show_exists' => true,
            'global_username' => 'TestUser',
            'in_progress_translation_button' => false,
            'tra_type' => 'lead',
            'user_coord' => false,
            'test' => true,
            'code_lang_name' => 'Arabic'
        ];

        $html = $loader->load($inputData);
        $this->assertStringContainsString('code:ar', $html);
        $this->assertStringContainsString('code_lang_name:Arabic', $html);

        $htmlFromFunc = results_loader_27($inputData);
        $this->assertNotEmpty($htmlFromFunc);
    }
}
