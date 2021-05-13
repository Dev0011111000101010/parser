<?php
require "parse-functions.php";
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//$time1 = microtime(true);
//$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load("testparsing.xlsx");
//$sheet = $spreadsheet->getActiveSheet();
//$sheetData = $sheet->toArray();
//unset($sheetData[0]);
//
//for ($i = 2; $i < count($sheetData); $i++) {
//    set_time_limit(0);
//    $link_to_rtf = $sheetData[$i][2];
//    if (isset($link_to_rtf)) {
//        $ch = curl_init();
//
//        curl_setopt($ch, CURLOPT_URL, $link_to_rtf);
//
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_USERAGENT, getRandomUserAgent());
//        $server_output = curl_exec($ch);
//
//        if (!$server_output) continue;
//
//        $parser = new RtfStringTexter($server_output);
//        $doc = mb_substr($parser->AsString(), 0, 1600);
//        if(!$doc) continue;
//
////        if(strpos($doc, 'захисника') > 0) {
////            $celltruekey = 'F'.$i;
////            $sheet->getCell($celltruekey)->setValue(true);
////        }
//
//        $re = '/(?<=захисника)(\s|-)+([а-яА-Я]+[a-яА-Я\s.]+),/';
//
//        preg_match($re, $doc, $matches, PREG_OFFSET_CAPTURE);
//
//        if(count($matches)) {
//            $realname = trim(str_replace(['адвоката', '-', 'чи законного представника,', '.,', ' ,'], ['', '', '', '.', '', ''], $matches[0][0]));
//            if($realname=="" || mb_strlen($realname) > 30) continue;
//            $cellname = 'G'.$i;
//            $sheet->getCell($cellname)->setValue($realname);
//        }
//
//        var_dump($i);
//
//        curl_close($ch);
//    }
//
//}
//
////write it again to Filesystem with the same name (=replace)
//$writer = new Xlsx($spreadsheet);
//$writer->save("testparsing1.xlsx");
//
//$time2 = microtime(true);
//
//var_dump($time1, $time2);
//$time3 = $time2-$time1;
//var_dump("execution time $time3");

//$ch = curl_init();
//
//curl_setopt($ch, CURLOPT_URL,"http://od.reyestr.court.gov.ua/files/43/0d0c7916713494d78e724feda2ad75af.rtf");
////curl_setopt($ch, CURLOPT_POST, 1);
//
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//$contents 	=  file_get_contents ( 'myfile.rtf' ) ;
//$server_output = curl_exec($ch);
//
//$parser 	=  new RtfStringTexter($contents);
//var_dump($parser->AsString());
//
//curl_close ($ch);

//https://regex101.com/r/wjgSdD/1
//https://regex101.com/r/znmRlx/1
//https://regex101.com/r/f8SNfR/1

$alldocs = scandir(__DIR__.'/splitted10k');
foreach ($alldocs as $docname) {
    if(strpos($docname, 'documents_part') === 0) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(__DIR__.'/splitted10k/'.$docname);
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $newdocname = str_replace('csv', 'xlsx', $docname);
        $objWriter->save(__DIR__.'/converted10k/'.$newdocname);
        $xlsxspreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(__DIR__.'/converted10k/'.$newdocname);
        mkdir("sources/$newdocname", 755, true);
        $sheet = $xlsxspreadsheet->getActiveSheet();
        $sheetData = $sheet->toArray();
        if($sheetData[0][0] !== 'doc_id') {
            for ($b=1; $b<count($sheettop); $b++) {
                $sheet->getCell($letters[$b-1].'1')->setValue($sheettop[$b-1]);
            }
        }
        $sheet->getCell("M1")->setValue("lawyer_name");
        unset($sheetData[0]);
        for ($i = 2; $i < count($sheetData); $i++) {
            $link_to_rtf = $sheet->getCell('J' . $i)->getValue();
            $realname = parseLinkLawyer($link_to_rtf, $newdocname);
            if ($realname) {
                $sheet->getCell('M' . $i)->setValue($realname);
            }
        }
        $writer = new Xlsx($xlsxspreadsheet);
        $writer->save(__DIR__.'/converted10k/'.$newdocname);
        $xlsxspreadsheet->disconnectWorksheets();
        $xlsxspreadsheet->garbageCollect();
        var_dump("file".$docname."parsed");
        if($i==200) die('END');
    }
}
sendMessage('mashine1');