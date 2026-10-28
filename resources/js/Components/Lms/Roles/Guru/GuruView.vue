<script setup>
import { computed, ref } from 'vue';
import LmsCapsuleCalendar from '@/Components/Lms/LmsCapsuleCalendar.vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import MapelGuruView from '@/Components/Lms/Roles/Guru/MapelGuruView.vue';
import RekapNilaiGuruView from '@/Components/Lms/Roles/Guru/RekapNilaiGuruView.vue';
import { guruMapelList } from './mapelGuruData';

const props = defineProps({
    role: { type: Object, required: true },
    activeTab: { type: String, default: 'home' },
    selectedDay: { type: Number, default: 18 },
    days: { type: Array, default: () => [] },
    teacherGateOpen: { type: Boolean, default: true },
    materialsLock: { type: Object, default: () => ({ 1: false, 2: false, 3: true, 4: true }) },
    isSidebarCollapsed: { type: Boolean, default: false },
});

const emit = defineEmits([
    'selectTab', 'selectDay', 'toggleGate', 'toggleLock',
    'openGradeModal', 'openMenu', 'openProfile', 'toggleSidebar',
]);

// ===== JADWAL MENGAJAR PER HARI =====
const jadwalPerHari = {
    18: [ // Senin
        {
            id: 1,
            mapelId: 'pwpb',
            nama: 'Pemrograman Web & Perangkat Bergerak',
            shortName: 'Web Dev & Mobile',
            rombel: 'XII RPL 1',
            jam: '08:00 – 10:30 WIB',
            ruang: 'Lab RPL 2',
            sesi: 3,
            totalSesi: 5,
            siswa: 34,
            hadir: 31,
            status: 'sedang', // 'sedang' | 'selesai' | 'mendatang'
            heroBg: 'hero-sage',
            badgeColor: 'bg-sage-light text-sage-dark',
        },
        {
            id: 2,
            mapelId: 'pwpb',
            nama: 'Pemrograman Web & Perangkat Bergerak',
            shortName: 'Web Dev & Mobile',
            rombel: 'XII RPL 2',
            jam: '10:30 – 13:00 WIB',
            ruang: 'Lab RPL 1',
            sesi: 3,
            totalSesi: 5,
            siswa: 33,
            hadir: 0,
            status: 'mendatang',
            heroBg: 'hero-blossom',
            badgeColor: 'bg-blossom-light text-blossom-dark',
        },
    ],
    19: [ // Selasa
        {
            id: 3,
            mapelId: 'bd',
            nama: 'Basis Data',
            shortName: 'Basis Data',
            rombel: 'XI RPL 1',
            jam: '10:30 – 12:00 WIB',
            ruang: 'Lab RPL 1',
            sesi: 3,
            totalSesi: 5,
            siswa: 36,
            hadir: 0,
            status: 'mendatang',
            heroBg: 'hero-sky',
            badgeColor: 'bg-sky-light text-sky-dark',
        },
    ],
    20: [], // Rabu - tidak mengajar
    21: [ // Kamis
        {
            id: 4,
            mapelId: 'pwpb',
            nama: 'Pemrograman Web & Perangkat Bergerak',
            shortName: 'Web Dev & Mobile',
            rombel: 'XII RPL 1',
            jam: '07:00 – 09:30 WIB',
            ruang: 'Lab RPL 2',
            sesi: 3,
            totalSesi: 5,
            siswa: 34,
            hadir: 0,
            status: 'mendatang',
            heroBg: 'hero-sage',
            badgeColor: 'bg-sage-light text-sage-dark',
        },
    ],
    22: [], // Jumat
    23: [], // Sabtu
};

const jadwalHariIni = computed(() => jadwalPerHari[props.selectedDay] || []);

const sesiAktif = computed(() => jadwalHariIni.value.find(j => j.status === 'sedang') || jadwalHariIni.value[0] || null);

const getDayLabel = (n) => {
    const map = { 18: 'Senin', 19: 'Selasa', 20: 'Rabu', 21: 'Kamis', 22: 'Jumat', 23: 'Sabtu' };
    return map[n] || '-';
};

