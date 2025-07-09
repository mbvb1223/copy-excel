@extends('layout')

@section('content')
    <h2 class="text-center w-100 mb-5">DANH SÁCH BẢNG ĐIỂM LỚP HỌC PHẦN</h2>
    <form action="{{ url()->current() }}" method="GET" style="width: 100%">
        @csrf
        <input type="hidden" name="admin" value="{{ request('admin') }}">
        <div class="form-row col-12">
            <div class="form-group col-md-6">
                <label for="nh">Năm học</label>
                <select name="year" id="nh" class="form-control">
                    @if(request()->get('admin') == 'khien')
                        <option value="">#</option>
                    @endif
                    @foreach($years as $year)
                        <option value="{{ $year }}" @if(request('year') == $year) selected @endif>{{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="hk">Học kỳ</label>
                <select name="semester" id="hk" class="form-control">
                    @if(request()->get('admin') == 'khien')
                        <option value="">#</option>
                    @endif
                    @foreach($semesters as $semester)
                        <option value="{{ $semester }}" @if(request('semester') == $semester) selected @endif>{{ $semester }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-row col-12">
            <div class="form-group col-md-6">
                <label for="tmh">Học phần (tên môn học)</label>
                <select name="name" id="tmh" class="form-control">
                    <option value="">#</option>
                    @foreach($names as $name)
                        <option value="{{ $name }}" @if(request('name') == $name) selected @endif>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="ml-hp">Mã lớp học phần</label>
                <select name="code" id="ml-hp" class="form-control">
                    <option value="">#</option>
                    @foreach($codes as $code)
                        <option value="{{ $code }}" @if(request('code') == $code) selected @endif>{{ $code }}</option>
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
            <th scope="col">Năm học, học kỳ</th>
            <th scope="col">
                @if($files->all())
                <a href="/bang-diem/download-filtered?{{ request()->getQueryString() }}">
                    <button class="btn btn-warning">
                        Tải hết
                    </button>
                </a>
                @endif
                @if($files->all() && request('admin') == 'khien')
                    <a href="/bang-diem/truncate" onclick="
                    if (!confirm('Are you sure?')) {
                        return false;
                    }">
                        <button type="button" class="btn btn-danger">Xoá hết</button>
                    </a>
                @endif
            </th>
        </tr>
        </thead>
        <tbody>
            @if(!$files->all()) <tr><td colspan="5" class="text-center">...</td></tr> @endif
            @foreach($files as $file)
                <tr>
                    <th scope="row">{{ $loop->index + 1 }}</th>
                    <td>{{ $file->name }}</td>
                    <td>{{ $file->code }}</td>
                    <td>{{ $file->year }}_Học kỳ {{ $file->semester }}</td>
                    <td>
                        <a href="/bang-diem/download/{{$file->id}}?hash={{ Str::random(20) }}">
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

@section('script')
<script>
    $(document).ready(function() {
        const allFiles = {!! json_encode($allFiles) !!} ;
        const selectedCode = `{!! request('code') !!}` ;
        $('#tmh').on('change', function() {
            const selectedName = $(this).val();
            const filteredFiles = allFiles.filter((item) => {
                return item.name === selectedName;
            })
            $('#ml-hp').html('<option value="">#</option>')
            $.each(filteredFiles, function(key, file) {
                $('#ml-hp')
                    .append($("<option></option>")
                        .attr("value", file.code)
                        .text(file.code));
            });
        }).trigger('change');

        $('#ml-hp').val(selectedCode)

        $('#tmh').select2({
            theme: 'bootstrap-5'
        });
    });
</script>

@endsection
