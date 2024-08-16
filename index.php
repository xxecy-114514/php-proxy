<?php
function getAbsoluteUrl($base, $relative) {
    $base = parse_url($base);
    if (strpos($relative, "//") === 0) {
        return $base["scheme"] . ":" . $relative;
    } elseif (parse_url($relative, PHP_URL_SCHEME) != '') {
        return $relative;
    } elseif ($relative[0] == '/') {
        return $base["scheme"] . "://" . $base["host"] . $relative;
    } else {
        $path = explode('/', $base["path"]);
        array_pop($path);
        $path = implode('/', $path) . "/";
        return $base["scheme"] . "://" . $base["host"] . $path . $relative;
    }
}

$requestUri = $_SERVER['REQUEST_URI'];
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];
$query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';

$method = $_SERVER['REQUEST_METHOD'];
$url1 = "https://github.com" . $path . $query;

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url1);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.0.0 Safari/537.36');

if ($method === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
}

$result = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimetype = finfo_buffer($finfo, $result);
    finfo_close($finfo);

    header('Content-Type: ' . $mimetype);

    // 如果是HTML文档，则解析并替换资源链接
    if (strpos($mimetype, 'text/html') !== false) {
        $dom = new DOMDocument();
        @$dom->loadHTML($result);

        // 查找并替换所有资源链接
        foreach (['img', 'script', 'link'] as $tag) {
            $elements = $dom->getElementsByTagName($tag);
            foreach ($elements as $element) {
                $attr = $tag === 'link' ? 'href' : 'src';
                if ($element->hasAttribute($attr)) {
                    $url = $element->getAttribute($attr);
                    $absoluteUrl = getAbsoluteUrl($url1, $url);
                    $element->setAttribute($attr, '/proxy.php?url=' . urlencode($absoluteUrl));
                }
            }
        }

        echo $dom->saveHTML();
    } else {
        echo $result;
    }
}

curl_close($ch);
?>
