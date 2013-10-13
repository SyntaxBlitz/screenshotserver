<?php
// I translated this to PHP from http://stackoverflow.com/a/1119769/1826496
// The original author is Baishampayan Ghose.

$base62_alphabet = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";

function base62_encode($num, $alphabet=$base62_alphabet) {
    if ($num == 0) {
        return substr($alphabet, 0, 1);
	}
    $arr = Array();
    $base = strlen($alphabet);
    while ($num!=0) {
        $rem = $num % $base;
        $num = floor($num / $base);
        $arr[] = substr($alphabet, $rem, 1);
	}
    $arr = array_reverse($arr);
    $out = "";
	foreach($arr as $char) {
		$out.=$char;
	}
	return $out;
}

function base62_decode($thisStr, $alphabet=$base62_alphabet) {
    $base = strlen($alphabet);
    $stringlength = strlen($thisStr);
    $num = 0;

    $idx = 0;
    for($iter = 0; $iter<$stringlength; $iter++) {
        $power = ($stringlength - ($idx + 1));
        $num += strpos($alphabet, substr($thisStr, $iter, 1)) * pow($base, $power);
        $idx += 1;
	}

    return $num;
}
?>