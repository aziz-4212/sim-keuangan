@extends('layouts.app')
@section('content')
    <style>
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5); /* Set overlay background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999; /* Make sure the overlay appears above all other content */
        }

    </style>
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        Overview - STEP 1
                    </div>
                    <h2 class="page-title">
                        Cek Klaim
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <form action="{{ route('cek-klaim') }}" method="get" autocomplete="off" novalidate="">
                        <div class="input-group">
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                        <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                                        <path d="M21 21l-6 -6"></path>
                                    </svg>
                                </span>
                                <input type="text" value="{{ request('search') }}" class="form-control" placeholder="Cari SEP"
                                    aria-label="Search in website" name="search">
                            </div>
                            <a href="{{ route('cek-klaim') }}" class="btn btn-outline-secondary ">
                                Reset
                            </a>
                        </div>
                    </form>

                    <form id="proses-selisih-form" method="POST" action="{{ route('proses-selisih') }}">
                        <button type="submit" class="btn bg-yellow" id="prosesButton">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-table-minus">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M12.5 21h-7.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10" />
                                <path d="M3 10h18" />
                                <path d="M10 3v18" />
                                <path d="M16 19h6" />
                            </svg>
                            Proses Selisih Minus </button>
                    </form>
                </div>
                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible" id="alert-success">
                            <strong>{{ session('success') }}</strong>
                        </div>
                    @elseif(session('error'))
                        <div class="alert alert-danger alert-dismissible" id="alert-error">
                            <strong>{{ session('error') }}</strong>
                        </div>
                    @endif

                    <!-- Loader Overlay -->
                    <div id="loader-overlay" class="overlay d-none">
                        <div class="spinner-border text-white" role="status">
                            <span class="visually-hidden">Sedang Proses...</span>
                        </div>

                        <div class="loader-text" style="margin-left: 10px; color:white; font-size:18px;">Data sedang diproses...</div> 
                    </div>

                    <div class="tab-content">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th scope="col">SEP</th>
                                        <th scope="col">INACBG</th>
                                        <th scope="col">TARIF RUMAH SAKIT</th>
                                        <th scope="col">TARIF KLAIM</th>
                                        <th scope="col">SELISIH</th>
                                        <th scope="col">NOREG</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_klaim as $item)
                                        @php
                                            $selisih = $item->TARIFKLAIM - $item->TARIFRS;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->SEP }}</td>
                                            <td>{{ $item->INACBG }}</td>
                                            <td>Rp {{ number_format($item->TARIFRS, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->TARIFKLAIM, 0, ',', '.') }}</td>
                                            <td>
                                                <input type="text" class="form-control" name="disabled-input"
                                                    value="Rp {{ number_format($selisih, 0, ',', '.') }}"
                                                    style="color: {{ $selisih < 0 ? 'red' : 'black' }};" disabled>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="disabled-input"
                                                    value="{{ $item->NOREG ?? 'Kosong' }}"
                                                    style="color: {{ $item->is_mapping_sep ? 'red' : 'black' }};" disabled>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $data_klaim->links('layouts._partials._pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('prosesButton').addEventListener('click', function(e) {
            e.preventDefault();  // Mencegah form submit langsung

            // Tampilkan overlay loader
            document.getElementById('loader-overlay').classList.remove('d-none');

            // Kirim form setelah 2 detik untuk menunjukkan loader
            setTimeout(function() {
                document.getElementById('proses-selisih-form').submit();
            }, 2000);
        });
    </script>

    <script>
        // Fungsi untuk menghilangkan alert setelah 5 detik
        window.onload = function() {
            setTimeout(function() {
                var successAlert = document.getElementById('alert-success');
                var errorAlert = document.getElementById('alert-error');
                
                if (successAlert) {
                    successAlert.style.display = 'none';
                }

                if (errorAlert) {
                    errorAlert.style.display = 'none';
                }
            }, 3000); 
        };
    </script>
@endsection


