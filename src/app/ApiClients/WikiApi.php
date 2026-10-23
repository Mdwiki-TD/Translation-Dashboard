<?php

namespace App\ApiClients;

class WikiApi
{
    public static function getUrlResultCurl(string $url): string
    {
        $usrAgent = "WikiProjectMed Translation Dashboard/1.0 (https://mdwiki.toolforge.org/; tools.mdwiki@toolforge.org)";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT      => $usrAgent,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT        => 5,
            // لإضافة ملفات الكوكيز لاحقًا فقط أزل علامة التعليق:
            // CURLOPT_COOKIEJAR => "cookie.txt",
            // CURLOPT_COOKIEFILE => "cookie.txt",
        ]);

        $output = curl_exec($ch);
        if ($output === false) {
            echo "<br>cURL Error: " . curl_error($ch) . "<br>$url";
        }

        return $output === false ? '' : $output;
    }
    /**
     * Builds the pageviews link/number for a single row.
     */
    public static function makeViewByNumber(
        string $target,
        mixed $numb,
        string $lang,
        ?string $pupdate
    ): string {
        // remove spaces and tab characters
        $target = trim($target);
        $numb2 = (!empty($numb)) ? $numb : "?";
        $start = !empty($pupdate) ? $pupdate : '2019-01-01';
        $end = date("Y-m-d", strtotime("yesterday"));
        $params = [
            'project'   => "$lang.wikipedia.org",
            'platform'  => 'all-access',
            'agent'     => 'all-agents',
            'start'     => $start,
            'end'       => $end,
            // 'range' => 'all-time',
            'redirects' => '0',
            'pages'     => $target,
        ];
        $url = 'https://pageviews.wmcloud.org/?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        $numb3 = (is_numeric($numb2)) ? number_format((float) $numb2) : $numb2;
        $link = "<a target='_blank' href='$url'>$numb3</a>";

        if (is_numeric($numb2) && intval($numb2) > 0) {
            return $link;
        }

        $start2 = !empty($pupdate) ? str_replace('-', '', $pupdate) : '20190101';

        $url2 = 'https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/' . $lang . '.wikipedia/all-access/all-agents/' . rawurlencode($target) . '/daily/' . $start2 . '/2030010100';

        return "<a target='_blank' name='toget' data-json-url='$url2' href='$url'>$numb2</a>";
    }
    public static function getViews(string $target, string $lang, ?string $pupdate): int
    {
        if ($target === '')
            return 0;
        $start2 = !empty($pupdate) ? str_replace('-', '', $pupdate) : '20190101';
        $url = 'https://wikimedia.org/api/rest_v1/metrics/pageviews/per-article/' . $lang . '.wikipedia/all-access/all-agents/' . rawurlencode($target) . '/daily/' . $start2 . '/2030010100';

        // $output = file_get_contents( $url );
        $output = self::getUrlResultCurl($url);

        $result = json_decode($output, true);

        if (!is_array($result)) {
            $result = [];
        }

        // $view = 0;
        // foreach ($result['items'] AS $da) $view += $da['views'];

        $view = isset($result['items']) ? array_sum(array_column($result['items'], 'views')) : 0;

        return $view;
    }
}
