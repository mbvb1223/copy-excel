@extends('layout')

@section('content')
    <form action="/bang-diem/save" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="formFile" class="form-label">Upload one zip file</label>
            <input class="form-control" type="file" id="formFile" name="file" required  accept=".zip">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

    <hr>
    <a href="{{ route('download_files') }}">
        <button type="submit" class="btn btn-success">Download all files</button>
    </a>

@endsection
