
<?php
$source = 'https://admin.blog-panel.clickstar.pl/api/websites/public/yoyoguide.com/sitemap.xml';

$ch = curl_init($source);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_HTTPHEADER => [
        'Accept: application/xml, text/xml'
    ]
]);

$xml = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($xml === false || $status !== 200) {
    http_response_code(502);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Sitemap source unavailable');
}

$xml = preg_replace('/^\xEF\xBB\xBF/', '', $xml);
$xml = ltrim($xml);

libxml_use_internal_errors(true);
$document = simplexml_load_string($xml);

if ($document === false ||
    !in_array($document->getName(), ['urlset', 'sitemapindex'], true)) {
    http_response_code(502);
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Invalid sitemap XML from source');
}

header('Content-Type: application/xml; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

echo $xml;
exit;
