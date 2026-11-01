<script setup>
import { ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import { useLmsStore } from '@/Stores/lmsStateStore';

const emit = defineEmits(['showToast']);

const { systemSettings, toggleMaintenanceMode } = useLmsStore();
const isBackingUp = ref(false);

const handleRunBackup = () => {
    isBackingUp.value = true;
    emit('showToast', 'Memulai proses pencadangan basis data snapshot...');
    setTimeout(() => {
        isBackingUp.value = false;
        const now = new Date();
        const timeStr = `Hari ini, ${String(now.getHours()).padStart(2, '0')}:${String(now.getMinutes()).padStart(2, '0')}:${String(now.getSeconds()).padStart(2, '0')} WIB`;
        systemSettings.lastBackupTime = timeStr;
        emit('showToast', 'Pencadangan basis data (snapshot SQL) berhasil disimpan.');
    }, 1200);
};

const handleToggleMaintenance = () => {
    const isNowActive = toggleMaintenanceMode();
    emit(
        'showToast',
        isNowActive
            ? 'PERINGATAN: Mode Pemeliharaan aktif. Akses siswa, guru, wali kelas, & kepsek terkunci.'
            : 'Mode Pemeliharaan dinonaktifkan. Akses seluruh peran normal kembali.'
    );
};
</script>

<template>
    <div class="space-y-4">
        <!-- Top Banner -->
        <div class="bg-white rounded-[26px] p-5 border border-[#ECEAE4] soft-shadow flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-xl bg-charcoal text-white flex items-center justify-center font-bold">
                        <LmsIcon name="settings" className="w-4 h-4" />
                    </span>
                    <h3 class="text-base font-extrabold text-charcoal">Pengaturan Sistem &amp; Batas Operasional LMS</h3>
                </div>
                <p class="text-xs text-subtext">
                    Konfigurasi kapasitas berkas pembelajaran, batas waktu sesi keamanan, status pemeliharaan, dan pencadangan basis data.
                </p>
            </div>
            <div class="flex items-center gap-2 flex-wrap">
                <span class="px-3 py-1.5 rounded-xl bg-canvas border border-[#ECEAE4] text-[11px] font-bold text-subtext">
                    Zona Waktu: <strong class="text-charcoal">{{ systemSettings.serverTimezone }}</strong>
                </span>
                <span class="px-3 py-1.5 rounded-xl bg-sage-light text-sage-dark text-[11px] font-black">
                    Dapodik: {{ systemSettings.dapodikStatus }}
                </span>
            </div>
        </div>

        <!-- Parameter Operasional Card -->
        <div class="bg-white rounded-[26px] p-6 border border-[#ECEAE4] soft-shadow space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-[#ECEAE4]">
                <div>
                    <h4 class="text-sm font-black text-charcoal">Parameter Operasional &amp; Batas Berkas</h4>
                    <p class="text-xs text-subtext mt-0.5">Konfigurasi batas penyimpanan dan keamanan sesi pengguna.</p>
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full bg-canvas border border-[#ECEAE4] text-charcoal">
                    Tahun Ajaran: {{ systemSettings.tahunAjaranAktif }}
                </span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Parameter 1: Batas Maksimal Upload Berkas -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-charcoal">Batas Maksimal Upload Berkas (Per File):</label>
                    <div class="grid grid-cols-4 gap-2">
                        <button
                            v-for="size in [10, 15, 25, 50]"
                            :key="size"
                            type="button"
                            @click="systemSettings.maxUploadMb = size; $emit('showToast', `Batas upload disetel ke ${size} MB`)"
                            class="cursor-pointer py-2 text-xs font-bold rounded-xl border text-center transition-all"
                            :class="systemSettings.maxUploadMb === size ? 'bg-charcoal text-white border-charcoal shadow-xs' : 'bg-canvas text-subtext border-[#ECEAE4] hover:text-charcoal hover:bg-white'"
                        >
                            {{ size }} MB
                        </button>
                    </div>
                    <p class="text-[11px] text-subtext">Berlaku untuk materi modul guru, pengumpulan tugas siswa, dan lampiran pengumuman.</p>
                </div>

                <!-- Parameter 2: Session Timeout -->
                <div class="space-y-2">
                    <label class="text-xs font-bold text-charcoal">Batas Waktu Sesi Tidak Aktif (Session Timeout):</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button
                            v-for="min in [30, 60, 120]"
                            :key="min"
                            type="button"
                            @click="systemSettings.sessionTimeoutMinutes = min; $emit('showToast', `Timeout sesi disetel ke ${min} Menit`)"
                            class="cursor-pointer py-2 text-xs font-bold rounded-xl border text-center transition-all"
                            :class="systemSettings.sessionTimeoutMinutes === min ? 'bg-charcoal text-white border-charcoal shadow-xs' : 'bg-canvas text-subtext border-[#ECEAE4] hover:text-charcoal hover:bg-white'"
                        >
                            {{ min }} Menit
                        </button>
                    </div>
                    <p class="text-[11px] text-subtext">Pengguna akan logout otomatis bila tidak ada aktivitas dalam durasi ini.</p>
                </div>

                <!-- Parameter 3: Mode Pemeliharaan -->
                <div class="space-y-2 lg:col-span-2 p-4 rounded-2xl bg-canvas border border-[#ECEAE4]">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold text-charcoal">Mode Pemeliharaan Sistem (Maintenance Mode)</div>
                            <div class="text-[11px] text-subtext mt-0.5">Kunci akses seluruh siswa, guru, dan staf saat pembaharuan sistem atau rekap nilai semester.</div>
                        </div>
                        <button
                            type="button"
                            @click="handleToggleMaintenance"
                            class="cursor-pointer shrink-0 w-12 h-6 rounded-full p-0.5 transition-colors duration-200"
                            :class="systemSettings.maintenanceMode ? 'bg-blossom-dark' : 'bg-gray-300'"
                            :title="systemSettings.maintenanceMode ? 'Nonaktifkan Mode Pemeliharaan' : 'Aktifkan Mode Pemeliharaan'"
                        >
                            <div
                                class="w-5 h-5 rounded-full bg-white shadow-md transform transition-transform duration-200"
                                :class="systemSettings.maintenanceMode ? 'translate-x-6' : 'translate-x-0'"
                            ></div>
                        </button>
                    </div>
                    <div
                        class="mt-3 p-3 rounded-xl text-xs font-medium border"
                        :class="systemSettings.maintenanceMode ? 'bg-blossom-light text-blossom-dark border-[#F8B4B4]' : 'bg-white text-subtext border-[#ECEAE4]'"
                    >
                        {{ systemSettings.maintenanceMode ? '[PERINGATAN] Mode Pemeliharaan Aktif: Sistem dikunci sementara, hanya akun Administrator yang dapat login.' : 'LMS beroperasi normal untuk seluruh siswa, guru, wali kelas, dan kepala sekolah.' }}
                    </div>
                </div>
            </div>

            <!-- Format Berkas Diizinkan -->
            <div class="pt-4 border-t border-[#ECEAE4]">
                <div class="text-xs font-bold text-charcoal mb-2">Format Ekstensi Berkas Pembelajaran Resmi:</div>
                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="ext in systemSettings.allowedExtensions"
                        :key="ext"
                        class="text-xs font-mono font-bold px-3 py-1 rounded-xl bg-canvas border border-[#ECEAE4] text-charcoal"
                    >
                        .{{ ext.toLowerCase() }}
                    </span>
                </div>
                <p class="text-[11px] text-subtext mt-2">Membatasi upload berkas berbahaya demi keamanan server.</p>
            </div>

            <!-- Cadangan Basis Data (Snapshot) -->
            <div class="pt-4 border-t border-[#ECEAE4] flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="text-xs font-bold text-charcoal">Pencadangan Basis Data Otomatis &amp; Pemulihan:</div>
                    <div class="text-[11px] text-subtext mt-0.5">
                        Jadwal: {{ systemSettings.backupSchedule }} • Terakhir berhasil: <span class="font-bold text-charcoal">{{ systemSettings.lastBackupTime }}</span>
                    </div>
                </div>
                <button
                    type="button"
                    :disabled="isBackingUp"
                    @click="handleRunBackup"
                    class="cursor-pointer px-4 py-2.5 rounded-xl bg-charcoal text-white text-xs font-bold hover:bg-black active:scale-95 transition-all shadow-xs inline-flex items-center gap-1.5 shrink-0 disabled:opacity-50"
                >
                    <LmsIcon name="download" className="w-3.5 h-3.5" />
                    <span>{{ isBackingUp ? 'Mencadangkan...' : 'Cadangkan Sekarang' }}</span>
                </button>
            </div>
        </div>
    </div>
</template>
