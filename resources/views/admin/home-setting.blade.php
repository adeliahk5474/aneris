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

    <form action="{{ route('admin.home-setting.update') }}" method="POST"
        enctype="multipart/form-data" id="hsForm">
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
                @error('hero_title')<div class="hs-error">{{ $message }}</div>@enderror
            </div>

            <div class="hs-field">
                <label class="hs-label">Subjudul Hero <span>(opsional)</span></label>
                <textarea name="hero_subtitle" class="hs-textarea"
                    placeholder="Deskripsi singkat di bawah judul...">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</textarea>
            </div>

            <div class="hs-field">
                <label class="hs-label">Gambar Background Hero <span>(opsional)</span></label>
                @if(!empty($settings['hero_image']))
                <div class="hs-current-img">
                    <img src="{{ $settings['hero_image'] }}" alt="Hero background">
                    <div class="hs-current-img-label">Gambar aktif</div>
                    <form action="{{ route('admin.home-setting.remove-image') }}"
                        method="POST" class="hs-remove-form">
                        @csrf @method('DELETE')
                        <input type="hidden" name="key" value="hero_image">
                        <button type="submit" class="hs-btn-remove"
                            onclick="return confirm('Hapus gambar hero?')">
                            <i class="bi bi-trash"></i> Hapus Gambar
                        </button>
                    </form>
                </div>
                @endif
                <div class="hs-upload-zone" id="hero_zone">
                    <input type="file" name="hero_image" accept="image/*"
                        onchange="previewZone(this,'hero_zone','hero_preview')">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <span>{{ !empty($settings['hero_image']) ? 'Ganti gambar' : 'Klik atau drag gambar' }}</span>
                    <span class="hs-upload-hint">JPG, PNG, WEBP — maks 5MB</span>
                </div>
                <img id="hero_preview" class="hs-new-preview" src="" alt="" style="display:none;">
                @error('hero_image')<div class="hs-error">{{ $message }}</div>@enderror
            </div>
        </div>

        {{-- ── BANNER KIRI ── --}}
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
                @error('banner1_title')<div class="hs-error">{{ $message }}</div>@enderror
            </div>

            <div class="hs-field">
                <label class="hs-label">Deskripsi</label>
                <textarea name="banner1_subtitle" class="hs-textarea"
                    placeholder="Deskripsi banner kiri..."
                    data-preview="b1sub">{{ old('banner1_subtitle', $settings['banner1_subtitle'] ?? '') }}</textarea>
            </div>

            <div class="hs-field">
                <label class="hs-label">Gambar Background Banner Kiri</label>
                @if(!empty($settings['banner1_image']))
                <div class="hs-current-img">
                    <img src="{{ $settings['banner1_image'] }}" alt="Banner 1">
                    <div class="hs-current-img-label">Gambar aktif</div>
                    <form action="{{ route('admin.home-setting.remove-image') }}"
                        method="POST" class="hs-remove-form">
                        @csrf @method('DELETE')
                        <input type="hidden" name="key" value="banner1_image">
                        <button type="submit" class="hs-btn-remove"
                            onclick="return confirm('Hapus gambar banner kiri?')">
                            <i class="bi bi-trash"></i> Hapus Gambar
                        </button>
                    </form>
                </div>
                @endif
                <div class="hs-upload-zone" id="banner1_zone">
                    <input type="file" name="banner1_image" accept="image/*"
                        onchange="previewZone(this,'banner1_zone','banner1_preview')">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <span>{{ !empty($settings['banner1_image']) ? 'Ganti gambar' : 'Klik atau drag gambar' }}</span>
                    <span class="hs-upload-hint">JPG, PNG, WEBP — maks 5MB</span>
                </div>
                <img id="banner1_preview" class="hs-new-preview" src="" alt="" style="display:none;">
                @error('banner1_image')<div class="hs-error">{{ $message }}</div>@enderror
            </div>

            {{-- Live preview teks --}}
            <div class="hs-preview" id="banner1_card"
                style="{{ !empty($settings['banner1_image']) ? 'background-image:url('.$settings['banner1_image'].');background-size:cover;background-position:center;' : 'background:#1a1a2e;' }}">
                <div class="hs-preview-title" id="b1title">{{ old('banner1_title', $settings['banner1_title'] ?? 'Made for creators') }}</div>
                <div class="hs-preview-sub" id="b1sub">{{ old('banner1_subtitle', $settings['banner1_subtitle'] ?? '') }}</div>
            </div>
        </div>

        {{-- ── BANNER KANAN ── --}}
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
                @error('banner2_title')<div class="hs-error">{{ $message }}</div>@enderror
            </div>

            <div class="hs-field">
                <label class="hs-label">Deskripsi</label>
                <textarea name="banner2_subtitle" class="hs-textarea"
                    placeholder="Deskripsi banner kanan..."
                    data-preview="b2sub">{{ old('banner2_subtitle', $settings['banner2_subtitle'] ?? '') }}</textarea>
            </div>

            <div class="hs-field">
                <label class="hs-label">Gambar Background Banner Kanan</label>
                @if(!empty($settings['banner2_image']))
                <div class="hs-current-img">
                    <img src="{{ $settings['banner2_image'] }}" alt="Banner 2">
                    <div class="hs-current-img-label">Gambar aktif</div>
                    <form action="{{ route('admin.home-setting.remove-image') }}"
                        method="POST" class="hs-remove-form">
                        @csrf @method('DELETE')
                        <input type="hidden" name="key" value="banner2_image">
                        <button type="submit" class="hs-btn-remove"
                            onclick="return confirm('Hapus gambar banner kanan?')">
                            <i class="bi bi-trash"></i> Hapus Gambar
                        </button>
                    </form>
                </div>
                @endif
                <div class="hs-upload-zone" id="banner2_zone">
                    <input type="file" name="banner2_image" accept="image/*"
                        onchange="previewZone(this,'banner2_zone','banner2_preview')">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <span>{{ !empty($settings['banner2_image']) ? 'Ganti gambar' : 'Klik atau drag gambar' }}</span>
                    <span class="hs-upload-hint">JPG, PNG, WEBP — maks 5MB</span>
                </div>
                <img id="banner2_preview" class="hs-new-preview" src="" alt="" style="display:none;">
                @error('banner2_image')<div class="hs-error">{{ $message }}</div>@enderror
            </div>

            {{-- Live preview teks --}}
            <div class="hs-preview" id="banner2_card"
                style="{{ !empty($settings['banner2_image']) ? 'background-image:url('.$settings['banner2_image'].');background-size:cover;background-position:center;' : 'background:#0d2818;' }}">
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
