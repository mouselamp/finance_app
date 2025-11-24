@extends('layouts.app')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-0">Analisis Struk Belanja</h2>
            <small class="text-muted">Unggah struk, periksa ringkasannya, lalu simpan sebagai transaksi.</small>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form id="receipt-upload-form" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="receiptInput">Pilih gambar struk (JPG/PNG, maks 5MB)</label>
                    <input type="file" class="form-control" id="receiptInput" name="receipt" accept="image/*" required>
                </div>
                <button type="submit" class="btn btn-primary">Analisis Struk</button>
                <span id="upload-status" class="ml-2 text-muted"></span>
            </form>
        </div>
    </div>

    <div id="analysis-card" class="card d-none mb-4">
        <div class="card-header d-flex justify-content-between">
            <span>Ringkasan AI</span>
            <small id="analysis-updated" class="text-muted"></small>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="border rounded p-2 text-center">
                        <small class="text-muted d-block">Struk</small>
                        <img id="receipt-image-preview" src="" alt="Receipt preview" class="img-fluid mt-2">
                    </div>
                </div>
                <div class="col-md-8">
                    <form id="receipt-save-form">
                        @csrf
                        <input type="hidden" id="receipt-upload-id">
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Merchant</label>
                                <input type="text" id="merchant" class="form-control" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Tipe Transaksi</label>
                                <select id="transaction-type" class="form-control">
                                    <option value="expense">Pengeluaran</option>
                                    <option value="income">Pemasukan</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label>Tanggal</label>
                                <input type="date" id="transaction-date" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Nominal</label>
                                <input type="number" step="0.01" id="transaction-amount" class="form-control" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label>Akun</label>
                                <select id="account-id" class="form-control" required>
                                    <option value="">Pilih Akun</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>Kategori</label>
                                <select id="category-id" class="form-control">
                                    <option value="">Tanpa Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Catatan</label>
                                <input type="text" id="transaction-note" class="form-control" maxlength="255">
                            </div>
                        </div>

                        <div id="paylater-options" class="border rounded p-3 mb-3 d-none">
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Jenis Pembayaran Paylater</label>
                                    <select id="payment-type" class="form-control">
                                        <option value="full">Bayar Penuh</option>
                                        <option value="installment">Cicilan</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Tenor (bulan)</label>
                                    <input type="number" id="installment-period" class="form-control" min="1" max="12">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Detil Item</label>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless" id="items-table">
                                    <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-right">Harga</th>
                                        <th class="text-right">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mb-3" id="discounts-wrapper" style="display:none;">
                            <label>Diskon</label>
                            <ul class="list-group" id="discounts-list"></ul>
                        </div>

                        <div class="form-group">
                            <label>Catatan AI</label>
                            <pre id="raw-json" class="bg-light p-2 rounded small"></pre>
                        </div>

                        <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                        <span id="save-status" class="ml-2 text-muted"></span>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Riwayat Analisis Terakhir</div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentReceipts as $receipt)
                            <tr>
                                <td>#{{ $receipt->id }}</td>
                                <td>{{ $receipt->created_at->format('d M Y H:i') }}</td>
                                <td>
                                    <span class="badge badge-{{ $receipt->status === 'ready' ? 'info' : ($receipt->status === 'saved' ? 'success' : ($receipt->status === 'failed' ? 'danger' : 'secondary')) }}">
                                        {{ ucfirst($receipt->status) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($receipt->error_message, 40) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada riwayat analisis.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const uploadForm = document.getElementById('receipt-upload-form');
    const saveForm = document.getElementById('receipt-save-form');
    const uploadStatus = document.getElementById('upload-status');
    const saveStatus = document.getElementById('save-status');
    const analysisCard = document.getElementById('analysis-card');
    const receiptIdInput = document.getElementById('receipt-upload-id');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const paylaterOptions = document.getElementById('paylater-options');

    const analyzeUrl = '{{ route('receipts.analyze') }}';
    const saveUrlTemplate = '{{ route('receipts.save', ['receipt' => '__RECEIPT_ID__']) }}';

    uploadForm.addEventListener('submit', (event) => {
        event.preventDefault();
        uploadStatus.textContent = 'Mengunggah & menganalisis...';
        saveStatus.textContent = '';

        const formData = new FormData(uploadForm);

        fetch(analyzeUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
            .then(async (response) => {
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || (errorData.errors?.receipt?.[0]) || 'Gagal menganalisis struk');
                }
                return response.json();
            })
            .then((data) => {
                uploadStatus.textContent = 'Analisis selesai.';
                fillAnalysis(data);
            })
            .catch((error) => {
                uploadStatus.textContent = error.message;
            });
    });

    saveForm.addEventListener('submit', (event) => {
        event.preventDefault();
        saveStatus.textContent = 'Menyimpan transaksi...';

        const receiptId = receiptIdInput.value;
        if (!receiptId) {
            saveStatus.textContent = 'Struk belum dianalisis.';
            return;
        }

        const payload = {
            type: document.getElementById('transaction-type').value,
            account_id: document.getElementById('account-id').value,
            category_id: document.getElementById('category-id').value || null,
            amount: document.getElementById('transaction-amount').value,
            date: document.getElementById('transaction-date').value,
            note: document.getElementById('transaction-note').value || 'Belanja ' + (document.getElementById('merchant').value || ''),
            payment_type: document.getElementById('payment-type').value,
            installment_period: document.getElementById('installment-period').value || null,
        };

        const url = saveUrlTemplate.replace('__RECEIPT_ID__', receiptId);

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload)
        })
            .then(async (response) => {
                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Gagal menyimpan transaksi');
                }
                return response.json();
            })
            .then((data) => {
                saveStatus.textContent = data.message;
            })
            .catch((error) => {
                saveStatus.textContent = error.message;
            });
    });

    document.getElementById('transaction-type').addEventListener('change', togglePaylater);
    document.getElementById('account-id').addEventListener('change', togglePaylater);

    function fillAnalysis(data) {
        const payload = data.parsed_payload || {};
        receiptIdInput.value = data.receipt_upload.id;
        document.getElementById('merchant').value = payload.merchant || '';
        document.getElementById('transaction-date').value = normalizeDate(payload.timestamp) || new Date().toISOString().slice(0, 10);
        document.getElementById('transaction-amount').value = payload.total_amount || '';
        document.getElementById('transaction-note').value = payload.notes || '';
        document.getElementById('receipt-image-preview').src = data.image_url;
        document.getElementById('analysis-updated').textContent = new Date().toLocaleString();
        document.getElementById('raw-json').textContent = JSON.stringify(payload, null, 2);

        renderItems(payload.items || []);
        renderDiscounts(payload.discounts || []);

        analysisCard.classList.remove('d-none');
        togglePaylater();
    }

    function renderItems(items) {
        const tbody = document.querySelector('#items-table tbody');
        tbody.innerHTML = '';
        if (!items.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="text-muted text-center">Tidak ada item terdeteksi.</td></tr>';
            return;
        }

        items.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.name || '-'}</td>
                <td class="text-center">${item.quantity ?? '-'}</td>
                <td class="text-right">${formatNumber(item.unit_price)}</td>
                <td class="text-right">${formatNumber(item.line_total)}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderDiscounts(discounts) {
        const wrapper = document.getElementById('discounts-wrapper');
        const list = document.getElementById('discounts-list');
        list.innerHTML = '';

        if (!discounts.length) {
            wrapper.style.display = 'none';
            return;
        }

        discounts.forEach(discount => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between';
            li.innerHTML = `
                <span>${discount.description || 'Diskon'}</span>
                <strong class="text-danger">${formatNumber(discount.amount)}</strong>
            `;
            list.appendChild(li);
        });

        wrapper.style.display = 'block';
    }

    function normalizeDate(value) {
        if (!value) {
            return '';
        }
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) {
            return '';
        }
        return date.toISOString().slice(0, 10);
    }

    function formatNumber(value) {
        if (value === undefined || value === null || value === '') {
            return '-';
        }
        return parseFloat(value).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    function togglePaylater() {
        const type = document.getElementById('transaction-type').value;
        const selectedAccount = document.getElementById('account-id');
        const option = selectedAccount.options[selectedAccount.selectedIndex];
        const isPaylater = option && option.textContent.toLowerCase().includes('paylater');

        if (type === 'expense' && isPaylater) {
            paylaterOptions.classList.remove('d-none');
        } else {
            paylaterOptions.classList.add('d-none');
            document.getElementById('payment-type').value = 'full';
            document.getElementById('installment-period').value = '';
        }
    }
});
</script>
@endpush
