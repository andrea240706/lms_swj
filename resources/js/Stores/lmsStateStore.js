import { computed, reactive, ref } from 'vue';
import {
    initialSystemSettings,
    initialUsersList,
    masterJurusan,
    masterMapel,
    masterRombel,
    masterTahunAjaran,
    initialPlottingWali,
    initialPlottingGuruMapel,
    initialJadwalMingguan,
} from '@/Components/Lms/Roles/Admin/adminData';
import { daftarSiswaPerwalian, rombelInfo } from '@/Components/Lms/Roles/WaliKelas/waliKelasData';
import { kepsekOverviewData, kbmTingkatData, teacherSupervisionList } from '@/Components/Lms/Roles/Kepsek/kepsekData';

/**
 * LMS CENTRALIZED REACTIVE STATE STORE
 * Menghubungkan seluruh 5 peran (Admin, Guru, Siswa, Wali Kelas, Kepsek)
 * secara realtime, dua arah, dan berkesinambungan.
 */

// 1. PENGATURAN SISTEM & MAINTENANCE MODE (Admin -> Seluruh Role)
const systemSettings = reactive({ ...initialSystemSettings });

const toggleMaintenanceMode = (override = null) => {
    if (override !== null) {
        systemSettings.maintenanceMode = override;
    } else {
        systemSettings.maintenanceMode = !systemSettings.maintenanceMode;
    }
    return systemSettings.maintenanceMode;
};

// 2. DATA MASTER AKADEMIK (Admin -> Guru, Siswa, Wali, Kepsek)
const usersList = ref([...initialUsersList]);
const rombelList = ref([...masterRombel]);
const mapelList = ref([...masterMapel]);
const jadwalList = ref([...initialJadwalMingguan]);
const plottingWaliList = ref([...initialPlottingWali]);
const plottingGuruList = ref([...initialPlottingGuruMapel]);

// 3. DATA SISWA & PERWALIAN (Wali Kelas <-> Siswa <-> Kepsek)
const waliKelasStudents = ref(JSON.parse(JSON.stringify(daftarSiswaPerwalian)));

// Hitung presensi agregat siswa X DKV 1 dari Wali Kelas
const waliKelasPresensiCounts = computed(() => {
    const counts = { Hadir: 0, Sakit: 0, Izin: 0, Alpa: 0, Terlambat: 0 };
    waliKelasStudents.value.forEach((s) => {
        const st = s.presensiHariIni || 'Hadir';
        if (counts[st] !== undefined) {
            counts[st]++;
        } else {
            counts.Hadir++;
        }
    });
    return counts;
});

// Update presensi per siswa oleh Wali Kelas (Otomatis merefleksikan ke Kepsek & Siswa)
const updateStudentPresensi = (studentId, status, keterangan = '') => {
    const student = waliKelasStudents.value.find((s) => s.id === studentId);
    if (student) {
        const oldStatus = student.presensiHariIni;
        student.presensiHariIni = status;
        if (keterangan) {
            student.keteranganPresensi = keterangan;
        }

        // Sinkronisasi ke rekap tingkat X di Kepala Sekolah
        const tingkatX = kbmTingkatData.find((t) => t.tingkat.includes('Kelas X'));
        if (tingkatX) {
            if (oldStatus === 'Hadir' && status !== 'Hadir') {
                tingkatX.hadir = Math.max(0, tingkatX.hadir - 1);
                if (status === 'Sakit') tingkatX.sakit++;
                if (status === 'Izin') tingkatX.izin++;
                if (status === 'Alpa') tingkatX.alpa++;
            } else if (oldStatus !== 'Hadir' && status === 'Hadir') {
                tingkatX.hadir++;
                if (oldStatus === 'Sakit') tingkatX.sakit = Math.max(0, tingkatX.sakit - 1);
                if (oldStatus === 'Izin') tingkatX.izin = Math.max(0, tingkatX.izin - 1);
                if (oldStatus === 'Alpa') tingkatX.alpa = Math.max(0, tingkatX.alpa - 1);
            }
            tingkatX.presensiPct = `${((tingkatX.hadir / tingkatX.siswaCount) * 100).toFixed(1)}%`;
        }

        // Sinkronisasi ke kartu KPI Eksekutif Kepala Sekolah
        const totalHadirSemua = kbmTingkatData.reduce((acc, curr) => acc + curr.hadir, 0);
        kepsekOverviewData.siswaHadirCount = totalHadirSemua;
        kepsekOverviewData.siswaHadirPct = `${((totalHadirSemua / kepsekOverviewData.totalSiswa) * 100).toFixed(1)}%`;
    }
};

