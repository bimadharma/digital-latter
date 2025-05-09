@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mt-5 text-center fw-bold">Laporan Pelaksanaan Kegiatan EUC</h2>

    <!-- <div class="row my-4">
        <div class="col-md-2">
            <img src="{{ asset('logo.png') }}" width="100%">
        </div>
        <div class="col-md-10">
            <h5>Laporan Pelaksanaan Kegiatan - END User Computing (EUC)</h5>
        </div>
    </div> -->

    <form method="POST" action="{{ route('submit.laporan', ['jenis' => $jenis]) }}" class="my-5">
        @csrf
        @foreach ($templateFields as $field)
        <div class="mb-3">
            <label class="form-label" for="{{ $field }}">{{ ucfirst($field) }}</label>
            <input type="text"  class="form-control" name="{{ $field }}" id="{{ $field }}" required placeholder="text..">
        </div>
        @endforeach
        <button type="submit" class="btn btn-success px-4 rounded-pill">Submit</button>
    </form>


</div>
@endsection