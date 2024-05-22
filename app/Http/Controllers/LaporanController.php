<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Models\Apvcrh;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $now = Carbon::now()->format('Y-m-d');
        if (!empty($request->get('tanggal_mulai')) && !empty($request->get('tanggal_akhir'))) {
            $apvcrh = Apvcrh::whereDate('DOCDATE', '>=', $request->get('tanggal_mulai'))
                ->whereDate('DOCDATE', '<=', $request->get('tanggal_akhir'))->orderBy('VENCD', 'desc')->paginate(15)->appends(request()->query());
        } elseif (!empty($request->get('tanggal_mulai'))) {
            $apvcrh = Apvcrh::whereDate('DOCDATE', $request->get('tanggal_mulai'))->orderBy('VENCD', 'desc')->paginate(15)->appends(request()->query());
        } elseif (!empty($request->get('tanggal_akhir'))) {
            $apvcrh = Apvcrh::whereDate('DOCDATE', $request->get('tanggal_akhir'))->orderBy('VENCD', 'desc')->paginate(15)->appends(request()->query());
        } else {
            $apvcrh = Apvcrh::paginate(15)->appends(request()->query());
        }
        // $apvcrh = Apvcrh::first();
        return view('laporan.index', compact('apvcrh'));
    }

    public function download_excel(Request $request)
    {
        set_time_limit((int) 90000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000);
        date_default_timezone_set('Asia/Jakarta');
        $now = Carbon::now()->format('Y-m-d');

        if (!empty($request->get('tanggal_mulai')) && !empty($request->get('tanggal_akhir'))) {
            $apvcrh = Apvcrh::whereDate('DOCDATE', '>=', $request->get('tanggal_mulai'))
                ->whereDate('DOCDATE', '<=', $request->get('tanggal_akhir'))->orderBy('VENCD', 'desc')->get();
        } elseif (!empty($request->get('tanggal_mulai'))) {
            $apvcrh = Apvcrh::whereDate('DOCDATE', $request->get('tanggal_mulai'))->orderBy('VENCD', 'desc')->get();
        } elseif (!empty($request->get('tanggal_akhir'))) {
            $apvcrh = Apvcrh::whereDate('DOCDATE', $request->get('tanggal_akhir'))->orderBy('VENCD', 'desc')->get();
        } else {
            $apvcrh = Apvcrh::orderBy('VENCD', 'desc')->get();
        }

        $data[] = [
            'VENCD' => 'VENCD',
            'VCRNO' => 'VCRNO',
            'APGRPID' => 'APGRPID',
            'CSTERMID' => 'CSTERMID',
            'CSMONTH' => 'CSMONTH',
            'CSYEAR' => 'CSYEAR',
            'USLOGNM' => 'USLOGNM',
            'MODULID' => 'MODULID',
            'REFDOCTP' => 'REFDOCTP',
            'CCODEGRP' => 'CCODEGRP',
            'APBTCHNO' => 'APBTCHNO',
            'APDPNO' => 'APDPNO',
            'DOCDATE' => 'DOCDATE',
            'DOCDESC' => 'DOCDESC',
            'DOCTEXT' => 'DOCTEXT',
            'DOCINPUT' => 'DOCINPUT',
            'DOCSTAT' => 'DOCSTAT',
            'VCRTYPE' => 'VCRTYPE',
            'TAXCALC' => 'TAXCALC',
            'KURS' => 'KURS',
            'DISTSAMT' => 'DISTSAMT',
            'DISTHAMT' => 'DISTHAMT',
            'TAXSAMT' => 'TAXSAMT',
            'TAXHAMT' => 'TAXHAMT',
            'DPRATE' => 'DPRATE',
            'DPSAMT' => 'DPSAMT',
            'DPHAMT' => 'DPHAMT',
            'VCRSAMT' => 'VCRSAMT',
            'VCRHAMT' => 'VCRHAMT',
            'COMPLETE' => 'COMPLETE',
            'DUEDATE' => 'DUEDATE',
            'DISCDATE' => 'DISCDATE',
            'DISCRATE' => 'DISCRATE',
            'BALSAMT' => 'BALSAMT',
            'BALHAMT' => 'BALHAMT',
            'BALRAMT' => 'BALRAMT',
            'CCODE' => 'CCODE',
            'TAXGRPID' => 'TAXGRPID',
            'CALC_TAX' => 'CALC_TAX',
            'WITH_DP' => 'WITH_DP',
            'KURSPAJAK' => 'KURSPAJAK',
            'CONTRACTNO' => 'CONTRACTNO',
            'CONTRACTNET' => 'CONTRACTNET',
            'PROGRESS' => 'PROGRESS',
            'PROGRESSNET' => 'PROGRESSNET',
            'VALIDDATE' => 'VALIDDATE',
            'VALIDBY' => 'VALIDBY',
            'FAKTDATE' => 'FAKTDATE',
            'FAKTNO' => 'FAKTNO',
            'VENNM' => 'VENNM',
            'VENADD' => 'VENADD',
        ];

        foreach ($apvcrh as $item) {
            $data[] = [
                'VENCD' => $item->VENCD,
                'VCRNO' => $item->VCRNO,
                'APGRPID' => $item->APGRPID,
                'CSTERMID' => $item->CSTERMID,
                'CSMONTH' => $item->CSMONTH,
                'CSYEAR' => $item->CSYEAR,
                'USLOGNM' => $item->USLOGNM,
                'MODULID' => $item->MODULID,
                'REFDOCTP' => $item->REFDOCTP,
                'CCODEGRP' => $item->CCODEGRP,
                'APBTCHNO' => $item->APBTCHNO,
                'APDPNO' => $item->APDPNO,
                'DOCDATE' => $item->DOCDATE,
                'DOCDESC' => $item->DOCDESC,
                'DOCTEXT' => $item->DOCTEXT,
                'DOCINPUT' => $item->DOCINPUT,
                'DOCSTAT' => $item->DOCSTAT,
                'VCRTYPE' => $item->VCRTYPE,
                'TAXCALC' => $item->TAXCALC,
                'KURS' => $item->KURS,
                'DISTSAMT' => $item->DISTSAMT,
                'DISTHAMT' => $item->DISTHAMT,
                'TAXSAMT' => $item->TAXSAMT,
                'TAXHAMT' => $item->TAXHAMT,
                'DPRATE' => $item->DPRATE,
                'DPSAMT' => $item->DPSAMT,
                'DPHAMT' => $item->DPHAMT,
                'VCRSAMT' => $item->VCRSAMT,
                'VCRHAMT' => $item->VCRHAMT,
                'COMPLETE' => $item->COMPLETE,
                'DUEDATE' => $item->DUEDATE,
                'DISCDATE' => $item->DISCDATE,
                'DISCRATE' => $item->DISCRATE,
                'BALSAMT' => $item->BALSAMT,
                'BALHAMT' => $item->BALHAMT,
                'BALRAMT' => $item->BALRAMT,
                'CCODE' => $item->CCODE,
                'TAXGRPID' => $item->TAXGRPID,
                'CALC_TAX' => $item->CALC_TAX,
                'WITH_DP' => $item->WITH_DP,
                'KURSPAJAK' => $item->KURSPAJAK,
                'CONTRACTNO' => $item->CONTRACTNO,
                'CONTRACTNET' => $item->CONTRACTNET,
                'PROGRESS' => $item->PROGRESS,
                'PROGRESSNET' => $item->PROGRESSNET,
                'VALIDDATE' => $item->VALIDDATE,
                'VALIDBY' => $item->VALIDBY,
                'FAKTDATE' => $item->FAKTDATE,
                'FAKTNO' => $item->FAKTNO,
                'VENNM' => $item->VENNM,
                'VENADD' => $item->VENADD,
            ];
        }

        if (!empty($request->get('tanggal_mulai')) && !empty($request->get('tanggal_akhir'))) {
            $nama_file_excel = 'Laporan Keuangan ' . $request->get('jenis_layanan') . ' tanggal mulai ' . $request->get('tanggal_mulai') . ' tanggal akhir ' . $request->get('tanggal_akhir') . '.xlsx';
        } elseif (!empty($request->get('tanggal_mulai'))) {
            $nama_file_excel = 'Laporan Keuangan ' . $request->get('jenis_layanan') . ' tanggal mulai ' . $request->get('tanggal_mulai') . '.xlsx';
        } elseif (!empty($request->get('tanggal_akhir'))) {
            $nama_file_excel = 'Laporan Keuangan ' . $request->get('jenis_layanan') . ' tanggal akhir ' . $request->get('tanggal_akhir') . '.xlsx';
        } else {
            $nama_file_excel = 'Laporan Keuangan tanggal' . $now . '.xlsx';
        }
        return Excel::download(new DataExport($data), $nama_file_excel);
    }
}
