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
            padding: 22px 28px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .page-header {
            display: flex;
            align-items: center;
            gap: 14px;
            border-bottom: 3px solid #1a237e;
            padding-bottom: 10px;
            position: relative;
        }
        .page-header::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -6px;
            border-bottom: 1px solid #1a237e;
        }
        .page-header img {
            height: 54px;
            width: auto;
        }
        .header-text h2 {
            margin: 0;
            font-size: 15pt;
            color: #1a237e;
            letter-spacing: 3px;
        }
        .header-text .sub {
            margin: 2px 0 0;
            font-size: 10pt;
            color: #333;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 6px 18px;
            font-size: 9.5pt;
            margin: 16px 0 12px;
            background: #f2f5fc;
            border: 1px solid #dde3f5;
            padding: 6px 12px;
            border-radius: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            border: 2px solid #1a237e;
        }
        th {
            background: #1a237e;
            color: #fff;
            padding: 7px 10px;
            text-align: left;
            font-size: 9.5pt;
            font-weight: 700;
            white-space: normal;
            border: 1px solid #14205c;
        }
        td {
            padding: 6px 10px;
            border: 1px solid #c9d0e6;
            vertical-align: top;
            font-size: 9.5pt;
        }
        tbody:nth-child(even) td {
            background: #fafbfd;
        }
        tbody {
            page-break-inside: avoid;
        }
        .col-no {
            text-align: center;
            color: #666;
            font-size: 9pt;
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
        .current-level {
            display: inline-block;
            padding: 2px 9px;
            border-radius: 4px;
            font-size: 9pt;
            font-weight: 700;
            border: 1px solid transparent;
        }
        .lv-0 { background: #e9ecef; color: #495057; border-color: #adb5bd; }
        .lv-1 { background: #cff4fc; color: #055160; border-color: #7bd3e8; }
        .lv-2 { background: #fff3cd; color: #664d03; border-color: #e0c36a; }
        .lv-3 { background: #cfe2ff; color: #084298; border-color: #85aff5; }
        .lv-4 { background: #a9dfbf; color: #052e16; border-color: #63b585; }
        .legend {
            font-size: 9pt;
            color: #444;
            margin-top: 8px;
            display: flex;
            gap: 8px 18px;
            flex-wrap: wrap;
            background: #f9fafc;
            padding: 7px 12px;
            border-radius: 4px;
            border: 1px solid #e3e6ee;
        }
        .legend-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .legend-swatch {
            width: 12px;
            height: 12px;
            border-radius: 3px;
            border: 1px solid transparent;
            flex: 0 0 auto;
        }
        .sw-0 { background: #e9ecef; border-color: #adb5bd; }
        .sw-1 { background: #cff4fc; border-color: #7bd3e8; }
        .sw-2 { background: #fff3cd; border-color: #e0c36a; }
        .sw-3 { background: #cfe2ff; border-color: #85aff5; }
        .sw-4 { background: #a9dfbf; border-color: #63b585; }
        .sign-block {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding: 0 40px;
            page-break-inside: avoid;
        }
        .sign-col {
            text-align: center;
            min-width: 220px;
            font-size: 9.5pt;
        }
        .sign-space {
            height: 56px;
        }
        .sign-name {
            margin: 0;
            font-weight: 700;
            border-top: 1px solid #333;
            display: inline-block;
            padding-top: 4px;
            min-width: 180px;
        }
        .sign-role {
            margin: 0;
            color: #555;
        }
        .no-data {
            color: #bbb;
            font-style: italic;
        }
        .page-break {
            page-break-after: always;
        }
        .page-footer {
            display: flex;
            justify-content: space-between;
            font-size: 7.5pt;
            color: #999;
            margin-top: 10px;
            border-top: 1px solid #ddd;
            padding-top: 4px;
        }
    </style>
</head>
<body>

@foreach($chunks as $pageIndex => $employeeChunk)
    <div class="{{ !$loop->last ? 'page-break' : '' }}">
        <div class="page-header">
            <img src="{{ asset('assets/compiled/svg/logo-metinca.svg') }}" alt="Logo PT. Metinca">
            <div class="header-text">
                <h2>PT. METINCA</h2>
                <p class="sub">LAPORAN RIWAYAT LEVEL SKILL</p>
            </div>
        </div>

        <div class="info-row">
            <span><strong>Divisi:</strong> {{ $division->name }} @if($division->department)&mdash; {{ $division->department->name }}@endif</span>
            <span><strong>Dicetak oleh:</strong> {{ auth()->user()->name }}</span>
            <span><strong>Tanggal Cetak:</strong> {{ now()->format('d/m/Y H:i') }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th width="34" class="col-no">No</th>
                    <th width="170">Nama</th>
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
                        <td class="col-no" rowspan="2" style="vertical-align:middle">{{ $pageIndex * 10 + $i + 1 }}</td>
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
                                    <span class="no-data">&mdash;</span>
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
            <span class="legend-item"><span class="legend-swatch sw-0"></span>Lv0 &ndash; Belum Terlatih</span>
            <span class="legend-item"><span class="legend-swatch sw-1"></span>Lv1 &ndash; Novice</span>
            <span class="legend-item"><span class="legend-swatch sw-2"></span>Lv2 &ndash; Competent</span>
            <span class="legend-item"><span class="legend-swatch sw-3"></span>Lv3 &ndash; Proficient</span>
            <span class="legend-item"><span class="legend-swatch sw-4"></span>Lv4 &ndash; Expert</span>
        </div>

        @if($loop->last)
            <div class="sign-block">
                <div class="sign-col">
                    <p class="sign-role">Dibuat oleh,</p>
                    <p class="sign-role">{{ now()->format('d F Y') }}</p>
                    <div class="sign-space"></div>
                    <p class="sign-name">{{ auth()->user()->name }}</p>
                </div>
                <div class="sign-col">
                    <p class="sign-role">Disetujui oleh,</p>
                    <div class="sign-space"></div>
                    <p class="sign-name">&nbsp;</p>
                </div>
            </div>
        @endif

        <div class="page-footer">
            <span>Dicetak dari Sistem Metinca</span>
            <span>Halaman {{ $pageIndex + 1 }} dari {{ count($chunks) }}</span>
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
