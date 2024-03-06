@extends('layouts.app')
@section('content')
    <div class="container-fluid p-3">
        <div class="card">
            <div class="card-header">
                <div class="row flex-between-end">
                    <div class="col-10 align-self-center">
                        <h5 class="mb-0" data-anchor="data-anchor">Laporan</h5>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible">
                        <button type="button" class="close text-white" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong>{{ session('success') }}</strong>
                    </div>
                @elseif(session('error'))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close text-white" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <strong>{{ session('error') }}</strong>
                    </div>
                @endif
                <div class="tab-content">
                    <div id="tableExample2">
                        <form method="GET" action="{{ route('laporan.index') }}">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <label for="tanggal_mulai">Tanggal Mulai</label>
                                        <input type="date" class="form-control" name="tanggal_mulai" id="tanggal_mulai" value="{{ request('tanggal_mulai') }}" placeholder="Tanggal Mulai">
                                    </div>
                                    <div class="col-sm-2">
                                        <label for="tanggal_akhir">Tanggal Akhir</label>
                                        <input type="date" class="form-control" name="tanggal_akhir" id="tanggal_akhir" value="{{ request('tanggal_akhir') }}" placeholder="Tanggal Akhir">
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn bg-maroon" style="margin-top: 25px">Cari</button>
                                        <a href="{{ route('laporan.index') }}" class="btn bg-maroon" style="margin-top: 25px">Reset Filter</a>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('laporan.download-excel') }}">
                            @csrf
                            <input type="hidden" name="jenis_layanan" value="{{ request('jenis_layanan') }}">
                            <input type="hidden" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}">
                            <input type="hidden" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}">
                            <button type="submit" class="btn bg-maroon" style="margin-top: 25px"><i class="fas fa-download"></i> Download Excel</button>
                        </form>

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
                                            <td>{{$item->VENCD}}</td>
                                            <td>{{$item->VCRNO}}</td>
                                            <td>{{$item->APGRPID}}</td>
                                            <td>{{$item->CSTERMID}}</td>
                                            <td>{{$item->CSMONTH}}</td>
                                            <td>{{$item->CSYEAR}}</td>
                                            <td>{{$item->USLOGNM}}</td>
                                            <td>{{$item->MODULID}}</td>
                                            <td>{{$item->REFDOCTP}}</td>
                                            <td>{{$item->CCODEGRP}}</td>
                                            <td>{{$item->APBTCHNO}}</td>
                                            <td>{{$item->APDPNO}}</td>
                                            <td>{{$item->DOCDATE}}</td>
                                            <td>{{$item->DOCDESC}}</td>
                                            <td>{{$item->DOCTEXT}}</td>
                                            <td>{{$item->DOCINPUT}}</td>
                                            <td>{{$item->DOCSTAT}}</td>
                                            <td>{{$item->VCRTYPE}}</td>
                                            <td>{{$item->TAXCALC}}</td>
                                            <td>{{$item->KURS}}</td>
                                            <td>{{$item->DISTSAMT}}</td>
                                            <td>{{$item->DISTHAMT}}</td>
                                            <td>{{$item->TAXSAMT}}</td>
                                            <td>{{$item->TAXHAMT}}</td>
                                            <td>{{$item->DPRATE}}</td>
                                            <td>{{$item->DPSAMT}}</td>
                                            <td>{{$item->DPHAMT}}</td>
                                            <td>{{$item->VCRSAMT}}</td>
                                            <td>{{$item->VCRHAMT}}</td>
                                            <td>{{$item->COMPLETE}}</td>
                                            <td>{{$item->DUEDATE}}</td>
                                            <td>{{$item->DISCDATE}}</td>
                                            <td>{{$item->DISCRATE}}</td>
                                            <td>{{$item->BALSAMT}}</td>
                                            <td>{{$item->BALHAMT}}</td>
                                            <td>{{$item->BALRAMT}}</td>
                                            <td>{{$item->CCODE}}</td>
                                            <td>{{$item->TAXGRPID}}</td>
                                            <td>{{$item->CALC_TAX}}</td>
                                            <td>{{$item->WITH_DP}}</td>
                                            <td>{{$item->KURSPAJAK}}</td>
                                            <td>{{$item->CONTRACTNO}}</td>
                                            <td>{{$item->CONTRACTNET}}</td>
                                            <td>{{$item->PROGRESS}}</td>
                                            <td>{{$item->PROGRESSNET}}</td>
                                            <td>{{$item->VALIDDATE}}</td>
                                            <td>{{$item->VALIDBY}}</td>
                                            <td>{{$item->FAKTDATE}}</td>
                                            <td>{{$item->FAKTNO}}</td>
                                            <td>{{$item->VENNM}}</td>
                                            <td>{{$item->VENADD}}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                            {{$apvcrh->links('layouts._partials._pagination')}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
