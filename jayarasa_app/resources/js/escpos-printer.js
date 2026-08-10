/**
 * Client-Side ESC/POS Thermal Printer Driver Suite
 * Supports Web Bluetooth GATT write and Android RawBT Intent printing.
 */

export class EscPosEncoder {
    constructor() {
        this.buffer = [];
    }

    initialize() {
        this.buffer.push(0x1B, 0x40); // ESC @ (Initialize printer)
        return this;
    }

    alignCenter() {
        this.buffer.push(0x1B, 0x61, 0x01); // ESC a 1 (Align center)
        return this;
    }

    alignLeft() {
        this.buffer.push(0x1B, 0x61, 0x00); // ESC a 0 (Align left)
        return this;
    }

    alignRight() {
        this.buffer.push(0x1B, 0x61, 0x02); // ESC a 2 (Align right)
        return this;
    }

    bold(on = true) {
        this.buffer.push(0x1B, 0x45, on ? 0x01 : 0x00); // ESC E 1 / ESC E 0 (Bold)
        return this;
    }

    doubleSize(on = true) {
        this.buffer.push(0x1D, 0x21, on ? 0x11 : 0x00); // GS ! 17 (Double width & height) / GS ! 0
        return this;
    }

    text(str) {
        const encoder = new TextEncoder();
        const bytes = encoder.encode(str);
        this.buffer.push(...bytes);
        return this;
    }

    line(str = '') {
        this.text(str + '\n');
        return this;
    }

    feed(lines = 3) {
        this.buffer.push(0x1B, 0x64, lines); // ESC d n (Feed n lines)
        return this;
    }

    cut() {
        this.buffer.push(0x1D, 0x56, 0x42, 0x00); // GS V 66 0 (Paper cut)
        return this;
    }

    // Generates a padded receipt line (32 characters for 58mm thermal printers)
    twoColumnRow(leftStr, rightStr, maxLength = 32) {
        const leftLen = leftStr.length;
        const rightLen = rightStr.length;
        const padSize = maxLength - leftLen - rightLen;
        
        if (padSize > 0) {
            const padding = ' '.repeat(padSize);
            this.line(leftStr + padding + rightStr);
        } else {
            // If it exceeds length, put right string on a new line or truncate
            this.line(leftStr);
            const padding = ' '.repeat(maxLength - rightLen);
            this.line(padding + rightStr);
        }
        return this;
    }

    getRaw() {
        return new Uint8Array(this.buffer);
    }
}

export class BluetoothPrinter {
    static STORAGE_KEY     = 'bt_printer_device_name';
    static CHAR_UUID_KEY   = 'bt_printer_char_uuid';
    static SERVICE_UUID_KEY = 'bt_printer_service_uuid';

    constructor() {
        this.device         = null;
        this.characteristic = null;
        this._disconnectListener = null; // track listener agar tidak menumpuk

        // Otomatis lepaskan koneksi GATT secara bersih sebelum refresh/meninggalkan halaman
        if (typeof window !== 'undefined') {
            window.addEventListener('beforeunload', () => {
                this.disconnectGently();
            });
        }
    }

    /** Connect via browser picker (requires user gesture) */
    async connect() {
        this.device = await navigator.bluetooth.requestDevice({
            acceptAllDevices: true,
            optionalServices: [
                '000018f0-0000-1000-8000-00805f9b34fb',
                '0000ffe0-0000-1000-8000-00805f9b34fb',
                '0000e7e7-0000-1000-8000-00805f9b34fb',
                '49535343-fe7d-41aa-8fa6-a70f71a274dd',
                'e081a770-f482-4c28-9189-980b67272828'
            ]
        });

        await this._connectToDevice(this.device);

        // Simpan nama + characteristic UUID agar reconnect lebih cepat
        localStorage.setItem(BluetoothPrinter.STORAGE_KEY, this.device.name || 'BT Printer');
        if (this.characteristic) {
            localStorage.setItem(BluetoothPrinter.CHAR_UUID_KEY, this.characteristic.uuid);
            localStorage.setItem(BluetoothPrinter.SERVICE_UUID_KEY, this.characteristic.service.uuid);
        }

        return true;
    }

