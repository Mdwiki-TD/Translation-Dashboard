<?php
namespace EnWiki\Fixes\RefWork;
/*
Usage:

use function EnWiki\Fixes\RefWork\check_one_cite;
use function EnWiki\Fixes\RefWork\remove_bad_refs;

*/
/*

https://en.wikipedia.org/wiki/Special:AbuseFilter/894
*
*
https://en.wikipedia.org/wiki/Special:AbuseFilter/891
* https://en.wikipedia.org/wiki/Special:AbuseLog/38203012
*

*/

use function WikiParse\Citations\getCitations;

/**
 * Check if the citation contains self-published information and filter it out.
 *
 * @param string $cite The citation text to check
 * @return bool Returns true if self-published information is found and removed, false otherwise
 */

const DOI_PATTERN = "/doi[ ]*?[=\|\:][ ]*?10\.(11648|1166|1234|12677|12692|12720|12988|13005|13172|13188|14218|14257|14303|14419|14445|1453|14569|14662|14738|15373|15406|15415|15680|15761|17265|18005|18052|18311|18775|19030|19044|19070|19080|1999|20319|20431| 20472|20849|20902|21102|21767|22158|23937|2495|30845|35841|36648|3844|3923|3968|4018|4156|4172|4236|4303|5267|5296|5376|5430|5455|5539|5567|5580|5772|5812|5815|5829|5897|5899|5923|5963|6007|7243|7439|7537|7575|7718|7763|9734)/i";

const OPEN_ACCESS_JOURNALS_PATTERN = "/(academicjournals\.com|academicjournals\.net|academicjournals\.org|academicpub\.org|academicresearchjournals\.org|aiac\.org\.au|aicit\.org|alliedacademies\.org|arcjournals\.org|ashdin\.com|aspbs\.com|avensonline\.org|biomedres\.info|biopublisher\.ca|bowenpublishing\.com|ccsenet\.org|cennser\.org|clinmedjournals\.org|cluteinstitute\.com|conferenceseries\.com|cpinet\.info|cscanada\.net|davidpublisher\.org|etpub\.com|eujournal\.org|growingscience\.com|grdspublishing\.org|hanspub\.org|hoajonline\.com|hrmars\.com|iacsit\.org|iamure\.com|\.idosi\.org|igi-global\.com|iises\.net|imedpub\.com|informaticsjournals\.com|innspub\.net|intechopen\.com|intechweb\.org|interesjournals\.org|internationaljournalssrg\.org|ispacs\.com|ispub\.com|julypress\.com|juniperpublishers\.com|kowsarpub\.com|kspjournals\.org|longdom\.org|m-hikari\.com|macrothink\.org|mecs-press\.org|medcraveonline\.com|oapublishinglondon\.com|oatext\.com|omicsonline\.org|ospcindia\.org|researchleap\.com|sapub\.org|scholink\.org|scialert\.net|scidoc\.org|sciencedomain\.org|sciencedomains\.org|sciedu\.ca|sciencepg\.com|sciencepub\.net|sciencepubco\.com|sciencepublication\.org|sciencepublishinggroup\.com|scipg\.net|scipress\.com|scirp\.org|scopemed\.com|sersc\.org|sphinxsai\.com|scholarpublishing\.org|\.ssjournals\.com|thesai\.org|waset\.org|witpress\.com|worldwidejournals\.com|xandhpublishing\.com|xiahepublishing\.com|zantworldpress\.com)/i";

const SELFPUB_PATTERN = "/(publisher|work)\s*[=,:]\s*(Author\s*House|CreateSpace|Trafford\s*Publishing|iUniverse\s*|Lulu|XLibris|Edwin\s*Mellen\s*Press|Grosvenor\s*House\s*Publishing)/i";

const SELFPUB_URL_PATTERN = "/(authorhouse\.com|createspace\.\w{2,3}|grosvenorhousepublishing\.com|iuniverse\.com|lulu\.com|mellenpress\.com|trafford\.com|xlibris\.com)/i";

function check_one_cite($cite)
{
    // ---
    // echo $cite . "<br";
    //---
    $cite_d = $cite;
    $cite_d = preg_replace(DOI_PATTERN, '', $cite_d);
    $cite_d = preg_replace(OPEN_ACCESS_JOURNALS_PATTERN, '', $cite_d);
    $cite_d = preg_replace(SELFPUB_PATTERN, '', $cite_d);
    $cite_d = preg_replace(SELFPUB_URL_PATTERN, '', $cite_d);
    //---
    if ($cite != $cite_d) {
        return true;
    }

    return false;
}

/**
 * Removes bad references from the provided text based on citation tags.
 *
 * @param string $text The text containing references to check and potentially remove
 * @return string The text with bad references removed
 */
function remove_bad_refs($text)
{
    // ---
    $citations = getCitations($text);
    // ---
    foreach ($citations as $citation) {
        $citation_tag = $citation["tag"];
        //---
        $is_bad_citation = check_one_cite($citation_tag);
        //---
        if ($is_bad_citation) {
            // echo htmlentities($citation_tag) . "<br>";
            $text = str_replace($citation_tag, "", $text);
        }
    }
    // ---
    return $text;
}
