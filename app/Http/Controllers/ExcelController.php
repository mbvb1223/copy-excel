<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use SplFileInfo;
use Symfony\Component\DomCrawler\Crawler;
use ZipArchive;
use Illuminate\Support\Facades\File;
use App\Models\File as FileModel;

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
        list($info, $path) = $this->exe($request['file']->get(), 'app/excel');

        return response()->download($path, $info['ma_lop'] . " " . $info['hoc_phan'] . ".xls")->deleteFileAfterSend();
    }

    public function showConvert()
    {
        return view('convert');
    }

    public function saveConvert(Request $request)
    {
        $folder = 'app/excel/files-data';
        if ($request->file("file")->extension() == "zip") {
            $storageDestinationPath = storage_path("app/excel/files-unzip/");
            File::deleteDirectory($storageDestinationPath);
            File::makeDirectory($storageDestinationPath, 0755, true);
            $zip = new ZipArchive();
            $zip->open($request->file("file")->getRealPath());
            $zip->extractTo($storageDestinationPath);
            $zip->close();

            $files = File::allFiles($storageDestinationPath);

            foreach ($files as $file) {
                list($info, $path) = $this->exe(File::get($file), $folder);
                $this->updateFile($info, $path);
            }

            File::deleteDirectory($storageDestinationPath);
        } else {
            list($info, $path) = $this->exe(File::get($request->file("file")), $folder);
            $this->updateFile($info, $path);
        }

        return redirect()->to('/bang-diem/search?admin=khien');
    }

    public function search(Request $request)
    {
        $allFiles = FileModel::orderBy('name')->orderBy('code')->get();
        $names = $allFiles->pluck('name')->unique()->all();
        $codes = $allFiles->pluck('code')->unique()->all();
        $years = $allFiles->pluck('year')->unique()->all();
        $semesters = $allFiles->pluck('semester')->unique()->all();

        $files = $this->getFilteredFiles($request, $allFiles);

        return view('search', compact(
            'names',
            'codes',
            'years',
            'semesters',
            'files',
            'allFiles'
        ));
    }

    public function exe($file, $directory)
    {
        $crawler = new Crawler($file);
        $info = [];
        $list = [];
        $crawler
            ->filter('table')
            ->each(function (Crawler $node, $i) use (&$info, &$list) {
                if ($i == 1) {
                    $title = $node->filter('tr td b')->first()->text();
                    $info['year'] = substr($title, -9);
                    $info['semester'] = $this->vn_to_str($title);
                    $info['semester'] = $this->removeRedundantCharacter($info)['semester'];
                    $info['semester'] = substr($info['semester'], 31);
                    $info['semester'] = substr($info['semester'], 0, strpos($info['semester'], "NAM HOC"));
                }
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

        $reader = IOFactory::createReader("Xls");
        $reader->setLoadSheetsOnly(["Sheet 1"]);

        $spreadsheet = $reader->load(public_path('/data/example.xls'));
        $spreadsheet->getActiveSheet()->setCellValue('C6', $info['hoc_phan']);
        $spreadsheet->getActiveSheet()->setCellValue('J6', $info['ma_lop']);
        $spreadsheet->getActiveSheet()->setCellValue('C7', $info['thu_tiet']);
        $spreadsheet->getActiveSheet()->setCellValue('E7', $info['giang_duong']);
        $spreadsheet->getActiveSheet()->setCellValue('J7', $info['tin_chi']);
        $row = 28;
        foreach ($list as $key => $student) {
            $spreadsheet->getActiveSheet()->setCellValue("A$row", $key);
            $spreadsheet->getActiveSheet()->setCellValue("B$row", $student[1]);
            $spreadsheet->getActiveSheet()->setCellValue("C$row", $student[2]);
            $spreadsheet->getActiveSheet()->setCellValue("D$row", $student[3]);
            $spreadsheet->getActiveSheet()->setCellValue("E$row", $student[4]);
            $spreadsheet->getActiveSheet()->setCellValue("L$row", $student[5]);
            $row++;
        }
        $spreadsheet->getActiveSheet()->removeRow($row, 169 - $row);

        $writer = new Xls($spreadsheet);

        $info = $this->removeRedundantCharacter($info);

        $name = $this->getName($info);
        $storageDestinationPath = storage_path($directory);
        if (!File::exists($storageDestinationPath)) {
            File::makeDirectory($storageDestinationPath, 0755, true);
        }
        $path = storage_path("$directory/$name.xls");
        $writer->save($path);

        return [$info, $path];
    }

    public function download(FileModel $file)
    {
        return response()->download(storage_path($file->url), $file->user_file_name);
    }

    public function downloadAll()
    {
        $path = storage_path('app/excel/files-data');
        $zip = new ZipArchive();
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        $now = Carbon::now()->setTimezone('+7')->format('d-m-Y_H:i:s');
        $zipPath = storage_path("app/excel/files_$now.zip");
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new RuntimeException('Cannot open');
        }
        $fileModelsKeyByUrl = FileModel::all()->keyBy('url')->all();
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $fileUrl = "/app/excel/files-data/{$file->getFilename()}";

            if (!$file->isDir()
                && $file->getExtension() == "xls"
                && isset($fileModelsKeyByUrl[$fileUrl])
            ) {
                /** @var SplFileInfo $file */
                $filePath = $file->getRealPath();
                $zip->addFile(
                    $filePath,
                    $fileModelsKeyByUrl[$fileUrl]->user_file_name
                );
            }
        }
        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend();
    }

    public function checkCode(Request $request)
    {
        $codes = preg_split("/\\r\\n|\\r|\\n/", $request['codes']) ?? [];
        $codes = array_filter(array_unique($codes));
        $existedCodes = FileModel::select('code')->get();
        $result = array_diff(
            $codes,
            $existedCodes->pluck('code')->all()
        );
        echo "<pre>";
        print_r($result);
    }

    public function downloadFilteredFiles(Request $request)
    {
        $allFiles = FileModel::orderBy('name')->orderBy('code')->get();
        $files = $this->getFilteredFiles($request, $allFiles);

        $zip = new ZipArchive();
        $now = Carbon::now()->setTimezone('+7')->format('d-m-Y_H:i:s');
        $zipPath = storage_path("app/excel/files_$now.zip");
        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            throw new RuntimeException('Cannot open');
        }
        /** @var FileModel $file */
        foreach ($files as $file) {
            $zip->addFile(
                storage_path($file->url),
                $file->user_file_name
            );
        }
        $zip->close();

        return response()->download($zipPath)->deleteFileAfterSend();
    }

    public function delete(FileModel $file)
    {
        File::delete(storage_path($file->url));
        $file->delete();
        return redirect()->back();
    }

    private function getName($info)
    {
        $name = $info['hoc_phan'] . "_" . $info['ma_lop'] . "_" . $info['year'] . "_" . $info['semester'];

        return $this->vn_to_str(trim($name));
    }

    private function removeRedundantCharacter(array $info)
    {
        return array_map(fn($item) => trim(str_replace("\u{A0}", " ", $item)), $info);
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

        return str_replace(' ', '_', $str);
    }

    public function convertDanhSachLichThi()
    {
        $reader = IOFactory::createReader("Xlsx");
        $reader->setLoadSheetsOnly(["Sheet 1"]);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load(public_path('/data/lich_thi/lich_thi.xlsx'));
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->rangeToArray("B12:I89", null, true, false, false);

        foreach ($rows as $key => $value) {
            if (true) {
                echo $key . PHP_EOL;

                $data = [];
                foreach ($value as $iter => $column_value) {
                    $data[] = $column_value;
                };

                $reader = IOFactory::createReader("Xls");
                $reader->setLoadAllSheets();
//                $reader->setLoadSheetsOnly(["Học phần"]);
//                $reader->setLoadSheetsOnly(["HP"]);
                $reader->setLoadSheetsOnly(["Sheet1"]);

                $spreadsheet = $reader->load(public_path('/data/lich_thi/mau.xls'));
//                $spreadsheet->getSheet(1)->setCellValue('C7', $data[3]);
//                $spreadsheet->getSheet(1)->setCellValue('C8', $data[6]);
//                $spreadsheet->getSheet(1)->setCellValue('C9', $data[2]);
//                $spreadsheet->getSheet(1)->setCellValue('E8', $data[1]);
//                $spreadsheet->getSheet(1)->setCellValue('E9', $data[0]);
//                $spreadsheet->getSheet(1)->setTitle($data[2] . "_" . $data[6]);

                $spreadsheet->getActiveSheet()->setCellValue('C7', $data[3]);
                $spreadsheet->getActiveSheet()->setCellValue('C8', $data[6]);
                $spreadsheet->getActiveSheet()->setCellValue('C9', $data[2]);
                $spreadsheet->getActiveSheet()->setCellValue('E8', $data[1]);
                $spreadsheet->getActiveSheet()->setCellValue('E9', $data[0]);
                $spreadsheet->getActiveSheet()->setTitle($data[2] . "_" . $data[6]);

                $writer = new Xls($spreadsheet);
                $data = array_map(fn($item) => trim(str_replace("\u{A0}", " ", $item)), $data);

                $name = str_replace("/", "-", $data[0]) . " " . trim($data[2]) . " " . trim($data[3] . " " . $data[6] . " " . $data[7]);

                $storageDestinationPath = storage_path('app/khien5');
                if (!File::exists($storageDestinationPath)) {
                    File::makeDirectory($storageDestinationPath, 0755, true);
                }
                $path = storage_path("app/khien5/$name.xls");
                $writer->save($path);
            }
        };
    }

