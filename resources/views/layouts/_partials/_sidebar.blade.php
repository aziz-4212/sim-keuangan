{{-- @php
    // Periksa apakah USGRPID dari pengguna adalah "dokter"
    $userGroup = DB::connection('sqlsrv1')
        ->table('USERGRUP')
        ->where('USLOGNM', auth()->user()->user_log->USLOGNM)
        ->first();
@endphp
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-lightblue">
    <!-- Brand Logo -->
    <a href="{{ route('/') }}" class="brand-link">
        <img src="{{ asset('assets/dist/img/AdminLTELogo.png') }}" alt="AdminLTE Logo"
            class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-bold">ERM RSI Kendal</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-2 pb-2 mb-2 nav-sidebar nav nav-pills flex-column">
            <li class="nav-item">
                <a href="{{ route('logout') }}" class="nav-link">
                    <i class="nav-icon fa-solid fa-right-from-bracket"></i>
                    <p>
                        Logout
                    </p>
                </a>
            </li>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('/') }}" class="nav-link{{ request()->is(['/']) ? ' active' : '' }}">
                        <i class="nav-icon fa-solid fa-gauge"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-header">ERM Rawat Jalan</li>
                @if ($userGroup && $userGroup->USGRPID === 'dokter')
                    <li class="nav-item">
                        <a href="{{ route('medical') }}"
                            class="nav-link{{ request()->is(['medical', 'resep/*', 'order/*', 'orderDetail/*', 'orderMedical/*', 'orderNonRacikan/*', 'orderRacikan/*', 'orderLaboratorium/*', 'orderRadiologi/*', 'orderICD/*', 'internalConsul/*', 'historyPasien/*']) ? ' active' : '' }}">
                            <i class="nav-icon fa-solid fa-laptop-medical"></i>
                            <p>
                                E-Medical
                            </p>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a href="{{ route('rawatJalan') }}"
                            class="nav-link{{ request()->is(['rawatJalan', 'menuRawatJalan/*', 'assesmentRawatJalan/*']) ? ' active' : '' }}">
                            <i class="nav-icon fa-solid fa-wheelchair"></i>
                            <p>
                                Rawat Jalan
                            </p>
                        </a>
                    </li>
                @endif

                @if ($userGroup && $userGroup->USGRPID === 'dokter')
                    <li class="nav-header">Konsultasi</li>
                    <li class="nav-item">
                        <a href="{{ route('consul') }}"
                            class="nav-link{{ request()->is(['consul', 'detailconsul/*']) ? ' active' : '' }}">
                            <i class="nav-icon fa-solid fa-comments"></i>
                            <p>
                                Daftar Konsul
                            </p>
                            @php
                                $user = auth()->user()->user_log->USFULLNM;
                                $kodeDokter = DB::connection('sqlsrv1')
                                    ->table('USERLOG')
                                    ->select('DOKTER.KODEDOKTER')
                                    ->join('DOKTER', 'USERLOG.USFULLNM', '=', 'DOKTER.NAMADOKTER')
                                    ->where('USERLOG.USFULLNM', $user)
                                    ->first();
                                $listConsul = DB::connection('sqlsrv1')
                                    ->table('REGMUT')
                                    ->select('HASILPEMERIKSAAN')
                                    ->where('KODEDR_B', $kodeDokter->KODEDOKTER)
                                    ->where('HASILPEMERIKSAAN', null)
                                    ->get();
                                $totalConsul = $listConsul->whereNull('HASILPEMERIKSAAN')->count();
                            @endphp
                            <span class="badge badge-danger right">{{ $totalConsul }}</span>
                        </a>
                    </li>
                @endif
                <li class="nav-header">ERM IGD</li>
                <li class="nav-item">
                    <a href="{{ route('erm-igd.index') }}"
                        class="nav-link{{ request()->is(['erm-igd', 'erm-igd/*']) ? ' active' : '' }}">
                        <i class="nav-icon fa-solid fa-laptop-medical"></i>
                        <p>
                            ERM IGD
                        </p>
                    </a>
                </li>
                <li class="nav-header">ERM Rawat Inap</li>
                <li class="nav-item">
                    <a href="{{ route('rawat-inap') }}"
                        class="nav-link{{ request()->is(['rawat-inap', 'rawat-inap/*', 'cppt', 'cppt/*', 'cpptDokter/*', 'resepRWI/*', 'reseprcRWI/*', 'laboratoriumRWI/*', 'radiologiRWI/*']) ? ' active' : '' }}">
                        <i class="nav-icon fa-solid fa-bed-pulse"></i>
                        <p>
                            Rawat Inap
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ibs') }}"
                        class="nav-link{{ request()->is(['ibs', 'ibs/*']) ? ' active' : '' }}">
                        <i class="nav-icon fa-solid fa-laptop-medical"></i>
                        <p>
                            IBS
                        </p>
                    </a>
                </li>

                <li class="nav-header">Pelayanan</li>
                <li class="nav-item">
                    <a href="{{ route('admin-poli') }}"
                        class="nav-link{{ request()->is(['admin-antrian', 'admin-antrian/*']) ? ' active' : '' }}">
                        <i class="nav-icon fa-solid fa-users"></i>
                        <p>
                            Antrian Poli
                        </p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin-kamar') }}"
                        class="nav-link{{ request()->is(['admin-kamar', 'admin-kamar/*']) ? ' active' : '' }}">
                        <i class="nav-icon fa-solid fa-bed"></i>
                        <p>
                            Keteresediaan Kamar
                        </p>
                    </a>
                </li>

                <li class="nav-header">Template</li>
                <li
                    class="nav-item{{ request()->is(['templateSOAP', 'templateResep', 'templateResepRacikan', 'templateCatatanOperasi', 'templateDeskripsiEcho']) ? ' menu-open' : '' }}">
                    <a href="#"
                        class="nav-link{{ request()->is(['templateSOAP', 'templateResep', 'templateResepRacikan', 'templateCatatanOperasi', 'templateDeskripsiEcho']) ? ' active' : '' }}">
                        <i class="nav-icon fas fa-edit"></i>
                        <p> Template <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('templateSOAP') }}"
                                class="nav-link{{ request()->is(['templateSOAP']) ? ' active' : '' }}">
                                <i class="nav-icon fa-solid fa-bookmark"></i>
                                <p>Template SOAP</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('templateResep') }}"
                                class="nav-link{{ request()->is(['templateResep']) ? ' active' : '' }}">
                                <i class="nav-icon fa-solid fa-prescription-bottle-medical"></i>
                                <p>Template Resep</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('templateResepRacikan') }}"
                                class="nav-link{{ request()->is(['templateResepRacikan']) ? ' active' : '' }}">
                                <i class="nav-icon fa-solid fa-prescription-bottle-medical"></i>
                                <p>Template Resep Racikan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('templateCatatanOperasi') }}"
                                class="nav-link{{ request()->is(['templateCatatanOperasi']) ? ' active' : '' }}">
                                <i class="nav-icon fa-solid fa-file-waveform"></i>
                                <p>Template Cat. Operasi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('templateDeskripsiEcho') }}"
                                class="nav-link{{ request()->is(['templateDeskripsiEcho']) ? ' active' : '' }}">
                                <i class="nav-icon fa-solid fa-file-contract"></i>
                                <p>Template Desc Echo</p>
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- <li class="nav-header">Master data</li>
                <li class="nav-item">
                    <a href="{{ route('master-dokter-intern.index') }}"
                        class="nav-link{{ request()->is(['master-dokter-intern', 'master-dokter-intern/*']) ? ' active' : '' }}">
                        <i class="nav-icon fa-solid fa-bed"></i>
                        <p>
                            Dokter Intern
                        </p>
                    </a>
                </li> --}}
                <li class="nav-header">Berkas</li>
                <li class="nav-item{{ request()->is(['berkas']) ? ' menu-open' : '' }}">
                <li class="nav-item">
                    <a href="{{ route('berkas') }}" class="nav-link{{ request()->is(['berkas']) ? ' active' : '' }}">
                        <i class="nav-icon fa-solid fa-bookmark"></i>
                        <p>Berkas</p>
                    </a>
                </li>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside> --}}
