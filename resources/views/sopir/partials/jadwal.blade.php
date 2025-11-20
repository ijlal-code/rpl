<div class="row g-3 mb-4">
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header">Atur Jadwal Keberangkatan</div>
            <div class="card-body">
                <form method="POST" action="{{ route('sopir.jadwal.store') }}" class="d-flex flex-column gap-3">
                    @csrf
                    <div>
                        <label class="form-label">Rute</label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="rute_pilihan" id="rute-majene-polewali" value="majene_polewali" required>
                                <label class="form-check-label" for="rute-majene-polewali">Majene - Polewali</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="rute_pilihan" id="rute-polewali-majene" value="polewali_majene" required>
                                <label class="form-check-label" for="rute-polewali-majene">Polewali - Majene</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="rute_pilihan" id="rute-custom" value="custom" required>
                                <label class="form-check-label" for="rute-custom">Rute lain (tulis manual)</label>
                            </div>
                            <input type="text" name="custom_rute" class="form-control" placeholder="Contoh: Majene - Mamuju" aria-label="Rute lain" disabled>
                            <small class="text-muted">Isi jika memilih rute lain. Gunakan format Asal - Tujuan.</small>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal_keberangkatan" id="tanggal-keberangkatan" value="{{ old('tanggal_keberangkatan') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jam</label>
                            <input type="time" name="jam_keberangkatan" id="jam-keberangkatan" value="{{ old('jam_keberangkatan') }}" class="form-control" required>
                        </div>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outline-secondary w-100" id="isi-waktu-sekarang">Gunakan waktu saat ini</button>
                    </div>
                    <div>
                        <label class="form-label">Status Awal</label>
                        <select name="status" class="form-select" required>
                            <option value="aktif">Aktif</option>
                            <option value="sedang_jalan">Sedang jalan</option>
                            <option value="tidak_aktif">Tidak aktif</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Contoh: menunggu penumpang di terminal..."></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan Jadwal</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header">Jadwal Saya</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>Rute</th>
                            <th>Keberangkatan</th>
                            <th>Status</th>
                            <th>Catatan</th>
                            <th class="text-nowrap">Aksi</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($jadwal as $item)
                            <tr>
                                <td>{{ $item->rute->nama_rute ?? '-' }}</td>
                                <td>{{ $item->tanggal_keberangkatan }} {{ $item->jam_keberangkatan }}</td>
                                <td><span class="badge text-bg-secondary text-capitalize">{{ str_replace('_', ' ', $item->status) }}</span></td>
                                <td>{{ $item->catatan ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('sopir.jadwal.edit', $item) }}" class="btn btn-sm btn-outline-secondary mb-2 w-100">Edit</a>
                                    <form method="POST" action="{{ route('sopir.jadwal.update', $item) }}" class="d-flex flex-column gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <div class="d-flex gap-2 flex-wrap">
                                            <select name="status" class="form-select form-select-sm flex-grow-1">
                                                <option value="aktif" @selected($item->status === 'aktif')>Aktif</option>
                                                <option value="sedang_jalan" @selected($item->status === 'sedang_jalan')>Sedang jalan</option>
                                                <option value="tidak_aktif" @selected($item->status === 'tidak_aktif')>Tidak aktif</option>
                                            </select>
                                            <button class="btn btn-sm btn-outline-primary" type="submit">Simpan</button>
                                        </div>
                                        <small class="text-muted">Perbarui catatan dari halaman edit jadwal.</small>
                                    </form>
                                    <form method="POST" action="{{ route('sopir.jadwal.destroy', $item) }}" class="mt-2" onsubmit="return confirm('Hapus jadwal ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger w-100" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">Belum ada jadwal yang dibuat.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const customInput = document.querySelector('input[name="custom_rute"]');
        const radios = document.querySelectorAll('input[name="rute_pilihan"]');
        const tombolWaktuSekarang = document.getElementById('isi-waktu-sekarang');
        const inputTanggal = document.getElementById('tanggal-keberangkatan');
        const inputJam = document.getElementById('jam-keberangkatan');

        const isiWaktuSekarang = () => {
            const sekarang = new Date();
            const pad = (angka) => angka.toString().padStart(2, '0');

            const tanggal = `${sekarang.getFullYear()}-${pad(sekarang.getMonth() + 1)}-${pad(sekarang.getDate())}`;
            const jam = `${pad(sekarang.getHours())}:${pad(sekarang.getMinutes())}`;

            inputTanggal.value = tanggal;
            inputJam.value = jam;
        };

        const toggleCustomInput = () => {
            const isCustom = document.getElementById('rute-custom').checked;
            customInput.disabled = !isCustom;
            if (!isCustom) {
                customInput.value = '';
            }
        };

        radios.forEach(radio => radio.addEventListener('change', toggleCustomInput));

        tombolWaktuSekarang?.addEventListener('click', isiWaktuSekarang);

        if (!inputTanggal.value || !inputJam.value) {
            isiWaktuSekarang();
        }
    });
</script>
