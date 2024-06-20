<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Support\Facades\DB;

class TestController extends Controller
{
    public function index()
    {
        for ($i = 0; $i < 10; $i++) {
            DB::beginTransaction();

            try {
                $user = new User();
                $user->name = 'User ' . $i;
                $user->email = 'email@gmail.colm' . uniqid() . $i;
                $user->password = 'email@gmail.colm' . $i;
                $user->save();
                sleep(3);
                if ($i % 2 == 0) {
                    throw new \Exception('Error');
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                file_put_contents('khien.log', $i . PHP_EOL, FILE_APPEND);
            }

        }
    }
}
