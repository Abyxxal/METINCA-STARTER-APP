@extends('layouts.app')

@section('title', 'Kompetensi Saya')

@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Kompetensi Saya</h3>
                <p class="text-subtitle text-muted">Daftar skill dan level kompetensi Anda</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('cbt.employee.dashboard') }}">CBT</a></li>
                        <li class="breadcrumb-item active">Kompetensi Saya</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<section class="section">
    @if($competencies->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-award text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Belum Ada Kompetensi</h4>
                <p class="text-muted">Anda belum memiliki kompetensi yang tercatat. Selesaikan ujian untuk mendapatkan kompetensi.</p>
                <a href="{{ route('cbt.employee.dashboard') }}" class="btn btn-primary">
                    <i class="bi bi-play"></i> Lihat Ujian Tersedia
                </a>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($competencies as $competency)
                @php
                    $levelColors = [
                        1 => 'secondary',
                        2 => 'info',
                        3 => 'primary',
                        4 => 'success',
                    ];
                    $levelNames = [
                        0 => 'None',
                        1 => 'Novice',
                        2 => 'Competent',
                        3 => 'Proficient',
                        4 => 'Expert',
                    ];
                @endphp
                
                <div class="col-md-6 col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">{{ $competency->skill->name }}</h5>
                                    <small class="text-muted">{{ $competency->skill->category }}</small>
                                </div>
                                <span class="badge bg-{{ $levelColors[$competency->level] ?? 'secondary' }} fs-6">
                                    Level {{ $competency->level }}
                                </span>
                            </div>

                            <div class="progress mb-2" style="height: 10px;">
                                <div class="progress-bar bg-{{ $levelColors[$competency->level] ?? 'secondary' }}" 
                                     style="width: {{ ($competency->level / 4) * 100 }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mb-3">
                                <small class="text-muted">{{ $levelNames[$competency->level] ?? '-' }}</small>
                                <small class="text-muted">{{ $competency->level }}/4</small>
                            </div>

                            @if($competency->verified_at)
                                <div class="alert alert-success py-2 mb-3">
                                    <small>
                                        <i class="bi bi-check-circle"></i> 
                                        Terverifikasi oleh {{ $competency->verifiedBy->name ?? 'Admin' }}
                                        <br>
                                        <span class="text-muted">{{ $competency->verified_at?->format('d M Y') ?? 'Belum diverifikasi' }}</span>
                                    </small>
                                </div>
                            @else
                                <div class="alert alert-warning py-2 mb-3">
                                    <small>
                                        <i class="bi bi-clock"></i> Menunggu verifikasi
                                    </small>
                                </div>
                            @endif

                            @if($competency->level < 4)
                                @php
                                    $nextLevelExam = \App\Models\Exam::where('skill_id', $competency->skill_id)
                                        ->where('target_level', $competency->level + 1)
                                        ->where('is_published', true)
                                        ->first();
                                @endphp
                                
                                @if($nextLevelExam)
                                    <div class="border-top pt-3">
                                        <small class="text-muted d-block mb-2">Upgrade ke Level {{ $competency->level + 1 }}:</small>
                                        <a href="{{ route('cbt.employee.exam-info', $nextLevelExam) }}" class="btn btn-sm btn-outline-primary w-100">
                                            <i class="bi bi-arrow-up-circle"></i> {{ $nextLevelExam->title }}
                                        </a>
                                    </div>
                                @else
                                    <div class="border-top pt-3 text-center">
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i> 
                                            Belum ada ujian untuk Level {{ $competency->level + 1 }}
                                        </small>
                                    </div>
                                @endif
                            @else
                                <div class="border-top pt-3 text-center">
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-star-fill"></i> Level Maksimal
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Available Skills without Competency --}}
    @if(isset($availableSkills) && $availableSkills->isNotEmpty())
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Skill Lain yang Tersedia</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">Anda belum memiliki kompetensi pada skill berikut:</p>
                <div class="row">
                    @foreach($availableSkills as $skill)
                        @php
                            $entryExam = \App\Models\Exam::where('skill_id', $skill->id)
                                ->where('target_level', 2)
                                ->where('is_published', true)
                                ->first();
                        @endphp
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="border rounded p-3">
                                <h6>{{ $skill->name }}</h6>
                                <small class="text-muted">{{ $skill->category }}</small>
                                @if($entryExam)
                                    <div class="mt-2">
                                        <a href="{{ route('cbt.employee.exam-info', $entryExam) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-play"></i> Mulai dari Level 2
                                        </a>
                                    </div>
                                @else
                                    <div class="mt-2">
                                        <small class="text-muted">Belum ada ujian tersedia</small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</section>
@endsection
