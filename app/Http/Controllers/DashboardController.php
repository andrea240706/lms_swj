<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the multi-role LMS dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $userRoleKey = $user ? $user->getRoleKey() : 'siswa';

        // Allow role switching/preview if provided in request query, default to authenticated user role
        $simulatedRole = $request->query('role', $userRoleKey);

        $roles = [
            'siswa' => [
                'id' => 'siswa',
                'label' => 'Siswa',
                'badge' => 'XII RPL 1',
                'userName' => $user && $user->role === 'siswa' ? $user->name : 'Nadia Putri',
                'userSub' => 'NISN: 008492104 • Rekayasa Perangkat Lunak',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-sage',
                'focusNote' => 'Akses sesi aktif, unduh modul belajar, kumpul tugas, & isi presensi mandiri.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Home', 'icon' => 'home'],
                    ['id' => 'materi', 'label' => 'Materi', 'icon' => 'book'],
                    ['id' => 'tugas', 'label' => 'Tugas', 'icon' => 'task'],
                    ['id' => 'presensi', 'label' => 'Rekap', 'icon' => 'chart'],
                ],
            ],
            'guru' => [
                'id' => 'guru',
                'label' => 'Guru Mapel',
                'badge' => 'Produktif RPL',
                'userName' => $user && in_array($user->role, ['guru', 'teacher']) ? $user->name : 'Budi Santoso, S.Kom',
                'userSub' => 'NIP: 19880412 201504 1 002 • Pengampu RPL',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-blossom',
                'focusNote' => 'Buka presensi dengan timer, kunci/buka bab materi, & nilai tugas cepat.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Home', 'icon' => 'home'],
                    ['id' => 'materi', 'label' => 'Materi', 'icon' => 'book'],
                    ['id' => 'tugas', 'label' => 'Nilai', 'icon' => 'task'],
                    ['id' => 'presensi', 'label' => 'Sesi', 'icon' => 'clock'],
                ],
            ],
            'wali_kelas' => [
                'id' => 'wali_kelas',
                'label' => 'Wali Kelas',
                'badge' => 'Wali X DKV 1',
                'userName' => $user && in_array($user->role, ['wali', 'wali_kelas']) ? $user->name : 'Siti Rahma, M.Pd',
                'userSub' => 'Pembina Rombel X DKV 1 • 36 Siswa',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-sky',
                'focusNote' => 'Early warning siswa bermasalah, presensi perwalian, & leger nilai rombel.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Home', 'icon' => 'home'],
                    ['id' => 'absensi', 'label' => 'Absensi', 'icon' => 'clock'],
                    ['id' => 'leger', 'label' => 'Leger', 'icon' => 'book'],
                    ['id' => 'laporan', 'label' => 'Rekap', 'icon' => 'chart'],
                ],
            ],
            'kepsek' => [
                'id' => 'kepsek',
                'label' => 'Kepala Sekolah',
                'badge' => 'Eksekutif',
                'userName' => $user && in_array($user->role, ['kepsek', 'principal']) ? $user->name : 'Drs. Hendra Wijaya',
                'userSub' => 'Kepala SMKN 1 Nusantara',
                'avatar' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-amber',
                'focusNote' => 'Monitoring keterlaksanaan KBM makro, analitik jurusan, & unduh PDF resmi.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Home', 'icon' => 'home'],
                    ['id' => 'jurusan', 'label' => 'Jurusan', 'icon' => 'chart'],
                    ['id' => 'kbm', 'label' => 'KBM', 'icon' => 'book'],
                    ['id' => 'ekspor', 'label' => 'Ekspor', 'icon' => 'download'],
                ],
            ],
            'admin' => [
                'id' => 'admin',
                'label' => 'Admin TU',
                'badge' => 'Operator',
                'userName' => $user && $user->role === 'admin' ? $user->name : 'Rizky Pratama',
                'userSub' => 'Operator Akademik & Kesiswaan',
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-lavender',
                'focusNote' => 'Kelola siswa, reset sandi instan, plotting guru/wali kelas, & koreksi absen.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Home', 'icon' => 'home'],
                    ['id' => 'pengguna', 'label' => 'Siswa', 'icon' => 'user'],
                    ['id' => 'plotting', 'label' => 'Plotting', 'icon' => 'book'],
                    ['id' => 'moderasi', 'label' => 'Koreksi', 'icon' => 'clock'],
                ],
            ],
            'super_admin' => [
                'id' => 'super_admin',
                'label' => 'Super Admin',
                'badge' => 'Root System',
                'userName' => $user && $user->role === 'super_admin' ? $user->name : 'Arif Darmawan',
                'userSub' => 'Kepala Pusdatin & Infrastruktur IT',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-sage',
                'focusNote' => 'Konfigurasi tahun ajaran aktif, data master jurusan/mapel, & audit log.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Home', 'icon' => 'home'],
                    ['id' => 'master', 'label' => 'Master', 'icon' => 'book'],
                    ['id' => 'akses', 'label' => 'Pejabat', 'icon' => 'user'],
                    ['id' => 'log', 'label' => 'Audit', 'icon' => 'chart'],
                ],
            ],
        ];

        $days = [
            ['d' => 'Sun', 'n' => 17],
            ['d' => 'Mon', 'n' => 18],
            ['d' => 'Tue', 'n' => 19],
            ['d' => 'Wed', 'n' => 20],
            ['d' => 'Thu', 'n' => 21],
            ['d' => 'Fri', 'n' => 22],
            ['d' => 'Sat', 'n' => 23],
        ];

        $assignments = [
            [
                'id' => 101,
                'title' => 'Praktikum REST API Laravel 13',
                'subject' => 'Pemrograman Web',
                'due' => 'Hari ini, 16:00',
                'status' => 'Perlu Dikumpulkan',
                'score' => null,
                'feedback' => '',
                'allowRevision' => true,
            ],
            [
                'id' => 102,
                'title' => 'Desain ERD Sistem Kasir SMK',
                'subject' => 'Basis Data',
                'due' => '22 Okt 2026',
                'status' => 'Dinilai',
                'score' => 92,
                'feedback' => 'Relasi antar tabel sudah sangat rapi dan memenuhi 3NF.',
                'allowRevision' => false,
            ],
            [
                'id' => 103,
                'title' => 'Konfigurasi VLAN & Routing Statis',
                'subject' => 'Infrastruktur Jaringan',
                'due' => '18 Okt 2026',
                'status' => 'Perlu Revisi',
                'score' => 70,
                'feedback' => 'Sub-interface router belum diberi IP gateway yang sesuai.',
                'allowRevision' => true,
            ],
        ];

        return Inertia::render('Dashboard', [
            'roles' => $roles,
            'initialRole' => isset($roles[$simulatedRole]) ? $simulatedRole : 'siswa',
            'days' => $days,
            'initialAssignments' => $assignments,
            'currentUser' => $user,
        ]);
    }
}
