<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class ExcelController extends Controller
{
    public function index(): void
    {
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xls");
        $reader->setLoadSheetsOnly(["1"]);
        $spreadsheet = $reader->load(public_path('/data/example.xls'));
        $spreadsheet->getActiveSheet()->setCellValue('C6','Khiendddd');

        $writer = new Xls($spreadsheet);
        $writer->save('data/1.xls');
        echo "Done";
    }
}
