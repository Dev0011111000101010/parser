<?php
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require "parse-functions.php";
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$alldocs = scandir(__DIR__.'/splitted10k');
foreach ($alldocs as $docname) {
    echo $docname;echo "\n";
    if(strpos($docname, 'documents_part') === 0) {
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
}
sendMessage('машина1');
