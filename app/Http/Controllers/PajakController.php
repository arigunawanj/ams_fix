<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Pajak;
use App\Models\DetailProfil;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class PajakController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pajak = Pajak::all();
        $customer = DB::table('customers')->select('nama_customer', 'id')->get();
        // Mengambil detail profil dengan user_id dengan ID yang sudah login
        $profil = DetailProfil::where('user_id', Auth::user()->id)->get();
        return view('menu.pajak', compact('pajak', 'customer', 'profil'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kode_laporan' => 'required|unique:customers',
            'customer_id' => 'required',
            'tanggal_rep' => 'required',
            'no_fakpajak' => 'required',
            'tanggal_upload' => 'required',
            'ket_rep' => 'required',
        ]);
        
         // Jika Validator yang dideklarasikan ada salah satu yang gagal maka akan error
        if($validator->fails()){
            Alert::toast('Gagal Menyimpan Data Pajak', 'error');
        } else {
            Alert::toast('Berhasil Menyimpan Data Pajak', 'success');
            Pajak::create($request->all());
        }

        return redirect('pajak');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pajak  $pajak
     * @return \Illuminate\Http\Response
     */
    public function show(Pajak $pajak)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pajak  $pajak
     * @return \Illuminate\Http\Response
     */
    public function edit(Pajak $pajak)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pajak  $pajak
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pajak $pajak)
    {
        $pajak->update($request->all());
        Alert::toast('Berhasil Mengubah Data Pajak', 'success');
        return redirect('pajak');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pajak  $pajak
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pajak $pajak)
    {
        $pajak->delete();
        Alert::toast('Berhasil Menghapus Data Pajak', 'success');
        return redirect('pajak');
    }

    public function printPajak()
    {
        $pajak = Pajak::all();
        $pdf = Pdf::loadView('print.pajakprint', ['pajak' => $pajak]);
        
        return $pdf->setPaper('a4', 'potrait')->stream('Data Laporan Faktur Pajak - '. Carbon::now(). '.pdf');
    }
}
