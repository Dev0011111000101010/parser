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
    	FilesProcessor($docname);
    }
}
sendMessage('машина1');
