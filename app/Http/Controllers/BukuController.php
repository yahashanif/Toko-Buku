<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Diskon;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BukuController extends Controller
{
    public function index()
    {
        $data['buku'] = Buku::with(['kategori'])->get();
        return view('page.home', $data);
    }

    public function inputBuku()
    {
        $data['kategori'] = Kategori::all();
        return view('page.inputBuku', $data);
    }

    public function saveBuku(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'kategori' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'tahun' => 'required',
            'jumlah' => 'required',
            'harga' => 'required',
            'gambar' => 'required',
            'deskripsi' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('input-buku')
                ->withErrors($validator)
                ->withInput();
        }
        $file = $request->file('gambar');
        $fileName = $file->getClientOriginalName();
        $file->move('gambar/', $fileName);

        $simpan = Buku::create([
            'id_kategori' => $request->kategori,
            'judul' => $request->judul,
            'pengarang' => $request->pengarang,
            'penerbit' => $request->penerbit,
            'tahun_terbit' => $request->tahun,
            'jumlah_buku' => $request->jumlah,
            'harga' => $request->harga,
            'gambar' => $fileName,
            'deskripsi' => $request->deskripsi,
        ]);
        if ($simpan) {
            return redirect('home')->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect('input-buku')->with('error', 'Data Gagal Disimpan');
        }
    }

    public function editBuku($id)
    {
        $data['kategori'] = Kategori::all();
        $data['buku'] = Buku::where('id', $id)->first();
        return view('page.editBuku', $data);
    }

    public function updateBuku(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required',
            'kategori' => 'required',
            'pengarang' => 'required',
            'penerbit' => 'required',
            'tahun' => 'required',
            'jumlah' => 'required',
            'harga' => 'required',
            'deskripsi' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('detail-buku', $id)
                ->withErrors($validator)
                ->withInput();
        }

        if ($request->file('gambar') == null) {
            $simpan = Buku::where('id', $id)->update([
                'id_kategori' => $request->kategori,
                'judul' => $request->judul,
                'pengarang' => $request->pengarang,
                'penerbit' => $request->penerbit,
                'tahun_terbit' => $request->tahun,
                'jumlah_buku' => $request->jumlah,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
            ]);
        } else {
            $fotolama = Buku::whereId($id)->first();
            if ($fotolama->gambar != NULL) {
                unlink('gambar/' . $fotolama->gambar);
            }
            $file = $request->file('gambar');
            $fileName = $file->getClientOriginalName();
            $file->move('gambar/', $fileName);

            $simpan = Buku::where('id', $id)->update([
                'id_kategori' => $request->kategori,
                'judul' => $request->judul,
                'pengarang' => $request->pengarang,
                'penerbit' => $request->penerbit,
                'tahun_terbit' => $request->tahun,
                'jumlah_buku' => $request->jumlah,
                'harga' => $request->harga,
                'deskripsi' => $request->deskripsi,
                'gambar' => $fileName
            ]);
        }
        if ($simpan) {
            return redirect('home')->with('success', 'Data Berhasil Diubah');
        } else {
            return redirect('detail-buku', $id)->with('error', 'Data Gagal Diubah');
        }
    }

    public function delete($id)
    {
        $delete = Buku::find($id);
        if ($delete->gambar != NULL) {
            unlink('gambar/' . $delete->gambar);
        }
        $delete->delete();

        if ($delete) {
            return redirect('home')->with('success', 'Sukses Delete Buku');
        } else {
            return redirect('home')->with('error', 'Gagal Delete Buku');
        }
    }

    public function tambahKategori(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kategori' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect('input-buku')
                ->withErrors($validator)
                ->withInput();
        }

        $simpan = Kategori::create([
            'nama_kategori' => $request->nama_kategori,
        ]);
        if ($simpan) {
            return redirect('input-buku')->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect('input-buku')->with('error', 'Data Gagal Disimpan');
        }
    }

    public function diskon()
    {
        $data['buku'] = Buku::all();

        $data['diskon'] = Diskon::with(['buku'])->get();
        // dd($data);
        return view('page.diskon', $data);
    }
    public function saveDiskon(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_buku' => 'required',
            'min_pem' => 'required',
            'diskon' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('diskon')
                ->withErrors($validator)
                ->withInput();
        }

        $simpan = Diskon::create([
            'id_buku' => $request->id_buku,
            'min_pembelian' => $request->min_pem,
            'diskon' => $request->diskon,
        ]);
        if ($simpan) {
            return redirect('diskon')->with('success', 'Data Berhasil Disimpan');
        } else {
            return redirect('diskon')->with('error', 'Data Gagal Disimpan');
        }
    }

    public function deleteDiskon($id)
    {
        $delete = Diskon::find($id);
        $delete->delete();

        if ($delete) {
            return redirect('diskon')->with('success', 'Sukses Delete Diskon');
        } else {
            return redirect('diskon')->with('error', 'Gagal Delete Diskon');
        }
    }

    public function detail($id)
    {
        $data['kategori'] = Kategori::all();
        $data['buku'] = Buku::where('id', $id)->first();
        return view('page.detailBuku', $data);
    }

    public function tambahStok(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required',
        ]);
        if ($validator->fails()) {
            return redirect('detail-buku', $id)
                ->withErrors($validator)
                ->withInput();
        }

        $buku = Buku::find($id);
        $buku->jumlah_buku = $buku->jumlah_buku + $request->jumlah;
        $buku->save();

        if ($buku) {
            return redirect('home')->with('success', 'Sukses Tambah Stok');
        } else {
            return redirect('detail-buku', $id)->with('error', 'Gagal Tambah Stok');
        }
    }

   
}
