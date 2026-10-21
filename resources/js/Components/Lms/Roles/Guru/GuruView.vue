<script setup>
import { ref } from 'vue';
import LmsCapsuleCalendar from '@/Components/Lms/LmsCapsuleCalendar.vue';
import LmsIcon from '@/Components/Lms/LmsIcon.vue';
import LmsTopBar from '@/Components/Lms/LmsTopBar.vue';
import MapelGuruView from '@/Components/Lms/Roles/Guru/MapelGuruView.vue';
import { guruMapelList } from './mapelGuruData';

const props = defineProps({
    role: {
        type: Object,
        required: true,
    },
    activeTab: {
        type: String,
        default: 'home',
    },
    selectedDay: {
        type: Number,
        default: 18,
    },
    days: {
        type: Array,
        default: () => [],
    },
    teacherGateOpen: {
        type: Boolean,
        default: true,
    },
    materialsLock: {
        type: Object,
        default: () => ({ 1: false, 2: false, 3: true, 4: true }),
    },
    isSidebarCollapsed: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits([
    'selectTab',
    'selectDay',
    'toggleGate',
    'toggleLock',
    'openGradeModal',
    'openMenu',
    'openProfile',
    'toggleSidebar',
]);

const chapters = [
    { id: 1, title: 'Bab 1: Arsitektur MVC & Routing', rombel: 'XII RPL 1, XII RPL 2' },
    { id: 2, title: 'Bab 2: Eloquent ORM & Migration', rombel: 'XII RPL 1, XII RPL 2' },
    { id: 3, title: 'Bab 3: Middleware & Multi-Role Auth', rombel: 'XII RPL 1' },
    { id: 4, title: 'Bab 4: Deployment Server Produksi', rombel: 'XII RPL 1' },
];

const submissions = ref([
    { name: 'Nadia Putri', nisn: '008492104', time: 'Tepat Waktu • 14:20', score: 92 },
    { name: 'Dimas Anggara', nisn: '008492119', time: 'Tepat Waktu • 14:45', score: null },
    { name: 'Putri Maharani', nisn: '008492130', time: 'Terlambat 10m • 16:10', score: null },
]);

const attendanceList = ref([
    { name: 'Nadia Putri', status: 'Hadir' },
    { name: 'Dimas Anggara', status: 'Hadir' },
    { name: 'Raka Pratama', status: 'Sakit' },
    { name: 'Fajar Nugraha', status: 'Hadir' },
]);

const setAttendanceStatus = (item, status) => {
    item.status = status;
};
</script>

<template>
    <div class="flex-1 flex flex-col overflow-hidden">
        <Transition name="tab-view" mode="out-in">
            <!-- ========================================== -->
            <!-- TAB 1: HOME (SESI MENGAJAR & JADWAL)      -->
            <!-- ========================================== -->
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

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
                <h2 class="text-[17px] font-extrabold text-charcoal mb-2.5">
                    Sesi Mengajar Hari Ini
                </h2>

                <div class="hero-blossom rounded-[28px] p-5 soft-shadow">
                    <div class="flex items-center justify-between">
                        <span
                            class="bg-white px-3 py-1 rounded-full text-[11px] font-extrabold text-charcoal shadow-2xs"
                        >
                            08:00 - 10:30 WIB
                        </span>
                        <span class="text-[11px] font-bold text-blossom-dark">Lab RPL 2</span>
                    </div>
                    <h3 class="text-[20px] font-extrabold text-charcoal mt-3 leading-tight">
                        Pemrograman Web • XII RPL 1
                    </h3>
                    <p class="text-xs text-charcoal/75 mt-1">
                        34 Siswa Terdaftar • Bab 06 Aktif
                    </p>

                    <div class="mt-4 flex items-center gap-2">
                        <button
                            type="button"
                            @click="$emit('toggleGate')"
                            class="px-4 py-2.5 rounded-full bg-charcoal text-white text-xs font-bold shadow-sm active:scale-95 transition-transform cursor-pointer"
                        >
                            {{ teacherGateOpen ? 'Tutup Gerbang Presensi (Aktif)' : 'Buka Presensi (15 Menit)' }}
                        </button>
                        <button
                            type="button"
                            @click="$emit('selectTab', 'tugas')"
                            class="px-3.5 py-2.5 rounded-full bg-white text-charcoal text-xs font-bold cursor-pointer hover:bg-muted transition-colors shadow-2xs"
                        >
                            Nilai Tugas (3)
                        </button>
                    </div>
                </div>

                <LmsCapsuleCalendar
                    title="Jadwal Mengajar"
                    :days="days"
                    :selectedDay="selectedDay"
                    @selectDay="(d) => $emit('selectDay', d)"
                />

                <h3 class="text-[15px] font-extrabold text-charcoal mt-5 mb-2.5">
                    Antrean Penilaian Cepat
                </h3>
                <div class="space-y-2.5">
                    <div
                        @click="$emit('selectTab', 'tugas')"
                        class="cursor-pointer bg-white rounded-[24px] p-3.5 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 transition-all"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="w-11 h-11 rounded-full bg-blossom-light text-blossom-dark flex items-center justify-center font-bold text-xs shrink-0"
                            >
                                XII
                            </div>
                            <div>
                                <div class="text-xs font-extrabold text-charcoal">
                                    Praktikum REST API Laravel
                                </div>
                                <div class="text-[11px] text-subtext">
                                    28/34 Siswa Mengumpulkan • 2 Belum Dinilai
                                </div>
                            </div>
                        </div>
                        <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-charcoal text-white">
                            Nilai
                        </span>
                    </div>
                </div>

                <!-- Mata Pelajaran yang Diampu -->
                <div class="mt-6">
                    <div class="flex items-center justify-between mb-2.5">
                        <h3 class="text-[15px] font-extrabold text-charcoal">
                            Mata Pelajaran yang Diampu
                        </h3>
                        <button
                            type="button"
                            @click="$emit('selectTab', 'mapel')"
                            class="text-xs font-bold text-blossom-dark hover:underline cursor-pointer"
                        >
                            Buka Semua
                        </button>
                    </div>

                    <div class="space-y-2.5">
                        <div
                            v-for="m in guruMapelList"
                            :key="m.id"
                            @click="$emit('selectTab', 'mapel')"
                            class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] soft-shadow flex items-center justify-between hover:border-charcoal/30 cursor-pointer transition-all group"
                        >
                            <div class="flex items-center gap-3.5">
                                <div
                                    class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 font-bold text-xs"
                                    :class="m.badgeColor"
                                >
                                    <LmsIcon name="book" className="w-5 h-5" />
                                </div>
                                <div>
                                    <div class="text-[11px] font-bold text-subtext">
                                        {{ m.code }} • {{ m.rombel.join(', ') }}
                                    </div>
                                    <h4 class="text-sm font-extrabold text-charcoal group-hover:text-blossom-dark transition-colors">
                                        {{ m.name }}
                                    </h4>
                                    <div class="text-[11px] text-subtext mt-0.5">
                                        {{ m.completedMeetings }}/{{ m.totalMeetings }} Pertemuan • {{ m.totalStudents }} Siswa
                                    </div>
                                </div>
                            </div>
                            <div class="w-9 h-9 rounded-full bg-muted flex items-center justify-center text-charcoal group-hover:bg-charcoal group-hover:text-white transition-all shrink-0">
                                <LmsIcon name="arrow-right" className="w-4 h-4" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: MAPEL (KELOLA & PENILAIAN PENUH)   -->
            <!-- ========================================== -->
            <MapelGuruView
                v-else-if="activeTab === 'mapel' || activeTab === 'materi'"
                key="mapel"
                :role="role"
                :isSidebarCollapsed="isSidebarCollapsed"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
                @toggleSidebar="$emit('toggleSidebar')"
            />

            <!-- ========================================== -->
            <!-- TAB 3: TUGAS (PENILAIAN TUGAS SISWA)      -->
            <!-- ========================================== -->
            <div
                v-else-if="activeTab === 'tugas'"
                key="tugas"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
            <LmsTopBar
                :role="role"
                :isSidebarCollapsed="isSidebarCollapsed"
                title="Penilaian Tugas Siswa"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
                @toggleSidebar="$emit('toggleSidebar')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
                <div class="bg-white rounded-[24px] p-4 border border-[#ECEAE4] mb-4 shadow-2xs">
                    <div class="text-[11px] font-bold text-subtext">Tugas Aktif • XII RPL 1</div>
                    <div class="text-sm font-extrabold text-charcoal">
                        Praktikum REST API Laravel 13
                    </div>
                </div>

                <div class="space-y-2.5">
                    <div
                        v-for="s in submissions"
                        :key="s.name"
                        class="bg-white rounded-[24px] p-3.5 border border-[#ECEAE4] soft-shadow flex items-center justify-between"
                    >
                        <div>
                            <div class="text-sm font-extrabold text-charcoal">{{ s.name }}</div>
                            <div class="text-[11px] text-subtext">{{ s.time }}</div>
                        </div>
                        <button
                            type="button"
                            @click="$emit('openGradeModal', s.name)"
                            class="px-3.5 py-2 rounded-full text-xs font-bold transition-all cursor-pointer"
                            :class="
                                s.score !== null
                                    ? 'bg-sage-light text-sage-dark'
                                    : 'bg-charcoal text-white hover:opacity-90'
                            "
                        >
                            {{ s.score !== null ? `Nilai: ${s.score}` : 'Beri Nilai' }}
                        </button>
                    </div>
                </div>
            </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 4: PRESENSI KELAS (MANUAL CHECKLIST)   -->
            <!-- ========================================== -->
            <div
                v-else-if="activeTab === 'presensi'"
                key="presensi"
                class="flex-1 flex flex-col h-full overflow-hidden"
            >
            <LmsTopBar
                :role="role"
                :isSidebarCollapsed="isSidebarCollapsed"
                title="Kontrol Presensi Kelas"
                @openMenu="$emit('openMenu')"
                @openProfile="$emit('openProfile')"
                @toggleSidebar="$emit('toggleSidebar')"
            />

            <div class="flex-1 overflow-y-auto no-scrollbar px-6 pb-28">
                <div class="space-y-2.5">
                    <div
                        v-for="st in attendanceList"
                        :key="st.name"
                        class="bg-white rounded-[22px] p-3.5 border border-[#ECEAE4] flex items-center justify-between shadow-2xs"
                    >
                        <div class="text-sm font-bold text-charcoal">{{ st.name }}</div>
                        <div class="flex gap-1">
                            <button
                                v-for="k in ['Hadir', 'Izin', 'Sakit', 'Alpa']"
                                :key="k"
                                type="button"
                                @click="setAttendanceStatus(st, k)"
                                class="px-2.5 py-1 rounded-full text-[10px] font-bold cursor-pointer transition-all"
                                :class="
                                    st.status === k
                                        ? 'bg-charcoal text-white shadow-2xs'
                                        : 'bg-muted text-subtext hover:bg-slate-200'
                                "
                            >
                                {{ k[0] }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            </div>
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
</style>
