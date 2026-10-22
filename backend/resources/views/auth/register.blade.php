<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sistem Peminjaman Alat</title>
    <!-- Memuat Tailwind CSS melalui CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">

    <!-- Card Container -->
    <div class="bg-white p-8 rounded-lg shadow-md w-96">
        <h3 class="text-2xl font-bold text-center text-gray-800 mb-6">Register Peminjam</h3>

        <!-- Alert Error Session -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                {{ session('success') }}
            </div>
        @endif

        <!-- Alert Error Validasi -->
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm">
                <ul class="list-disc pl-5 mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST">
            @csrf

            <!-- Input Nama -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap</label>
                <input type="text" name="name" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Input Email -->
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Input Password -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Input Konfirmasi Password -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-semibold mb-2">Konfirmasi Password</label>
                <input type="password" name="password.confirmation" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Tombol Submit -->
            <button type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                Daftar
            </button>

            <!-- Link ke halaman login -->
             <p class = "text-center text-sm text-gray-600 mt-5">
                Sudah punya akun?
                <a href = "{{route('login')}}"
                class = "text-blue-600 font-semibold hover:text-blue-700">
                    Masuk di sini
                </a>
            </p>

        </form>
    </div>

</body>
</html>