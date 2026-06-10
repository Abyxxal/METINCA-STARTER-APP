<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Level Skill - {{ $division->name }}</title>
    <style>
        @page {
            size: landscape;
            margin: 0;
        }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 10pt;
            color: #111;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page-header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 3px solid #1a237e;
            padding-bottom: 8px;
        }
        .page-header h2 {
            margin: 0;
            font-size: 14pt;
            color: #1a237e;
            letter-spacing: 2px;
        }
        .page-header .sub {
            font-size: 10pt;
            color: #555;
            margin: 2px 0 0;
            font-weight: 600;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            font-size: 9.5pt;
            margin: 6px 0 10px;
            background: #f0f0f0;
            padding: 5px 10px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            border: 2px solid #222;
        }
        th {
            background: #1a237e;
            color: #fff;
            padding: 7px 10px;
            text-align: left;
            font-size: 9.5pt;
            font-weight: 700;
            white-space: normal;
            border: 2px solid #0d1450;
        }
        td {
            padding: 6px 10px;
            border: 2px solid #222;
            vertical-align: top;
            font-size: 9.5pt;
        }
        /* Striped employee blocks — setiap tbody = 1 employee (baris 1 & 2) */
        tbody:nth-child(even) td {
            background: #f8f9fa;
        }
        tbody {
            page-break-inside: avoid;
        }
        .emp-name {
            font-weight: 700;
        }
        .emp-nik {
            color: #555;
            font-size: 8.5pt;
            margin-top: 2px;
        }
        .change-block {
            margin-bottom: 3px;
            line-height: 1.4;
        }
        .change-block .meta {
            color: #777;
            font-size: 7pt;
        }
        .change-block .note {
            font-style: italic;
        }
        .change-sep {
            margin: 3px 0;
            border: none;
            border-top: 1px solid #e0e0e0;
        }
        .page-break {
            page-break-after: always;
        }
        .page-footer {
            text-align: center;
            font-size: 7pt;
            color: #999;
            margin-top: 8px;
            border-top: 1px solid #ddd;
            padding-top: 4px;
        }
        .legend {
            font-size: 9pt;
            color: #555;
            margin-top: 6px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            background: #f9f9f9;
            padding: 6px 10px;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        .no-data {
            color: #bbb;
            font-style: italic;
        }
        .current-level {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: 700;
        }
        .lv-0 { background: #757575; color: #fff; border: 1px solid #555; }
        .lv-1 { background: #1565c0; color: #fff; border: 1px solid #0d47a1; }
        .lv-2 { background: #e65100; color: #fff; border: 1px solid #bf360c; }
        .lv-3 { background: #2e7d32; color: #fff; border: 1px solid #1b5e20; }
        .lv-4 { background: #1b5e20; color: #fff; border: 1px solid #0d3c12; }
    </style>
</head>
<body>

@foreach($chunks as $pageIndex => $employeeChunk)
    <div class="{{ !$loop->last ? 'page-break' : '' }}">
        <div class="page-header">
            <h2>PT. METINCA</h2>
            <p class="sub">LAPORAN RIWAYAT LEVEL SKILL</p>
        </div>

        <div class="info-row">
            <span><strong>Divisi:</strong> {{ $division->name }} @if($division->department)({{ $division->department->name }})@endif</span>
            <span><strong>Cetak:</strong> {{ now()->format('d/m/Y H:i') }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="180">Nama</th>
                    @foreach($skills as $skill)
                        <th>{{ $skill->name }}</th>
                    @endforeach
                </tr>
            </thead>
            @foreach($employeeChunk as $i => $employee)
                @php
                    $empHistories = $histories->get($employee->nik, collect());
                    $groupedBySkill = $empHistories->groupBy(fn($h) => $h->competency->skill_id);
                @endphp
                <tbody>
                    <tr>
                        <td rowspan="2" style="vertical-align:middle">
                            <div class="emp-name">{{ $employee->name }}</div>
                            <div class="emp-nik">{{ $employee->nik }}</div>
                        </td>
                        @foreach($skills as $skill)
                            @php
                                $comp = $employee->competencies->firstWhere('skill_id', $skill->id);
                            @endphp
                            <td>
                                @if($comp)
                                    <span class="current-level lv-{{ $comp->level }}">
                                        Lv{{ $comp->level }}
                                    </span>
                                @else
                                    <span class="current-level lv-0">Lv0</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($skills as $skill)
                            @php $skillHistories = $groupedBySkill->get($skill->id, collect()); @endphp
                            <td>
                                @if($skillHistories->isEmpty())
                                    <span class="no-data">—</span>
                                @else
                                    @php $latestHistory = $skillHistories->first(); @endphp
                                    <div class="change-block">
                                        <span class="meta">{{ $latestHistory->created_at->format('d/m/Y') }}</span>
                                        @if($latestHistory->notes)
                                            <br><span class="meta note">{{ $latestHistory->notes }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                </tbody>
            @endforeach
        </table>

        <div class="legend">
            <span><strong>Level:</strong> Lv0–Lv4</span>
        </div>

        <div class="page-footer">
            Halaman {{ $pageIndex + 1 }} dari {{ count($chunks) }}
        </div>
    </div>
@endforeach

<script>
    window.onload = function() {
        window.print();
    };
</script>
</body>
</html>
