<div class="w-full max-w-md bg-white p-10 rounded-2xl shadow-xl border border-gray-100">

    <div class="text-center mb-8">
        <div class="text-5xl mb-3">🍽️</div>
        <h2 class="text-3xl font-bold text-gray-800">Resto POS</h2>
        <p class="text-gray-500 mt-2">Silakan login untuk melanjutkan</p>
    </div>

    @if (session()->has('error'))
        <div class="bg-red-50 text-red-600 border border-red-200 px-4 py-3 rounded-lg relative mb-6 text-center text-sm font-semibold">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="authenticate" class="flex flex-col gap-5">
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Email Pegawai</label>
            <input type="email" wire:model="email" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                   placeholder="admin@resto.com">
        </div>

        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
            <input type="password" wire:model="password" required
                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                   placeholder="••••••••">
        </div>

        <button type="submit" class="w-full bg-gray-900 hover:bg-gray-800 text-white font-bold py-3 px-4 rounded-lg transition duration-200 mt-4 shadow-md">
            Login ke Dashboard
        </button>
    </form>

</div>
