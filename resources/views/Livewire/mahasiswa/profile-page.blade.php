<div class="space-y-8">
    <div class="md:flex md:items-center md:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Profil & Pengaturan Akun
            </h2>
            <p class="mt-1 text-sm text-gray-500">Kelola data diri, data orang tua, keamanan, dan berkas administrasi.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        {{-- KOLOM KIRI: UPDATE BIODATA & PASSWORD --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Form Biodata --}}
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mb-4">Biodata & Keluarga (Standar Feeder)</h3>
                    
                    @if (session()->has('success_profile'))
                        <div class="mb-4 bg-green-50 p-3 rounded text-green-700 text-sm font-bold">{{ session('success_profile') }}</div>
                    @endif

                    <form wire:submit.prevent="updateProfile">
                        <div class="grid grid-cols-6 gap-6">
                            
                            {{-- IDENTITAS DIRI --}}
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                                <input type="text" value="{{ $mahasiswa->nama_lengkap }}" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm text-gray-500 cursor-not-allowed">
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIM</label>
                                <input type="text" value="{{ $mahasiswa->nim }}" readonly class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm sm:text-sm text-gray-500 cursor-not-allowed">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email Pribadi</label>
                                <input type="email" wire:model="email_pribadi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('email_pribadi') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nomor HP / WhatsApp</label>
                                <input type="text" wire:model="nomor_hp" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('nomor_hp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            {{-- ALAMAT DOMISILI --}}
                            <div class="col-span-6 border-t pt-4 mt-2">
                                <h4 class="text-xs font-black text-indigo-700 uppercase tracking-wide">Alamat Domisili</h4>
                            </div>

                            <div class="col-span-6">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jalan / Nama Tempat</label>
                                <input type="text" wire:model="jalan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="Jl. Merdeka No. 10">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dusun / Lingkungan</label>
                                <input type="text" wire:model="dusun" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kelurahan / Desa</label>
                                <input type="text" wire:model="kelurahan" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="col-span-3 sm:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">RT</label>
                                <input type="number" wire:model="rt" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="col-span-3 sm:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">RW</label>
                                <input type="number" wire:model="rw" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="col-span-6 sm:col-span-1">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Kode Pos</label>
                                <input type="text" wire:model="kode_pos" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Jenis Tinggal</label>
                                <select wire:model="jenis_tinggal" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Pilih...</option>
                                    <option value="Bersama Orang Tua">Bersama Orang Tua</option>
                                    <option value="Wali">Wali</option>
                                    <option value="Kos">Kos</option>
                                    <option value="Asrama">Asrama</option>
                                    <option value="Panti Asuhan">Panti Asuhan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Alat Transportasi</label>
                                <select wire:model="alat_transportasi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Pilih...</option>
                                    <option value="Jalan Kaki">Jalan Kaki</option>
                                    <option value="Sepeda Motor">Sepeda Motor</option>
                                    <option value="Mobil">Mobil</option>
                                    <option value="Angkutan Umum">Angkutan Umum</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            {{-- DATA AYAH --}}
                            <div class="col-span-6 border-t pt-4 mt-2">
                                <h4 class="text-xs font-black text-indigo-700 uppercase tracking-wide">Data Ayah</h4>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIK Ayah</label>
                                <input type="text" wire:model="nik_ayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Ayah</label>
                                <input type="text" wire:model="nama_ayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tgl Lahir</label>
                                <input type="date" wire:model="tanggal_lahir_ayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pendidikan</label>
                                <select wire:model="pendidikan_ayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Pilih...</option>
                                    <option value="Tidak Sekolah">Tidak Sekolah</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA/SMK</option>
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1/D4</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pekerjaan</label>
                                <select wire:model="pekerjaan_ayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Pilih...</option>
                                    <option value="Tidak Bekerja">Tidak Bekerja</option>
                                    <option value="PNS">PNS</option>
                                    <option value="TNI/Polri">TNI/Polri</option>
                                    <option value="Pegawai Swasta">Pegawai Swasta</option>
                                    <option value="Wiraswasta">Wiraswasta</option>
                                    <option value="Petani">Petani</option>
                                    <option value="Nelayan">Nelayan</option>
                                    <option value="Buruh">Buruh</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Penghasilan</label>
                                <select wire:model="penghasilan_ayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Pilih...</option>
                                    <option value="< 1 Juta">< Rp 1.000.000</option>
                                    <option value="1-3 Juta">Rp 1.000.000 - Rp 3.000.000</option>
                                    <option value="3-5 Juta">Rp 3.000.000 - Rp 5.000.000</option>
                                    <option value="5-10 Juta">Rp 5.000.000 - Rp 10.000.000</option>
                                    <option value="> 10 Juta">> Rp 10.000.000</option>
                                </select>
                            </div>

                            {{-- DATA IBU --}}
                            <div class="col-span-6 border-t pt-4 mt-2">
                                <h4 class="text-xs font-black text-indigo-700 uppercase tracking-wide">Data Ibu</h4>
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">NIK Ibu</label>
                                <input type="text" wire:model="nik_ibu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Ibu</label>
                                <input type="text" wire:model="nama_ibu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tgl Lahir</label>
                                <input type="date" wire:model="tanggal_lahir_ibu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pendidikan</label>
                                <select wire:model="pendidikan_ibu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Pilih...</option>
                                    <option value="Tidak Sekolah">Tidak Sekolah</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA/SMK</option>
                                    <option value="D1">D1</option>
                                    <option value="D2">D2</option>
                                    <option value="D3">D3</option>
                                    <option value="S1">S1/D4</option>
                                    <option value="S2">S2</option>
                                    <option value="S3">S3</option>
                                </select>
                            </div>
                            <div class="col-span-6 sm:col-span-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pekerjaan</label>
                                <select wire:model="pekerjaan_ibu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Pilih...</option>
                                    <option value="Tidak Bekerja">Tidak Bekerja</option>
                                    <option value="PNS">PNS</option>
                                    <option value="TNI/Polri">TNI/Polri</option>
                                    <option value="Pegawai Swasta">Pegawai Swasta</option>
                                    <option value="Wiraswasta">Wiraswasta</option>
                                    <option value="Petani">Petani</option>
                                    <option value="Nelayan">Nelayan</option>
                                    <option value="Buruh">Buruh</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Penghasilan</label>
                                <select wire:model="penghasilan_ibu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Pilih...</option>
                                    <option value="< 1 Juta">< Rp 1.000.000</option>
                                    <option value="1-3 Juta">Rp 1.000.000 - Rp 3.000.000</option>
                                    <option value="3-5 Juta">Rp 3.000.000 - Rp 5.000.000</option>
                                    <option value="5-10 Juta">Rp 5.000.000 - Rp 10.000.000</option>
                                    <option value="> 10 Juta">> Rp 10.000.000</option>
                                </select>
                            </div>

                            {{-- WALI --}}
                            <div class="col-span-6 border-t pt-4 mt-2">
                                <h4 class="text-xs font-black text-indigo-700 uppercase tracking-wide">Data Wali (Opsional)</h4>
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nama Wali</label>
                                <input type="text" wire:model="nama_wali" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                        </div>

                        <div class="mt-8 text-right border-t border-gray-100 pt-4">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2.5 px-6 text-sm font-bold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Form Ganti Password --}}
            <div class="bg-white shadow sm:rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900 border-b pb-2 mb-4">Ganti Password</h3>

                    @if (session()->has('success_password'))
                        <div class="mb-4 bg-green-50 p-3 rounded text-green-700 text-sm font-bold">{{ session('success_password') }}</div>
                    @endif

                    <form wire:submit.prevent="updatePassword">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password Saat Ini</label>
                                <input type="password" wire:model="current_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('current_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password Baru</label>
                                <input type="password" wire:model="new_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @error('new_password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Konfirmasi Password Baru</label>
                                <input type="password" wire:model="new_password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>
                        <div class="mt-4 text-right">
                            <button type="submit" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none">
                                Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- KOLOM KANAN: DOKUMEN DIKTI --}}
        <div class="lg:col-span-1">
            <div class="bg-white shadow sm:rounded-lg overflow-hidden sticky top-6">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-bold leading-6 text-gray-900 mb-1">Berkas Digital</h3>
                    <p class="text-xs text-gray-500 mb-6 border-b pb-2">Unggah dokumen wajib untuk pelaporan PDDIKTI (Max 2MB, PDF/JPG).</p>

                    @if (session()->has('success_file'))
                        <div class="mb-4 bg-green-50 p-2 rounded text-green-700 text-xs font-bold">{{ session('success_file') }}</div>
                    @endif

                    <div class="space-y-6">
                        
                        {{-- Kartu Keluarga --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Kartu Keluarga (KK)</label>
                            <div class="flex items-center space-x-2">
                                <input type="file" wire:model="file_kk" class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <button wire:click="uploadDokumen('kk')" wire:loading.attr="disabled" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-indigo-700 disabled:opacity-50">Upload</button>
                            </div>
                            @error('file_kk') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            
                            @if(isset($existing_files['kk']))
                                <a href="{{ asset('storage/'.$existing_files['kk']) }}" target="_blank" class="mt-2 inline-flex items-center text-xs text-green-600 font-medium hover:underline">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Lihat File Tersimpan
                                </a>
                            @endif
                        </div>

                        {{-- KTP --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">KTP / Kartu Identitas</label>
                            <div class="flex items-center space-x-2">
                                <input type="file" wire:model="file_ktp" class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <button wire:click="uploadDokumen('ktp')" wire:loading.attr="disabled" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-indigo-700 disabled:opacity-50">Upload</button>
                            </div>
                            @error('file_ktp') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                            @if(isset($existing_files['ktp']))
                                <a href="{{ asset('storage/'.$existing_files['ktp']) }}" target="_blank" class="mt-2 inline-flex items-center text-xs text-green-600 font-medium hover:underline">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Lihat File Tersimpan
                                </a>
                            @endif
                        </div>

                        {{-- Ijazah --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Ijazah Terakhir</label>
                            <div class="flex items-center space-x-2">
                                <input type="file" wire:model="file_ijazah" class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <button wire:click="uploadDokumen('ijazah')" wire:loading.attr="disabled" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-indigo-700 disabled:opacity-50">Upload</button>
                            </div>
                            @error('file_ijazah') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                            @if(isset($existing_files['ijazah']))
                                <a href="{{ asset('storage/'.$existing_files['ijazah']) }}" target="_blank" class="mt-2 inline-flex items-center text-xs text-green-600 font-medium hover:underline">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Lihat File Tersimpan
                                </a>
                            @endif
                        </div>

                        {{-- Foto --}}
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1 uppercase">Pas Foto (Almamater)</label>
                            <div class="flex items-center space-x-2">
                                <input type="file" wire:model="file_foto" class="block w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <button wire:click="uploadDokumen('foto')" wire:loading.attr="disabled" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-indigo-700 disabled:opacity-50">Upload</button>
                            </div>
                            @error('file_foto') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                            @if(isset($existing_files['foto']))
                                <a href="{{ asset('storage/'.$existing_files['foto']) }}" target="_blank" class="mt-2 inline-flex items-center text-xs text-green-600 font-medium hover:underline">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Lihat File Tersimpan
                                </a>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>