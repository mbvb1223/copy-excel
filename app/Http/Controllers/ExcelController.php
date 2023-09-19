<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\DomCrawler\Crawler;

class ExcelController extends Controller
{
    public function upload()
    {
        return view('upload');
    }

    public function handle(Request $request)
    {
        $crawler = new Crawler($request['file']->get());
        $info = [];
        $list = [];
        $crawler
            ->filter('table')
            ->each(function (Crawler $node, $i) use (&$info, &$list) {
                if ($i == 2) {
                    $node->filter('tr')->each(function (Crawler $nodeTr, $i) use (&$info) {
                        if ($i === 1) {
                            $info['hoc_phan'] = $nodeTr->filter('td')->eq(1)->text();
                            $info['ma_lop'] = $nodeTr->filter('td')->eq(3)->text();
                            $info['tin_chi'] = $nodeTr->filter('td')->eq(5)->text();
                        }

                        if ($i === 2) {
                            $info['thu_tiet'] = trim(str_replace("Thứ - Tiết:", "", $nodeTr->filter('td')->eq(0)->text()));
                            $info['giang_duong'] = $nodeTr->filter('td')->eq(2)->text();
                        }
                    });
                }

                if ($i == 3) {
                    $node->filter('tr')->each(function (Crawler $listTr, $i) use (&$list) {
                        if ($i > 0) {
                            $list[$i][] = $listTr->filter('td')->eq(0)->text();
                            $list[$i][] = $listTr->filter('td')->eq(1)->text();
                            $list[$i][] = $listTr->filter('td')->eq(2)->text();
                            $list[$i][] = $listTr->filter('td')->eq(3)->text();
                            $list[$i][] = $listTr->filter('td')->eq(4)->text();
                            $list[$i][] = $listTr->filter('td')->eq(5)->text();
                        }
                    });
                }
            });

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xls");
        $reader->setLoadSheetsOnly(["1"]);
        $spreadsheet = $reader->load(public_path('/data/example.xls'));
        $spreadsheet->getActiveSheet()->setCellValue('C6', $info['hoc_phan']);
        $spreadsheet->getActiveSheet()->setCellValue('J6', $info['ma_lop']);
        $spreadsheet->getActiveSheet()->setCellValue('C7', $info['thu_tiet']);
        $spreadsheet->getActiveSheet()->setCellValue('E7', $info['giang_duong']);
        $spreadsheet->getActiveSheet()->setCellValue('J7', $info['tin_chi']);
        $row = 28;
        foreach ($list as $student) {
            $spreadsheet->getActiveSheet()->setCellValue("B$row", $student[1]);
            $spreadsheet->getActiveSheet()->setCellValue("C$row", $student[2]);
            $spreadsheet->getActiveSheet()->setCellValue("D$row", $student[3]);
            $spreadsheet->getActiveSheet()->setCellValue("E$row", $student[4]);
            $spreadsheet->getActiveSheet()->setCellValue("L$row", $student[5]);
            $row++;
        }

        $writer = new Xls($spreadsheet);
        $file = 'data/1.xls';
        $writer->save($file);

        $name = trim($info['ma_lop']) . "_" . trim($info['hoc_phan']);
        return response()->download($file, "$name.xls");
    }
}