//    public function uploadZip()
//    {
//        return view('upload_zip');
//    }
//
//    public function handleZip(Request $request)
//    {
//        File::deleteDirectory(storage_path("app/excel/unzip"));
//        File::deleteDirectory(storage_path("app/excel/converted"));
//
//        $storageDestinationPath = storage_path("app/excel/unzip/");
//        if (!File::exists($storageDestinationPath)) {
//            File::makeDirectory($storageDestinationPath, 0755, true);
//        }
//        $zip = new ZipArchive();
//        $zip->open($request->file("file")->getRealPath());
//        $zip->extractTo($storageDestinationPath);
//        $zip->close();
//
//        $files = File::allFiles($storageDestinationPath);
//        $path = 'app/excel/converted';
//        foreach ($files as $file) {
//            $this->exe(File::get($file), $path);
//        }
//
//        $zip = new \ZipArchive();
//        $zipPath = storage_path("app/excel/files.zip");
//        if ($zip->open($zipPath, \ZipArchive::CREATE) !== true) {
//            throw new \RuntimeException('Cannot open');
//        }
//
//        $path = storage_path($path);
//        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
//        foreach ($files as $name => $file) {
//            // We're skipping all subfolders
//            if (!$file->isDir()) {
//                $filePath = $file->getRealPath();
//                $fileName = substr($filePath, strlen($path) + 1);
//                $zip->addFile($filePath, $fileName);
//            }
//        }
//        $zip->close();
//
//        File::deleteDirectory(storage_path("app/excel/unzip"));
//        File::deleteDirectory(storage_path("app/excel/converted"));
//
//        return response()->download($zipPath)->deleteFileAfterSend();
//    }

    private function getFilteredFiles(Request $request, Collection $allFiles): \Illuminate\Support\Collection
    {
        if (!$request['name']
            && !$request['code']
            && !$request['year']
            && !$request['semester']
        ) {
            if ($request['admin'] && $request['_token']) {
                return $allFiles;
            }

            return collect();
        }

        return $allFiles
            ->when($request['name'], fn($query) => $query->where('name', $request['name']))
            ->when($request['code'], fn($query) => $query->where('code', $request['code']))
            ->when($request['year'], fn($query) => $query->where('year', $request['year']))
            ->when($request['semester'], fn($query) => $query->where('semester', $request['semester']));
    }

    private function updateFile(array $info, string $path): void
    {
        FileModel::updateOrCreate([
            'name' => $info['hoc_phan'],
            'code' => $info['ma_lop'],
            'year' => $info['year'],
            'semester' => $info['semester'],
        ], [
            'name' => $info['hoc_phan'],
            'code' => $info['ma_lop'],
            'year' => $info['year'],
            'semester' => $info['semester'],
            'url' => str_replace(storage_path(), "", $path),
        ]);
    }
}
