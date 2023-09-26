@extends('layout')

@section('content')
    <form action="/handle-bang-diem-xls" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="formFile" class="form-label">Upload one excel file</label>
            <input class="form-control" type="file" id="formFile" name="file" required  accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

@endsection
