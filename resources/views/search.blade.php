@extends('layout')

@section('content')
    <form action="/handle-hang-diem-zip" method="POST" enctype="multipart/form-data" style="width: 100%">
        @csrf
        <div class="form-row col-12">
            <div class="form-group col-md-6">
                <label for="tmh">Học phần (tên môn học)</label>
                <select id="tmh" class="form-control">
                    <option>...</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="ml-hp">Mã lớp học phần</label>
                <select id="ml-hp" class="form-control">
                    <option>...</option>
                </select>
            </div>
        </div>
        <div class="form-row col-12">
            <div class="form-group col-md-6">
                <label for="nh">Năm học</label>
                <select id="nh" class="form-control">
                    <option>...</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="hk">Học kỳ</label>
                <select id="hk" class="form-control">
                    <option>...</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
@endsection
