import './bootstrap';
import $ from 'jquery';
import.meta.glob([
  '../img/**',
  '../fonts/**',
]);

window.jQuery = window.$ = $;

import 'flowbite';

import 'overlayscrollbars/overlayscrollbars.css';
import { 
  OverlayScrollbars, 
  ScrollbarsHidingPlugin, 
  SizeObserverPlugin, 
  ClickScrollPlugin 
} from 'overlayscrollbars';

window.OverlayScrollbars = OverlayScrollbars;

import DataTable from 'datatables.net-dt';
window.DataTable = DataTable;

import { cAlert, oAlert, cConfirm } from "./alert";
window.cAlert = cAlert;
window.oAlert = oAlert;
window.cConfirm = cConfirm;
cAlert("green", "Berhasil", "Data berhasil dimuat", false, null);

//Loading
import { loading, removeLoading } from "./loading";
window.loading = loading;
window.removeLoading = removeLoading;

//Modal
import { Modal } from "flowbite";
window.Modal = Modal;
window.modal = Modal;

//Sortable
import Sortable from 'sortablejs';
window.Sortable = Sortable;

import moment from 'moment';
moment.locale('id');
window.moment = moment;

//ESC/POS Bluetooth & RawBT Printer
import { EscPosEncoder, BluetoothPrinter, printViaRawBT } from "./escpos-printer";
window.EscPosEncoder = EscPosEncoder;
window.BluetoothPrinter = BluetoothPrinter;
window.printViaRawBT = printViaRawBT;
window.bluetoothPrinterInstance = new BluetoothPrinter(); // Global singleton

/**
 * initBluetoothUI()
 * Panggil sekali pada DOMContentLoaded di setiap halaman yang punya tombol Bluetooth.
 * Tugasnya:
 *  1. Cek localStorage → jika ada device tersimpan, tampilkan nama & status "reconnecting"
 *  2. Coba auto-reconnect via getDevices() (tanpa dialog, tanpa user gesture)
 *  3. Update tombol UI sesuai hasil reconnect
 *  4. Pasang listener 'bt-printer-disconnected' agar UI diperbarui saat koneksi tiba-tiba putus
 */
