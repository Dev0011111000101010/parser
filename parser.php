<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

@mkdir("sources/results", 0755, true);

require "parse-functions.php";
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$alldocs = scandir(__DIR__.'/splitted10k');
foreach ($alldocs as $docname) {
    echo $docname;echo "\n";
    if(strpos($docname, 'documents_part') === 0) {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(__DIR__.'/splitted10k/'.$docname);
        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $newdocname = str_replace('csv', 'xlsx', $docname);
        $objWriter->save(__DIR__.'/converted10k/'.$newdocname);
        echo memory_get_usage(); echo "\n";
        $spreadsheet->disconnectWorksheets();
        $spreadsheet->garbageCollect();
        echo memory_get_usage();echo "\n";
//    $xlsxspreadsheet = $spreadsheet;
        $xlsxspreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load(__DIR__.'/converted10k/'.$newdocname);
        echo memory_get_usage();echo "\n";
//	var_dump($xlsxspreadsheet);
//	mkdir("sources/$newdocname", 0755, true);
//	var_dump('after');
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
            $realname = parseLinkLawyer($link_to_rtf, "results",$newdocname);
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
sendMessage('машина1');
