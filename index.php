<?php
$url = $_GET["URL"];

// 初始化 curl
$ch = curl_init();

// 设置 curl 选项
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// 模拟浏览器请求
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36');
curl_setopt($ch, CURLOPT_REFERER, 'https://example.com');

// 执行请求
$result = curl_exec($ch);

// 检查错误
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
} else {
    // 获取图片类型
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimetype = finfo_buffer($finfo, $result);
    finfo_close($finfo);

    // 设置响应头
    header('Content-Type: ' . $mimetype);

    // 输出图片内容
    echo $result;
}

curl_close($ch);

?>