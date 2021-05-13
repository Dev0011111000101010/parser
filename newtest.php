<?php
function getRandomUserAgent ( ) {
    static $UA = array (
        "Mozilla/5.0 (Windows; U; Windows NT 6.0; fr; rv:1.9.1b1) Gecko/20081007 Firefox/3.1b1",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.1) Gecko/2008070208 Firefox/3.0.0",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.19 (KHTML, like Gecko) Chrome/0.4.154.18 Safari/525.19",
        "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/525.13 (KHTML, like Gecko) Chrome/0.2.149.27 Safari/525.13",
        "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30)",
        "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322; .NET CLR 2.0.40607)",
        "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.1.4322)",
        "Mozilla/4.0 (compatible; MSIE 7.0b; Windows NT 5.1; .NET CLR 1.0.3705; Media Center PC 3.1; Alexa Toolbar; .NET CLR 1.1.4322; .NET CLR 2.0.50727)",
        "Mozilla/45.0 (compatible; MSIE 6.0; Windows NT 5.1)",
        "Mozilla/4.08 (compatible; MSIE 6.0; Windows NT 5.1)",
        "Mozilla/4.01 (compatible; MSIE 6.0; Windows NT 5.1)");
    srand((double)microtime()*1000000);
    return $UA[rand(0,count($UA)-1)];
}
require __DIR__ . '/vendor/autoload.php';
include 'parser/sources/RtfTexter.php';
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("testparsing.xlsx");
$sheet = $spreadsheet->getActiveSheet();
$sheetData = $sheet->toArray();
unset($sheetData[0]);

$ch = curl_init();

$link_to_rtf = 'http://od.reyestr.court.gov.ua/files/43/1fe0cd85da95d311803424ef94ea1251.rtf';

curl_setopt($ch, CURLOPT_URL, $link_to_rtf);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, getRandomUserAgent());
$server_output = curl_exec($ch);

$parser = new RtfStringTexter($server_output);
$doc = $parser->AsString();

$re = '/(?<=захисника)(\s|-)+([а-яА-Я]+[a-яА-Я\s.]+),/';

preg_match($re, $doc, $matches, PREG_OFFSET_CAPTURE);

// Print the entire match result
var_dump($matches);



curl_close($ch);