    /**
     * Auto-reconnect ke device yang pernah di-pair, tanpa user gesture.
     * Urutan: direct connect → watchAdvertisements → retry dengan delay
     */
    async reconnectToSavedDevice() {
        const savedName = localStorage.getItem(BluetoothPrinter.STORAGE_KEY);
        if (!savedName) return false;

        // Tambahkan delay awal agar sub-sistem Bluetooth OS dan printer selesai memproses pemutusan koneksi sebelumnya
        console.log('[BT] Memulai auto-reconnect dalam 1.2 detik...');
        await new Promise(r => setTimeout(r, 1200));

        if (!navigator.bluetooth || typeof navigator.bluetooth.getDevices !== 'function') {
            console.warn('[BT] getDevices() tidak tersedia pada browser ini.');
            return false;
        }

        let devices;
        try {
            devices = await navigator.bluetooth.getDevices();
        } catch (e) {
            console.warn('[BT] getDevices() error:', e);
            return false;
        }

        if (!devices || devices.length === 0) {
            console.warn('[BT] getDevices() kosong. Browser tidak menyimpan izin Bluetooth setelah refresh.');
            // Beritahu global UI bahwa izin hilang agar bisa diinfokan ke user
            window.dispatchEvent(new CustomEvent('bt-printer-permission-lost', {
                detail: { deviceName: savedName }
            }));
            return false;
        }

        const target = devices.find(d => (d.name || '') === savedName) || devices[0];
        if (!target) return false;

        console.log('[BT] Menghubungkan ke perangkat:', target.name || savedName);

        // Coba koneksi langsung hingga 3 kali dengan progresif delay
        const maxAttempts = 3;
        const delays = [0, 1500, 3000]; // Coba langsung, lalu tunggu 1.5 detik, lalu 3 detik
        let lastError = null;

        for (let attempt = 0; attempt < maxAttempts; attempt++) {
            if (delays[attempt] > 0) {
                console.log(`[BT] Menunggu ${delays[attempt]}ms sebelum mencoba kembali...`);
                await new Promise(r => setTimeout(r, delays[attempt]));
            }
            console.log(`[BT] Upaya koneksi GATT ke-${attempt + 1}...`);
            try {
                await this._connectToDevice(target);
                this._saveDeviceInfo();
                console.log('[BT] Auto-reconnect berhasil!');
                return true;
            } catch (err) {
                console.warn(`[BT] Upaya ke-${attempt + 1} gagal:`, err.message || err);
                lastError = err;
            }
        }

        console.warn('[BT] Semua upaya auto-reconnect gagal.');
        if (lastError) {
            throw lastError; // Lempar kembali error terakhir agar app.js bisa mendeteksi SecurityError
        }
        return false;
    }

    /** Simpan UUID service & characteristic untuk reconnect lebih cepat ke depannya */
    _saveDeviceInfo() {
        if (this.device) {
            localStorage.setItem(BluetoothPrinter.STORAGE_KEY, this.device.name || 'BT Printer');
        }
        if (this.characteristic) {
            localStorage.setItem(BluetoothPrinter.CHAR_UUID_KEY, this.characteristic.uuid);
            localStorage.setItem(BluetoothPrinter.SERVICE_UUID_KEY, this.characteristic.service.uuid);
        }
    }