// Pending grading queue
const pendingNilai = [
    { nama: 'Praktikum REST API Laravel', rombel: 'XII RPL 1', belumDinilai: 6, totalKumpul: 28, mapel: 'Web Dev' },
    { nama: 'Desain Skema DDL Perpustakaan', rombel: 'XI RPL 1', belumDinilai: 5, totalKumpul: 33, mapel: 'Basis Data' },
];

const statusSesiColor = (status) => {
    if (status === 'sedang') return 'bg-[#E8F5E9] text-[#2E7D32]';
    if (status === 'selesai') return 'bg-sage-light text-sage-dark';
    return 'bg-muted text-subtext';
};
const statusSesiLabel = (status) => {
    if (status === 'sedang') return '● Sedang Berlangsung';
    if (status === 'selesai') return '✓ Selesai';
    return '○ Mendatang';
};
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <Transition name="tab-view" mode="out-in">

            <!-- ==========================================
                 TAB 1: HOME — DASHBOARD JADWAL MENGAJAR
                 ========================================== -->
            <div
                v-if="activeTab === 'home'"
                key="home"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
                <LmsTopBar
                    :role="role"
                    :isSidebarCollapsed="isSidebarCollapsed"
                    @openMenu="$emit('openMenu')"
                    @openProfile="$emit('openProfile')"
                    @toggleSidebar="$emit('toggleSidebar')"
                />

                <div class="flex-1 overflow-y-auto no-scrollbar px-4 sm:px-6 lg:px-8 xl:px-10 pb-28 lg:pb-8">
                    <div class="w-full max-w-[1800px] mx-auto space-y-5">

                        <!-- 1. HERO: Sesi Aktif / Sesi Terdekat -->
                        <div v-if="sesiAktif" class="rounded-[28px] p-5 relative overflow-hidden soft-shadow" :class="sesiAktif.heroBg">
                            <!-- Top row -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-charcoal/80">
                                    <span
                                        class="w-2 h-2 rounded-full"
                                        :class="sesiAktif.status === 'sedang' ? 'bg-[#2E7D32] live-dot' : 'bg-subtext'"
                                    ></span>
                                    <span>{{ sesiAktif.status === 'sedang' ? 'Sesi Aktif' : 'Sesi Terdekat' }} • {{ sesiAktif.ruang }}</span>
                                </div>
                                <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-white/90 text-charcoal border border-white/60 shadow-2xs">
                                    {{ getDayLabel(selectedDay) }}, {{ selectedDay }} Okt
                                </span>
                            </div>

                            <!-- Mapel & Rombel -->
                            <h3 class="text-[22px] font-extrabold text-charcoal mt-2 leading-tight">
                                {{ sesiAktif.shortName }}
                            </h3>
                            <p class="text-xs font-semibold text-charcoal/75 mt-0.5">
                                {{ sesiAktif.rombel }} • {{ sesiAktif.jam }}
                            </p>

                            <!-- Stats row -->
                            <div class="flex items-center gap-3 mt-4">
                                <div class="flex-1 bg-white/80 rounded-2xl px-3.5 py-2.5">
                                    <div class="text-[10px] font-bold text-charcoal/60 uppercase tracking-wider">Siswa</div>
                                    <div class="text-base font-extrabold text-charcoal mt-0.5">{{ sesiAktif.siswa }} Orang</div>
                                </div>
                                <div class="flex-1 bg-white/80 rounded-2xl px-3.5 py-2.5">
                                    <div class="text-[10px] font-bold text-charcoal/60 uppercase tracking-wider">Sesi</div>
                                    <div class="text-base font-extrabold text-charcoal mt-0.5">{{ sesiAktif.sesi }}/{{ sesiAktif.totalSesi }}</div>
                                </div>
                                <div v-if="sesiAktif.status === 'sedang'" class="flex-1 bg-white/80 rounded-2xl px-3.5 py-2.5">
                                    <div class="text-[10px] font-bold text-charcoal/60 uppercase tracking-wider">Hadir</div>
                                    <div class="text-base font-extrabold text-[#2E7D32] mt-0.5">{{ sesiAktif.hadir }} <span class="text-xs text-charcoal/50">orang</span></div>
                                </div>
                            </div>

                            <!-- CTA Buttons -->
                            <div class="flex items-center gap-2 mt-4">
                                <button
                                    type="button"
                                    @click="$emit('toggleGate')"
                                    class="px-4 py-2.5 rounded-full bg-charcoal text-white text-xs font-extrabold shadow-sm active:scale-95 transition-transform cursor-pointer"
                                >
                                    {{ teacherGateOpen ? '🔴 Tutup Presensi' : '🟢 Buka Presensi (15m)' }}
                                </button>
                                <button
                                    type="button"
                                    @click="$emit('selectTab', 'mapel')"
                                    class="px-3.5 py-2.5 rounded-full bg-white/90 text-charcoal text-xs font-bold cursor-pointer hover:bg-white border border-white/60 transition-colors shadow-2xs"
                                >
                                    Kelola Mapel →
                                </button>
                            </div>

                            <!-- Decorative blobs -->
                            <div class="absolute -right-6 -bottom-6 w-28 h-28 rounded-full bg-white/15 pointer-events-none"></div>
                            <div class="absolute right-10 -bottom-10 w-16 h-16 rounded-full bg-white/10 pointer-events-none"></div>
                        </div>

                        <!-- Empty state hari tanpa jadwal -->
                        <div v-else class="rounded-[28px] p-6 bg-white border border-[#ECEAE4] soft-shadow text-center">
                            <div class="w-14 h-14 rounded-full bg-muted flex items-center justify-center mx-auto mb-3">
                                <LmsIcon name="clock" className="w-6 h-6 text-subtext" />
                            </div>
                            <h3 class="text-sm font-extrabold text-charcoal">Tidak Ada Jadwal Mengajar</h3>
                            <p class="text-xs text-subtext mt-1">{{ getDayLabel(selectedDay) }} tidak ada sesi mengajar terjadwal.</p>
                        </div>

                        <!-- 2. KAPSUL KALENDER JADWAL MINGGUAN -->
                        <LmsCapsuleCalendar
                            title="Jadwal Mengajar Mingguan"
                            :days="days"
                            :selectedDay="selectedDay"
                            @selectDay="(d) => $emit('selectDay', d)"
                        />

                        <!-- 3. SEMUA SESI HARI INI (jika > 1) -->
                        <div v-if="jadwalHariIni.length > 1">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-[15px] font-extrabold text-charcoal">Semua Sesi Hari Ini</h3>
                                <span class="text-[11px] font-bold text-subtext">{{ jadwalHariIni.length }} Sesi</span>
                            </div>
                            <div class="space-y-2.5">
                                <div
                                    v-for="sesi in jadwalHariIni"
                                    :key="sesi.id"
                                    @click="$emit('selectTab', 'mapel')"
                                    class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow cursor-pointer hover:border-charcoal/25 active:scale-[0.99] transition-all flex items-center gap-3.5 group"
                                >
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 font-extrabold text-xs" :class="sesi.badgeColor">
                                        <LmsIcon name="book" className="w-5 h-5" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" :class="statusSesiColor(sesi.status)">
                                                {{ statusSesiLabel(sesi.status) }}
                                            </span>
                                        </div>
                                        <h4 class="text-sm font-extrabold text-charcoal group-hover:text-black truncate">{{ sesi.shortName }}</h4>
                                        <p class="text-[11px] text-subtext mt-0.5">{{ sesi.rombel }} • {{ sesi.jam }} • {{ sesi.ruang }}</p>
                                    </div>
                                    <LmsIcon name="chevronRight" className="w-4 h-4 text-subtext shrink-0" />
                                </div>
                            </div>
                        </div>

                        <!-- 4. TWO-COLUMN BOTTOM: Antrean Nilai + Mapel Diampu -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Antrean Penilaian Cepat -->
                            <div class="bg-white rounded-[28px] p-5 border border-[#ECEAE4] soft-shadow">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center">
                                            <LmsIcon name="task" className="w-3.5 h-3.5" />
                                        </span>
                                        <h3 class="text-sm font-extrabold text-charcoal">Antrean Penilaian</h3>
                                    </div>
                                    <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-[#FFF3E0] text-[#E65100]">
                                        {{ pendingNilai.reduce((a, b) => a + b.belumDinilai, 0) }} Belum Dinilai
                                    </span>
                                </div>
                                <div class="space-y-2.5">
                                    <div
                                        v-for="(item, i) in pendingNilai"
                                        :key="i"
                                        @click="$emit('selectTab', 'mapel')"
                                        class="bg-canvas rounded-[20px] p-3.5 border border-[#ECEAE4] cursor-pointer hover:border-charcoal/25 transition-all group flex items-center gap-3"
                                    >
                                        <div class="flex-1 min-w-0">
                                            <div class="text-[10px] font-bold text-subtext">{{ item.mapel }} • {{ item.rombel }}</div>
                                            <div class="text-xs font-extrabold text-charcoal group-hover:text-black truncate mt-0.5">{{ item.nama }}</div>
                                            <div class="text-[10px] text-subtext mt-0.5">{{ item.totalKumpul }} Mengumpulkan • {{ item.belumDinilai }} Belum Dinilai</div>
                                        </div>
                                        <span class="px-3 py-1.5 rounded-full bg-charcoal text-white text-[11px] font-extrabold shrink-0 group-hover:opacity-80 transition">Nilai</span>
                                    </div>
                                </div>
                                <div v-if="pendingNilai.length === 0" class="text-center py-6 text-subtext text-xs">
                                    Semua tugas sudah dinilai 🎉
                                </div>
                            </div>

                            <!-- Mapel Diampu -->
                            <div class="bg-white rounded-[28px] p-5 border border-[#ECEAE4] soft-shadow">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-full bg-sage-light text-sage-dark flex items-center justify-center">
                                            <LmsIcon name="book" className="w-3.5 h-3.5" />
                                        </span>
                                        <h3 class="text-sm font-extrabold text-charcoal">Mata Pelajaran</h3>
                                    </div>
                                    <button type="button" @click="$emit('selectTab', 'mapel')" class="text-xs font-bold text-blossom-dark hover:underline cursor-pointer">
                                        Kelola Semua →
                                    </button>
                                </div>
                                <div class="space-y-2.5">
                                    <div
                                        v-for="m in guruMapelList"
                                        :key="m.id"
                                        @click="$emit('selectTab', 'mapel')"
                                        class="bg-canvas rounded-[20px] p-3.5 border border-[#ECEAE4] cursor-pointer hover:border-charcoal/25 active:scale-[0.99] transition-all group flex items-center gap-3"
                                    >
                                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 font-extrabold text-xs" :class="m.badgeColor">
                                            <LmsIcon name="book" className="w-4 h-4" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-[10px] font-bold text-subtext">{{ m.code }} • {{ m.rombel.join(', ') }}</div>
                                            <h4 class="text-xs font-extrabold text-charcoal group-hover:text-black truncate">{{ m.name }}</h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="flex-1 h-1 bg-muted rounded-full overflow-hidden">
                                                    <div
                                                        class="h-full bg-charcoal rounded-full"
                                                        :style="`width:${(m.completedMeetings/m.totalMeetings)*100}%`"
                                                    ></div>
                                                </div>
                                                <span class="text-[10px] font-bold text-charcoal shrink-0">{{ m.completedMeetings }}/{{ m.totalMeetings }}</span>
                                            </div>
                                        </div>
                                        <LmsIcon name="chevronRight" className="w-4 h-4 text-subtext shrink-0" />
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ==========================================
                 TAB 2: MAPEL (Materi, Tugas, Kuis Online)
                 ========================================== -->
            <MapelGuruView
                v-else-if="activeTab === 'mapel' || activeTab === 'materi'"
                key="mapel"
                :role="role"
                :isSidebarCollapsed="isSidebarCollapsed"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
                @toggleSidebar="$emit('toggleSidebar')"
            />

            <!-- ==========================================
                 TAB 3: REKAP NILAI MAPEL
                 ========================================== -->
            <RekapNilaiGuruView
                v-else-if="activeTab === 'rekapNilai'"
                key="rekapNilai"
                :role="role"
                :isSidebarCollapsed="isSidebarCollapsed"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
                @toggleSidebar="$emit('toggleSidebar')"
            />

        </Transition>
    </div>
</template>

<style scoped>
.tab-view-enter-active,
.tab-view-leave-active {
    transition: opacity 0.22s cubic-bezier(0.16, 1, 0.3, 1), transform 0.22s cubic-bezier(0.16, 1, 0.3, 1);
}
.tab-view-enter-from {
    opacity: 0;
    transform: translateY(6px) scale(0.995);
}
.tab-view-leave-to {
    opacity: 0;
    transform: translateY(-6px) scale(0.995);
}
.live-dot {
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(0.7); }
}
</style>
