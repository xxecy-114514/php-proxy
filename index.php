<?php
$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];
$query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
$lastPart = basename($path) . $query;
$url1 = "https://example.com/" . $lastPart;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36');

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimetype = finfo_buffer($finfo, $result);
    finfo_close($finfo);

    header('Content-Type: ' . $mimetype);

    echo $result;
}

curl_close($ch);
?>
