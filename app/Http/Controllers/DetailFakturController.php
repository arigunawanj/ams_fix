<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Models\Barang;
use App\Models\Customer;
use App\Models\DetailFaktur;
use App\Models\DetailProfil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class DetailFakturController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $detail = DetailFaktur::all();
        // Mengambil detail profil dengan user_id dengan ID yang sudah login
        $profil = DetailProfil::where('user_id', Auth::user()->id)->get();
        $barang = Barang::all();
        $cust = Customer::all();
        $stok = Stok::all();
        return view('faktur.detailfaktur', compact('detail', 'barang', 'cust', 'stok', 'profil'));
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
        $data = $request->all();
        $validator = Validator::make($request->all(), [
            'kode_faktur' => 'required',
            'tanggal_keluar' => 'required',
            'barang_id' => 'required',
            'stok_keluar' => 'required',
            'diskon' => 'required',
            'subtotal' => 'required',
            'customer_id' => 'required',
        ]);

         // Jika Validator yang dideklarasikan ada salah satu yang gagal maka akan error
        if($validator->fails()){
            Alert::toast('Gagal Menyimpan Detail Faktur', 'error');
        } else {
            $barang = Barang::find($request->barang_id);
            $barang->update([
                'stok' => $barang->stok - $request->stok_keluar,
            ]);
            DetailFaktur::create($data);
            Alert::toast('Berhasil Menyimpan Detail Faktur', 'success');
        }
        
        return redirect('detailfaktur');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\DetailFaktur  $detailFaktur
     * @return \Illuminate\Http\Response
     */
    public function show(DetailFaktur $detailfaktur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\DetailFaktur  $detailFaktur
     * @return \Illuminate\Http\Response
     */
    public function edit(DetailFaktur $detailFaktur)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\DetailFaktur  $detailFaktur
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, DetailFaktur $detailFaktur)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\DetailFaktur  $detailFaktur
     * @return \Illuminate\Http\Response
     */
    public function destroy(DetailFaktur $detailfaktur)
    {
        $barang = Barang::find($detailfaktur->barang_id);
        $barang->update([
            'stok' => $barang->stok + $detailfaktur->stok_keluar,
        ]);
        $detailfaktur->delete();
        Alert::toast('Berhasil Menghapus Detail Faktur', 'success');
        return redirect('detailfaktur');
    }

    public function getHarga()
    {
        $barang = Barang::all();
        return response()->json($barang);
    }

    public function getBarang($id)
    {
        $data = Barang::where('id', $id)->get();
        return response()->json($data);
    }
}
