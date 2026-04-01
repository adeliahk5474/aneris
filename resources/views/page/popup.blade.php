@extends('layouts.app')

@section('content')

<style>
    .popup-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.25);
        backdrop-filter: blur(3px);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 2000;
    }
    .popup-card {
        width: 95%;
        max-width: 500px;
        background: #ffffff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        animation: fadeIn .2s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: scale(.97); }
        to   { opacity: 1; transform: scale(1); }
    }
    .active-tab {
        background: #4f46e5 !important;
        color: #fff !important;
        border-color: #4f46e5 !important;
    }
</style>

<div class="popup-overlay">

    <div class="popup-card">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-dark m-0">Upload</h4>

        <a href="javascript:history.back();" 
        style="padding:6px; border-radius:50%; background:#f3f3f3; display:flex;"
        class="text-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M6 18L18 6M6 6l12 12" />
            </svg>
        </a>

        </div>

        {{-- Tab --}}
        <div class="d-flex gap-2 mb-4">
            <button id="tabArtwork"
                class="flex-fill py-2 rounded border border-primary fw-semibold active-tab">
                Artwork
            </button>

            @if(Auth::user()->role === 'artist')
            <button id="tabCommission"
                class="flex-fill py-2 rounded border border-primary fw-semibold">
                Commission
            </button>
            @endif
        </div>

        {{-- Dynamic Form --}}
        <div id="formArea"></div>

    </div>

</div>

<script>
    const role = "{{ Auth::user()->role }}";
    const formArea = document.getElementById("formArea");
    const categories = @json($categories);

const artworkFormHTML = `
    <form action="{{ route('upload.artwork') }}" 
          method="POST" enctype="multipart/form-data" class="space-y-3">
        @csrf

        <div class="mb-3">
            <label class="fw-semibold">Image</label>
            <input type="file" name="image" class="form-control mt-1" required>
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Caption</label>
            <textarea name="title" rows="3" 
                      class="form-control mt-1" placeholder="Write a caption..."></textarea>
        </div>

        <div class="mb-3">
            <label class="fw-semibold">Category</label>
            <select name="category_id" class="form-control mt-1" required>
                <option value="">-- Select Category --</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            Post Artwork
        </button>
    </form>
`;


    const commissionFormHTML = `
        <form action="{{ route('upload.commission') }}" 
            method="POST" enctype="multipart/form-data" class="space-y-3">
            @csrf

            <div class="mb-3">
                <label class="fw-semibold">Cover Image</label>
                <input type="file" name="image" class="form-control mt-1" required>
            </div>

            <div class="mb-3">
                <label class="fw-semibold">Category</label>
                <select name="category_id" class="form-control mt-1" required>
                    ${categories.map(c => `<option value="${c.category_id}">${c.name}</option>`).join('')}
                </select>
            </div>

            <div class="mb-3">
                <label class="fw-semibold">Title</label>
                <input type="text" name="title" class="form-control mt-1" placeholder="Commission title" required>
            </div>

            <div class="mb-3">
                <label class="fw-semibold">Price</label>
                <input type="number" name="price" class="form-control mt-1" placeholder="Price" required>
            </div>

            <div class="mb-3">
                <label class="fw-semibold">Description</label>
                <textarea name="description" rows="4"
                        class="form-control mt-1" placeholder="Describe the service..." required></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100">
                Publish Commission
            </button>
        </form>
    `;


    // Default (Artwork)
    formArea.innerHTML = artworkFormHTML;

    const tabArtwork = document.getElementById("tabArtwork");
    const tabCommission = document.getElementById("tabCommission");

    tabArtwork.onclick = () => {
        tabArtwork.classList.add("active-tab");
        if(tabCommission) tabCommission.classList.remove("active-tab");
        formArea.innerHTML = artworkFormHTML;
    };

    if (role === "artist" && tabCommission) {
        tabCommission.onclick = () => {
            tabCommission.classList.add("active-tab");
            tabArtwork.classList.remove("active-tab");
            formArea.innerHTML = commissionFormHTML;
        };
    }
</script>

@endsection
