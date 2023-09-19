<?php

namespace App\Http\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\DomCrawler\Crawler;

class ExcelController extends Controller
{
    public function index(): void
    {
        $crawler = new Crawler(file_get_contents('data/source.html'));
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
                            $info['thu_tiet'] = str_replace("Thứ - Tiết:", "", $nodeTr->filter('td')->eq(0)->text());
                            $info['giang_duong'] = $nodeTr->filter('td')->eq(2)->text();
                        }
                    });
                }

                if ($i == 3) {
                    $node->filter('tr')->each(function (Crawler $listTr, $i) use (&$list){
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
        $spreadsheet->getActiveSheet()->setCellValue('C6', 'Khiendddd');

        $writer = new Xls($spreadsheet);
        $writer->save('data/1.xls');
        echo "Done";
    }
}