// 4. PENUGASAN, TUGAS & NILAI (Siswa <-> Guru <-> Wali Kelas Leger)
const assignments = ref([
    {
        id: 101,
        title: 'Praktikum REST API Laravel 13',
        subject: 'Pemrograman Web',
        mapelCode: 'dkv', // Relasi ke leger
        due: 'Hari ini, 16:00',
        status: 'Perlu Dikumpulkan',
        score: null,
        feedback: '',
        allowRevision: true,
        studentName: 'Nadia Putri',
        studentNisn: '008492104',
    },
    {
        id: 102,
        title: 'Desain ERD Sistem Kasir SMK',
        subject: 'Basis Data',
        mapelCode: 'grafis',
        due: '22 Okt 2026',
        status: 'Dinilai',
        score: 92,
        feedback: 'Relasi antar tabel sudah sangat rapi dan memenuhi 3NF.',
        allowRevision: false,
        studentName: 'Nadia Putri',
        studentNisn: '008492104',
    },
    {
        id: 103,
        title: 'Konfigurasi VLAN & Routing Statis',
        subject: 'Infrastruktur Jaringan',
        mapelCode: 'foto',
        due: '18 Okt 2026',
        status: 'Perlu Revisi',
        score: 70,
        feedback: 'Sub-interface router belum diberi IP gateway yang sesuai.',
        allowRevision: true,
        studentName: 'Nadia Putri',
        studentNisn: '008492104',
    },
]);

// Siswa mengumpulkan tugas -> Update status tugas & antrean guru
const submitStudentAssignment = (assignmentId) => {
    const target = assignments.value.find((a) => a.id === assignmentId);
    if (target) {
        target.status = 'Menunggu Penilaian Guru';
    }
};

// Guru menilai tugas -> Update nilai siswa & sinkron ke Leger Nilai Wali Kelas
const gradeStudentAssignment = (assignmentId, score, feedback = '') => {
    const target = assignments.value.find((a) => a.id === assignmentId);
    if (target) {
        target.score = Number(score);
        target.feedback = feedback;
        target.status = 'Dinilai';

        // Sinkronisasi otomatis ke Leger Nilai Wali Kelas
        // Cari siswa pertama di perwalian untuk refleksi live
        if (waliKelasStudents.value.length > 0 && target.mapelCode) {
            const firstStudent = waliKelasStudents.value[0];
            if (firstStudent.grades && firstStudent.grades[target.mapelCode] !== undefined) {
                firstStudent.grades[target.mapelCode] = Number(score);
            }
        }
    }
};

// 5. KUNCI MATERI GURU (Guru -> Siswa)
const materialsLock = reactive({ 1: false, 2: false, 3: true, 4: true });

const toggleMaterialLock = (meetingId) => {
    materialsLock[meetingId] = !materialsLock[meetingId];
};

// 6. SUPERVISI GURU & JURNAL (Guru -> Kepsek)
const teacherList = ref([...teacherSupervisionList]);

const updateTeacherJournalStatus = (teacherId, status = 'Tertib Terisi') => {
    const t = teacherList.value.find((item) => item.id === teacherId);
    if (t) {
        t.journalStatus = status;
    }
};

export const useLmsStore = () => {
    return {
        // System & Maintenance
        systemSettings,
        toggleMaintenanceMode,

        // Data Master
        usersList,
        rombelList,
        mapelList,
        jadwalList,
        plottingWaliList,
        plottingGuruList,

        // Wali Kelas & Presensi
        waliKelasStudents,
        waliKelasPresensiCounts,
        updateStudentPresensi,
        rombelInfo,

        // Siswa & Guru (Tugas & Nilai)
        assignments,
        submitStudentAssignment,
        gradeStudentAssignment,

        // Kunci Materi
        materialsLock,
        toggleMaterialLock,

        // Kepsek
        teacherList,
        updateTeacherJournalStatus,
        kepsekOverviewData,
        kbmTingkatData,
    };
};
