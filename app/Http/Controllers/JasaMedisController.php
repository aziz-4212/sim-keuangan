<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class JasaMedisController extends Controller
{
    public function jasaMedis(Request $request)
    {
        $result = null;

        $search = $request->input('search');

        if ($search) {
            $query = DB::connection('sqlsrv')
                ->table('TRXPMR')
                ->select('TRXPMR.NOKONFIRM',
                    'TRXPMR.TGLCUTOFFJPPIU',
                    'TRXPMR.TGCUTOFFJMPT',
                    'TRXPMR.NOREG',
                    'DOKTER.NAMADOKTER',
                    'MAPMR.NAMAPMR',
                    'PASIEN.NOPASIEN',
                    'PASIEN.NAMAPASIEN',
                    'TRXPMR.BIAYAST',
                    'TRXPMR.BIAYADR')
                ->join('MAPMR', 'MAPMR.KODEPMR', '=', 'TRXPMR.KODEPMR')
                ->join('DOKTER', 'DOKTER.KODEDOKTER', '=', 'TRXPMR.DRPERIKSA')
                ->join('REGPAS', 'REGPAS.NOREG', '=', 'TRXPMR.NOREG')
                ->join('PASIEN', 'PASIEN.NOPASIEN', '=', 'REGPAS.NOPASIEN')
                ->where('TRXPMR.NOREG', '=', $search);

            $result = $query->get();

            // Menghitung total BIAYAST dan BIAYADR
            $totalBiayaST = $query->sum('TRXPMR.BIAYAST');
            $totalBiayaDR = $query->sum('TRXPMR.BIAYADR');
        } else {
            $totalBiayaST = 0;
            $totalBiayaDR = 0;
        }

        return view('jasamedis.v_jasamedispertransaksi', compact('result', 'totalBiayaST', 'totalBiayaDR'));
    }
}
