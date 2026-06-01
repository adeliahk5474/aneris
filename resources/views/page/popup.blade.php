{{-- resources/views/page/popup.blade.php --}}
@extends('layouts.app')
@section('title', 'Upload — Aneris')
@section('content')
@vite('resources/css/page/popup.css')

<div class="popup-wrap"
    data-switch-commission="{{ $switchToCommissionTab ? 'true' : 'false' }}"
    data-is-verified="{{ $isVerified ? 'true' : 'false' }}"
    data-portfolio-url="{{ route('artist.dashboard') }}?tab=portfolio">

    @if(session('success'))
    <div class="flash-ok"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif
    @if(session('info'))
    <div class="flash-info" style="background:rgba(108,108,255,.12);border:1px solid rgba(108,108,255,.25);color:#a5a5ff;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;display:flex;align-items:center;gap:8px;">
        <i class="bi bi-info-circle-fill"></i> {{ session('info') }}
    </div>
    @endif
    @if($errors->any())
    <div class="flash-err">
        <i class="bi bi-exclamation-circle-fill"></i>
        <div>@foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
    </div>
    @endif

    <div class="tab-row">
        <button class="tab-btn active" onclick="switchTab('artwork',this)" type="button">
            <i class="bi bi-image"></i> Post Artwork
        </button>
        @if($isArtist)
        <button class="tab-btn {{ !$isVerified ? 'tab-locked' : '' }}"
            onclick="{{ $isVerified ? 'switchTab(\'commission\',this)' : 'redirectToVerif()' }}"
            type="button"
            title="{{ !$isVerified ? 'Verifikasi portfolio dulu untuk membuka fitur ini' : '' }}">
            <i class="bi bi-briefcase-fill"></i> New Commission Service
            @if(!$isVerified)
            <i class="bi bi-lock-fill" style="font-size:11px; margin-left:4px; opacity:.7;"></i>
            @endif
        </button>
        @endif
    </div>

    {{-- ══ ARTWORK PANEL ══ --}}
    <div id="panel-artwork" class="panel active">
        <form action="{{ route('upload.artwork') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="scard">
                <div class="stitle"><span class="stitle-left"><i class="bi bi-image"></i> Upload Gambar</span></div>
                <div class="fg">
                    <div class="upload-slot" style="min-height:220px;" id="art-zone">
                        <input type="file" name="image" id="art-f" accept="image/*" required onchange="previewSlot(this,'art-zone')">
                        <div class="slot-ph">
                            <div class="slot-icon"><i class="bi bi-cloud-upload"></i></div>
                            <div class="slot-text">Klik atau drag untuk upload<br><small style="opacity:.6;font-size:11px">PNG · JPG · WEBP · Maks 8MB</small></div>
                        </div>
                        <button type="button" class="slot-clear" onclick="clearSlot(event,'art-zone','art-f')"><i class="bi bi-x"></i></button>
                    </div>
                </div>
                <div class="fg">
                    <label class="fl">Kategori <span>(opsional)</span></label>
                    <div class="cat-pills">
                        @foreach($categories as $cat)
                        <input type="radio" name="category_id" id="ac_{{ $cat->category_id }}" value="{{ $cat->category_id }}" class="cat-r">
                        <label for="ac_{{ $cat->category_id }}" class="cat-l">{{ $cat->name }}</label>
                        @endforeach
                    </div>
                </div>
                <div class="fg">
                    <label class="fl">Caption <span>(opsional)</span></label>
                    <textarea name="caption" class="fta" placeholder="Ceritakan karya ini..." style="min-height:80px;" maxlength="500" oninput="countChars(this,'art-cap-count')"></textarea>
                    <div class="char-counter"><span id="art-cap-count">0</span>/500</div>
                </div>
            </div>
            <div class="submit-row">
                <a href="{{ url()->previous() }}" class="btn-c"><i class="bi bi-arrow-left"></i> Batal</a>
                <button type="submit" class="btn-p"><i class="bi bi-send-fill"></i> Publikasikan</button>
            </div>
        </form>
    </div>

    {{-- ══ COMMISSION PANEL ══ --}}
    @if($isArtist)

    @if(!$isVerified)
    <div id="panel-commission" class="panel" style="display:none;">
        <div style="text-align:center; padding:60px 24px;">
            <div style="font-size:48px; margin-bottom:16px;">🔒</div>
            <div style="font-size:18px; font-weight:700; color:var(--text); margin-bottom:10px;">
                Verifikasi Portfolio Dulu
            </div>
            <div style="font-size:14px; color:var(--muted); max-width:380px; margin:0 auto 24px;">
                Untuk membuka commission service, kamu perlu mendapat badge
                <strong style="color:var(--accent);">Verified Non-AI</strong> terlebih dahulu.
                Upload portofoliomu dan tim kami akan mereview dalam 3–5 hari kerja.
            </div>
            <a href="{{ route('artist.dashboard') }}?tab=portfolio"
                style="display:inline-flex; align-items:center; gap:8px; background:var(--accent); color:#fff; padding:12px 28px; border-radius:10px; font-size:14px; font-weight:600; text-decoration:none; transition:opacity .2s;"
                onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                <i class="bi bi-patch-check"></i> Ajukan Verifikasi Portfolio
            </a>
            <div style="margin-top:16px;">
                <a href="{{ url()->previous() }}" style="font-size:13px; color:var(--muted); text-decoration:none;">
                    ← Kembali
                </a>
            </div>
        </div>
    </div>

    @else
    <div id="panel-commission" class="panel">
        <form action="{{ route('upload.commission') }}" method="POST" enctype="multipart/form-data" id="commForm">
            @csrf
            <input type="hidden" name="status" id="commStatus" value="active">

            <div class="scard">
                <div class="stitle">
                    <span class="stitle-left"><i class="bi bi-images"></i> Cover & Gallery</span>
                    <span class="preview-badge"><i class="bi bi-eye"></i> Cover + 3 Gallery</span>
                </div>
                <div class="img-upload-section">
                    <div class="upload-slot cover-main" id="cov0">
                        <input type="file" name="image" id="cov0f" accept="image/*" required onchange="previewSlot(this,'cov0')">
                        <div class="slot-ph">
                            <div class="slot-icon"><i class="bi bi-image-fill"></i></div>
                            <div class="slot-text">
                                <strong style="color:var(--text);font-size:14px;">Cover Thumbnail</strong><br>
                                <small style="opacity:.6;font-size:11px">Vertical 4:5 works best · Max 10MB</small>
                            </div>
                        </div>
                        <span class="gallery-slot-label">Cover *</span>
                        <button type="button" class="slot-clear" onclick="clearSlot(event,'cov0','cov0f')"><i class="bi bi-x"></i></button>
                    </div>
                    <div class="gallery-slots">
                        @foreach([1,2,3] as $gi)
                        <div class="upload-slot" id="cov{{ $gi }}" style="{{ $gi===3 ? 'grid-column:1/3;' : '' }}">
                            <input type="file" name="gallery[]" id="cov{{ $gi }}f" accept="image/*" onchange="previewSlot(this,'cov{{ $gi }}')">
                            <div class="slot-ph">
                                <div class="slot-icon" style="font-size:18px;"><i class="bi bi-plus-lg"></i></div>
                                <div class="slot-text" style="font-size:11px;">Gallery {{ $gi }}</div>
                            </div>
                            <span class="gallery-slot-label">Gallery {{ $gi }}</span>
                            <button type="button" class="slot-clear" onclick="clearSlot(event,'cov{{ $gi }}','cov{{ $gi }}f')"><i class="bi bi-x"></i></button>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="scard">
                <div class="stitle"><span class="stitle-left"><i class="bi bi-info-circle-fill"></i> Service Info</span></div>

                <div class="fg">
                    <label class="fl" for="st">Service Title <span>*</span></label>
                    <input type="text" id="st" name="title" class="fi"
                        placeholder="e.g. Custom Character Illustration — Full Color"
                        value="{{ old('title') }}" required maxlength="100"
                        oninput="countChars(this,'title-count')">
                    <div class="char-counter"><span id="title-count">{{ $titleCharCount }}</span>/100</div>
                </div>

                <div class="fgrid">
                    <div class="fg">
                        <label class="fl">Category <span>*</span></label>
                        <select name="category_id" class="fsel" required>
                            <option value="">— Select Category —</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->category_id }}" {{ old('category_id')==$cat->category_id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="fg">
                        <label class="fl">Base Price <span>*</span></label>
                        <div class="price-wrap">
                            <span class="price-pfx">Rp</span>
                            <input type="number" name="base_price" placeholder="150,000" min="1000" step="1000" value="{{ old('base_price') }}" required>
                        </div>
                    </div>
                </div>

                <div class="fgrid3">
                    <div class="fg">
                        <label class="fl">Estimated Days <span>*</span></label>
                        <input type="number" name="estimated_days" class="fi"
                            placeholder="7" min="1" max="365"
                            value="{{ old('estimated_days', 7) }}" required>
                    </div>
                    <div class="fg">
                        <label class="fl">Slots Available</label>
                        <input type="number" name="queue_slots" class="fi"
                            placeholder="5" min="1" max="50"
                            value="{{ old('queue_slots', 5) }}">
                    </div>
                    <div class="fg">
                        <label class="fl">Max Revisions</label>
                        <input type="number" name="revision_limit" class="fi"
                            placeholder="2" min="0" max="20"
                            value="{{ old('revision_limit', 2) }}">
                    </div>
                </div>

                <div class="fg">
                    <label class="fl" for="sd">Service Description <span>*</span></label>
                    <textarea id="sd" name="description" class="fta"
                        placeholder="Describe what clients can expect..."
                        required maxlength="2000"
                        oninput="countChars(this,'desc-count')">{{ old('description') }}</textarea>
                    <div class="char-counter"><span id="desc-count">{{ $descCharCount }}</span>/2000</div>
                </div>
            </div>

            <div class="scard">
                <div class="stitle">
                    <span class="stitle-left"><i class="bi bi-plus-circle-fill"></i> Optional Add-ons</span>
                    <button type="button" class="btn-c" style="padding:5px 12px;font-size:11px;" onclick="addAddon()">
                        <i class="bi bi-plus"></i> Add Row
                    </button>
                </div>
                <div class="addon-header">
                    <span class="addon-col-label">Name</span>
                    <span class="addon-col-label">Description</span>
                    <span class="addon-col-label">Price (Rp)</span>
                    <span></span>
                </div>
                <div id="addon-list">
                    <div class="addon-row">
                        <input type="text" name="addons[0][name]" class="afield" placeholder="Commercial License" value="Commercial Use License">
                        <input type="text" name="addons[0][description]" class="afield" placeholder="What's included?" value="Full rights for business use">
                        <div class="apbox"><span class="appfx">Rp</span><input type="number" name="addons[0][price]" class="apinp" placeholder="0" value="500000" min="0" step="1000"></div>
                        <button type="button" class="arm" onclick="removeAddon(this)"><i class="bi bi-trash3"></i></button>
                    </div>
                    <div class="addon-row">
                        <input type="text" name="addons[1][name]" class="afield" placeholder="Source Files" value="Source Files (.PSD / .AI)">
                        <input type="text" name="addons[1][description]" class="afield" placeholder="What's included?" value="Layered working files">
                        <div class="apbox"><span class="appfx">Rp</span><input type="number" name="addons[1][price]" class="apinp" placeholder="0" value="250000" min="0" step="1000"></div>
                        <button type="button" class="arm" onclick="removeAddon(this)"><i class="bi bi-trash3"></i></button>
                    </div>
                </div>
                <button type="button" class="addon-add-btn" onclick="addAddon()">
                    <i class="bi bi-plus-circle"></i> Add Another Add-on
                </button>
            </div>

            <div class="scard">
                <div class="stitle"><span class="stitle-left"><i class="bi bi-list-check"></i> Do & Don't</span></div>
                <div class="gl-grid">
                    <div class="gl-box" style="border-color:rgba(34,197,94,.15);">
                        <div class="gl-head will"><i class="bi bi-check-circle-fill"></i> I WILL DO</div>
                        <textarea name="will_do" class="gl-ta"
                            placeholder="Original Characters&#10;Fan Art (non-commercial)&#10;Environments & Backgrounds&#10;Couples & Group of 2&#10;Anthro / Furry">{{ old('will_do') }}</textarea>
                        <div class="gl-hint"><i class="bi bi-info-circle" style="font-size:11px;"></i> One item per line</div>
                    </div>
                    <div class="gl-box" style="border-color:rgba(239,68,68,.15);">
                        <div class="gl-head wont"><i class="bi bi-x-circle-fill"></i> I WON'T DO</div>
                        <textarea name="wont_do" class="gl-ta"
                            placeholder="NSFW / 18+ content&#10;Complex Mecha / Hard surfaces&#10;Extreme Gore or Violence&#10;Real Person Portraits&#10;Traced artwork">{{ old('wont_do') }}</textarea>
                        <div class="gl-hint"><i class="bi bi-info-circle" style="font-size:11px;"></i> One item per line</div>
                    </div>
                </div>
            </div>

            <div class="submit-row">
                <a href="{{ url()->previous() }}" class="btn-c"><i class="bi bi-arrow-left"></i> Cancel</a>
                <button type="button" class="btn-c" onclick="saveDraft()">
                    <i class="bi bi-floppy"></i> Save as Draft
                </button>
                <button type="submit" class="btn-p">
                    <i class="bi bi-rocket-takeoff-fill"></i> Publish Service
                </button>
            </div>
        </form>
    </div>
    @endif

    @endif
</div>

@vite('resources/js/page/popup.js')
@endsection