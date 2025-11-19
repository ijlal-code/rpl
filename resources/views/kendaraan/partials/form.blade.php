<div class="mb-3">
    <label class="form-label">Nama Kendaraan</label>
    <input type="text" name="nama_kendaraan" class="form-control" value="{{ old('nama_kendaraan', $kendaraan->nama_kendaraan ?? '') }}" required>
</div>
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Sopir</label>
        <select name="sopir_id" class="form-select" required>
            @foreach($sopir as $item)
                <option value="{{ $item->id }}" @selected(old('sopir_id', $kendaraan->sopir_id ?? '') == $item->id)>{{ $item->nama }} ({{ $item->user->email }})</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label">Rute</label>
        <select name="rute_id" class="form-select" required>
            @foreach($rute as $item)
                <option value="{{ $item->id }}" @selected(old('rute_id', $kendaraan->rute_id ?? '') == $item->id)>{{ $item->nama_rute }}</option>
            @endforeach
        </select>
    </div>
</div>
<div class="row g-3 mt-3">
    <div class="col-md-6">
        <label class="form-label">Kapasitas</label>
        <input type="number" name="kapasitas" class="form-control" min="1" value="{{ old('kapasitas', $kendaraan->kapasitas ?? '') }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Status</label>
        <select name="status" class="form-select" required>
            @foreach(['siap','jalan','selesai'] as $status)
                <option value="{{ $status }}" @selected(old('status', $kendaraan->status ?? 'siap') == $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
</div>
