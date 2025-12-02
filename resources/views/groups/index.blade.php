@extends('layouts.finance')

@section('title', 'Grup Keluarga')
@section('page-title', 'Grup Keluarga')

@section('content')
<!-- Header Section -->
<div class="mb-6">
    <div class="flex items-center">
        <div class="mr-4 bg-indigo-100 rounded-full p-3">
            <i class="fas fa-users text-indigo-600 text-xl"></i>
        </div>
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Grup Keluarga</h2>
            <p class="mt-1 text-sm text-gray-600">Kelola keuangan bersama keluarga atau pasangan dalam satu grup.</p>
        </div>
    </div>
</div>

<!-- Loading State -->
<div id="loadingState" class="bg-white rounded-xl shadow-sm p-8 mb-6">
    <div class="flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"></div>
        <p class="mt-4 text-gray-600 font-medium">Memuat informasi grup...</p>
    </div>
</div>

<!-- No Group State -->
<div id="noGroupState" class="hidden">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Create Group Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="p-6 border-b border-gray-50 bg-gradient-to-r from-indigo-50 to-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-plus text-indigo-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Buat Grup Baru</h3>
                        <p class="text-sm text-gray-500">Mulai kelola keuangan bersama</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-6 text-sm leading-relaxed">
                    Buat grup baru dan jadilah admin. Anda akan mendapatkan kode undangan unik untuk mengajak anggota keluarga lainnya bergabung.
                </p>

                <form id="createGroupForm" class="space-y-4">
                    <div>
                        <label for="groupName" class="block text-sm font-medium text-gray-700 mb-1">Nama Grup</label>
                        <input type="text" id="groupName" required placeholder="Contoh: Keluarga Cemara"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2.5 border">
                    </div>
                    <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-plus-circle mr-2"></i> Buat Grup
                    </button>
                </form>
            </div>
        </div>

        <!-- Join Group Card -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
            <div class="p-6 border-b border-gray-50 bg-gradient-to-r from-blue-50 to-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center shadow-sm">
                        <i class="fas fa-user-plus text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Gabung Grup</h3>
                        <p class="text-sm text-gray-500">Masuk ke grup yang sudah ada</p>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-6 text-sm leading-relaxed">
                    Sudah punya kode undangan? Masukkan kode tersebut di bawah ini untuk bergabung dengan grup keluarga Anda.
                </p>

                <form id="joinGroupForm" class="space-y-4">
                    <div>
                        <label for="groupCode" class="block text-sm font-medium text-gray-700 mb-1">Kode Undangan</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-400"></i>
                            </div>
                            <input type="text" id="groupCode" required placeholder="GRP-XXXXXX"
                                   class="block w-full pl-10 rounded-lg border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm p-2.5 border uppercase tracking-wider font-mono">
                        </div>
                    </div>
                    <button type="submit" class="w-full flex justify-center items-center px-4 py-2.5 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i> Gabung Grup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Active Group State -->
<div id="activeGroupState" class="hidden space-y-6">
    <!-- Group Info Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 sm:p-8 bg-gradient-to-r from-indigo-600 to-blue-600">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2 text-white">
                        <h2 class="text-3xl font-bold" id="displayGroupName">Nama Grup</h2>
                        <span class="px-3 py-1 rounded-full bg-white/20 text-xs font-medium backdrop-blur-sm">
                            <i class="fas fa-shield-alt mr-1"></i> Private Group
                        </span>
                    </div>
                    <p class="text-indigo-100 text-sm">Dibuat pada <span id="createdDate">-</span></p>
                </div>

                <div class="flex flex-col items-end gap-2 w-full md:w-auto">
                    <p class="text-xs text-indigo-200 uppercase tracking-wider font-semibold">Kode Undangan</p>
                    <div class="flex items-center bg-white/10 rounded-lg p-1 pl-4 backdrop-blur-sm border border-white/20">
                        <span class="font-mono text-xl font-bold tracking-widest mr-3" id="displayGroupCode">CODE</span>
                        <button onclick="copyCode()" class="p-2 hover:bg-white/20 rounded-md transition-colors text-white" title="Salin Kode">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Bar -->
        <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100 bg-gray-50">
            <div class="p-4 text-center">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Total Anggota</p>
                <p class="text-2xl font-bold text-gray-900" id="memberCount">0</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Admin</p>
                <p class="text-sm font-semibold text-gray-900 mt-1" id="adminName">-</p>
            </div>
            <div class="p-4 text-center">
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-1">Status</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                    Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- Members List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                <i class="fas fa-users text-gray-400"></i> Anggota Grup
            </h3>
            <button onclick="leaveGroup()" class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center gap-2 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors border border-transparent hover:border-red-200">
                <i class="fas fa-sign-out-alt"></i> Keluar Grup
            </button>
        </div>
        <div class="divide-y divide-gray-100" id="membersList">
            <!-- Members will be inserted here -->
        </div>
    </div>

    <!-- Info Box -->
    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100 flex gap-4 items-start">
        <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
            <i class="fas fa-info text-blue-600"></i>
        </div>
        <div>
            <h4 class="text-sm font-bold text-blue-900">Informasi Sinkronisasi Data</h4>
            <p class="text-sm text-blue-700 mt-1 leading-relaxed">
                Sebagai anggota grup, <strong>Total Saldo</strong> dan <strong>Riwayat Transaksi</strong> Anda akan digabungkan dengan anggota lain di halaman Dashboard dan Laporan. Namun, Anda hanya dapat mengedit atau menghapus transaksi yang Anda buat sendiri.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Helper Functions
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all transform duration-300 ${
        type === 'success' ? 'bg-green-100 border-l-4 border-green-500 text-green-700' :
        'bg-red-100 border-l-4 border-red-500 text-red-700'
    }`;
    alertDiv.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-3"></i>
            <p class="font-medium">${message}</p>
        </div>
    `;
    document.body.appendChild(alertDiv);
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

