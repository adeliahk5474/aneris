{{-- resources/views/admin/home-setting.blade.php --}}
@extends('layouts.admin')
@section('title', 'Pengaturan Homepage')

@section('content')
@vite('resources/css/admin/home-setting.css')

<div class="hs-wrap">

    <div class="hs-breadcrumb">
        <a href="{{ route('admin.dashboard') }}">Dashboard</a>
        <span class="sep">/</span>
        <span>Tampilan Home</span>
    </div>

    <div class="hs-title">Tampilan Homepage</div>

    @if(session('success'))
    <div class="hs-alert">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
    @endif

    <form action="{{ route('admin.home-setting.update') }}" method="POST" id="hsForm">
        @csrf @method('PATCH')

        {{-- ── HERO ── --}}
        <div class="hs-card">
            <div class="hs-card-head">
                <i class="bi bi-type-h1"></i> Hero Section
            </div>

            <div class="hs-field">
                <label class="hs-label">Judul Hero <span>(wajib)</span></label>
                <input type="text" name="hero_title" class="hs-input"
                    value="{{ old('hero_title', $settings['hero_title'] ?? '') }}"
                    placeholder="For the love of human creativity" required>
                @error('hero_title')
                <div class="hs-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="hs-field">
                <label class="hs-label">Subjudul Hero <span>(opsional)</span></label>
                <textarea name="hero_subtitle" class="hs-textarea"
                    placeholder="Deskripsi singkat di bawah judul...">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea>
            </div>
        </div>

        {{-- ── BANNER 1 ── --}}
        <div class="hs-card">
            <div class="hs-card-head">
                <i class="bi bi-card-image"></i> Banner Kiri
            </div>

            <div class="hs-field">
                <label class="hs-label">Judul</label>
                <input type="text" name="banner1_title" class="hs-input"
                    value="{{ old('banner1_title', $settings['banner1_title'] ?? '') }}"
                    placeholder="Made for creators" required
                    data-preview="b1title">
                @error('banner1_title')
                <div class="hs-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="hs-field">
                <label class="hs-label">Deskripsi</label>
                <textarea name="banner1_subtitle" class="hs-textarea"
                    placeholder="Deskripsi banner kiri..."
                    data-preview="b1sub">{{ old('banner1_subtitle', $settings['banner1_subtitle'] ?? '') }}</textarea>
            </div>

            <div class="hs-field">
                <label class="hs-label">Warna Latar</label>
                <div class="hs-color-row">
                    <input type="text" name="banner1_color" id="banner1_color_text" class="hs-input"
                        value="{{ old('banner1_color', $settings['banner1_color'] ?? '#1a1a2e') }}"
                        placeholder="#1a1a2e"
                        data-color="banner1">
                    <div class="hs-color-preview" id="banner1_preview_box"
                        style="background:{{ old('banner1_color', $settings['banner1_color'] ?? '#1a1a2e') }}"
                        data-picker="banner1_color_picker">
                    </div>
                    <input type="color" id="banner1_color_picker"
                        value="{{ old('banner1_color', $settings['banner1_color'] ?? '#1a1a2e') }}"
                        data-color="banner1">
                </div>
            </div>

            <div class="hs-preview" id="banner1_card"
                style="background:{{ old('banner1_color', $settings['banner1_color'] ?? '#1a1a2e') }}">
                <div class="hs-preview-title" id="b1title">{{ old('banner1_title', $settings['banner1_title'] ?? 'Made for creators') }}</div>
                <div class="hs-preview-sub" id="b1sub">{{ old('banner1_subtitle', $settings['banner1_subtitle'] ?? '') }}</div>
            </div>
        </div>

        {{-- ── BANNER 2 ── --}}
        <div class="hs-card">
            <div class="hs-card-head">
                <i class="bi bi-card-image"></i> Banner Kanan
            </div>

            <div class="hs-field">
                <label class="hs-label">Judul</label>
                <input type="text" name="banner2_title" class="hs-input"
                    value="{{ old('banner2_title', $settings['banner2_title'] ?? '') }}"
                    placeholder="No Generative AI" required
                    data-preview="b2title">
                @error('banner2_title')
                <div class="hs-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="hs-field">
                <label class="hs-label">Deskripsi</label>
                <textarea name="banner2_subtitle" class="hs-textarea"
                    placeholder="Deskripsi banner kanan..."
                    data-preview="b2sub">{{ old('banner2_subtitle', $settings['banner2_subtitle'] ?? '') }}</textarea>
            </div>

            <div class="hs-field">
                <label class="hs-label">Warna Latar</label>
                <div class="hs-color-row">
                    <input type="text" name="banner2_color" id="banner2_color_text" class="hs-input"
                        value="{{ old('banner2_color', $settings['banner2_color'] ?? '#0d2818') }}"
                        placeholder="#0d2818"
                        data-color="banner2">
                    <div class="hs-color-preview" id="banner2_preview_box"
                        style="background:{{ old('banner2_color', $settings['banner2_color'] ?? '#0d2818') }}"
                        data-picker="banner2_color_picker">
                    </div>
                    <input type="color" id="banner2_color_picker"
                        value="{{ old('banner2_color', $settings['banner2_color'] ?? '#0d2818') }}"
                        data-color="banner2">
                </div>
            </div>

            <div class="hs-preview" id="banner2_card"
                style="background:{{ old('banner2_color', $settings['banner2_color'] ?? '#0d2818') }}">
                <div class="hs-preview-title" id="b2title">{{ old('banner2_title', $settings['banner2_title'] ?? 'No Generative AI') }}</div>
                <div class="hs-preview-sub" id="b2sub">{{ old('banner2_subtitle', $settings['banner2_subtitle'] ?? '') }}</div>
            </div>
        </div>

        <div class="hs-actions">
            <button type="submit" class="btn-save">
                <i class="bi bi-floppy"></i> Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@vite('resources/js/admin/home-setting.js')
@endsection
