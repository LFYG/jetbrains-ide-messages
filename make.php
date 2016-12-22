<?php
error_reporting(E_ALL);


$files = glob('messages_zh/*.properties');
$files = array_map(function ($item) {
    return basename($item);
}, $files);


foreach ($files as $key => $item) {
    $item_cn = 'messages/' . $item;
    $item_zh = 'messages_zh/' . $item;

    if (is_file($item_cn) && (filemtime($item_zh) < filemtime($item_cn))) {
        // continue;
    } else {
        echo str_pad($key, 2, '0', STR_PAD_LEFT), ': ',$item, PHP_EOL;
    }

    $data = file_get_contents($item_zh);
    $data = utf8_unicode($data);
    // $data = unicode_decode($data);
    file_put_contents($item_cn, $data);
}





//////////////////////////////////////////////////////////////////////////////
/**
 * utf-8 转unicode
 *
 * @param string $name
 * @return string
 */
function utf8_unicode($string)
{
    $string = iconv('UTF-8', 'UCS-2', $string);

    $str = '';
    $len = strlen($string);

    for ($i = 0; $i < $len - 1; $i = $i + 2) {
        $c1 = $string[$i];
        $c2 = $string[$i + 1];

        $c1_ord = ord($c1);
        $c2_ord = ord($c2);

        $c1_hex = str_pad(dechex($c1_ord), 2, 0, STR_PAD_LEFT);
        $c2_hex = str_pad(dechex($c2_ord), 2, 0, STR_PAD_LEFT);

        $str .= $c1_ord > 0 ? ('\u' . $c1_hex . $c2_hex) : $c2;
    }

    return $str;
}

// 将UNICODE编码后的内容进行解码
function unicode_decode($string) {

    $string = preg_replace_callback('/\\\\u([0-9a-f]{2})([0-9a-f]{2})/im', function ($matches) {
        $c1   = hexdec($matches[1]);
        $c2   = hexdec($matches[2]);
        $code = chr($c1) . chr($c2);
        $utf8 = iconv('UCS-2', 'UTF-8', $code);
        return $utf8;
    }, $string);

    return $string;
}