function copyCode() {
    const code = document.getElementById('displayGroupCode').textContent;
    
    // Fallback for non-HTTPS (local development)
    const textArea = document.createElement('textarea');
    textArea.value = code;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    document.body.appendChild(textArea);
    textArea.select();
    
    try {
        document.execCommand('copy');
        showAlert('success', 'Kode berhasil disalin!');
    } catch (err) {
        showAlert('error', 'Gagal menyalin kode');
    }
    
    document.body.removeChild(textArea);
}

// API Functions
async function loadGroupInfo() {
    try {
        document.getElementById('loadingState').classList.remove('hidden');
        document.getElementById('noGroupState').classList.add('hidden');
        document.getElementById('activeGroupState').classList.add('hidden');

        const response = await axios.get('{{ route("api.groups.info") }}');

        if (response.data.success && response.data.data) {
            displayGroupInfo(response.data.data);
        } else {
            document.getElementById('noGroupState').classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error loading group:', error);
        // If error or no group found (and api returns error for no group?), show no group state
        document.getElementById('noGroupState').classList.remove('hidden');
    } finally {
        document.getElementById('loadingState').classList.add('hidden');
    }
}

function displayGroupInfo(group) {
    document.getElementById('noGroupState').classList.add('hidden');
    document.getElementById('activeGroupState').classList.remove('hidden');

    document.getElementById('displayGroupName').textContent = group.name;
    document.getElementById('displayGroupCode').textContent = group.code;
    document.getElementById('memberCount').textContent = group.users.length;
    document.getElementById('createdDate').textContent = new Date(group.created_at).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });

    const list = document.getElementById('membersList');
    list.innerHTML = '';

    const currentUserId = {{ Auth::id() }}; // Blade variable

    // Find admin name
    const admin = group.users.find(u => u.id === group.created_by);
    document.getElementById('adminName').textContent = admin ? admin.name : 'Unknown';

    group.users.forEach(user => {
        const div = document.createElement('div');
        div.className = 'p-4 flex items-center justify-between hover:bg-gray-50';
        div.innerHTML = `
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 font-bold">
                    ${user.name.charAt(0).toUpperCase()}
                </div>
                <div>
                    <p class="font-medium text-gray-900 flex items-center gap-2">
                        ${user.name}
                        ${user.id === group.created_by ? '<span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Admin</span>' : ''}
                        ${user.id === currentUserId ? '<span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">Anda</span>' : ''}
                    </p>
                    <p class="text-sm text-gray-500">${user.email}</p>
                </div>
            </div>
            <div class="text-sm text-gray-400">
                Bergabung ${new Date(user.created_at).toLocaleDateString('id-ID')}
            </div>
        `;
        list.appendChild(div);
    });
}

// Event Listeners
document.addEventListener('DOMContentLoaded', () => {
    loadGroupInfo();

    document.getElementById('createGroupForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = document.getElementById('groupName').value;
        try {
            const response = await axios.post('{{ route("api.groups.create") }}', { name });
            if (response.data.success) {
                showAlert('success', 'Grup berhasil dibuat!');
                loadGroupInfo();
            }
        } catch (error) {
            showAlert('error', error.response?.data?.message || 'Gagal membuat grup');
        }
    });

    document.getElementById('joinGroupForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const code = document.getElementById('groupCode').value;
        try {
            const response = await axios.post('{{ route("api.groups.join") }}', { code });
            if (response.data.success) {
                showAlert('success', 'Berhasil bergabung ke grup!');
                loadGroupInfo();
            }
        } catch (error) {
            showAlert('error', error.response?.data?.message || 'Gagal bergabung (Kode salah?)');
        }
    });
});

window.leaveGroup = async function() {
    if (confirm('Apakah Anda yakin ingin keluar dari grup ini? Anda tidak akan bisa melihat data grup lagi.')) {
        try {
            const response = await axios.post('{{ route("api.groups.leave") }}');
            if (response.data.success) {
                showAlert('success', 'Berhasil keluar grup');
                loadGroupInfo();
            }
        } catch (error) {
            showAlert('error', 'Gagal keluar grup');
        }
    }
};
</script>
@endpush
