@extends('layouts.app')

@section('title', 'Upload Bukti Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pb-12">
    <!-- Header -->
    <div class="animate-fade-in-up">
        <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-2">
                <li><a href="/user/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a></li>
                <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><a href="/user/riwayat" class="hover:text-primary-600 transition-colors">Pesanan</a></div></li>
                <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Upload Bukti</span></div></li>
            </ol>
        </nav>
        <h1 class="text-[28px] md:text-3xl font-bold text-gray-900 tracking-tight">Upload Bukti Pembayaran</h1>
        <p class="text-gray-500 mt-1 text-sm">Order: <span class="font-mono font-semibold text-primary-600">{{ $order->order_number }}</span></p>
    </div>

    <!-- Payment Method Info -->
    @if($order->paymentMethod)
    <div class="card p-5 bg-gradient-to-br from-primary-50/30 to-white border-primary-200 animate-fade-in-up delay-100">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-primary-100 text-primary-600 flex items-center justify-center">
                <i class="ph ph-bank w-6 h-6"></i>
            </div>
            <div>
                <h2 class="font-semibold text-gray-900">{{ $order->paymentMethod->name }}</h2>
                <p class="text-sm text-gray-600">{{ $order->paymentMethod->bank_name }}</p>
            </div>
        </div>
        <div class="bg-white rounded-lg p-4 border border-gray-200">
            <p class="text-sm text-gray-600 mb-1">Transfer ke:</p>
            <p class="font-mono text-lg font-bold text-gray-900 tracking-wider">{{ $order->paymentMethod->account_number }}</p>
            <p class="text-sm text-gray-600 mt-1">a.n. {{ $order->paymentMethod->account_name }}</p>
        </div>
    </div>
    @endif

    <!-- Upload Form -->
    <div class="card p-6 animate-fade-in-up delay-150">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <i class="ph ph-upload-simple w-5 h-5 text-primary-600"></i>
            Upload Bukti Transfer
        </h2>

        <form action="{{ route('user.payments.upload-proof', $order) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- File Upload -->
            <div>
                <label for="proof_image" class="form-label flex items-center gap-2">
                    <span>Gambar Bukti Pembayaran</span>
                    <span class="text-red-500" aria-label="wajib diisi">*</span>
                </label>
                
                <div class="mt-2">
                    <div class="flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-primary-400 hover:bg-primary-50/20 transition-colors cursor-pointer relative" onclick="document.getElementById('proof_image').click()">
                        <div class="space-y-2 text-center">
                            <div class="mx-auto h-12 w-12 text-gray-400">
                                <i class="ph ph-image w-12 h-12"></i>
                            </div>
                            <div class="flex text-sm text-gray-600 justify-center">
                                <span class="relative cursor-pointer rounded-md font-medium text-primary-600 hover:text-primary-500">
                                    <span>Pilih file</span>
                                    <input id="proof_image" name="proof_image" type="file" class="sr-only" accept="image/jpeg,image/png,image/jpg" required aria-describedby="file-help" onchange="updateFileName(this)">
                                </span>
                                <p class="pl-1">atau drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500" id="file-help">PNG, JPG, JPEG up to 5MB</p>
                        </div>
                    </div>
                    
                    <!-- Selected file name -->
                    <p id="selected-file" class="mt-2 text-sm text-gray-600 hidden">
                        <i class="ph ph-check-circle w-4 h-4 text-green-500 inline mr-1"></i>
                        File terpilih: <span class="font-medium" id="file-name"></span>
                    </p>
                </div>

                @error('proof_image')
                    <div class="form-error-msg mt-2" role="alert">
                        <i class="ph ph-warning-circle w-4 h-4 shrink-0"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="form-label flex items-center gap-2">
                    <span>Catatan</span>
                    <span class="text-gray-400 text-xs">(opsional)</span>
                </label>
                <textarea 
                    name="notes" 
                    id="notes" 
                    rows="3" 
                    class="form-input-clean mt-2" 
                    placeholder="Contoh: Transfer dari Bank BCA, atas nama [Nama Anda]..."
                    maxlength="500"
                    aria-describedby="notes-help"
                ></textarea>
                <p id="notes-help" class="mt-1 text-xs text-gray-500">Maksimal 500 karakter</p>
                
                @error('notes')
                    <div class="form-error-msg mt-2" role="alert">
                        <i class="ph ph-warning-circle w-4 h-4 shrink-0"></i>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <!-- Instructions -->
            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 text-sm text-amber-800">
                <div class="flex items-start gap-3">
                    <i class="ph ph-info w-5 h-5 shrink-0 mt-0.5"></i>
                    <div>
                        <p class="font-semibold mb-1">Petunjuk Upload:</p>
                        <ul class="list-disc list-inside space-y-1 text-amber-700">
                            <li>Pastikan bukti transfer terlihat jelas</li>
                            <li>Nomor rekening tujuan dan jumlah transfer terbaca</li>
                            <li>Format file: JPG, JPEG, atau PNG</li>
                            <li>Ukuran maksimal: 5MB</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4">
                <button type="submit" class="btn-primary flex-1 flex items-center justify-center gap-2">
                    <i class="ph ph-upload-simple w-5 h-5"></i>
                    Upload Bukti Pembayaran
                </button>
                <a href="{{ route('user.payments.select-method', $order) }}" class="btn-secondary text-center">
                    Kembali
                </a>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="text-center animate-fade-in-up delay-200">
        <p class="text-sm text-gray-500">
            Butuh bantuan? 
            <a href="/user/bantuan" class="text-primary-600 hover:text-primary-700 font-medium">Hubungi kami</a>
        </p>
    </div>
</div>

<script>
function updateFileName(input) {
    const fileName = input.files[0]?.name;
    const selectedFileDiv = document.getElementById('selected-file');
    const fileNameSpan = document.getElementById('file-name');
    
    if (fileName) {
        fileNameSpan.textContent = fileName;
        selectedFileDiv.classList.remove('hidden');
    } else {
        selectedFileDiv.classList.add('hidden');
    }
}

// Drag and drop support
const dropZone = document.querySelector('.border-dashed');

['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

['dragenter', 'dragover'].forEach(eventName => {
    dropZone.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropZone.addEventListener(eventName, unhighlight, false);
});

function highlight(e) {
    dropZone.classList.add('border-primary-500', 'bg-primary-50');
}

function unhighlight(e) {
    dropZone.classList.remove('border-primary-500', 'bg-primary-50');
}

dropZone.addEventListener('drop', handleDrop, false);

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    
    if (files.length) {
        document.getElementById('proof_image').files = files;
        updateFileName(document.getElementById('proof_image'));
    }
}
</script>
@endsection
