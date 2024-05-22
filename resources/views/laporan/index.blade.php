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
                        Laporan Keuangan
                    </h2>
                </div>
                <!-- Page title actions -->
                <div class="col-auto ms-auto d-print-none">
                    <div class="btn-list">
                        <a href="#" class="btn btn-primary d-none d-sm-inline-block" data-bs-toggle="modal"
                            data-bs-target="#modal-report">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-horizontal">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 6m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M4 6l8 0" />
                                <path d="M16 6l4 0" />
                                <path d="M8 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M4 12l2 0" />
                                <path d="M10 12l10 0" />
                                <path d="M17 18m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M4 18l11 0" />
                                <path d="M19 18l1 0" />
                            </svg>
                            Filter
                        </a>
                        <a href="#" class="btn btn-primary d-sm-none btn-icon" data-bs-toggle="modal"
                            data-bs-target="#modal-report" aria-label="Create new report">
                            <!-- Download SVG icon from http://tabler-icons.io/i/plus -->
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-adjustments-horizontal">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 6m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M4 6l8 0" />
                                <path d="M16 6l4 0" />
                                <path d="M8 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M4 12l2 0" />
                                <path d="M10 12l10 0" />
                                <path d="M17 18m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                                <path d="M4 18l11 0" />
                                <path d="M19 18l1 0" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page-body">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <form method="POST" action="{{ route('laporan.download-excel') }}">
                        @csrf
                        <input type="hidden" name="jenis_layanan" value="{{ request('jenis_layanan') }}">
                        <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                        <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                        <button type="submit" class="btn btn-success">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="icon icon-tabler icons-tabler-outline icon-tabler-file-spreadsheet">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                <path d="M14 3v4a1 1 0 0 0 1 1h4" />
                                <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                <path d="M8 11h8v7h-8z" />
                                <path d="M8 15h8" />
                                <path d="M11 11v7" />
                            </svg> Download Excel</button>
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
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col">VENCD</th>
                                        <th scope="col">VCRNO</th>
                                        <th scope="col">APGRPID</th>
                                        <th scope="col">CSTERMID</th>
                                        <th scope="col">CSMONTH</th>
                                        <th scope="col">CSYEAR</th>
                                        <th scope="col">USLOGNM</th>
                                        <th scope="col">MODULID</th>
                                        <th scope="col">REFDOCTP</th>
                                        <th scope="col">CCODEGRP</th>
                                        <th scope="col">APBTCHNO</th>
                                        <th scope="col">APDPNO</th>
                                        <th scope="col">DOCDATE</th>
                                        <th scope="col">DOCDESC</th>
                                        <th scope="col">DOCTEXT</th>
                                        <th scope="col">DOCINPUT</th>
                                        <th scope="col">DOCSTAT</th>
                                        <th scope="col">VCRTYPE</th>
                                        <th scope="col">TAXCALC</th>
                                        <th scope="col">KURS</th>
                                        <th scope="col">DISTSAMT</th>
                                        <th scope="col">DISTHAMT</th>
                                        <th scope="col">TAXSAMT</th>
                                        <th scope="col">TAXHAMT</th>
                                        <th scope="col">DPRATE</th>
                                        <th scope="col">DPSAMT</th>
                                        <th scope="col">DPHAMT</th>
                                        <th scope="col">VCRSAMT</th>
                                        <th scope="col">VCRHAMT</th>
                                        <th scope="col">COMPLETE</th>
                                        <th scope="col">DUEDATE</th>
                                        <th scope="col">DISCDATE</th>
                                        <th scope="col">DISCRATE</th>
                                        <th scope="col">BALSAMT</th>
                                        <th scope="col">BALHAMT</th>
                                        <th scope="col">BALRAMT</th>
                                        <th scope="col">CCODE</th>
                                        <th scope="col">TAXGRPID</th>
                                        <th scope="col">CALC_TAX</th>
                                        <th scope="col">WITH_DP</th>
                                        <th scope="col">KURSPAJAK</th>
                                        <th scope="col">CONTRACTNO</th>
                                        <th scope="col">CONTRACTNET</th>
                                        <th scope="col">PROGRESS</th>
                                        <th scope="col">PROGRESSNET</th>
                                        <th scope="col">VALIDDATE</th>
                                        <th scope="col">VALIDBY</th>
                                        <th scope="col">FAKTDATE</th>
                                        <th scope="col">FAKTNO</th>
                                        <th scope="col">VENNM</th>
                                        <th scope="col">VENADD</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($apvcrh as $item)
                                        <tr class="clickable-row">
                                            <td>{{ $item->VENCD }}</td>
                                            <td>{{ $item->VCRNO }}</td>
                                            <td>{{ $item->APGRPID }}</td>
                                            <td>{{ $item->CSTERMID }}</td>
                                            <td>{{ $item->CSMONTH }}</td>
                                            <td>{{ $item->CSYEAR }}</td>
                                            <td>{{ $item->USLOGNM }}</td>
                                            <td>{{ $item->MODULID }}</td>
                                            <td>{{ $item->REFDOCTP }}</td>
                                            <td>{{ $item->CCODEGRP }}</td>
                                            <td>{{ $item->APBTCHNO }}</td>
                                            <td>{{ $item->APDPNO }}</td>
                                            <td>{{ $item->DOCDATE }}</td>
                                            <td>{{ $item->DOCDESC }}</td>
                                            <td>{{ $item->DOCTEXT }}</td>
                                            <td>{{ $item->DOCINPUT }}</td>
                                            <td>{{ $item->DOCSTAT }}</td>
                                            <td>{{ $item->VCRTYPE }}</td>
                                            <td>{{ $item->TAXCALC }}</td>
                                            <td>{{ $item->KURS }}</td>
                                            <td>{{ $item->DISTSAMT }}</td>
                                            <td>{{ $item->DISTHAMT }}</td>
                                            <td>{{ $item->TAXSAMT }}</td>
                                            <td>{{ $item->TAXHAMT }}</td>
                                            <td>{{ $item->DPRATE }}</td>
                                            <td>{{ $item->DPSAMT }}</td>
                                            <td>{{ $item->DPHAMT }}</td>
                                            <td>{{ $item->VCRSAMT }}</td>
                                            <td>{{ $item->VCRHAMT }}</td>
                                            <td>{{ $item->COMPLETE }}</td>
                                            <td>{{ $item->DUEDATE }}</td>
                                            <td>{{ $item->DISCDATE }}</td>
                                            <td>{{ $item->DISCRATE }}</td>
                                            <td>{{ $item->BALSAMT }}</td>
                                            <td>{{ $item->BALHAMT }}</td>
                                            <td>{{ $item->BALRAMT }}</td>
                                            <td>{{ $item->CCODE }}</td>
                                            <td>{{ $item->TAXGRPID }}</td>
                                            <td>{{ $item->CALC_TAX }}</td>
                                            <td>{{ $item->WITH_DP }}</td>
                                            <td>{{ $item->KURSPAJAK }}</td>
                                            <td>{{ $item->CONTRACTNO }}</td>
                                            <td>{{ $item->CONTRACTNET }}</td>
                                            <td>{{ $item->PROGRESS }}</td>
                                            <td>{{ $item->PROGRESSNET }}</td>
                                            <td>{{ $item->VALIDDATE }}</td>
                                            <td>{{ $item->VALIDBY }}</td>
                                            <td>{{ $item->FAKTDATE }}</td>
                                            <td>{{ $item->FAKTNO }}</td>
                                            <td>{{ $item->VENNM }}</td>
                                            <td>{{ $item->VENADD }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $apvcrh->links('layouts._partials._pagination') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal modal-blur fade" id="modal-report" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('laporan.index') }}" method="GET">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                                    <input type="text" class="form-control" placeholder="yyyy-mm-dd"
                                        id="datepicker-default" name="tanggal_mulai"
                                        value="{{ request('tanggal_mulai') }}" placeholder="Tanggal Mulai">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="mb-3">
                                    <label for="tanggal_akhir" class="form-label">Tanggal Akhir</label>
                                    <input type="text" class="form-control" placeholder="yyyy-mm-dd"
                                        id="datepicker-default1" name="tanggal_akhir"
                                        value="{{ request('tanggal_akhir') }}" placeholder="Tanggal Akhir">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('laporan.index') }}" class="btn btn-link link-secondary">
                            Reset Filter
                        </a>
                        <button type="submit" class="btn btn-primary ms-auto" data-bs-dismiss="modal">
                            Apply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
