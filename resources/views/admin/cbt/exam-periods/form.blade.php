@csrf

<div class="mb-3">
    <label class="form-label">Nama Periode <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
           value="{{ old('name', $period->name ?? '') }}" placeholder="Contoh: Periode Ujian QC Sep 2026" required>
    @error('name')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="mb-3">
    <label class="form-label">Departemen</label>
    <select name="department_id" id="selectDepartemen" class="form-select @error('department_id') is-invalid @enderror">
        <option value="">-- Semua Departemen --</option>
        @foreach($departments as $department)
            <option value="{{ $department->id }}"
                {{ old('department_id', $period->department_id ?? null) == $department->id ? 'selected' : '' }}>
                {{ $department->name }}
            </option>
        @endforeach
    </select>
    @error('department_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Kosongkan untuk semua departemen. Opsi divisi mengikuti pilihan departemen.</small>
</div>

<div class="mb-3">
    <label class="form-label">Divisi</label>
    <select name="division_id" id="selectDivisi" class="form-select @error('division_id') is-invalid @enderror">
        <option value="">-- Semua Divisi --</option>
        @foreach($divisions as $division)
            <option value="{{ $division->id }}" data-department="{{ $division->department_id }}"
                {{ old('division_id', $period->division_id ?? null) == $division->id ? 'selected' : '' }}>
                {{ $division->name }}
            </option>
        @endforeach
    </select>
    @error('division_id')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Kosongkan jika periode berlaku untuk seluruh divisi di lingkup di atas.</small>
</div>

<div class="mb-3">
    <label class="form-label">Tanggal &amp; Jam Mulai <span class="text-danger">*</span></label>
    <input type="datetime-local" name="start_at" class="form-control @error('start_at') is-invalid @enderror"
           value="{{ old('start_at', isset($period) && $period->start_at ? $period->start_at->format('Y-m-d\TH:i') : '') }}" required>
    @error('start_at')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Waktu pertama ujian boleh dikerjakan.</small>
</div>

<div class="mb-3">
    <label class="form-label">Tanggal &amp; Jam Berakhir <span class="text-danger">*</span></label>
    <input type="datetime-local" name="end_at" class="form-control @error('end_at') is-invalid @enderror"
           value="{{ old('end_at', isset($period) && $period->end_at ? $period->end_at->format('Y-m-d\TH:i') : '') }}" required>
    @error('end_at')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    <small class="text-muted">Batas akhir ujian dikerjakan (wajib setelah waktu mulai).</small>
</div>

<div class="mb-3">
    <label class="form-label">Catatan (Opsional)</label>
    <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
              placeholder="Catatan untuk supervisor...">{{ old('notes', $period->notes ?? '') }}</textarea>
    @error('notes')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const deptSelect = document.getElementById('selectDepartemen');
    const divSelect = document.getElementById('selectDivisi');
    if (!deptSelect || !divSelect) return;

    const allDivisions = Array.from(divSelect.options).slice(1); // tanpa "-- Semua Divisi --"

    function filterDivisi() {
        const dept = deptSelect.value;
        const current = divSelect.value;

        divSelect.innerHTML = '<option value="">-- Semua Divisi --</option>';
        allDivisions.forEach(function(opt) {
            if (dept === '' || opt.dataset.department === dept) {
                divSelect.appendChild(opt);
            }
        });

        // Pertahankan pilihan yang masih valid (sesuai dept terpilih).
        const stillExists = Array.from(divSelect.options).some(function(o) { return o.value === current; });
        if (stillExists) {
            divSelect.value = current;
        }
    }

    deptSelect.addEventListener('change', filterDivisi);
    filterDivisi();
});
</script>
@endpush