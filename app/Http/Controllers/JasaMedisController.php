<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class JasaMedisController extends Controller
{
    public function jasaMedis(Request $request)
    {
        $perPage = 15;
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

            $paginationParams = $request->except('page');
            $paginationParams['search'] = $search;

            $result = $query->paginate($perPage)->appends($paginationParams);
        }

        return view('jasamedis.v_jasamedispertransaksi', compact('result'));
    }
}
