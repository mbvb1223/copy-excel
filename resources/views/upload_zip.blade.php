@extends('layout')

@section('content')
    <form action="/handle-hang-diem-zip" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="formFile" class="form-label">Upload one zip file</label>
            <input class="form-control" type="file" id="formFile" name="file" required  accept=".zip">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

@endsection
