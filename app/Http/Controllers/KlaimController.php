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
    public function cek_klaim(Request $request)
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

    public function proses_selisih()
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

    public function cek_klaim_minus_selisih(Request $request)
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

    // public function jasa_visit_minus(Request $request)
    // {
    //     // Ambil data klaim dari database
    //     $data_klaim = DB::connection('sqlsrv')
    //         ->table('klaim_minus') // Ganti dengan nama tabel sebenarnya
    //         ->select(
    //             'klaim_minus.SEP', 
    //             'klaim_minus.INACBG', 
    //             'klaim_minus.TARIFRS', 
    //             'klaim_minus.TARIFKLAIM', 
    //             'klaim_minus.JENIS', 
    //             'klaim_minus.NOREG', 
    //             'klaim_minus.SELISIH'
    //         )
    //         ->where('klaim_minus.SELISIH', '<', 0)
    //         ->leftJoin('ERM2.dbo.ibs_laporan_operasi', 'klaim_minus.NOREG', '=', 'ERM2.dbo.ibs_laporan_operasi.noreg')
    //         ->whereNull('ERM2.dbo.ibs_laporan_operasi.noreg') // Filter data tanpa laporan operasi
    //         ->whereNotNull('klaim_minus.NOREG')
    //         ->groupBy(
    //             'klaim_minus.SEP', 
    //             'klaim_minus.INACBG', 
    //             'klaim_minus.TARIFRS', 
    //             'klaim_minus.TARIFKLAIM', 
    //             'klaim_minus.JENIS', 
    //             'klaim_minus.NOREG', 
    //             'klaim_minus.SELISIH'
    //         )
    //         ->orderBy('klaim_minus.SELISIH', 'ASC')
    //         ->get();

    //     // Filter data klaim dengan perhitungan `SELISIH JASA VISIT`
    //     $filtered_data = $data_klaim->filter(function ($item) {
    //         $enampersen = 0.06 * $item->TARIFKLAIM;

    //         $jasa_visit = DB::connection('sqlsrv')
    //             ->table('TRXPMR')
    //             ->where('NOREG', $item->NOREG)
    //             ->where('KODEPMR', 'like', 'V%')
    //             ->sum('BIAYADRRIIL'); // Gunakan sum langsung

    //         $selisih_jasavisit = $enampersen - $jasa_visit;

    //         return $selisih_jasavisit < 0;
    //     });

    //     dd($filtered_data);

    //     // Kembalikan view dengan data yang difilter
    //     return view('klaim.cek-klaim-jasa-visit', [
    //         'data_klaim' => $filtered_data->paginate(10) // Gunakan paginate jika data besar
    //     ]);
    // }

    // public function jasa_visit_minus(Request $request)
    // {
    //     // Ambil data klaim dengan klaim minus
    //     $data_klaim = DB::connection('sqlsrv')
    //         ->table('klaim_minus')
    //         ->select(
    //             'klaim_minus.SEP', 
    //             'klaim_minus.INACBG', 
    //             'klaim_minus.TARIFRS', 
    //             'klaim_minus.TARIFKLAIM', 
    //             'klaim_minus.JENIS', 
    //             'klaim_minus.NOREG', 
    //             'klaim_minus.SELISIH'
    //         )
    //         ->where('klaim_minus.SELISIH', '<', 0)
    //         ->leftJoin('ERM2.dbo.ibs_laporan_operasi', 'klaim_minus.NOREG', '=', 'ERM2.dbo.ibs_laporan_operasi.noreg')
    //         ->whereNull('ERM2.dbo.ibs_laporan_operasi.noreg') // Filter data tanpa laporan operasi
    //         ->whereNotNull('klaim_minus.NOREG')
    //         ->groupBy(
    //             'klaim_minus.SEP', 
    //             'klaim_minus.INACBG', 
    //             'klaim_minus.TARIFRS', 
    //             'klaim_minus.TARIFKLAIM', 
    //             'klaim_minus.JENIS', 
    //             'klaim_minus.NOREG', 
    //             'klaim_minus.SELISIH'
    //         )
    //         ->orderBy('klaim_minus.SELISIH', 'ASC')
    //         ->get();

    //     // Ambil semua data Jasa Visit dari TRXPMR sekaligus
    //     $jasa_visits = DB::connection('sqlsrv')
    //         ->table('TRXPMR')
    //         ->select('NOREG', DB::raw('SUM(BIAYADRRIIL) as total_biaya'))
    //         ->whereIn('NOREG', $data_klaim->pluck('NOREG')->toArray()) // Ambil hanya NOREG yang relevan
    //         ->where('KODEPMR', 'like', 'V%')
    //         ->groupBy('NOREG')
    //         ->get()
    //         ->keyBy('NOREG'); // Index berdasarkan NOREG untuk akses cepat

    //     // Filter data klaim berdasarkan perhitungan selisih jasa visit
    //     $filtered_data = $data_klaim->filter(function ($item) use ($jasa_visits) {
    //         $enampersen = 0.06 * $item->TARIFKLAIM;

    //         // Cari jasa visit berdasarkan NOREG
    //         $jasa_visit = $jasa_visits->get($item->NOREG)?->total_biaya ?? 0;

    //         $selisih_jasavisit = $enampersen - $jasa_visit;

    //         return $selisih_jasavisit < 0;
    //     });

    //     // Kembalikan view dengan data yang difilter
    //     return view('klaim.cek-klaim-jasa-visit', [
    //         'data_klaim' => $filtered_data->paginate(10) // Gunakan paginate jika data besar
    //     ]);
    // }

    public function jasa_visit_minus(Request $request)
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

    // public function update_jasa_visit(Request $request)
    // {
    //     // Ambil data dari request
    //     $data = $request->all();

    //     // Log data yang diterima
    //     // Log::info('Data yang diterima:', $data);
    //     // Dump data untuk melihat isinya
    //     dd($data);
        
    //     // Lakukan validasi jika diperlukan
    //     $validated = $request->validate([
    //         'NOPMR.*' => 'required|exists:trxpmr_update,NOPMR', // Pastikan NOPMR ada di tabel TRXPMR_UPDATE
    //         'BIAYADRRIIL.*' => 'required|numeric',
    //         'UPDATE_BIAYADR.*' => 'required|numeric',
    //         'BIAYARSRIIL.*' => 'required|numeric',
    //         'UPDATE_BIAYARS.*' => 'required|numeric',
    //     ]);

    //     try {
    //         // Lakukan iterasi untuk setiap data yang diterima
    //         foreach ($data['NOPMR'] as $index => $nopmr) {
    //             // Temukan record berdasarkan NOPMR dan lakukan update
    //             $trxpmrUpdate = TrxpmrUpdate::where('NOPMR', $nopmr)->first();

    //             if ($trxpmrUpdate) {
    //                 // Update data sesuai dengan yang dikirimkan
    //                 $trxpmrUpdate->BIAYADRRIIL = $data['BIAYADRRIIL'][$index];
    //                 $trxpmrUpdate->UPDATE_BIAYADR = $data['UPDATE_BIAYADR'][$index];
    //                 $trxpmrUpdate->BIAYARSRIIL = $data['BIAYARSRIIL'][$index];
    //                 $trxpmrUpdate->UPDATE_BIAYARS = $data['UPDATE_BIAYARS'][$index];

    //                 // Simpan perubahan
    //                 $trxpmrUpdate->save();
    //             }
    //         }

    //         // Jika update berhasil, beri respons sukses
    //         return response()->json([
    //             'message' => 'Data berhasil diperbarui',
    //         ]);
    //     } catch (\Exception $e) {
    //         // Jika terjadi error, beri respons error
    //         return response()->json([
    //             'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

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

}