    /** Koneksi GATT internal — dipakai oleh connect() dan reconnectToSavedDevice() */
    async _connectToDevice(device) {
        this.device = device;

        // Hapus listener lama supaya tidak menumpuk (penting saat reconnect!)
        if (this._disconnectListener) {
            this.device.removeEventListener('gattserverdisconnected', this._disconnectListener);
            this._disconnectListener = null;
        }

        console.log('[BT] Connecting GATT...');
        const server = await this.device.gatt.connect();

        // Coba pakai service/characteristic UUID yang sudah tersimpan (lebih cepat)
        const savedServiceUUID = localStorage.getItem(BluetoothPrinter.SERVICE_UUID_KEY);
        const savedCharUUID    = localStorage.getItem(BluetoothPrinter.CHAR_UUID_KEY);

        this.characteristic = null;

        if (savedServiceUUID && savedCharUUID) {
            try {
                const service = await server.getPrimaryService(savedServiceUUID);
                const char    = await service.getCharacteristic(savedCharUUID);
                if (char.properties.write || char.properties.writeWithoutResponse) {
                    this.characteristic = char;
                    console.log('[BT] Terhubung (fast path) via saved UUIDs.');
                }
            } catch (e) {
                console.warn('[BT] Fast-path gagal, fallback ke full scan:', e.message || e);
            }
        }

        // Full service discovery jika fast-path gagal atau belum ada UUID tersimpan
        if (!this.characteristic) {
            console.log('[BT] Full service discovery...');
            const services = await server.getPrimaryServices();
            for (const service of services) {
                try {
                    const characteristics = await service.getCharacteristics();
                    for (const char of characteristics) {
                        if (char.properties.write || char.properties.writeWithoutResponse) {
                            this.characteristic = char;
                            break;
                        }
                    }
                } catch (e) {
                    // Abaikan service yang tidak bisa dibaca
                }
                if (this.characteristic) break;
            }
        }

        if (!this.characteristic) {
            await this.device.gatt.disconnect();
            throw new Error('Tidak ditemukan write characteristic pada printer ini.');
        }

        // Pasang listener baru (satu saja)
        this._disconnectListener = () => {
            this.characteristic = null;
            console.warn('[BT] GATT terputus (gattserverdisconnected).');
            window.dispatchEvent(new CustomEvent('bt-printer-disconnected'));
        };
        this.device.addEventListener('gattserverdisconnected', this._disconnectListener);

        console.log('[BT] Printer terhubung! Characteristic:', this.characteristic.uuid);
    }

    async print(bytes) {
        if (!this.characteristic) {
            throw new Error('Printer belum terhubung.');
        }

        const chunkSize = 20;
        for (let i = 0; i < bytes.length; i += chunkSize) {
            const chunk = bytes.slice(i, i + chunkSize);
            if (this.characteristic.properties.writeWithoutResponse) {
                await this.characteristic.writeValueWithoutResponse(chunk);
            } else {
                await this.characteristic.writeValueWithResponse(chunk);
            }
            await new Promise(resolve => setTimeout(resolve, 15));
        }
    }

    disconnect() {
        if (this._disconnectListener && this.device) {
            this.device.removeEventListener('gattserverdisconnected', this._disconnectListener);
            this._disconnectListener = null;
        }
        if (this.device && this.device.gatt.connected) {
            this.device.gatt.disconnect();
        }
        this.device         = null;
        this.characteristic = null;

        // Hapus SEMUA data tersimpan supaya tidak auto-reconnect lagi
        localStorage.removeItem(BluetoothPrinter.STORAGE_KEY);
        localStorage.removeItem(BluetoothPrinter.CHAR_UUID_KEY);
        localStorage.removeItem(BluetoothPrinter.SERVICE_UUID_KEY);
        console.log('[BT] Disconnected & data cleared.');
    }

    /** Putuskan koneksi GATT fisik secara anggun tanpa menghapus data localStorage (untuk page refresh) */
    disconnectGently() {
        if (this._disconnectListener && this.device) {
            try {
                this.device.removeEventListener('gattserverdisconnected', this._disconnectListener);
            } catch (e) {}
            this._disconnectListener = null;
        }
        if (this.device && this.device.gatt && this.device.gatt.connected) {
            try {
                this.device.gatt.disconnect();
                console.log('[BT] GATT disconnected gently.');
            } catch (e) {
                console.warn('[BT] Gentle disconnect error:', e);
            }
        }
        this.device         = null;
        this.characteristic = null;
    }

    isConnected() {
        return !!(this.device && this.device.gatt && this.device.gatt.connected);
    }

    static getSavedDeviceName() {
        return localStorage.getItem(BluetoothPrinter.STORAGE_KEY);
    }
}

/**
 * Sends ESC/POS raw bytes to the RawBT Android print application via Intent URI.
 */
export function printViaRawBT(bytes) {
    let binary = '';
    const len = bytes.byteLength;
    for (let i = 0; i < len; i++) {
        binary += String.fromCharCode(bytes[i]);
    }
    const base64Data = window.btoa(binary);
    
    // Launch RawBT app using official Android Intent URI structure with embedded base64 payload
    const url = `intent:base64,${encodeURIComponent(base64Data)}#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;`;
    window.location.href = url;
}
