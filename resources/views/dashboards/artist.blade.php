@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 text-gray-800 p-6">

    {{-- Header --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-indigo-600">Dashboard Artist</h1>
        <div class="flex items-center space-x-3">
            <img src="{{ $artist->avatar ?? asset('images/default-avatar.png') }}" class="w-10 h-10 rounded-full object-cover">
            <span>{{ $artist->name }}</span>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="bg-white p-5 rounded-xl text-center shadow">
            <p class="text-gray-500 text-sm">Komisi Aktif</p>
            <h3 class="text-2xl font-bold text-indigo-600">{{ $activeCommissions ?? 0 }}</h3>
        </div>

        <div class="bg-white p-5 rounded-xl text-center shadow">
            <p class="text-gray-500 text-sm">Pendapatan</p>
            <h3 class="text-2xl font-bold text-indigo-600">Rp{{ number_format($totalEarnings ?? 0,0,',','.') }}</h3>
        </div>

        <div class="bg-white p-5 rounded-xl text-center shadow">
            <p class="text-gray-500 text-sm">Rating</p>
            <h3 class="text-2xl font-bold text-yellow-500">{{ $averageRating ? number_format($averageRating,1).'★' : 'Belum ada' }}</h3>
        </div>
    </div>

    {{-- Portofolio --}}
    <div class="mb-10">
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-xl font-semibold">Portofolio</h2>
            <button onclick="document.getElementById('addArtworkModal').classList.remove('hidden')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                Tambah Karya
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($artworks as $art)
                <div class="bg-white rounded-xl shadow hover:shadow-md transition duration-200">
                    <img src="{{ $art->preview_url ? asset('storage/'.$art->preview_url) : asset('images/default-art.png') }}" 
                         class="rounded-t-xl w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-semibold text-lg">{{ $art->title }}</h3>
                        <p class="text-sm text-gray-500 mb-2">{{ $art->description }}</p>
                        <p class="text-indigo-600 font-bold">Rp{{ number_format($art->price,0,',','.') }}</p>
                        <div class="flex justify-end space-x-3 mt-3">
                            <form method="POST" action="{{ route('artist.artwork.destroy',$art->artwork_id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Commission Services --}}
    <div>
        <div class="flex justify-between items-center mb-5">
            <h2 class="text-xl font-semibold">Commission Services</h2>
            <button onclick="document.getElementById('addServiceModal').classList.remove('hidden')"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                Tambah Service
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($services as $service)
                <div class="bg-white rounded-xl shadow p-4">
                    <h3 class="font-semibold">{{ $service->title }}</h3>
                    <p class="text-sm text-gray-500 mb-2">{{ $service->description }}</p>
                    <p class="text-indigo-600 font-bold">Rp{{ number_format($service->price,0,',','.') }}</p>
                    <div class="flex justify-end space-x-3 mt-2">
                        <form method="POST" action="{{ route('artist.service.destroy',$service->service_id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline text-sm">Hapus</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>

{{-- Modals (hidden by default) --}}
<div id="addArtworkModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
        <h2 class="text-xl font-bold mb-4">Tambah Karya Baru</h2>
        <form action="{{ route('artist.artwork.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="text" name="title" placeholder="Judul" class="w-full border rounded-md px-3 py-2">
            <textarea name="description" placeholder="Deskripsi" class="w-full border rounded-md px-3 py-2"></textarea>
            <input type="number" name="price" placeholder="Harga" class="w-full border rounded-md px-3 py-2">
            <input type="file" name="file" class="w-full border rounded-md px-3 py-2">
            <input type="file" name="preview" class="w-full border rounded-md px-3 py-2">
            <div class="flex justify-end space-x-2 pt-3">
                <button type="button" onclick="document.getElementById('addArtworkModal').classList.add('hidden')" class="bg-gray-200 px-4 py-2 rounded-md">Batal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="addServiceModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">
        <h2 class="text-xl font-bold mb-4">Tambah Commission Service</h2>
        <form action="{{ route('artist.service.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <input type="text" name="title" placeholder="Judul Service" class="w-full border rounded-md px-3 py-2">
            <textarea name="description" placeholder="Deskripsi" class="w-full border rounded-md px-3 py-2"></textarea>
            <input type="number" name="price" placeholder="Harga" class="w-full border rounded-md px-3 py-2">
            <select name="category_id" class="w-full border rounded-md px-3 py-2">
                <option value="">Pilih Kategori</option>
                @foreach(\App\Models\Category::all() as $cat)
                    <option value="{{ $cat->category_id }}">{{ $cat->name }}</option>
                @endforeach
            </select>
            <input type="file" name="reference_file" class="w-full border rounded-md px-3 py-2">
            <div class="flex justify-end space-x-2 pt-3">
                <button type="button" onclick="document.getElementById('addServiceModal').classList.add('hidden')" class="bg-gray-200 px-4 py-2 rounded-md">Batal</button>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
