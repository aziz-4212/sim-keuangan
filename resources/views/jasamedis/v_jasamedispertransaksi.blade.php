@extends('layouts.app')
@section('content')
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        Overview
                    </div>
                    <h2 class="page-title">
                        Jasa Medis
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-end align-items-center">
                    <form action="{{ route('jasaMedis') }}" method="get" autocomplete="off" novalidate="">
                        <div class="input-icon">
                            <span class="input-icon-addon">
                                <!-- Download SVG icon from http://tabler-icons.io/i/search -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                    <path d="M21 21l-6 -6"></path>
                                </svg>
                            </span>
                            <input type="text" value="{{ request('search') }}" class="form-control" placeholder="Search…"
                                aria-label="Search in website" name="search">
                        </div>
                    </form>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close text-white" data-dismiss="alert"
                                aria-hidden="true">&times;</button>
                            <strong>{{ session('success') }}</strong>
                        </div>
                    @elseif(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close text-white" data-dismiss="alert"
                                aria-hidden="true">&times;</button>
                            <strong>{{ session('error') }}</strong>
                        </div>
                    @endif
                    <div class="tab-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">NOVOUCHER</th>
                                        <th scope="col">TANGGAL AWAL</th>
                                        <th scope="col">TANGGAL AKHIR</th>
                                        <th scope="col">NOREG</th>
                                        <th scope="col">DOKTER</th>
                                        <th scope="col">PMR</th>
                                        <th scope="col">NOPASIEN</th>
                                        <th scope="col">NAMA</th>
                                        <th scope="col">PERAWAT</th>
                                        <th scope="col">SHAREDOKTER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($result && $result->count() > 0)
                                        @foreach ($result as $a)
                                            <tr>
                                                <td>{{ $a->NOKONFIRM }}</td>
                                                <td>{{ $a->TGLCUTOFFJPPIU }}</td>
                                                <td>{{ $a->TGCUTOFFJMPT }}</td>
                                                <td>{{ $a->NOREG }}</td>
                                                <td>{{ $a->NAMADOKTER }}</td>
                                                <td>{{ $a->NAMAPMR }}</td>
                                                <td>{{ $a->NOPASIEN }}</td>
                                                <td>{{ $a->NAMAPASIEN }}</td>
                                                <td>{{ formatRupiah($a->BIAYAST) }}</td>
                                                <td>{{ formatRupiah($a->BIAYADR) }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="10" class="text-center"><img src="{{ asset('img/search.png') }}"
                                                    alt="empty" width="200px"></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        @if ($result && $result->count() > 0)
                            {{ $result->links('layouts._partials._pagination') }}
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
