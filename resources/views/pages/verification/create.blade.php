{{-- resources/views/pages/verification/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Ajukan Verifikasi — Aneris')

@section('content')
@vite('resources/css/pages/verification/create.css')

<div class="verif-wrap">
    <div class="verif-card">

        {{-- HEADER --}}
        <div class="verif-header">
            <div class="verif-badge">
                <i class="bi bi-patch-check"></i> Verifikasi Artist
            </div>
            <h1 class="verif-title">Buktikan Karyamu Asli</h1>
            <p class="verif-sub">
                Upload 3–10 gambar portofolio dan minimal 1 link akun sosial media
                yang menampilkan karya kamu. Admin akan mereview dalam 3–5 hari kerja.
            </p>
        </div>

        @if(session('error'))
        <div class="verif-alert error">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="verif-alert error">
            <i class="bi bi-exclamation-circle"></i>
            <ul style="margin:0; padding-left:16px;">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('verification.store') }}"
            enctype="multipart/form-data" id="verif-form">
            @csrf

            {{-- ── SECTION 1: FILE PORTOFOLIO ── --}}
            <div class="form-section">
                <div class="section-head">
                    <div class="section-num">1</div>
                    <div>
                        <div class="section-label">
                            Gambar Portofolio
                            <span class="section-required">Wajib · min 3 file</span>
                        </div>
                        <p class="section-hint">
                            Upload hasil karya kamu dalam format gambar apapun —
                            JPG, PNG, WEBP, GIF, dan lainnya.
                            Maks 10 file, ukuran maks 20MB per file.
                        </p>
                    </div>
                </div>

                <div class="file-drop-zone" id="fileDropZone">
                    <input type="file" name="portfolio_files[]" id="portfolioFiles"
                        multiple
                        accept="image/*"
                        style="display:none">
                    <i class="bi bi-images file-drop-icon"></i>
                    <div class="file-drop-text">Drag & drop gambar di sini</div>
                    <div class="file-drop-sub">atau</div>
                    <button type="button" class="btn-browse" id="btnBrowse">
                        <i class="bi bi-folder2-open"></i> Pilih File
                    </button>
                    <div class="file-drop-formats">
                        JPG · PNG · WEBP · GIF · BMP · HEIC · TIFF · dan lainnya
                    </div>
                </div>

                <div class="file-list" id="fileList"></div>
                <div class="file-count-info" id="fileCountInfo"></div>
            </div>

            {{-- ── SECTION 2: LINK SOSIAL MEDIA ── --}}
            <div class="form-section">
                <div class="section-head">
                    <div class="section-num">2</div>
                    <div>
                        <div class="section-label">
                            Link Sosial Media
                            <span class="section-required">Wajib · min 1 link</span>
                        </div>
                        <p class="section-hint">
                            Masukkan link profil atau postingan sosial media yang menampilkan
                            karya kamu. Bisa Instagram, TikTok, X/Twitter, YouTube, Pixiv,
                            DeviantArt, atau platform lainnya.
                        </p>
                    </div>
                </div>

                {{-- INFO BOX: mengapa sosmed diminta --}}
                <div class="why-box">
                    <div class="why-box-head">
                        <i class="bi bi-info-circle-fill"></i>
                        Mengapa kami meminta link sosial media?
                    </div>
                    <p class="why-box-body">
                        Sosial media membantu admin memverifikasi bahwa karya yang kamu upload
                        benar-benar milikmu. Tanpa bukti eksternal, sulit membedakan karya asli
                        dari hasil repost atau AI-generated.
                    </p>
                    <div class="why-box-checks">
                        <div class="why-check">
                            <i class="bi bi-check2-circle"></i>
                            <div>
                                <strong>Usia akun</strong> — Akun yang sudah aktif lebih dari
                                1 bulan dan memiliki riwayat posting karya jauh lebih dipercaya
                                dibanding akun baru.
                            </div>
                        </div>
                        <div class="why-check">
                            <i class="bi bi-check2-circle"></i>
                            <div>
                                <strong>Timelapse & WIP</strong> — Admin akan memeriksa apakah
                                kamu pernah memposting proses menggambar, timelapse, atau
                                foto/video WIP (work-in-progress) yang cocok dengan karya
                                yang diupload.
                            </div>
                        </div>
                        <div class="why-check">
                            <i class="bi bi-check2-circle"></i>
                            <div>
                                <strong>Konsistensi style</strong> — Karya di sosial media
                                dan portofolio yang kamu upload harus memiliki style yang
                                konsisten satu sama lain.
                            </div>
                        </div>
                        <div class="why-check">
                            <i class="bi bi-check2-circle"></i>
                            <div>
                                <strong>Interaksi nyata</strong> — Akun dengan interaksi
                                organik (komentar dari sesama artist, pelanggan, dll) lebih
                                meyakinkan dibanding akun yang sepi atau baru saja dibuat.
                            </div>
                        </div>
                    </div>
                    <div class="why-box-tip">
                        <i class="bi bi-lightbulb"></i>
                        <strong>Tips:</strong> Link ke postingan timelapse atau WIP spesifik
                        jauh lebih kuat daripada hanya link ke profil.
                    </div>
                </div>

                <div id="socialLinks">
                    <div class="social-link-row" data-index="0">
                        <div class="social-link-platform" id="platform_0">
                            <i class="bi bi-link-45deg"></i>
                        </div>
                        <input type="url" name="social_media_links[]"
                            placeholder="https://instagram.com/username"
                            class="social-input"
                            value="{{ old('social_media_links.0') }}"
                            oninput="detectPlatform(this, 0)">
                        <button type="button" class="btn-remove-link" style="display:none"
                            onclick="removeLink(this)">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>

                <button type="button" class="btn-add-link" id="btnAddLink">
                    <i class="bi bi-plus-circle"></i> Tambah Link Lain
                </button>

                <div class="supported-platforms">
                    <span class="platforms-label">Platform yang didukung:</span>
                    <span class="platform-chip"><i class="bi bi-instagram"></i> Instagram</span>
                    <span class="platform-chip"><i class="bi bi-tiktok"></i> TikTok</span>
                    <span class="platform-chip"><i class="bi bi-twitter-x"></i> X/Twitter</span>
                    <span class="platform-chip"><i class="bi bi-youtube"></i> YouTube</span>
                    <span class="platform-chip"><i class="bi bi-image"></i> Pixiv</span>
                    <span class="platform-chip"><i class="bi bi-palette"></i> DeviantArt</span>
                    <span class="platform-chip"><i class="bi bi-brush"></i> ArtStation</span>
                    <span class="platform-chip"><i class="bi bi-plus"></i> Platform lain</span>
                </div>
            </div>

            {{-- ── DECLARATION ── --}}
            <div class="form-section">
                <label class="declaration-box" id="declarationBox">
                    <input type="checkbox" name="declaration" value="1"
                        id="declarationCheck"
                        {{ old('declaration') ? 'checked' : '' }}
                        onchange="toggleDeclare(this)">
                    <div class="declaration-text">
                        <strong>Pernyataan Keaslian</strong>
                        <span>
                            Saya menyatakan bahwa semua karya yang diupload adalah hasil
                            karya saya sendiri, bukan hasil AI-generated, bukan repost dari
                            karya orang lain, dan link sosial media yang saya berikan adalah
                            akun yang benar-benar saya miliki.
                        </span>
                    </div>
                </label>
                @error('declaration')
                <div class="field-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── SUBMIT ── --}}
            <button type="submit" class="btn-submit" id="btnSubmit" disabled>
                <span id="submitText">
                    <i class="bi bi-send"></i> Kirim untuk Diverifikasi
                </span>
                <span id="submitLoading" style="display:none">
                    <i class="bi bi-arrow-clockwise spin"></i> Mengupload...
                </span>
            </button>

            <p class="submit-note">
                Setelah submit, kamu tetap bisa upload artwork.
                Fitur commission akan terbuka setelah verifikasi disetujui.
            </p>

        </form>
    </div>
</div>

@vite('resources/js/pages/verification/create.js')
@endsection
