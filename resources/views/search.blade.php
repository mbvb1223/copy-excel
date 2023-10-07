@extends('layout')

@section('content')
    <h2 class="text-center w-100 mb-5">Tìm kiếm bảng điểm</h2>
    <form action="{{ url()->current() }}" method="GET" style="width: 100%">
        @csrf
        <input type="hidden" name="admin" value="{{ request('admin') }}">
        <div class="form-row col-12">
            <div class="form-group col-md-6">
                <label for="tmh">Học phần (tên môn học)</label>
                <select name="name" id="tmh" class="form-control">
                    @if(request()->get('admin') == 'khien')
                        <option value="">#</option>
                    @endif
                    @foreach($names as $name)
                        <option value="{{ $name }}" @if(request('name') == $name) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="ml-hp">Mã lớp học phần</label>
                <select name="code" id="ml-hp" class="form-control">
                    @if(request()->get('admin') == 'khien')
                        <option value="">#</option>
                    @endif
                    @foreach($codes as $code)
                        <option value="{{ $code }}" @if(request('code') == $code) selected @endif>{{ $code }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row col-12">
            <div class="form-group col-md-6">
                <label for="nh">Năm học</label>
                <select name="year" id="nh" class="form-control">
                    <option value="">#</option>
                    @foreach($years as $year)
                        <option value="{{ $year }}" @if(request('year') == $year) selected @endif>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="hk">Học kỳ</label>
                <select name="semester" id="hk" class="form-control">
                    <option value="">#</option>
                    @foreach($semesters as $semester)
                        <option value="{{ $semester }}" @if(request('semester') == $semester) selected @endif>{{ $semester }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-row col-12"><button type="submit" class="btn btn-primary">Tìm kiếm</button></div>
    </form>


    <table class="table table-hover" style="margin-top: 20px">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Học phần</th>
            <th scope="col">Mã lớp</th>
            <th scope="col">Thời gian</th>
            <th scope="col">Tải về</th>
        </tr>
        </thead>
        <tbody>
            @if(!$files->all()) <tr><td colspan="5" class="text-center">Không tìm thấy kết quả phù hợp</td></tr> @endif
            @foreach($files as $file)
                <tr>
                    <th scope="row">{{ $loop->index + 1 }}</th>
                    <td>{{ $file->name }}</td>
                    <td>{{ $file->code }}</td>
                    <td>{{ $file->year }}_Học kỳ {{ $file->semester }}</td>
                    <td>
                        <a href="/bang-diem/download/{{$file->id}}">
                        <button type="button" class="btn btn-info">Tải về</button>
                        </a>
                        @if(request('admin') == 'khien')
                        <a href="/bang-diem/delete/{{$file->id}}" onclick="
                        if (!confirm('Are you sure?')) {
                            return false;
                        }">
                            <button type="button" class="btn btn-danger">Xoá</button>
                        </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

@endsection