window.initBluetoothUI = async function() {
    const PRINT_METHOD_KEY = 'pos_print_method';
    const savedName   = BluetoothPrinter.getSavedDeviceName();
    const savedMethod = localStorage.getItem(PRINT_METHOD_KEY) || 'html';

    // ── Helper: update tombol BT di halaman ini (sinkron ke semua modal) ─────
    function setBtUI(connected, deviceName) {
        const name = deviceName || savedName || 'BT Printer';
        const $buttons = $('#btn-toggle-bluetooth, #dp-btn-toggle-bluetooth, #dps-btn-toggle-bluetooth');
        const $texts   = $('#bt-status-text, #dp-bt-status-text, #dps-bt-status-text');
        const $devices = $('#bt-device-name, #dp-bt-device-name, #dps-bt-device-name');
        const $selects = $('#print-method-select, #dp-print-method-select, #dps-print-method-select');

        if (connected) {
            $buttons
                .removeClass('bg-brand hover:bg-brand-strong bg-amber-500 hover:bg-amber-600')
                .addClass('bg-red-500 hover:bg-red-600');
            $texts.text('Putuskan');
            $devices.removeClass('hidden').text(name);
            // Saat terhubung: otomatis pilih bluetooth & simpan
            $selects.val('bluetooth');
            localStorage.setItem(PRINT_METHOD_KEY, 'bluetooth');
        } else {
            $buttons
                .removeClass('bg-red-500 hover:bg-red-600 bg-amber-500 hover:bg-amber-600')
                .addClass('bg-brand hover:bg-brand-strong');
            $texts.text('Hubungkan Bluetooth');
            $devices.addClass('hidden').text('');
        }
    }

    // Helper: status sedang menunggu (kuning/amber — menandakan proses berjalan)
    function setBtWaiting(deviceName) {
        const name = deviceName || savedName || 'BT Printer';
        const $buttons = $('#btn-toggle-bluetooth, #dp-btn-toggle-bluetooth, #dps-btn-toggle-bluetooth');
        const $texts   = $('#bt-status-text, #dp-bt-status-text, #dps-bt-status-text');
        const $devices = $('#bt-device-name, #dp-bt-device-name, #dps-bt-device-name');

        $buttons
            .removeClass('bg-brand hover:bg-brand-strong bg-red-500 hover:bg-red-600')
            .addClass('bg-amber-500 hover:bg-amber-600');
        $texts.text('Menunggu printer...');
        $devices.removeClass('hidden').text(name);
    }

    window._setBtUI = setBtUI;

    // ── Restore metode cetak tersimpan ────────────────────────────────────
    const $select = $('#print-method-select');
    if ($select.length) {
        $select.val(savedMethod);
        $select.off('change.printmethod').on('change.printmethod', function() {
            localStorage.setItem(PRINT_METHOD_KEY, $(this).val());
        });
    }

    // ── Auto-reconnect Bluetooth ──────────────────────────────────────────
    if (savedName) {
        // Tampilkan status "Menghubungkan..." segera
        setBtWaiting(savedName);

        // Helper fungsi reconnect internal yang bisa dipanggil berulang (immediate atau fallback)
        async function attemptReconnect(isUserGesture = false) {
            try {
                console.log(`[BT] Memulai upaya reconnect (UserGesture: ${isUserGesture})...`);
                const ok = await window.bluetoothPrinterInstance.reconnectToSavedDevice();
                if (ok) {
                    const name = window.bluetoothPrinterInstance.device
                        ? (window.bluetoothPrinterInstance.device.name || savedName)
                        : savedName;
                    setBtUI(true, name);
                    if (savedMethod === 'bluetooth') $select.val('bluetooth');
                    console.log('[BT] Reconnect sukses!');
                    return true;
                } else {
                    setBtUI(false, null);
                    $('#bt-device-name').removeClass('hidden').text(savedName + ' (tap untuk konek)');
                    if (savedMethod === 'bluetooth') $select.val('html');
                    return false;
                }
            } catch (e) {
                console.error('[BT] Reconnect error:', e);
                
                // Cek jika error adalah masalah SecurityError (user gesture)
                const errMsg = (e.message || '').toLowerCase();
                if (errMsg.includes('user gesture') || errMsg.includes('security') || e.name === 'SecurityError') {
                    console.log('[BT] Terdeteksi pembatasan user gesture. Menyiapkan fallback interaksi pertama...');
                    setBtWaiting(savedName + ' (ketuk layar untuk konek)');
                    
                    if (!isUserGesture) {
                        // Pasang listener interaksi pertama kali pada halaman
                        const triggerFallback = async () => {
                            document.removeEventListener('click', triggerFallback);
                            document.removeEventListener('touchstart', triggerFallback);
                            setBtWaiting(savedName);
                            await attemptReconnect(true);
                        };
                        document.addEventListener('click', triggerFallback);
                        document.addEventListener('touchstart', triggerFallback);
                    }
                } else {
                    setBtUI(false, null);
                    $('#bt-device-name').removeClass('hidden').text(savedName + ' (tap untuk konek)');
                    if (savedMethod === 'bluetooth') $select.val('html');
                }
                return false;
            }
        }

        // Coba jalankan Path A secara langsung saat halaman load
        attemptReconnect(false);
    }

    // ── Listener: koneksi GATT tiba-tiba putus ────────────────────────────
    window.addEventListener('bt-printer-disconnected', function() {
        setBtUI(false, null);
        if ($('#print-method-select').val() === 'bluetooth') {
            $('#print-method-select').val('html');
        }
        if (typeof liveToast === 'function') liveToast('Printer Bluetooth terputus.');
    });

    // ── Listener: izin hilang setelah refresh ──────────────────────────────
    window.addEventListener('bt-printer-permission-lost', function(e) {
        const name = e.detail && e.detail.deviceName ? e.detail.deviceName : 'printer';
        if (typeof oAlert === 'function') {
            oAlert('orange', 'Izin Dibutuhkan', `Browser membatasi koneksi otomatis ke printer Bluetooth Anda (${name}) setelah refresh. Silakan ketuk tombol "Hubungkan Bluetooth" di samping tombol cetak untuk mengaktifkan kembali.`, false);
        } else if (typeof liveToast === 'function') {
            liveToast(`Browser menahan izin Bluetooth. Silakan ketuk tombol "Hubungkan" untuk menyambungkan kembali.`);
        }
    });
};




$('.open-sidebar').on('click',function() {
    $('.sidebar').toggleClass('hidden');

    $('.close-sidebar').on('click','button',function() {
        $('.sidebar').addClass('hidden');
    });
});

// Add Commas Function
function addCommas(nStr)
{
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
window.addCommas = addCommas;