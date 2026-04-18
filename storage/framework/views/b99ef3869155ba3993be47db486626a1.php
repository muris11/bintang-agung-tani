<?php $__env->startSection('title', 'Scan QR Pengambilan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-main space-y-6 fade-in" id="scan-container">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-2">
                    <li><a href="/admin/dashboard" class="hover:text-primary-600 transition-colors">Dashboard</a></li>
                    <li><div class="flex items-center"><i class="ph ph-caret-right mx-1 text-gray-400 w-3 h-3"></i><span class="text-gray-900 font-medium">Scan QR</span></div></li>
                </ol>
            </nav>
            <h1 class="heading-page text-gray-900">Pindai QR Pengambilan</h1>
            <p class="text-gray-500 mt-1 body-small">Arahkan kamera ke QR code pesanan pelanggan untuk verifikasi otomatis.</p>
        </div>
        <a href="/admin/dashboard" class="btn-secondary h-10 px-4 shadow-subtle">
            <i class="ph ph-arrow-left ph-bold w-4 h-4"></i>
            <span class="hidden sm:inline">Kembali Dashboard</span>
        </a>
    </div>

    <!-- Scanner Container -->
    <div class="card-elevated overflow-hidden">
        <!-- Header Strip -->
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="relative flex h-2.5 w-2.5" id="status-indicator">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                </div>
                <span class="text-xs font-bold text-gray-600 tracking-wide uppercase" id="status-text">Kamera Siap</span>
            </div>

            <div class="flex items-center gap-2">
                <select id="camera-select" class="text-xs font-medium border border-gray-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-100 transition-all">
                    <option value="">Pilih Kamera...</option>
                </select>
                <button id="start-btn" class="btn-primary h-9 px-4 text-xs shadow-soft">
                    Mulai Scan
                </button>
                <button id="stop-btn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-semibold transition-all shadow-soft hidden">
                    Berhenti
                </button>
            </div>
        </div>

        <!-- Camera Scanner Area -->
        <div class="bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 relative flex flex-col items-center justify-center min-h-[500px]" id="scanner-area">

            <div class="absolute inset-0 opacity-5" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 200 200%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noiseFilter%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.65%22 numOctaves=%223%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noiseFilter)%22/%3E%3C/svg%3E');"></div>

            <!-- QR Scanner Element -->
            <div class="relative">
                <div id="qr-reader" class="w-[280px] h-[280px] rounded-2xl overflow-hidden bg-black shadow-lg border-2 border-primary-500/30"></div>
                <div class="absolute -inset-4 border-2 border-dashed border-primary-500/20 rounded-3xl -z-10"></div>
            </div>

            <p class="text-white/70 mt-6 text-sm font-medium relative z-10 bg-black/50 px-5 py-2.5 rounded-full border border-white/20" id="instruction-text">
                <i class="ph ph-qr-code w-4 h-4 inline mr-1.5"></i>
                Pilih kamera dan klik "Mulai Scan"
            </p>

            <!-- Manual Input Fallback -->
            <div class="mt-8 w-full max-w-md px-6">
                <form id="manual-form" action="<?php echo e(route('admin.scan-qr.scan')); ?>" method="POST" class="flex gap-2">
                    <?php echo csrf_field(); ?>
                    <input type="text" name="qr_data" id="manual-input" placeholder="Atau masukkan kode manual..."
                        class="flex-1 px-4 py-2.5 bg-white/10 border border-white/20 rounded-xl text-white placeholder-white/40 text-sm focus:outline-none focus:border-primary-400 focus:ring-2 focus:ring-primary-500/30 transition-all">
                    <button type="submit" class="bg-white/20 hover:bg-white/30 text-white px-5 py-2.5 rounded-xl text-sm font-semibold transition-all border border-white/30">
                        <i class="ph ph-magnifying-glass w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- Success State -->
        <div id="success-state" class="hidden bg-gradient-to-br from-primary-500 to-emerald-600 p-12 text-center text-white min-h-[500px] flex flex-col items-center justify-center relative overflow-hidden">

            <div class="absolute top-0 right-0 -mt-32 -mr-32 w-96 h-96 bg-white opacity-5 rounded-full"></div>
            <div class="absolute bottom-0 left-0 -mb-32 -ml-32 w-96 h-96 bg-black opacity-5 rounded-full"></div>

            <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center mb-6 shadow-2xl relative z-10">
                <i class="ph ph-check-circle ph-fill w-16 h-16 text-primary-600 animate-[bounce_0.5s_ease-in-out_2]"></i>
            </div>

            <h2 class="text-2xl font-black tracking-tight mb-2 relative z-10">Verifikasi Berhasil!</h2>
            <p class="text-white/90 text-base mb-8 relative z-10 font-medium">QR Terdaftar atas ID <strong class="font-mono bg-black/20 px-3 py-1 rounded-lg ml-1 text-white shadow-inner" id="order-number">-</strong></p>

            <div class="flex flex-col sm:flex-row gap-3 relative z-10">
                <a href="/admin/pesanan" id="view-order-link" class="bg-white text-primary-600 px-8 py-3 rounded-xl font-bold hover:bg-gray-50 transition-all shadow-lg flex items-center justify-center gap-2">
                    <i class="ph ph-eye ph-bold w-5 h-5"></i> Lihat Pesanan
                </a>
                <button onclick="resetScanner()" class="px-8 py-3 rounded-xl font-bold text-white border-2 border-white/40 hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                    <i class="ph ph-arrows-clockwise ph-bold w-5 h-5"></i> Scan Ulang
                </button>
            </div>
        </div>

        <!-- Error State -->
        <div id="error-state" class="hidden bg-gradient-to-br from-red-500 to-red-600 p-12 text-center text-white min-h-[500px] flex flex-col items-center justify-center relative overflow-hidden">

            <div class="absolute top-0 right-0 -mt-32 -mr-32 w-96 h-96 bg-white opacity-5 rounded-full"></div>

            <div class="w-32 h-32 bg-white rounded-full flex items-center justify-center mb-6 shadow-2xl relative z-10">
                <i class="ph ph-x-circle ph-fill w-16 h-16 text-red-600"></i>
            </div>

            <h2 class="text-2xl font-black tracking-tight mb-2 relative z-10">QR Tidak Valid!</h2>
            <p class="text-white/90 text-base mb-8 relative z-10 font-medium" id="error-message">Data QR code tidak ditemukan.</p>

            <button onclick="resetScanner()" class="px-8 py-3 rounded-xl font-bold text-white border-2 border-white/40 hover:bg-white/10 transition-all flex items-center justify-center gap-2 relative z-10">
                <i class="ph ph-arrows-clockwise ph-bold w-5 h-5"></i> Coba Lagi
            </button>
        </div>
    </div>

    <!-- Hidden Form for AJAX Submission -->
    <form id="scan-form" action="<?php echo e(route('admin.scan-qr.scan')); ?>" method="POST" class="hidden">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="qr_data" id="qr-data-input">
    </form>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
    let html5QrCode;
    let isScanning = false;
    let currentCamera = null;

    // Get DOM elements
    const cameraSelect = document.getElementById('camera-select');
    const startBtn = document.getElementById('start-btn');
    const stopBtn = document.getElementById('stop-btn');
    const scannerArea = document.getElementById('scanner-area');
    const successState = document.getElementById('success-state');
    const errorState = document.getElementById('error-state');
    const statusIndicator = document.getElementById('status-indicator');
    const statusText = document.getElementById('status-text');
    const instructionText = document.getElementById('instruction-text');

    // Initialize camera list
    async function initCameras() {
        try {
            const devices = await Html5Qrcode.getCameras();
            if (devices && devices.length) {
                cameraSelect.innerHTML = '<option value="">Pilih Kamera...</option>';
                devices.forEach((device, index) => {
                    const option = document.createElement('option');
                    option.value = device.id;
                    option.text = device.label || `Kamera ${index + 1}`;
                    cameraSelect.appendChild(option);
                });
                
                if (devices.length > 0) {
                    cameraSelect.value = devices[0].id;
                }
            }
        } catch (err) {
            console.error('Error getting cameras:', err);
            instructionText.textContent = 'Tidak dapat mengakses kamera. Gunakan input manual.';
        }
    }

    // Start scanning
    async function startScanning() {
        const cameraId = cameraSelect.value;
        if (!cameraId) {
            alert('Pilih kamera terlebih dahulu!');
            return;
        }

        try {
            html5QrCode = new Html5Qrcode("qr-reader");
            
            await html5QrCode.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 }
                },
                onScanSuccess,
                onScanFailure
            );

            isScanning = true;
            currentCamera = cameraId;
            
            // Update UI
            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');
            cameraSelect.disabled = true;
            instructionText.textContent = 'Arahkan QR code ke tengah area scan';
            
            // Update status indicator
            statusIndicator.innerHTML = `
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
            `;
            statusText.textContent = 'Memindai...';
            statusText.classList.add('text-green-600');
            
        } catch (err) {
            console.error('Error starting scanner:', err);
            alert('Gagal memulai kamera: ' + err.message);
        }
    }

    // Stop scanning
    async function stopScanning() {
        if (html5QrCode && isScanning) {
            try {
                await html5QrCode.stop();
                isScanning = false;
                
                // Update UI
                startBtn.classList.remove('hidden');
                stopBtn.classList.add('hidden');
                cameraSelect.disabled = false;
                instructionText.textContent = 'Pilih kamera dan klik "Mulai Scan"';
                
                // Reset status
                statusIndicator.innerHTML = `
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                `;
                statusText.textContent = 'Kamera Scanner Aktif';
                statusText.classList.remove('text-green-600');
                
            } catch (err) {
                console.error('Error stopping scanner:', err);
            }
        }
    }

    // On scan success
    async function onScanSuccess(decodedText, decodedResult) {
        console.log('QR Code scanned:', decodedText);
        
        // Stop scanning
        await stopScanning();
        
        // Submit to backend
        submitQRData(decodedText);
    }

    // On scan failure (just log, don't alert)
    function onScanFailure(error) {
        // console.warn(`QR scan error: ${error}`);
    }

    // Submit QR data to backend
    async function submitQRData(qrData) {
        try {
            const form = document.getElementById('scan-form');
            const input = document.getElementById('qr-data-input');
            input.value = qrData;
            
            const formData = new FormData(form);
            
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.redirected) {
                // If redirected, show success and then navigate
                showSuccess(qrData);
                setTimeout(() => {
                    window.location.href = response.url;
                }, 1500);
            } else {
                const result = await response.json();
                if (result.success) {
                    showSuccess(result.order_number);
                } else {
                    showError(result.message || 'QR code tidak valid');
                }
            }
        } catch (err) {
            console.error('Error submitting QR:', err);
            showError('Terjadi kesalahan saat memproses QR code');
        }
    }

    // Show success state
    function showSuccess(orderNumber) {
        scannerArea.classList.add('hidden');
        successState.classList.remove('hidden');
        document.getElementById('order-number').textContent = orderNumber || '-';
        
        // Update status
        statusIndicator.innerHTML = `
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
        `;
        statusText.textContent = 'Verifikasi Sukses';
    }

    // Show error state
    function showError(message) {
        scannerArea.classList.add('hidden');
        errorState.classList.remove('hidden');
        document.getElementById('error-message').textContent = message;
        
        // Update status
        statusIndicator.innerHTML = `
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        `;
        statusText.textContent = 'QR Tidak Valid';
    }

    // Reset scanner
    function resetScanner() {
        scannerArea.classList.remove('hidden');
        successState.classList.add('hidden');
        errorState.classList.add('hidden');
        document.getElementById('manual-input').value = '';
        
        // Reset status
        statusIndicator.innerHTML = `
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
            <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
        `;
        statusText.textContent = 'Kamera Scanner Aktif';
        statusText.classList.remove('text-green-600');
        instructionText.textContent = 'Pilih kamera dan klik "Mulai Scan"';
    }

    // Event listeners
    startBtn.addEventListener('click', startScanning);
    stopBtn.addEventListener('click', stopScanning);
    
    // Manual form submission
    document.getElementById('manual-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const qrData = document.getElementById('manual-input').value.trim();
        if (qrData) {
            submitQRData(qrData);
        }
    });

    // Initialize on load
    document.addEventListener('DOMContentLoaded', initCameras);
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\bintang-agung-tani\resources\views\admin\scan-barcode.blade.php ENDPATH**/ ?>