@extends('layouts.app')
@section('content')
    <style>
        #loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            /* Set overlay background */
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            /* Make sure the overlay appears above all other content */
        }
    </style>
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <!-- Page pre-title -->
                    <div class="page-pretitle">
                        Overview - STEP 3
                    </div>
                    <h2 class="page-title">
                        Cek Klaim - Hasil Sortir (Jasa Visit Minus)
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header d-flex justify-content-end align-items-center">

                    {{-- <form id="update-jasa-visit-form" method="POST" action="{{ route('update-jasa-visit') }}">
                        @csrf
                        <button type="submit" class="btn btn-success" id="prosesButton">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-refresh">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M20 11a8.1 8.1 0 0 0 -15.5 -2m-.5 -4v4h4" />
                                <path d="M4 13a8.1 8.1 0 0 0 15.5 2m.5 4v-4h-4" />
                            </svg>
                            Update Data Biaya Jasa Visit</button>
                    </form> --}}
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

                        <div class="loader-text" style="margin-left: 10px; color:white; font-size:18px;">Data sedang
                            diproses...</div>
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
                                        <th scope="col">NOMINAL (6%)</th>
                                        <th scope="col">JASA VISIT RILL</th>
                                        <th scope="col">SELISIH JASA VISIT</th>
                                        <th scope="col">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($data_klaim as $item)
                                        @php
                                            $enampersen = 0.06 * $item->TARIFKLAIM;

                                            $jasa_visit = DB::connection('sqlsrv')
                                                ->table('TRXPMR')
                                                ->select('BIAYADRRIIL')
                                                ->where('NOREG', $item->NOREG)
                                                ->where('KODEPMR', 'like', 'V%')
                                                ->get();
                                            $total_jasavisit = $jasa_visit->sum('BIAYADRRIIL');
                                            $selisih_jasavisit = $enampersen - $total_jasavisit;
                                        @endphp
                                        <tr @if($item->STATUS == 1) style="background-color: darkseagreen; color: white;" @endif>
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
                                                    value="Rp {{ number_format($enampersen, 0, ',', '.') }}"
                                                    style="color: black" disabled>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" name="disabled-input"
                                                    value="Rp {{ number_format($total_jasavisit, 0, ',', '.') }}"
                                                    style="color: black" disabled>
                                            </td>
                                            
                                            <td>
                                                @if($item->STATUS == 1)
                                                @else
                                                <input type="text" class="form-control" name="disabled-input"
                                                    value="Rp {{ number_format($selisih_jasavisit, 0, ',', '.') }}"
                                                    style="color: {{ $selisih_jasavisit < 0 ? 'red' : 'black' }};"
                                                    disabled>
                                                @endif
                                            </td>
                                            <td>
                                                @if ($item->STATUS == 1)
                                                    <span class="badge badge-outline text-red">Sudah Update</span>
                                                @else
                                                <button class="btn btn-success btn-detail" data-bs-toggle="modal"
                                                    data-bs-target="#modal-simple" data-noreg="{{ $item->NOREG }}"
                                                    data-enampersen="{{ $enampersen }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-spreadsheet">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                                        <path
                                                            d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                        <path d="M8 11h8v7h-8z" />
                                                        <path d="M8 15h8" />
                                                        <path d="M11 11v7" />
                                                    </svg> Detail</button>
                                                @endif
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

    <div class="modal modal-blur fade" id="modal-simple" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Jasa Visit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Data akan dimuat di sini -->
                    <p>Loading...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn me-auto" data-bs-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Update Data</button> --}}
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('prosesButton').addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah form submit langsung

            // Tampilkan overlay loader
            document.getElementById('loader-overlay').classList.remove('d-none');

            // Kirim form setelah 2 detik untuk menunjukkan loader
            setTimeout(function() {
                document.getElementById('update-jasa-visit-form').submit();
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

    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tangkap semua tombol dengan kelas btn-detail
            const detailButtons = document.querySelectorAll('.btn-detail');

            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const noreg = this.getAttribute('data-noreg'); // Ambil nilai NOREG
                    const enampersen = this.getAttribute(
                    'data-enampersen'); // Ambil nilai enampersen
                    const modalBody = document.querySelector('#modal-simple .modal-body');
                    const modalTitle = document.querySelector('#modal-simple .modal-title');
                    // Kosongkan isi modal body sementara
                    modalBody.innerHTML = '<p>Loading...</p>';
                    // Update judul modal dengan NOREG
                    modalTitle.innerHTML = `Detail Jasa Visit - ${noreg}`;

                    // Kirim AJAX request untuk mendapatkan data
                    fetch(`/detail-jasa-visit?noreg=${noreg}&enampersen=${enampersen}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            let html = `
                            <form id="updateForm">
                                <table class="table table-vcenter card-table" border="1" style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                    <thead>
                                        <tr>
                                            <th style="padding: 8px; text-align: center;">NOPMR</th>
                                            <th style="padding: 8px; text-align: center;">KODEPMR</th>
                                            <th style="padding: 8px; text-align: center;">NAMA</th>
                                            <th style="padding: 8px; text-align: center;">BIAYADR RIIL</th>
                                            <th style="padding: 8px; text-align: center;">BIAYADR (6%)</th>
                                            <th style="padding: 8px; text-align: center;">BIAYARS RIIL</th>
                                            <th style="padding: 8px; text-align: center;">BIAYARS UPDATE</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                        `;

                            let totalBiaya = 0; // Variabel untuk menghitung total biaya
                            let totalEnamPersen = 0;
                            let selisih_biayars = 0;
                            let update_biayars = 0;
                            let totalBiayaRS = 0;
                            let totalBiayaRS_update = 0;

                            if (data.length > 0) {
                                data.forEach((item, index) => {
                                    const biayadr = parseFloat(item.BIAYADRRIIL) || 0;
                                    const biayars = parseFloat(item.BIAYARSRIIL) || 0;
                                    const nilai_perbandingan = parseFloat(item.perbandingan) || 0;
                                    totalBiaya += biayadr;
                                    totalEnamPersen += nilai_perbandingan;
                                    selisih_biayars = biayadr - nilai_perbandingan;
                                    update_biayars = biayars + selisih_biayars;
                                    totalBiayaRS += biayars;
                                    totalBiayaRS_update += update_biayars;

                                    html += `
                                    <tr>
                                        <input type="hidden" name="NOREG[${index}]" value="${item.NOREG}" class="form-control">

                                        <td style="padding: 8px;">${item.NOPMR}</td>
                                        <input type="hidden" name="NOPMR[${index}]" value="${item.NOPMR}" class="form-control">

                                        <td style="padding: 8px;">${item.KODEPMR}</td>
                                        <input type="hidden" name="KODEPMR[${index}]" value="${item.KODEPMR}" class="form-control">

                                        <td style="padding: 8px;">${item.NAMAPMR}</td>
                                        
                                        <td style="padding: 8px;">Rp ${new Intl.NumberFormat('id-ID').format(item.BIAYADRRIIL)}</td>
                                        <input type="hidden" name="BIAYADRRIIL[${index}]" value="${item.BIAYADRRIIL}" class="form-control">

                                        <td style="padding: 8px;">Rp ${new Intl.NumberFormat('id-ID').format(item.perbandingan)}</td>
                                        <input type="hidden" name="UPDATE_BIAYADR[${index}]" value="${item.perbandingan}" class="form-control">

                                        <td style="padding: 8px;">Rp ${new Intl.NumberFormat('id-ID').format(item.BIAYARSRIIL)}</td>
                                        <input type="hidden" name="BIAYARSRIIL[${index}]" value="${item.BIAYARSRIIL}" class="form-control">

                                        <td style="padding: 8px;">Rp ${new Intl.NumberFormat('id-ID').format(update_biayars)}</td>
                                        <input type="hidden" name="UPDATE_BIAYARS[${index}]" value="${update_biayars}" class="form-control">
                                    </tr>
                                `;
                                });

                                // Tambahkan baris total di bawah tabel
                                html += `
                                <tr>
                                    <td colspan="3" style="padding: 8px; font-weight: bold; text-align: center;">TOTAL</td>
                                    <td style="padding: 8px; font-weight: bold;">Rp ${new Intl.NumberFormat('id-ID').format(totalBiaya)}</td>
                                    <td style="padding: 8px; font-weight: bold;">Rp ${new Intl.NumberFormat('id-ID').format(totalEnamPersen)}</td>
                                    <td style="padding: 8px; font-weight: bold;">Rp ${new Intl.NumberFormat('id-ID').format(totalBiayaRS)}</td>
                                    <td style="padding: 8px; font-weight: bold;">Rp ${new Intl.NumberFormat('id-ID').format(totalBiayaRS_update)}</td>
                                </tr>
                            `;
                            } else {
                                html += `
                                <tr>
                                    <td colspan="3" style="padding: 8px; text-align: center;">No data found</td>
                                </tr>
                            `;
                            }

                            html += `
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-primary float-end" style="margin-top: 20px;">Update Perubahan</button>
                            </form>
                        `;

                            modalBody.innerHTML = html;

                            // Handle form submission
                            document.querySelector('#updateForm').addEventListener('submit', function(event) {
                                event.preventDefault();
                                
                                const formData = new FormData(this);
        
                                fetch('/update-jasa-visit', {
                                    method: 'POST',
                                    body: formData
                                })
                                .then(response => response.json())
                                .then(result => {
                                    alert(result.message);
                                    // Optionally close modal or refresh data
                                })
                                .catch(error => {
                                    console.error('Error updating data:', error);
                                });
                            });

                        })
                        .catch(error => {
                            modalBody.innerHTML =
                            `<p>Error fetching data: ${error.message}</p>`;
                            console.error(error);
                        });

                });
            });
        });
    </script> --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tangkap semua tombol dengan kelas btn-detail
            const detailButtons = document.querySelectorAll('.btn-detail');
    
            detailButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const noreg = this.getAttribute('data-noreg'); // Ambil nilai NOREG
                    const enampersen = this.getAttribute('data-enampersen'); // Ambil nilai enampersen
                    const modalBody = document.querySelector('#modal-simple .modal-body');
                    const modalTitle = document.querySelector('#modal-simple .modal-title');
                    
                    // Kosongkan isi modal body sementara
                    modalBody.innerHTML = '<p>Loading...</p>';
                    
                    // Update judul modal dengan NOREG
                    modalTitle.innerHTML = `Detail Jasa Visit - ${noreg}`;
    
                    // Kirim AJAX request untuk mendapatkan data
                    fetch(`/detail-jasa-visit?noreg=${noreg}&enampersen=${enampersen}`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            const jasaVisitDokter = data.jasa_visit_dokter; // Data array
                            const showUpdateButton = data.showUpdateButton; // Properti boolean
                            let html = `
                                <form id="updateForm" method="POST" action="{{ route('update-jasa-visit') }}">
                                    @csrf
                                    <table class="table table-vcenter card-table" border="1" style="width: 100%; border-collapse: collapse; font-size: 11px;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 8px; text-align: center;">NOPMR</th>
                                                <th style="padding: 8px; text-align: center;">KODEPMR</th>
                                                <th style="padding: 8px; text-align: center;">NAMA</th>
                                                <th style="padding: 8px; text-align: center;">BIAYADR RIIL</th>
                                                <th style="padding: 8px; text-align: center;">BIAYADR (6%)</th>
                                                <th style="padding: 8px; text-align: center;">BIAYARS RIIL</th>
                                                <th style="padding: 8px; text-align: center;">BIAYARS UPDATE</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                            `;
    
                            let totalBiaya = 0; // Variabel untuk menghitung total biaya
                            let totalEnamPersen = 0;
                            let totalBiayaRS = 0;
                            let totalBiayaRS_update = 0;
    
                            if (jasaVisitDokter.length > 0) {
                                jasaVisitDokter.forEach((item, index) => {
                                    const biayadr = parseFloat(item.BIAYADRRIIL) || 0;
                                    const biayars = parseFloat(item.BIAYARSRIIL) || 0;
                                    const perbandingan = parseFloat(item.perbandingan) || 0;
                                    const selisih_biayars = biayadr - perbandingan;
                                    const update_biayars = biayars + selisih_biayars;
    
                                    totalBiaya += biayadr;
                                    totalEnamPersen += perbandingan;
                                    totalBiayaRS += biayars;
                                    totalBiayaRS_update += update_biayars;
    
                                    html += `
                                        <tr>
                                            <input type="hidden" name="NOREG[${index}]" value="${item.NOREG}" class="form-control">
                                            <td style="padding: 8px;">${item.NOPMR}</td>
                                            <input type="hidden" name="NOPMR[${index}]" value="${item.NOPMR}" class="form-control">
                                            <td style="padding: 8px;">${item.KODEPMR}</td>
                                            <input type="hidden" name="KODEPMR[${index}]" value="${item.KODEPMR}" class="form-control">
                                            <td style="padding: 8px;">${item.NAMAPMR}</td>
                                            <td style="padding: 8px;">Rp ${new Intl.NumberFormat('id-ID').format(item.BIAYADRRIIL)}</td>
                                            <input type="hidden" name="BIAYADRRIIL[${index}]" value="${item.BIAYADRRIIL}" class="form-control">
                                            <td style="padding: 8px;">Rp ${new Intl.NumberFormat('id-ID').format(perbandingan)}</td>
                                            <input type="hidden" name="UPDATE_BIAYADR[${index}]" value="${perbandingan}" class="form-control">
                                            <td style="padding: 8px;">Rp ${new Intl.NumberFormat('id-ID').format(item.BIAYARSRIIL)}</td>
                                            <input type="hidden" name="BIAYARSRIIL[${index}]" value="${item.BIAYARSRIIL}" class="form-control">
                                            <td style="padding: 8px;">Rp ${new Intl.NumberFormat('id-ID').format(update_biayars)}</td>
                                            <input type="hidden" name="UPDATE_BIAYARS[${index}]" value="${update_biayars}" class="form-control">
                                        </tr>
                                    `;
                                });
    
                                // Tambahkan baris total di bawah tabel
                                html += `
                                    <tr>
                                        <td colspan="3" style="padding: 8px; font-weight: bold; text-align: center;">TOTAL</td>
                                        <td style="padding: 8px; font-weight: bold;">Rp ${new Intl.NumberFormat('id-ID').format(totalBiaya)}</td>
                                        <td style="padding: 8px; font-weight: bold;">Rp ${new Intl.NumberFormat('id-ID').format(totalEnamPersen)}</td>
                                        <td style="padding: 8px; font-weight: bold;">Rp ${new Intl.NumberFormat('id-ID').format(totalBiayaRS)}</td>
                                        <td style="padding: 8px; font-weight: bold;">Rp ${new Intl.NumberFormat('id-ID').format(totalBiayaRS_update)}</td>
                                    </tr>
                                `;
                            } else {
                                html += `
                                    <tr>
                                        <td colspan="7" style="padding: 8px; text-align: center;">No data found</td>
                                    </tr>
                                `;
                            }
    
                            html += `
                                    </tbody>
                                </table>
                            `;

                            if (showUpdateButton) {
                                html += `
                                    <button type="submit" class="btn btn-primary float-end" style="margin-top: 20px;">Update Perubahan</button>
                                `;
                            }

                            html += `</form>`;
                            modalBody.innerHTML = html;

                            const updateForm = document.getElementById('updateForm');
                            const updateButton = document.getElementById('updateButton');

                            updateForm.addEventListener('submit', function (event) {
                                event.preventDefault(); // Mencegah form dikirim langsung

                                if (confirm('Apakah anda yakin ingin mengupdate data?')) {
                                    // Tampilkan overlay loader
                                    const overlay = document.getElementById('loader-overlay');
                                    overlay.classList.remove('d-none');

                                    // Kirim form setelah 0.5 detik (untuk memastikan loader terlihat)
                                    setTimeout(() => {
                                        updateForm.submit();
                                    }, 500);
                                }
                            });
    
                        })
                        .catch(error => {
                            modalBody.innerHTML = `<p>Error fetching data: ${error.message}</p>`;
                            console.error(error);
                        });
    
                });
            });
        });
    </script>
    
@endsection
