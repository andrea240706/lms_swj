<?php

namespace App\Http\Controllers;

use App\Http\Resources\MaterialResource;
use App\Models\Material;
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
                'userName' => $user && $user->getRoleKey() === 'siswa' ? $user->name : 'Nadia Putri',
                'userSub' => $user && $user->getRoleKey() === 'siswa' ? 'NISN: '.$user->username.' • Rekayasa Perangkat Lunak' : 'NISN: 008492104 • Rekayasa Perangkat Lunak',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-sage',
                'focusNote' => 'Akses sesi aktif, unduh modul belajar, kumpul tugas, & isi presensi mandiri.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Home', 'icon' => 'home'],
                    ['id' => 'mapel', 'label' => 'Mapel', 'icon' => 'book'],
                    ['id' => 'tugas', 'label' => 'Tugas', 'icon' => 'task'],
                    ['id' => 'presensi', 'label' => 'Rekap', 'icon' => 'chart'],
                ],
            ],
            'guru' => [
                'id' => 'guru',
                'label' => 'Guru Mapel',
                'badge' => 'Produktif RPL',
                'userName' => $user && $user->getRoleKey() === 'guru' ? $user->name : 'Budi Santoso, S.Kom',
                'userSub' => $user && $user->getRoleKey() === 'guru' ? 'NIP: '.$user->username.' • Pengampu RPL' : 'NIP: 19880412 201504 1 002 • Pengampu RPL',
                'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-blossom',
                'focusNote' => 'Buka presensi dengan timer, kunci/buka bab materi, & nilai tugas cepat.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Dashboard', 'icon' => 'home'],
                    ['id' => 'mapel', 'label' => 'Kelas & Mapel', 'icon' => 'book'],
                    ['id' => 'rekapNilai', 'label' => 'Rekap Nilai', 'icon' => 'chart'],
                ],
            ],
            'wali_kelas' => [
                'id' => 'wali_kelas',
                'label' => 'Wali Kelas',
                'badge' => 'Wali X DKV 1',
                'userName' => $user && $user->getRoleKey() === 'wali_kelas' ? $user->name : 'Siti Rahma, M.Pd',
                'userSub' => 'Pembina Rombel X DKV 1 • 36 Siswa',
                'avatar' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-sky',
                'focusNote' => 'Early warning siswa bermasalah, presensi perwalian, & leger nilai rombel.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Dashboard', 'icon' => 'home'],
                    ['id' => 'presensiPagi', 'label' => 'Presensi Pagi', 'icon' => 'clock'],
                    ['id' => 'siswaPerwalian', 'label' => 'Data Siswa', 'icon' => 'user'],
                    ['id' => 'rekapNilai', 'label' => 'Rekap Nilai', 'icon' => 'chart'],
                ],
            ],
            'kepsek' => [
                'id' => 'kepsek',
                'label' => 'Kepala Sekolah',
                'badge' => 'Eksekutif',
                'userName' => $user && $user->getRoleKey() === 'kepsek' ? $user->name : 'Drs. Hendra Wijaya',
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
                'userName' => $user && $user->getRoleKey() === 'admin' ? $user->name : 'Rizky Pratama',
                'userSub' => 'Operator Akademik & Kesiswaan',
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-lavender',
                'focusNote' => 'Kelola siswa, reset sandi instan, plotting guru/wali kelas, & koreksi absen.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Operasional', 'icon' => 'home'],
                    ['id' => 'pengguna', 'label' => 'Pengguna', 'icon' => 'user'],
                    ['id' => 'master', 'label' => 'Data Master', 'icon' => 'book'],
                    ['id' => 'penugasan', 'label' => 'Penugasan & Jadwal', 'icon' => 'clock'],
                    ['id' => 'pengumuman', 'label' => 'Pengumuman', 'icon' => 'task'],
                ],
            ],
            'super_admin' => [
                'id' => 'super_admin',
                'label' => 'Super Admin',
                'badge' => 'Root System',
                'userName' => $user && $user->getRoleKey() === 'super_admin' ? $user->name : 'Arif Darmawan',
                'userSub' => 'Kepala Pusdatin & Infrastruktur IT',
                'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-sage',
                'focusNote' => 'Kelola akun operator/admin dan konfigurasi global menu admin.',
                'tabs' => [
                    ['id' => 'admin_mgmt', 'label' => 'Manajemen Admin', 'icon' => 'user'],
                    ['id' => 'system_config', 'label' => 'Pengaturan Sistem', 'icon' => 'lock'],
                ],
            ],
        ];

        $days = [
            ['d' => 'Sen', 'n' => 18, 'full' => 'Senin'],
            ['d' => 'Sel', 'n' => 19, 'full' => 'Selasa'],
            ['d' => 'Rab', 'n' => 20, 'full' => 'Rabu'],
            ['d' => 'Kam', 'n' => 21, 'full' => 'Kamis'],
            ['d' => 'Jum', 'n' => 22, 'full' => 'Jumat'],
            ['d' => 'Sab', 'n' => 23, 'full' => 'Sabtu'],
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

        $canManageMaterials = $user !== null && ($user->isTeacher() || $user->isAdmin());

        $materials = Material::query()
            ->with(['files' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')])
            ->when(! $canManageMaterials, fn ($query) => $query->where('is_published', true))
            ->latest()
            ->latest('id')
            ->get();

        return Inertia::render('Dashboard', [
            'roles' => $roles,
            'initialRole' => isset($roles[$simulatedRole]) ? $simulatedRole : 'siswa',
            'days' => $days,
            'initialAssignments' => $assignments,
            'currentUser' => $user,
            'materials' => MaterialResource::collection($materials)->resolve($request),
            'canManageMaterials' => $canManageMaterials,
            'materialConfig' => [
                'disk' => config('materials.disk'),
                'max_files' => config('materials.max_files_per_material'),
                'max_file_size_kb' => config('materials.max_file_size_kb'),
                'max_total_size_kb' => config('materials.max_total_size_kb'),
                'allowed_extensions' => config('materials.allowed_extensions'),
            ],
        ]);
    }
}
