@extends('layouts.finance')

@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-4">
            <a href="{{ route('transactions.index') }}"
               class="text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Detail Transaksi</h1>
        </div>

        <!-- Transaction Card -->
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <!-- Transaction Header -->
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <!-- Transaction Icon -->
                        <div class="w-12 h-12 {{ ($transaction->type === 'income' ? 'bg-green-100 text-green-600' :
                                           ($transaction->type === 'expense' ? 'bg-red-100 text-red-600' :
                                           'bg-blue-100 text-blue-600')) }} rounded-full flex items-center justify-center">
                            <i class="fas {{ ($transaction->type === 'income' ? 'fa-arrow-down' :
                                           ($transaction->type === 'expense' ? 'fa-arrow-up' :
                                           'fa-exchange-alt')) }} text-lg"></i>
                        </div>

                        <div>
                            <h2 class="text-lg font-semibold text-gray-900 break-words whitespace-pre-wrap">
                                {{ $transaction->note ?: 'Tanpa Catatan' }}
                            </h2>
                            <p class="text-sm text-gray-500">
                                {{ ($transaction->type === 'income' ? 'Pemasukan' :
                                   ($transaction->type === 'expense' ? 'Pengeluaran' : 'Transfer')) }}
                            </p>
                        </div>
                    </div>

                    <div class="text-right">
                        <p class="text-2xl font-bold {{ ($transaction->type === 'income' ? 'text-green-600' :
                                                         ($transaction->type === 'expense' ? 'text-red-600' :
                                                         'text-blue-600')) }}">
                            {{ ($transaction->type === 'income' ? '+' :
                               ($transaction->type === 'expense' ? '-' : '')) }}
                            Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="px-6 py-4 space-y-4">
                <!-- Account Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">
                            {{ $transaction->type === 'transfer' ? 'Dari Akun' : 'Akun' }}
                        </h3>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-wallet text-xs"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $transaction->account->name }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->account->type_label }}</p>
                            </div>
                        </div>
                    </div>

                    @if($transaction->type === 'transfer' && $transaction->relatedAccount)
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Ke Akun</h3>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                                <i class="fas fa-wallet text-xs"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $transaction->relatedAccount->name }}</p>
                                <p class="text-xs text-gray-500">{{ $transaction->relatedAccount->type_label }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Category Information -->
                @if($transaction->category)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Kategori</h3>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-tag text-xs"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $transaction->category->name }}</p>
                            <p class="text-xs text-gray-500">{{ $transaction->category->type }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Additional Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Tanggal Transaksi</h3>
                        <p class="font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($transaction->date)->format('l, d F Y') }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($transaction->date)->format('H:i') }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">ID Transaksi</h3>
                        <p class="font-medium text-gray-900">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</p>
                        <p class="text-xs text-gray-500">
                            Dibuat {{ $transaction->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                <!-- Note -->
                @if($transaction->note)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Catatan</h3>
                    <p class="text-gray-900">{{ $transaction->note }}</p>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($transaction->user_id === auth()->id())
                        <a href="{{ route('transactions.edit', $transaction->id) }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Transaksi
                        </a>

                        <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-trash mr-2"></i>
                                Hapus
                            </button>
                        </form>
                        @else
                        <span class="text-sm text-gray-500 italic">
                            <i class="fas fa-lock mr-1"></i> Transaksi milik {{ $transaction->user->name ?? 'Anggota Grup' }}
                        </span>
                        @endif
                    </div>

                    <a href="{{ route('transactions.index') }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection