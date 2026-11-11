@extends('layouts.app')

@section('title', 'Profil Saya')
@section('header-title', 'Profil Saya')

@section('content')
    @php
        $photoUrl = $user->foto_profile ? \Illuminate\Support\Facades\Storage::disk('public')->url($user->foto_profile) : null;
        $initial = \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1));
    @endphp

    <div class="mx-auto max-w-5xl space-y-6">
        <section class="flex flex-col items-center gap-6 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 p-8 text-white shadow-lg sm:flex-row">
            @if ($photoUrl)
                <img src="{{ $photoUrl }}" alt="Foto profil {{ $user->name }}" class="h-24 w-24 rounded-2xl border-4 border-white/20 object-cover">
            @else
                <div class="flex h-24 w-24 items-center justify-center rounded-2xl border-4 border-white/20 bg-white/10 text-3xl font-bold">
                    {{ $initial }}
                </div>
            @endif

            <div class="flex-1 text-center sm:text-left">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-200">Akun Saya</p>
                <h2 class="mt-2 text-3xl font-bold">{{ $user->name }}</h2>
                <p class="mt-1 text-indigo-100">{{ '@' . $user->username }}</p>
                <span class="mt-4 inline-flex rounded-full bg-white/15 px-3 py-1 text-sm font-semibold capitalize">
                    {{ $user->role }}
                </span>
            </div>

            <a href="#informasi-profil" class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-800 transition hover:bg-indigo-50">
                <i class="fa-regular fa-pen-to-square mr-2"></i>Edit Profil
            </a>
        </section>

        <section id="informasi-profil" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <i class="fa-regular fa-address-card"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Informasi Profil</h3>
                    <p class="text-sm text-slate-500">Informasi akun dan kontak Anda.</p>
                </div>
            </div>

            @if (session('status'))
                <div class="mb-5 rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="foto_profile" class="mb-2 block text-sm font-semibold text-slate-700">Foto Profil</label>
                    <input id="foto_profile" name="foto_profile" type="file" accept=".jpg,.jpeg,.png"
                           class="block w-full rounded-xl border border-slate-200 bg-slate-50 text-sm text-slate-600 file:mr-4 file:border-0 file:bg-indigo-50 file:px-4 file:py-3 file:font-semibold file:text-indigo-700">
                    <p class="mt-2 text-xs text-slate-500">JPG atau PNG, maksimal 2 MB.</p>
                    @error('foto_profile')
                        <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-slate-700">Nama Lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                        @error('name')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="username" class="mb-2 block text-sm font-semibold text-slate-700">Username</label>
                        <input id="username" name="username" type="text" value="{{ old('username', $user->username) }}" required maxlength="30"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                        <p class="mt-2 text-xs text-slate-500">Gunakan huruf, angka, garis bawah, atau tanda hubung.</p>
                        @error('username')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                        @error('email')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="no_hp" class="mb-2 block text-sm font-semibold text-slate-700">Nomor Telepon</label>
                        <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', $user->no_hp) }}" maxlength="20"
                               class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                        @error('no_hp')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="alamat" class="mb-2 block text-sm font-semibold text-slate-700">Alamat</label>
                        <textarea id="alamat" name="alamat" rows="3" maxlength="2000"
                                  class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')
                            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-100 pt-5">
                    <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-indigo-700">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
