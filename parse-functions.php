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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$sheettop = [
    "doc_id",
    "court_code",
    "judgment_code",
    "justice_kind",
    "category_code",
    "cause_num",
    "adjudication_date",
    "receipt_date",
    "judge",
    "doc_url",
    "status",
    "date_publ",
    "lawyer_name",
];

$letters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'];

function parseLinkLawyer($link_to_rtf = '', $dirname) {
    if (isset($link_to_rtf) && !empty($link_to_rtf)) {

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

        // нежесткая регулярка
        $pregold = '/(?:(?<=захисника)|(?<=адвоката)|(?<=захисник)|(?<=преставника)|(?<=представника))[\s|\n]*[обвинуваченого|обвинуваченої|, - адвоката:]*(\s|-)+([а-яА-ЯІіЇї]+)\s*[А-ЯІЇ]\.*[А-ЯІЇ]\./u';

        // жесткая регулярка
        $pregname = '/[а-яА-ЯіІiIїгЄє]+(\s|\\n)[А-ЯіІЄє]\.[А-ЯіІЄє]\./u';

        $filearr = explode('/', $link_to_rtf);
        $filename = $filearr[count($filearr) - 1];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $link_to_rtf);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, getRandomUserAgent());
        $server_output = curl_exec($ch);

        if (!$server_output) return false;

        file_put_contents("sources/$dirname/$filename", $server_output);

        $parser = new RtfStringTexter($server_output);
        $doc = mb_substr($parser->AsString(), 0, 1600);
        if (!$doc) return false;

        preg_match($pregold, $doc, $matches, PREG_OFFSET_CAPTURE);

        if(!count($matches)) return false;

        $prerealname = trim(str_replace(array_keys($replace), array_values($replace), $matches[0][0]));

        if (!isset($prerealname)) return false;

        preg_match($pregname, $prerealname, $matches2, PREG_OFFSET_CAPTURE);

        if (!isset($matches2[0])) return false;

        $realname = $matches2[0][0];

        var_dump($realname);

        if (mb_strlen($realname) > 30) return false;
        if (!$realname == "") return $realname;

        return false;
    }
}

function sendMessage($vmnumber, $chatid = 454255748) {
    $message = "Парсинг $vmnumber закончен";

    $token = '1732465913:AAEPSUXxROLIhpcxTSIygaLgEN7sLduPuME';
    $url = "https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chatid}&text=";
    $url .= urlencode($message);
    $ch = curl_init();
    $options = array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true
    );
    curl_setopt_array($ch, $options);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function FilesProcessor($docname) {
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(__DIR__.'/splitted10k/'.$docname);
    $newdocname = str_replace('csv', 'xlsx', $docname);

    echo memory_get_usage();echo "\n";

    $newdirname = str_replace('.xlsx', '', $newdocname);
    @mkdir("sources/$newdirname", 0755, true);
    $sheet = $spreadsheet->getActiveSheet();
    $sheetData = $sheet->toArray();
    for ($i = 0; $i < count($sheetData); $i++) {

        $link_to_rtf = $sheetData[$i][9];
        $realname = parseLinkLawyer($link_to_rtf, $newdirname);
        if ($realname) {
//                var_dump($realname);
            $sheetData[$i][] = $realname;
        }
    }

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray($sheetData, NULL, 'A1');

    $writer = new Xlsx($spreadsheet);
    $writer->save(__DIR__.'/converted10k/'.$newdocname);

    $spreadsheet->disconnectWorksheets();
    $spreadsheet->garbageCollect();

    rename(__DIR__.'/splitted10k/'.$docname, __DIR__.'/splitted10k/_'.$docname);
    var_dump("file".$docname."parsed");

    unset($sheetData);
    unset($writer);
}