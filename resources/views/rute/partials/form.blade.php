<div class="mb-3">
    <label class="form-label">Nama Rute</label>
    <input type="text" name="nama_rute" class="form-control" value="{{ old('nama_rute', $rute->nama_rute ?? '') }}" required>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Titik Berangkat</label>
        <input type="text" name="titik_berangkat" class="form-control" value="{{ old('titik_berangkat', $rute->titik_berangkat ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Titik Tujuan</label>
        <input type="text" name="titik_tujuan" class="form-control" value="{{ old('titik_tujuan', $rute->titik_tujuan ?? '') }}" required>
    </div>
</div>
<div class="mb-3 mt-3">
    <label class="form-label">Estimasi Waktu (menit)</label>
    <input type="number" name="estimasi_waktu" class="form-control" min="1" value="{{ old('estimasi_waktu', $rute->estimasi_waktu ?? '') }}" required>
</div>
