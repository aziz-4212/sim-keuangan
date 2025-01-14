<?php

namespace App\Http\Controllers;

use App\Exports\DataExport;
use App\Models\Klaim;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class KlaimController extends Controller
{
    // =====================================Klaim Jasa Medis Non Tindakan===========================================

    public function cek_klaim_non_tindakan(Request $request)
    {
        $search = $request->input('search');
        $query = Klaim::query();

        if ($search) {
            $query->where('SEP', 'like', '%' . $search . '%');
        }

        $data_klaim = $query->paginate(10);

        $mapping_sep = DB::connection('sqlsrv4')
            ->table('MAPPING_SEP')
            ->pluck('NOREG', 'SEP')
            ->toArray();

        foreach ($data_klaim as $item) {
            if (isset($mapping_sep[$item->SEP])) {
                $item->NOREG = $mapping_sep[$item->SEP];
                $item->is_mapping_sep = true; // Menandai sumber data dari mapping_sep
            } else {
                $item->is_mapping_sep = false; // Sumber data langsung dari klaim
            }
        }

        return view('klaim.cek-klaim', compact('data_klaim'));
    }

    public function proses_selisih_non_tindakan()
    {
        // Ambil data dari tabel klaim_minus di mana NOREG dan SELISIH masih kosong
        $klaimMinusData = DB::table('klaim_minus')
            ->whereNull('NOREG')
            ->orWhereNull('SELISIH')
            ->get();

        // Ambil mapping SEP -> NOREG
        $mappingSEP = DB::connection('sqlsrv4')
            ->table('MAPPING_SEP')
            ->pluck('NOREG', 'SEP')
            ->toArray();

        // Daftar update untuk klaim_minus
        foreach ($klaimMinusData as $klaim) {
            // Cek apakah SEP ditemukan dalam MAPPING_SEP
            if (isset($mappingSEP[$klaim->SEP])) {
                $noreg = $mappingSEP[$klaim->SEP];
                $selisih = $klaim->TARIFKLAIM - $klaim->TARIFRS;

                // Update klaim_minus berdasarkan SEP
                DB::table('klaim_minus')
                    ->where('SEP', $klaim->SEP)
                    ->update([
                        'NOREG' => $noreg,
                        'SELISIH' => $selisih
                    ]);
            }
        }
        return redirect()->route('cek-klaim-minus-selisih')->with('success', 'Proses Pencarian Selisih Minus selesai.');
    }

    public function cek_klaim_minus_selisih_non_tindakan(Request $request)
    {
        $search = $request->input('search');
        $query = Klaim::query()
            ->from('klaim_minus')
            ->select(
                'klaim_minus.SEP', 
                'klaim_minus.INACBG', 
                'klaim_minus.TARIFRS', 
                'klaim_minus.TARIFKLAIM', 
                'klaim_minus.JENIS', 
                'klaim_minus.NOREG', 
                'klaim_minus.SELISIH'
            )
            ->where('klaim_minus.SELISIH', '<', 0)
            ->leftJoin(
                'ERM2.dbo.ibs_laporan_operasi', 
                'klaim_minus.NOREG', 
                '=', 
                'ERM2.dbo.ibs_laporan_operasi.noreg'
            )
            ->whereNull('ERM2.dbo.ibs_laporan_operasi.noreg') // Filter data tanpa laporan operasi
            ->whereNotNull('klaim_minus.NOREG')
            ->groupBy(
                'klaim_minus.SEP', 
                'klaim_minus.INACBG', 
                'klaim_minus.TARIFRS', 
                'klaim_minus.TARIFKLAIM', 
                'klaim_minus.JENIS', 
                'klaim_minus.NOREG', 
                'klaim_minus.SELISIH'
            )
            ->orderBy('klaim_minus.SELISIH', 'ASC');

        if ($search) {
            $query->where('klaim_minus.SEP', 'like', '%' . $search . '%');
        }

        $data_klaim = $query->paginate(10);

        return view('klaim.cek-klaim-proses', compact('data_klaim'));
    }

    public function jasa_visit_minus_non_tindakan(Request $request)
    {
        // Ambil data klaim dengan klaim minus
        $data_klaim = DB::connection('sqlsrv')
            ->table('klaim_minus')
            ->select(
                'klaim_minus.SEP', 
                'klaim_minus.INACBG', 
                'klaim_minus.TARIFRS', 
                'klaim_minus.TARIFKLAIM', 
                'klaim_minus.JENIS', 
                'klaim_minus.NOREG', 
                'klaim_minus.SELISIH',
                'klaim_minus.STATUS'
            )
            ->where('klaim_minus.SELISIH', '<', 0)
            ->leftJoin('ERM2.dbo.ibs_laporan_operasi', 'klaim_minus.NOREG', '=', 'ERM2.dbo.ibs_laporan_operasi.noreg')
            ->whereNull('ERM2.dbo.ibs_laporan_operasi.noreg') // Filter data tanpa laporan operasi
            ->whereNotNull('klaim_minus.NOREG')
            ->groupBy(
                'klaim_minus.SEP', 
                'klaim_minus.INACBG', 
                'klaim_minus.TARIFRS', 
                'klaim_minus.TARIFKLAIM', 
                'klaim_minus.JENIS', 
                'klaim_minus.NOREG', 
                'klaim_minus.SELISIH',
                'klaim_minus.STATUS'
            )
            ->orderBy('klaim_minus.SELISIH', 'ASC')
            ->get();

        // Ambil semua data Jasa Visit dari TRXPMR sekaligus
        $jasa_visits = DB::connection('sqlsrv')
            ->table('TRXPMR')
            ->select('NOREG', DB::raw('SUM(BIAYADRRIIL) as total_biaya'))
            ->whereIn('NOREG', $data_klaim->pluck('NOREG')->toArray()) // Ambil hanya NOREG yang relevan
            ->where('KODEPMR', 'like', 'V%')
            ->groupBy('NOREG')
            ->get()
            ->keyBy('NOREG'); // Index berdasarkan NOREG untuk akses cepat

        // Filter data klaim berdasarkan perhitungan selisih jasa visit
        $filtered_data = $data_klaim->filter(function ($item) use ($jasa_visits) {
            $enampersen = 0.06 * $item->TARIFKLAIM;

            // Cari jasa visit berdasarkan NOREG
            $jasa_visit = $jasa_visits->get($item->NOREG)?->total_biaya ?? 0;

            $selisih_jasavisit = $enampersen - $jasa_visit;

            return $selisih_jasavisit < 0;
        });

        // Manual Pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Jumlah item per halaman
        $currentPageItems = $filtered_data->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedData = new LengthAwarePaginator(
            $currentPageItems, 
            $filtered_data->count(), 
            $perPage, 
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Kembalikan view dengan data yang dipaginasi
        return view('klaim.cek-klaim-jasa-visit', ['data_klaim' => $paginatedData]);
    }

    public function get_detail_jasa_visit(Request $request)
    {
        $noreg = $request->input('noreg');
        $enampersen = $request->input('enampersen');

        $status = DB::connection('sqlsrv')
        ->table('klaim_minus')
        ->select('STATUS')
        ->where('NOREG', $noreg)
        ->first();

        $showUpdateButton = ($status && $status->STATUS != 1);

        // Query data jasa visit berdasarkan NOREG
        $jasa_visit_dokter = DB::connection('sqlsrv')
            ->table('TRXPMR')
            ->select('TRXPMR.NOPMR','TRXPMR.NOREG', 'TRXPMR.KODEPMR', 'TRXPMR.BIAYADRRIIL', 'TRXPMR.BIAYARSRIIL', 'MAPMR.NAMAPMR')
            ->join('MAPMR', 'TRXPMR.KODEPMR', '=', 'MAPMR.KODEPMR')
            ->where('TRXPMR.KODEPMR', 'like', 'V%')
            ->where('TRXPMR.NOREG', $noreg)
            ->get();

        // Menghitung total BIAYADRRIIL
        $total_biayadrriil = $jasa_visit_dokter->sum('BIAYADRRIIL');

        // Menambahkan perhitungan ke setiap data jasa visit
        $jasa_visit_dokter = $jasa_visit_dokter->map(function ($item) use ($total_biayadrriil, $enampersen) {
            $item->perbandingan = ($item->BIAYADRRIIL / $total_biayadrriil) * $enampersen;
            return $item;
        });

        // Kembalikan data sebagai JSON
        // return response()->json($jasa_visit_dokter);
        return response()->json([
            'jasa_visit_dokter' => $jasa_visit_dokter,
            'showUpdateButton' => $showUpdateButton,
        ]);
    }

    public function update_jasa_visit(Request $request)
    {
        // Ambil data dari request
        $data = $request->all();
        $userlog = auth()->user()->user_log->USLOGNM;
        $currentDate = Carbon::now()->setTimezone('Asia/Jakarta');

        // Validasi data
        // $validated = $request->validate([
        //     'NOPMR.*' => 'required|exists:TRXPMR_UPDATE,NOPMR', 
        //     'BIAYADRRIIL.*' => 'required|numeric',
        //     'UPDATE_BIAYADR.*' => 'required|numeric',
        //     'BIAYARSRIIL.*' => 'required|numeric',
        //     'UPDATE_BIAYARS.*' => 'required|numeric',
        // ]);

        try {
            // Iterasi data dan update menggunakan query builder
            foreach ($data['NOPMR'] as $index => $nopmr) {
                DB::connection('sqlsrv')
                ->table('LOG_UPDATE_TRXPMR')
                ->insert([
                    'NOREG' => $data['NOREG'][$index],
                    'NOPMR' => $data['NOPMR'][$index],
                    'KODEPMR' => $data['KODEPMR'][$index],
                    'BIAYADRRIIL' => $data['BIAYADRRIIL'][$index],
                    'BIAYADRRIIL_UPDATE' => $data['UPDATE_BIAYADR'][$index],
                    'BIAYARSRIIL' => $data['BIAYARSRIIL'][$index],
                    'BIAYARSRIIL_UPDATE' => $data['UPDATE_BIAYARS'][$index],
                    'USERLOG' => $userlog,
                    'CREATED_AT' => $currentDate,
                ]);

                DB::connection('sqlsrv')
                    ->table('TRXPMR')
                    ->where('NOPMR', $nopmr)
                    ->update([
                        'BIAYADRRIIL' => $data['UPDATE_BIAYADR'][$index],
                        'BIAYARSRIIL' => $data['UPDATE_BIAYARS'][$index], // Opsional: jika tabel memiliki kolom `updated_at`
                    ]);
                
                DB::connection('sqlsrv')
                    ->table('klaim_minus')
                    ->where('NOREG', $data['NOREG'][$index])
                    ->update([
                        'STATUS' => 1,
                    ]);
            }

            // Jika update berhasil, beri respons sukses
            // return response()->json(['message' => 'Data berhasil diperbarui', ]);
            return redirect()->back()->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            // Jika terjadi error, beri respons error
            // return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage(),], 500);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    // =====================================End Klaim Jasa Medis Non Tindakan===========================================


    // =====================================Klaim Jasa Medis Tindakan IBS===========================================
    public function cek_klaim_minus_selisih_tindakan (Request $request)
    {
        $search = $request->input('search');
        $query = Klaim::query()
            ->from('klaim_minus')
            ->select(
                'klaim_minus.SEP', 
                'klaim_minus.INACBG', 
                'klaim_minus.TARIFRS', 
                'klaim_minus.TARIFKLAIM', 
                'klaim_minus.JENIS', 
                'klaim_minus.NOREG', 
                'klaim_minus.SELISIH'
            )
            ->where('klaim_minus.SELISIH', '<', 0)
            ->leftJoin(
                'ERM2.dbo.ibs_laporan_operasi', 
                'klaim_minus.NOREG', 
                '=', 
                'ERM2.dbo.ibs_laporan_operasi.noreg'
            )
            ->whereNotNull('ERM2.dbo.ibs_laporan_operasi.noreg') // Filter data tanpa laporan operasi
            ->whereNotNull('klaim_minus.NOREG')
            ->groupBy(
                'klaim_minus.SEP', 
                'klaim_minus.INACBG', 
                'klaim_minus.TARIFRS', 
                'klaim_minus.TARIFKLAIM', 
                'klaim_minus.JENIS', 
                'klaim_minus.NOREG', 
                'klaim_minus.SELISIH'
            )
            ->orderBy('klaim_minus.SELISIH', 'ASC');

        if ($search) {
            $query->where('klaim_minus.SEP', 'like', '%' . $search . '%');
        }

        $data_klaim = $query->paginate(10);

        return view('klaim.tindakan.cek-klaim-proses-tindakan', compact('data_klaim'));
    }

    public function jasa_medis_minus_tindakan(Request $request)
    {
        // Ambil data klaim dengan klaim minus
        $data_klaim = DB::connection('sqlsrv')
            ->table('klaim_minus')
            ->select(
                'klaim_minus.SEP', 
                'klaim_minus.INACBG', 
                'klaim_minus.TARIFRS', 
                'klaim_minus.TARIFKLAIM', 
                'klaim_minus.JENIS', 
                'klaim_minus.NOREG', 
                'klaim_minus.SELISIH',
                'klaim_minus.STATUS_IBS'
            )
            ->where('klaim_minus.SELISIH', '<', 0)
            ->leftJoin('ERM2.dbo.ibs_laporan_operasi', 'klaim_minus.NOREG', '=', 'ERM2.dbo.ibs_laporan_operasi.noreg')
            ->whereNotNull('ERM2.dbo.ibs_laporan_operasi.noreg') // Filter data tanpa laporan operasi
            ->whereNotNull('klaim_minus.NOREG')
            ->groupBy(
                'klaim_minus.SEP', 
                'klaim_minus.INACBG', 
                'klaim_minus.TARIFRS', 
                'klaim_minus.TARIFKLAIM', 
                'klaim_minus.JENIS', 
                'klaim_minus.NOREG', 
                'klaim_minus.SELISIH',
                'klaim_minus.STATUS_IBS'
            )
            ->orderBy('klaim_minus.SELISIH', 'ASC')
            ->get();

        // Ambil semua data Jasa Medis dari TRXPMR sekaligus
        $jasa_medis_all = DB::connection('sqlsrv')
            ->table('TRXPMR')
            ->select('NOREG', DB::raw('SUM(BIAYADRRIIL) as total_biaya'))
            ->whereIn('NOREG', $data_klaim->pluck('NOREG')->toArray()) // Ambil hanya NOREG yang relevan
            ->whereIn('KODEPMR', ['OKAD01','OKAD02'])
            ->groupBy('NOREG')
            ->get()
            ->keyBy('NOREG'); // Index berdasarkan NOREG untuk akses cepat

        // Filter data klaim berdasarkan perhitungan selisih jasa medis
        $filtered_data = $data_klaim->filter(function ($item) use ($jasa_medis_all) {
            $duapuluhlimapersen = 0.25 * $item->TARIFKLAIM;

            // Cari jasa medis berdasarkan NOREG
            $jasa_medis = $jasa_medis_all->get($item->NOREG)?->total_biaya ?? 0;

            $selisih_jasamedis = $duapuluhlimapersen - $jasa_medis;

            return $selisih_jasamedis < 0;
        });

        // Manual Pagination
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 10; // Jumlah item per halaman
        $currentPageItems = $filtered_data->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginatedData = new LengthAwarePaginator(
            $currentPageItems, 
            $filtered_data->count(), 
            $perPage, 
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );

        // Kembalikan view dengan data yang dipaginasi
        return view('klaim.tindakan.cek-klaim-jasa-medis-tindakan', ['data_klaim' => $paginatedData]);
    }

    // public function get_detail_jasa_medis_tindakan (Request $request)
    // {
    //     $noreg = $request->input('noreg');
    //     $duapuluhlimapersen = $request->input('duapuluhlimapersen');

    //     $status = DB::connection('sqlsrv')
    //     ->table('klaim_minus')
    //     ->select('STATUS_IBS')
    //     ->where('NOREG', $noreg)
    //     ->first();

    //     $showUpdateButton = ($status && $status->STATUS_IBS != 1);

    //     // Query data jasa visit berdasarkan NOREG
    //     $jasa_visit_dokter = DB::connection('sqlsrv')
    //         ->table('TRXPMR')
    //         ->select('TRXPMR.NOPMR','TRXPMR.NOREG', 'TRXPMR.KODEPMR', 'TRXPMR.BIAYADRRIIL', 'TRXPMR.BIAYARSRIIL', 'MAPMR.NAMAPMR')
    //         ->join('MAPMR', 'TRXPMR.KODEPMR', '=', 'MAPMR.KODEPMR')
    //         ->whereIn('TRXPMR.KODEPMR', ['OKAD01','OKAD02'])
    //         ->where('TRXPMR.NOREG', $noreg)
    //         ->orderBy('TRXPMR.NOPMR', 'ASC')
    //         ->get();

        
    //      // Menambahkan perhitungan ke setiap data jasa visit
    //     $jasa_visit_dokter = $jasa_visit_dokter->map(function ($item) use ($duapuluhlimapersen) {
    //         $jasa_okad01 = round(($duapuluhlimapersen * 66.7) / 100, 2); // 66.7% dari $duapuluhlimapersen
    //         $jasa_okad02 = $duapuluhlimapersen - $jasa_okad01; // Sisa dari $duapuluhlimapersen

    //         // Menentukan perhitungan berdasarkan KODEPMR
    //         if ($item->KODEPMR === 'OKAD01') {
    //             $item->jasa_dialokasikan = $jasa_okad01;
    //         } elseif ($item->KODEPMR === 'OKAD02') {
    //             $item->jasa_dialokasikan = $jasa_okad02;
    //         } else {
    //             $item->jasa_dialokasikan = 0; // Default jika KODEPMR tidak dikenal
    //         }

    //         return $item;
    //     });

    //     // Kembalikan data sebagai JSON
    //     return response()->json([
    //         'jasa_visit_dokter' => $jasa_visit_dokter,
    //         'showUpdateButton' => $showUpdateButton,
    //     ]);
    // }

    public function get_detail_jasa_medis_tindakan(Request $request)
    {
        $noreg = $request->input('noreg');
        $duapuluhlimapersen = $request->input('duapuluhlimapersen');

        $status = DB::connection('sqlsrv')
            ->table('klaim_minus')
            ->select('STATUS_IBS')
            ->where('NOREG', $noreg)
            ->first();

        $showUpdateButton = ($status && $status->STATUS_IBS != 1);

        // Query data jasa visit berdasarkan NOREG
        $jasa_visit_dokter = DB::connection('sqlsrv')
            ->table('TRXPMR')
            ->select('TRXPMR.NOPMR', 'TRXPMR.NOREG', 'TRXPMR.KODEPMR', 'TRXPMR.BIAYADRRIIL', 'TRXPMR.BIAYARSRIIL', 'MAPMR.NAMAPMR')
            ->join('MAPMR', 'TRXPMR.KODEPMR', '=', 'MAPMR.KODEPMR')
            ->whereIn('TRXPMR.KODEPMR', ['OKAD01', 'OKAD02'])
            ->where('TRXPMR.NOREG', $noreg)
            ->orderBy('TRXPMR.NOPMR', 'ASC')
            ->get();

        // Hitung total BIAYADRRIIL untuk setiap KODEPMR
        $total_biayadrriil_okad01 = $jasa_visit_dokter->where('KODEPMR', 'OKAD01')->sum('BIAYADRRIIL');
        $total_biayadrriil_okad02 = $jasa_visit_dokter->where('KODEPMR', 'OKAD02')->sum('BIAYADRRIIL');

        // Hitung total alokasi untuk setiap KODEPMR
        $alokasi_okad01 = round(($duapuluhlimapersen * 66.7) / 100, 2);
        $alokasi_okad02 = $duapuluhlimapersen - $alokasi_okad01;

        // Menambahkan perhitungan ke setiap data jasa visit
        $jasa_visit_dokter = $jasa_visit_dokter->map(function ($item) use (
            $total_biayadrriil_okad01,
            $total_biayadrriil_okad02,
            $alokasi_okad01,
            $alokasi_okad02,
        ) {
            if ($item->KODEPMR === 'OKAD01' && $total_biayadrriil_okad01 > 0) {
                // Proporsi untuk OKAD01
                $item->jasa_dialokasikan = round(($item->BIAYADRRIIL / $total_biayadrriil_okad01) * $alokasi_okad01, 2);
            } elseif ($item->KODEPMR === 'OKAD02' && $total_biayadrriil_okad02 > 0) {
                // Proporsi untuk OKAD02
                $item->jasa_dialokasikan = round(($item->BIAYADRRIIL / $total_biayadrriil_okad02) * $alokasi_okad02, 2);
            } else {
                $item->jasa_dialokasikan = 0; // Default jika KODEPMR tidak dikenal atau tidak ada data
            }

            return $item;
        });

        // Kembalikan data sebagai JSON
        return response()->json([
            'jasa_visit_dokter' => $jasa_visit_dokter,
            'showUpdateButton' => $showUpdateButton,
        ]);
    }

    public function update_jasa_medis_tindakan(Request $request)
    {
        // Ambil data dari request
        $data = $request->all();
        $userlog = auth()->user()->user_log->USLOGNM;
        $currentDate = Carbon::now()->setTimezone('Asia/Jakarta');

        try {
            // Iterasi data dan update menggunakan query builder
            foreach ($data['NOPMR'] as $index => $nopmr) {
                DB::connection('sqlsrv')
                ->table('LOG_UPDATE_TRXPMR')
                ->insert([
                    'NOREG' => $data['NOREG'][$index],
                    'NOPMR' => $data['NOPMR'][$index],
                    'KODEPMR' => $data['KODEPMR'][$index],
                    'BIAYADRRIIL' => $data['BIAYADRRIIL'][$index],
                    'BIAYADRRIIL_UPDATE' => $data['UPDATE_BIAYADR'][$index],
                    'BIAYARSRIIL' => $data['BIAYARSRIIL'][$index],
                    'BIAYARSRIIL_UPDATE' => $data['UPDATE_BIAYARS'][$index],
                    'USERLOG' => $userlog,
                    'CREATED_AT' => $currentDate,
                ]);

                DB::connection('sqlsrv')
                    ->table('TRXPMR')
                    ->where('NOPMR', $nopmr)
                    ->update([
                        'BIAYADRRIIL' => $data['UPDATE_BIAYADR'][$index],
                        'BIAYARSRIIL' => $data['UPDATE_BIAYARS'][$index], // Opsional: jika tabel memiliki kolom `updated_at`
                    ]);
                
                DB::connection('sqlsrv')
                    ->table('klaim_minus')
                    ->where('NOREG', $data['NOREG'][$index])
                    ->update([
                        'STATUS_IBS' => 1,
                    ]);
            }

            // Jika update berhasil, beri respons sukses
            return redirect()->back()->with('success', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            // Jika terjadi error, beri respons error
            // return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage(),], 500);
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // =====================================End Klaim Jasa Medis Tindakan IBS===========================================

}
