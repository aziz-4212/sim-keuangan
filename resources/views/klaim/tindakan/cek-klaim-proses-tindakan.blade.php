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
                        Overview - STEP 2
                    </div>
                    <h2 class="page-title">
                        Cek Klaim (Tindakan IBS) - Hasil Selisih Klaim Minus
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <form action="{{ route('cek-klaim-minus-selisih') }}" method="get" autocomplete="off" novalidate="">
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
                            <a href="{{ route('cek-klaim-minus-selisih') }}" class="btn btn-outline-secondary ">
                                Reset
                            </a>
                        </div>
                    </form>

                    <a href="{{ route('jasa-medis-minus-tindakan') }}" class="btn bg-orange" id="prosesButton">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-table-minus">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M12.5 21h-7.5a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10" />
                            <path d="M3 10h18" />
                            <path d="M10 3v18" />
                            <path d="M16 19h6" />
                        </svg>
                        Sortir Jasa Medis Minus
                    </a>
                    
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
                                        <th scope="col">SELISIH KLAIM</th>
                                        <th scope="col">NOREG</th>
                                        <th scope="col">NOMINAL (25%)</th>
                                        <th scope="col">JASA MEDIS RILL</th>
                                        <th scope="col">SELISIH JASA VISIT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_klaim as $item)
                                        @php
                                            $duapuluhlimapersen = 0.25 * $item->TARIFKLAIM;

                                            $jasa_medis = DB::connection('sqlsrv')
                                                ->table('TRXPMR')
                                                ->select('BIAYADRRIIL')
                                                ->where('NOREG', $item->NOREG)
                                                ->whereIn('KODEPMR', ['OKAD01','OKAD02'])
                                                ->get();
                                            $total_jasamedis = $jasa_medis->sum('BIAYADRRIIL');

                                            $selisih_jasamedis = $duapuluhlimapersen - $total_jasamedis;
                                        @endphp
                                        <tr>
                                            <td>{{ $item->SEP }}</td>
                                            <td>{{ $item->INACBG }}</td>
                                            <td>Rp {{ number_format($item->TARIFRS, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($item->TARIFKLAIM, 0, ',', '.') }}</td>
                                            <td style="color: red;">
                                                Rp {{ number_format($item->SELISIH, 0, ',', '.') }}
                                            </td>
                                            <td>
                                                {{ $item->NOREG ?? 'Kosong' }}
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="disabled-input"
                                                value="Rp {{ number_format($duapuluhlimapersen, 0, ',', '.') }}"
                                                style="color: black" disabled>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="disabled-input"
                                                value="Rp {{ number_format($total_jasamedis, 0, ',', '.') }}"
                                                style="color: black" disabled>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="disabled-input"
                                                    value="Rp {{ number_format($selisih_jasamedis, 0, ',', '.') }}"
                                                    style="color: {{ $selisih_jasamedis < 0 ? 'red' : 'black' }};" disabled>
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
            e.preventDefault(); // Mencegah navigasi langsung
    
            // Tampilkan overlay loader
            document.getElementById('loader-overlay').classList.remove('d-none');
    
            // Tunggu 2 detik sebelum mengarahkan ke rute
            setTimeout(function() {
                window.location.href = "{{ route('jasa-medis-minus-tindakan') }}";
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


