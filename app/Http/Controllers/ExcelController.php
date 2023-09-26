<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\DomCrawler\Crawler;
use ZipArchive;
use Illuminate\Support\Facades\File;

class ExcelController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function upload()
    {
        return view('upload');
    }

    public function handle(Request $request)
    {
        $path = $this->exe($request['file']->get(), 'app/excel');

        return response()->download($path)->deleteFileAfterSend();
    }

    public function uploadZip()
    {
        return view('upload_zip');
    }

    public function handleZip(Request $request)
    {
        File::deleteDirectory(storage_path("app/excel/unzip"));
        File::deleteDirectory(storage_path("app/excel/converted"));

        $storageDestinationPath = storage_path("app/excel/unzip/");
        if (!File::exists($storageDestinationPath)) {
            File::makeDirectory($storageDestinationPath, 0755, true);
        }
        $zip = new ZipArchive();
        $zip->open($request->file("file")->getRealPath());
        $zip->extractTo($storageDestinationPath);
        $zip->close();

        $files = File::allFiles($storageDestinationPath);
        $path = 'app/excel/converted';
        foreach ($files as $file) {
            $this->exe(File::get($file), $path);
        }

        $zip = new \ZipArchive();
        $zipPath = storage_path("app/excel/files.zip");
        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
            throw new \RuntimeException('Cannot open ');
        }

        $path = storage_path($path);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file) {
            // We're skipping all subfolders
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $fileName = substr($filePath, strlen($path) + 1);
                $zip->addFile($filePath, $fileName);
            }
        }
        $zip->close();

        File::deleteDirectory(storage_path("app/excel/unzip"));
        File::deleteDirectory(storage_path("app/excel/converted"));

        return response()->download($zipPath)->deleteFileAfterSend();
    }

    public function exe($file, $directory)
    {
        $crawler = new Crawler($file);
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
        $reader->setLoadSheetsOnly(["Sheet 1"]);
        $spreadsheet = $reader->load(public_path('/data/example.xls'));
        $spreadsheet->getActiveSheet()->setCellValue('C6', $info['hoc_phan']);
        $spreadsheet->getActiveSheet()->setCellValue('J6', $info['ma_lop']);
        $spreadsheet->getActiveSheet()->setCellValue('C7', $info['thu_tiet']);
        $spreadsheet->getActiveSheet()->setCellValue('E7', $info['giang_duong']);
        $spreadsheet->getActiveSheet()->setCellValue('J7', $info['tin_chi']);
        $row = 28;
        foreach ($list as $key => $student) {
            $spreadsheet->getActiveSheet()->setCellValue("A$row", $key + 1);
            $spreadsheet->getActiveSheet()->setCellValue("B$row", $student[1]);
            $spreadsheet->getActiveSheet()->setCellValue("C$row", $student[2]);
            $spreadsheet->getActiveSheet()->setCellValue("D$row", $student[3]);
            $spreadsheet->getActiveSheet()->setCellValue("E$row", $student[4]);
            $spreadsheet->getActiveSheet()->setCellValue("L$row", $student[5]);
            $row++;
        }

        $writer = new Xls($spreadsheet);

        $name = $info['ma_lop'] . " " . $info['hoc_phan'];
        $name = str_replace("\u{A0}", "", $name);
        $storageDestinationPath = storage_path($directory);
        if (!\File::exists($storageDestinationPath)) {
            \File::makeDirectory($storageDestinationPath, 0755, true);
        }
        $path = storage_path("$directory/$name.xls");
        $writer->save($path);

        return $path;
    }

    function vn_to_str($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );

        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        $str = str_replace(' ', '_', $str);

        return $str;
    }
}
