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
        $filearr = explode('/', $link_to_rtf);
        $filename = $filearr[count($filearr) - 1];
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $link_to_rtf);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, getRandomUserAgent());
        $server_output = curl_exec($ch);

        if (!$server_output) return 'no server output';

        file_put_contents("sources/$dirname/$filename", $server_output);

        $parser = new RtfStringTexter($server_output);
        $doc = mb_substr($parser->AsString(), 0, 1600);
        if (!$doc) return false;

        $re = '/(?<=захисника)(\s|-)+([а-яА-Я]+[a-яА-Я\s.]+),/';

        $secondre = '/(?<=адвоката)(\s|-)+([а-яА-Я]+[a-яА-Я\s.]+),/';

        preg_match($re, $doc, $matches, PREG_OFFSET_CAPTURE);
        preg_match($re, $doc, $matches2, PREG_OFFSET_CAPTURE);

        if (count($matches)) {
            $realname = trim(str_replace(['адвоката', '-', 'чи законного представника,', '.,', ' ,', 'особи,'], ['', '', '', '.', '', '', ''], $matches[0][0]));
            if (mb_strlen($realname) > 30) return false;
            if(!$realname=="") return $realname;
        }
        if (count($matches2)) {
            $realname = trim(str_replace(['адвоката', '-', 'чи законного представника,', '.,', ' ,', 'особи,'], ['', '', '', '.', '', '', ''], $matches2[0][0]));
            if ($realname == "" || mb_strlen($realname) > 30) return false;
            return $realname;
        }

        return false;
    }
}

function sendMessage($vmnumber, $chatid = 335765864) {
    $message = "Виртуальная машина $vmnumber закончила работу";

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