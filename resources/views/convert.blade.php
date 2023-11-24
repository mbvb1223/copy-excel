@extends('layout')

@section('content')
    <div class="row">
        <form action="/bang-diem/save" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label for="formFile" class="form-label">Upload one .zip/.xls file</label>
                <input class="form-control" type="file" id="formFile" name="file" required accept=".zip, .xls">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

    <hr>
    <div class="row">
        <a href="{{ route('download_files') }}">
            <button type="submit" class="btn btn-success">Download all files</button>
        </a>
    </div>
    <hr>

    <div class="row">
        <form action="/bang-diem/code/check" method="POST">
            @csrf
            <div class="mb-3">
                <label for="list_code" class="form-label">Danh sách mã LHP cần kiểm tra</label>
                <textarea class="form-control" id="list_code" name="codes" rows="10" placeholder="code1&#10;code2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>

@endsection
