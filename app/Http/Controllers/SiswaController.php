<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Siswa;
use App\Kelas;
use App\Spp;
use Illuminate\Support\Facades\Hash;
use App\User;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //ambil data max 10
        $data = Siswa::paginate(10);

        //membuat variabel tampil yang diisi dengan data
        foreach ($data as $item) {
            $item->kelas = Kelas::find($item->id_kelas);
            $item->spp = Spp::find($item->id_spp);
            $item->user = Siswa::find($item->id_user);
        }
        $tampil['data'] = $data;
        //tampilkan resources/views/siswa/index.blade.php beserta variabel tampil
        return view("siswa.index", $tampil);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        //tampilkan resources/views/siswa/create.blade.php
        $data['kelas'] = Kelas::get();
        $data['spp'] = Spp::get();
        return view("siswa.create", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        //validasi inputan
        $this->validate($request, [
            'nisn' => 'required|unique:siswas',
            'nis' => 'required|unique:siswas',
            'nama' => 'required',
            'alamat' => 'required',
            'no_telp' => 'required',
            'id_kelas' => 'required',
            'id_spp' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
        ]);
        //enkripsi password
        $enkripsi = Hash::make($request->password);
        $request->merge(['password' => $enkripsi]);
        //isi name dengan nama
        $request->merge(['name' => $request->nama]);
        //isi hak_akses dengan 'siswa'
        $request->merge(['hak_akses' => "siswa"]);
        //buat data User
        $dataUser = User::create($request->all());
        //isi id_siswa
        $request->merge(['id_user' => $dataUser->id]);
        //buat data siswa
        $dataSiswa = Siswa::create($request->all());
        return redirect()->route("siswa.index")->with(
            "success",
            "Data berhasil disimpan."
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($siswa)
    {
        //
        $data = Siswa::findOrFail($siswa);
        $data->kelas = Kelas::get();
        $data->spp = Spp::get();
        $data->user = User::find($data->id_user);
        //tampilkan resources/views/siswa/edit.blade.php
        return view("siswa.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $siswa)
    {
        //
        //validasi inputan
        $this->validate($request, [
            'nis'
            => 'required',
            'nama'
            => 'required',
            'alamat'
            => 'required',
            'no_telp' => 'required',
            'id_kelas' => 'required',
            'id_spp'
            => 'required',
            'email'
            => 'required|email',
        ]);
        $dataSiswa = Siswa::findOrFail($siswa);
        $dataSiswa->nis = $request->nis;
        $dataSiswa->nama = $request->nama;
        $dataSiswa->alamat = $request->alamat;
        $dataSiswa->no_telp = $request->no_telp;
        $dataSiswa->id_kelas = $request->id_kelas;
        $dataSiswa->id_spp = $request->id_spp;
        $dataSiswa->save();
        $dataUser = User::findOrFail($dataSiswa->id_user);
        $dataUser->name = $request->nama;
        $dataUser->email = $request->email;
        //jika password tidak kosong
        if ($request->password != "") {
            $enkripsi = Hash::make($request->password);
            $dataUser->password = $enkripsi;
        }
        $dataUser->save();
        return redirect()->route("siswa.index")->with(
            "success",
            "Data berhasil diubah."
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($siswa)
    {
        //
        $dataSiswa = Siswa::findOrFail($siswa);
        $dataSiswa->delete();
        $dataUser = User::findOrFail($dataSiswa->id_user);
        $dataUser->delete();
    }
}
