<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require "parse-functions.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$pregold = '/(?:(?<=захисника)|(?<=адвоката)|(?<=захисник)|(?<=преставника)|(?<=представника))[\s|\n]*[обвинуваченого|обвинуваченої|, - адвоката:]*(\s|-)+([а-яА-ЯІіЇї]+)\s*[А-ЯІЇ]\.*[А-ЯІЇ]\./u';
$pregname = '/[а-яА-ЯІіЇїГгЄє]+[\s|\\n][А-ЯІЄ]\.[А-ЯІЄ]\./u';
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://od.reyestr.court.gov.ua/files/43/2d4e776a9183b4fe8114e80cf713315e.rtf");

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, getRandomUserAgent());
$server_output = curl_exec($ch);
$parser = new RtfStringTexter($server_output);
$doc = mb_substr($parser->AsString(), 0, 1600);
// библиотека замены значений
$replace = [
    ',' => '',
    "/n" => ' ',
    'підозрюваного' => '',
    'підозрюваної' => '',
    'потерпілої' => '',
    'адвокат' => '',
    'потерпілого' => '',
    'та' => '',
    'обвинуваченого' => '',
    'обвинувачених' => '',
    'підсудного' => '',
    'позивача' => '',
    'відповідача' => '',
];

preg_match($pregold, $doc, $matches, PREG_OFFSET_CAPTURE, 0);

var_dump($doc);
var_dump($matches);

$predata = $matches[0];
$returnnames = [];


foreach ($predata as $elem) {
    $prerealname = trim(str_replace(array_keys($replace), array_values($replace), $elem[0]));
    preg_match($pregname, $prerealname, $matches2, PREG_OFFSET_CAPTURE, 0);

    foreach ($matches2 as $name) {
        $returnnames[] = $name[0];
    }
}

$returnnames = implode(', ', $returnnames);
var_dump($returnnames);

die();
$prerealname = trim(str_replace(array_keys($replace), array_values($replace), $matches[0][0]));
preg_match($pregname, $prerealname, $matches2, PREG_OFFSET_CAPTURE, 0);

// Print the entire match result
?>