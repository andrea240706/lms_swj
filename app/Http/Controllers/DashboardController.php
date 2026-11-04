<?php

namespace App\Http\Controllers;

use App\Http\Resources\AssignmentResource;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\MeetingVideoResource;
use App\Models\Assignment;
use App\Models\Material;
use App\Models\MeetingVideo;
use App\Services\AcademicCalendarService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Display the multi-role LMS dashboard.
     */
    public function index(Request $request, AcademicCalendarService $calendarService): Response
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
                'focusNote' => 'Akses sesi aktif, unduh modul belajar, dan kumpul tugas.',
                'tabs' => [
                    ['id' => 'home', 'label' => 'Home', 'icon' => 'home'],
                    ['id' => 'mapel', 'label' => 'Mapel', 'icon' => 'book'],
                    ['id' => 'tugas', 'label' => 'Tugas', 'icon' => 'task'],
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
                'focusNote' => 'Kelola modul pembelajaran, kunci/buka bab materi, & nilai tugas cepat.',
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
                'focusNote' => 'Pantauan makro KBM harian, rekapitulasi kehadiran siswa, dan supervisi kinerja guru.',
                'tabs' => [
                    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                    ['id' => 'kbm', 'label' => 'KBM & Siswa', 'icon' => 'book'],
                    ['id' => 'kinerja', 'label' => 'Kinerja Guru', 'icon' => 'user'],
                ],
            ],
            'admin' => [
                'id' => 'admin',
                'label' => 'Administrator',
                'badge' => 'Administrator',
                'userName' => $user && $user->getRoleKey() === 'admin' ? $user->name : 'Administrator Utama',
                'userSub' => 'Pengelola Sistem & Akademik Sekolah',
                'avatar' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?w=150&auto=format&fit=crop&q=80',
                'heroStyle' => 'hero-lavender',
                'focusNote' => 'Kelola akun civitas, data master, plotting jadwal, dan pengaturan sistem.',
                'sections' => [
                    [
                        'title' => 'Menu Utama',
                        'tabs' => [
                            ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                            ['id' => 'pengguna', 'label' => 'Pengguna', 'icon' => 'user'],
                        ],
                    ],
                    [
                        'title' => 'Data Master',
                        'tabs' => [
                            ['id' => 'tahunajaran', 'label' => 'Tahun Ajaran', 'icon' => 'clock'],
                            ['id' => 'jurusan', 'label' => 'Jurusan (Keahlian)', 'icon' => 'folder'],
                            ['id' => 'rombel', 'label' => 'Rombel (Kelas)', 'icon' => 'book'],
                            ['id' => 'matapelajaran', 'label' => 'Mata Pelajaran', 'icon' => 'task'],
                        ],
                    ],
                    [
                        'title' => 'Penugasan & Jadwal',
                        'tabs' => [
                            ['id' => 'jadwalpelajaran', 'label' => 'Jadwal Pelajaran', 'icon' => 'clock'],
                            ['id' => 'plottingguru', 'label' => 'Plotting Guru Mapel', 'icon' => 'user'],
                            ['id' => 'walikelas', 'label' => 'Penetapan Wali Kelas', 'icon' => 'shield'],
                        ],
                    ],
                    [
                        'title' => 'Informasi & Sistem',
                        'tabs' => [
                            ['id' => 'pengaturansistem', 'label' => 'Pengaturan Sistem', 'icon' => 'settings'],
                        ],
                    ],
                ],
                'tabs' => [
                    ['id' => 'dashboard', 'label' => 'Dashboard', 'icon' => 'home'],
                    ['id' => 'pengguna', 'label' => 'Pengguna', 'icon' => 'user'],
                    ['id' => 'tahunajaran', 'label' => 'Tahun Ajaran', 'icon' => 'clock'],
                    ['id' => 'jurusan', 'label' => 'Jurusan (Keahlian)', 'icon' => 'folder'],
                    ['id' => 'rombel', 'label' => 'Rombel (Kelas)', 'icon' => 'book'],
                    ['id' => 'matapelajaran', 'label' => 'Mata Pelajaran', 'icon' => 'task'],
                    ['id' => 'jadwalpelajaran', 'label' => 'Jadwal Pelajaran', 'icon' => 'clock'],
                    ['id' => 'plottingguru', 'label' => 'Plotting Guru Mapel', 'icon' => 'user'],
                    ['id' => 'walikelas', 'label' => 'Penetapan Wali Kelas', 'icon' => 'shield'],
                    ['id' => 'pengaturansistem', 'label' => 'Pengaturan Sistem', 'icon' => 'settings'],
                ],
            ],
        ];

        $days = $calendarService->getWeekDays();
        $calendarEvents = $calendarService->getCombinedEvents((int) Carbon::now()->year);

        $assignments = [];

        $canManageMaterials = $user !== null && ($user->isTeacher() || $user->isAdmin());

        $materials = Material::query()
            ->with(['files' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')])
            ->when(! $canManageMaterials, fn ($query) => $query->where('is_published', true))
            ->latest()
            ->latest('id')
            ->get();

        $meetingVideos = MeetingVideo::query()
            ->when(! $canManageMaterials, fn ($query) => $query->where('is_published', true))
            ->get();

        $assignmentsData = Assignment::query()
            ->with(['files', 'submissions.files', 'submissions.student'])
            ->when(! $canManageMaterials, fn ($query) => $query->where('is_published', true))
            ->get();

        return Inertia::render('Dashboard', [
            'roles' => $roles,
            'initialRole' => isset($roles[$simulatedRole]) ? $simulatedRole : 'siswa',
            'days' => $days,
            'calendarEvents' => $calendarEvents,
            'todayDate' => Carbon::now()->toDateString(),
            'todayDayNum' => Carbon::now()->day,
            'initialAssignments' => $assignments,
            'currentUser' => $user,
            'materials' => MaterialResource::collection($materials)->resolve($request),
            'meetingVideos' => MeetingVideoResource::collection($meetingVideos)->resolve($request),
            'meetingAssignments' => AssignmentResource::collection($assignmentsData)->resolve($request),
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
