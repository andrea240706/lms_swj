<script setup>
import { ref } from 'vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import {
    adminStats,
    initialJadwalMingguan,
    initialUsersList,
    masterRombel,
} from './adminData';

const emit = defineEmits(['selectTab', 'showToast']);

const userList = ref(JSON.parse(JSON.stringify(initialUsersList)));
const masterRombelList = ref(JSON.parse(JSON.stringify(masterRombel)));
const jadwalMingguan = ref(JSON.parse(JSON.stringify(initialJadwalMingguan)));
</script>

<template>
    <div class="space-y-5">
        <!-- Hero Banner: Operational Overview -->
        <div class="hero-lavender rounded-[28px] p-5 soft-shadow relative overflow-hidden">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-white/95 text-charcoal px-3 py-1 rounded-full text-[11px] font-black shadow-2xs">
                            Operator Sekolah • Tata Usaha
                        </span>
                        <span class="text-xs font-bold text-lavender-dark flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-[#2E7D32] animate-pulse"></span>
                            Dapodik Terkoneksi: {{ adminStats.terakhirSinkronDapodik }}
                        </span>
                    </div>
                    <h2 class="text-[22px] sm:text-[24px] font-black text-charcoal leading-tight">
                        Pusat Operasional Akademik
                    </h2>
                    <p class="text-xs text-charcoal/80 mt-1 max-w-xl">
                        Tahun Ajaran {{ adminStats.tahunAjaranAktif }} • Pengelolaan 1.240 Siswa, 68 Tenaga Pendidik, &amp; 42 Rombel
                    </p>
                </div>

                <div class="flex items-center gap-2 self-start md:self-auto shrink-0">
                    <button
                        type="button"
                        @click="$emit('showToast', 'Sinkronisasi Dapodik berhasil diproses. Seluruh data siswa dan rombel terverifikasi.')"
                        class="px-4 py-2.5 rounded-2xl bg-charcoal text-white text-xs font-extrabold hover:bg-black active:scale-95 transition-all shadow-sm cursor-pointer flex items-center gap-2"
                    >
                        <LmsIcon name="clock" className="w-4 h-4 text-white" />
                        <span>Sinkron Dapodik</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- 4 Operational KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            <!-- KPI 1: Siswa -->
            <div
                @click="$emit('selectTab', 'pengguna')"
                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between cursor-pointer hover:border-charcoal/30 transition-colors"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-subtext">Total Siswa Terdata</span>
                    <span class="w-8 h-8 rounded-full bg-sky-light text-sky-dark flex items-center justify-center font-bold text-xs">
                        <LmsIcon name="user" className="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-2xl font-black text-charcoal">{{ userList.filter(u => u.role === 'siswa').length }} Akun</div>
                    <div class="text-[11px] font-medium text-subtext mt-0.5">{{ masterRombelList.length }} Rombel Aktif</div>
                </div>
            </div>

            <!-- KPI 2: Guru & Pendidik -->
            <div
                @click="$emit('selectTab', 'pengguna')"
                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between cursor-pointer hover:border-charcoal/30 transition-colors"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-subtext">Guru &amp; Pendidik</span>
                    <span class="w-8 h-8 rounded-full bg-sage-light text-sage-dark flex items-center justify-center font-bold text-xs">
                        <LmsIcon name="book" className="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-2xl font-black text-charcoal">{{ userList.filter(u => u.role === 'guru' || u.role === 'wali').length }} Guru</div>
                    <div class="text-[11px] font-medium text-subtext mt-0.5">{{ userList.filter(u => u.role === 'wali').length }} Wali Kelas Terplot</div>
                </div>
            </div>

            <!-- KPI 3: Plotting Jadwal -->
            <div
                @click="$emit('selectTab', 'jadwalpelajaran')"
                class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between cursor-pointer hover:border-charcoal/30 transition-colors"
            >
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-subtext">Jadwal Mingguan</span>
                    <span class="w-8 h-8 rounded-full bg-lavender-light text-lavender-dark flex items-center justify-center font-bold text-xs">
                        <LmsIcon name="clock" className="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-2xl font-black text-charcoal">{{ jadwalMingguan.length }} Sesi</div>
                    <div class="text-[11px] font-medium text-subtext mt-0.5">100% Terdistribusi Valid</div>
                </div>
            </div>

            <!-- KPI 4: Konflik Jadwal -->
            <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold text-subtext">Status Konflik</span>
                    <span class="w-8 h-8 rounded-full bg-[#E8F5E9] text-[#2E7D32] flex items-center justify-center font-black text-xs">
                        <LmsIcon name="check" className="w-4 h-4" />
                    </span>
                </div>
                <div class="mt-2">
                    <div class="text-2xl font-black text-[#2E7D32]">0 Konflik</div>
                    <div class="text-[11px] font-medium text-subtext mt-0.5">Bebas bentrok ruang &amp; guru</div>
                </div>
            </div>
        </div>

        <!-- 2 Columns: Tindakan Cepat Operator & Live Audit Trail -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <!-- Left: Tindakan Cepat Operator -->
            <div class="lg:col-span-7 space-y-3.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-charcoal"></span>
                        <h3 class="text-sm font-black text-charcoal uppercase tracking-wider">
                            Aksi Cepat Operasional
                        </h3>
                    </div>
                    <span class="text-[11px] font-bold text-subtext">Pintasan Operator</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div
                        @click="$emit('selectTab', 'jadwalpelajaran')"
                        class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                    >
                        <div>
                            <span class="w-9 h-9 rounded-2xl bg-lavender-light text-lavender-dark flex items-center justify-center mb-2.5 font-bold">
                                <LmsIcon name="clock" className="w-4 h-4" />
                            </span>
                            <h4 class="text-sm font-extrabold text-charcoal">Plotting Guru &amp; Jadwal</h4>
                            <p class="text-[11px] text-subtext mt-1">Petakan guru pengampu, wali kelas rombel, dan jadwal pelajaran mingguan.</p>
                        </div>
                        <div class="text-xs font-bold text-charcoal mt-3 flex items-center gap-1">
                            Buka Plotting →
                        </div>
                    </div>

                    <div
                        @click="$emit('selectTab', 'pengguna')"
                        class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                    >
                        <div>
                            <span class="w-9 h-9 rounded-2xl bg-sky-light text-sky-dark flex items-center justify-center mb-2.5 font-bold">
                                <LmsIcon name="user" className="w-4 h-4" />
                            </span>
                            <h4 class="text-sm font-extrabold text-charcoal">Manajemen Pengguna</h4>
                            <p class="text-[11px] text-subtext mt-1">Kelola akun Siswa, Guru, Wali Kelas, dan Kepala Sekolah. Aktivasi status akun.</p>
                        </div>
                        <div class="text-xs font-bold text-charcoal mt-3 flex items-center gap-1">
                            Kelola Akun →
                        </div>
                    </div>

                    <div
                        @click="$emit('selectTab', 'jurusan')"
                        class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                    >
                        <div>
                            <span class="w-9 h-9 rounded-2xl bg-sage-light text-sage-dark flex items-center justify-center mb-2.5 font-bold">
                                <LmsIcon name="book" className="w-4 h-4" />
                            </span>
                            <h4 class="text-sm font-extrabold text-charcoal">Data Master Akademik</h4>
                            <p class="text-[11px] text-subtext mt-1">Konfigurasi Tahun Ajaran, Program Keahlian (Jurusan), Rombel, &amp; Mapel.</p>
                        </div>
                        <div class="text-xs font-bold text-charcoal mt-3 flex items-center gap-1">
                            Kelola Master →
                        </div>
                    </div>

                    <div
                        @click="$emit('selectTab', 'pengumuman')"
                        class="cursor-pointer bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow hover:border-charcoal/30 active:scale-[0.99] transition-all flex flex-col justify-between"
                    >
                        <div>
                            <span class="w-9 h-9 rounded-2xl bg-amberpastel-light text-amberpastel-dark flex items-center justify-center mb-2.5 font-bold">
                                <LmsIcon name="task" className="w-4 h-4" />
                            </span>
                            <h4 class="text-sm font-extrabold text-charcoal">Buat Pengumuman</h4>
                            <p class="text-[11px] text-subtext mt-1">Broadcast informasi KBM, surat edaran, atau pengingat ke siswa dan guru.</p>
                        </div>
                        <div class="text-xs font-bold text-charcoal mt-3 flex items-center gap-1">
                            + Broadcast Baru →
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Live Log Aktivitas Operator -->
            <div class="lg:col-span-5 space-y-3.5">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sage-dark"></span>
                        <h3 class="text-sm font-black text-charcoal uppercase tracking-wider">
                            Log Mutasi &amp; Aktivitas
                        </h3>
                    </div>
                    <span class="text-[11px] font-bold text-subtext">Realtime</span>
                </div>

                <div class="bg-white rounded-[26px] p-4 border border-[#ECEAE4] soft-shadow space-y-3">
                    <div class="space-y-3 text-xs">
                        <div class="flex items-start gap-2.5 pb-2.5 border-b border-[#F2F0EC]">
                            <span class="w-2 h-2 rounded-full bg-[#2E7D32] mt-1.5 shrink-0"></span>
                            <div>
                                <div class="font-extrabold text-charcoal">Sinkronisasi Dapodik Berhasil</div>
                                <div class="text-[10px] text-subtext">Hari ini, 06:30 WIB • 1.240 data siswa diverifikasi</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5 pb-2.5 border-b border-[#F2F0EC]">
                            <span class="w-2 h-2 rounded-full bg-sky-dark mt-1.5 shrink-0"></span>
                            <div>
                                <div class="font-extrabold text-charcoal">Aktivasi Akun: Dimas Anggara (XII RPL 2)</div>
                                <div class="text-[10px] text-subtext">Hari ini, 07:15 WIB • Status akun diaktifkan kembali</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5 pb-2.5 border-b border-[#F2F0EC]">
                            <span class="w-2 h-2 rounded-full bg-lavender-dark mt-1.5 shrink-0"></span>
                            <div>
                                <div class="font-extrabold text-charcoal">Plotting Wali Kelas X DKV 1</div>
                                <div class="text-[10px] text-subtext">Kemarin, 14:20 WIB • Siti Rahma, M.Pd disahkan</div>
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-2 h-2 rounded-full bg-amberpastel-dark mt-1.5 shrink-0"></span>
                            <div>
                                <div class="font-extrabold text-charcoal">Publikasi Pengumuman ATS Ganjil</div>
                                <div class="text-[10px] text-subtext">Kemarin, 10:00 WIB • Ditayangkan ke seluruh pengguna</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
