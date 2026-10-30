<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Rombel;
use App\Models\Schedule;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AcademicScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tahun Ajaran
        $activeTa = AcademicYear::firstOrCreate(
            ['year' => '2026/2027', 'semester' => 'Ganjil'],
            [
                'is_active' => true,
                'start_date' => '2026-07-13',
                'end_date' => '2026-12-19',
                'effective_days' => 108,
            ]
        );

        AcademicYear::firstOrCreate(
            ['year' => '2026/2027', 'semester' => 'Genap'],
            [
                'is_active' => false,
                'start_date' => '2027-01-04',
                'end_date' => '2027-06-19',
                'effective_days' => 110,
            ]
        );

        // 2. Guru & Akun Tambahan
        $guruTbsm = User::firstOrCreate(
            ['email' => 'hendra.gunawan@smk.sch.id'],
            [
                'name' => 'Hendra Gunawan, S.T',
                'username' => '198503142011011004',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );

        $guruPbs = User::firstOrCreate(
            ['email' => 'nurul.hidayah@smk.sch.id'],
            [
                'name' => 'Nurul Hidayah, S.E.I',
                'username' => '199108152019032006',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );

        $guruBin = User::firstOrCreate(
            ['email' => 'sri.mulyani@smk.sch.id'],
            [
                'name' => 'Dra. Sri Mulyani',
                'username' => '196802141994032001',
                'password' => Hash::make('password'),
                'role' => 'guru',
            ]
        );

        $guruWaliB = User::firstOrCreate(
            ['email' => 'dedi.kurniawan@smk.sch.id'],
            [
                'name' => 'Dedi Kurniawan, S.Pd',
                'username' => '198904222016021003',
                'password' => Hash::make('password'),
                'role' => 'wali_kelas',
            ]
        );

        // 3. Jurusan (Departments)
        $tbsm = Department::firstOrCreate(
            ['code' => 'TBSM'],
            [
                'name' => 'Teknik & Bisnis Sepeda Motor',
                'description' => 'Program keahlian teknik otomotif perawatan dan bisnis sepeda motor roda dua.',
                'head_of_department_id' => $guruTbsm->id,
            ]
        );

        $pbs = Department::firstOrCreate(
            ['code' => 'PBS'],
            [
                'name' => 'Perbankan Syariah',
                'description' => 'Program keahlian perbankan mikro, operasional kas, dan transaksi syariah.',
                'head_of_department_id' => $guruPbs->id,
            ]
        );

        Department::firstOrCreate(['code' => 'RPL'], ['name' => 'Rekayasa Perangkat Lunak']);
        Department::firstOrCreate(['code' => 'DKV'], ['name' => 'Desain Komunikasi Visual']);

        // 4. Rombel (Kelas)
        $rombel10TbsmA = Rombel::firstOrCreate(
            ['academic_year_id' => $activeTa->id, 'name' => 'X TBSM A'],
            [
                'department_id' => $tbsm->id,
                'grade_level' => '10',
                'homeroom_teacher_id' => $guruTbsm->id,
                'room' => 'Bengkel TBSM 1',
                'capacity' => 36,
            ]
        );

        $rombel10TbsmB = Rombel::firstOrCreate(
            ['academic_year_id' => $activeTa->id, 'name' => 'X TBSM B'],
            [
                'department_id' => $tbsm->id,
                'grade_level' => '10',
                'homeroom_teacher_id' => $guruWaliB->id,
                'room' => 'Bengkel TBSM 2',
                'capacity' => 36,
            ]
        );

        $rombel10Pbs = Rombel::firstOrCreate(
            ['academic_year_id' => $activeTa->id, 'name' => 'X PBS'],
            [
                'department_id' => $pbs->id,
                'grade_level' => '10',
                'homeroom_teacher_id' => $guruPbs->id,
                'room' => 'Mini Bank Syariah',
                'capacity' => 36,
            ]
        );

        Rombel::firstOrCreate(
            ['academic_year_id' => $activeTa->id, 'name' => 'XI TBSM'],
            [
                'department_id' => $tbsm->id,
                'grade_level' => '11',
                'homeroom_teacher_id' => $guruTbsm->id,
                'room' => 'Bengkel TBSM 1',
                'capacity' => 36,
            ]
        );

        Rombel::firstOrCreate(
            ['academic_year_id' => $activeTa->id, 'name' => 'XI PBS'],
            [
                'department_id' => $pbs->id,
                'grade_level' => '11',
                'homeroom_teacher_id' => $guruPbs->id,
                'room' => 'Mini Bank Syariah',
                'capacity' => 36,
            ]
        );

        Rombel::firstOrCreate(
            ['academic_year_id' => $activeTa->id, 'name' => 'XII TBSM'],
            [
                'department_id' => $tbsm->id,
                'grade_level' => '12',
                'homeroom_teacher_id' => $guruWaliB->id,
                'room' => 'Bengkel TBSM 2',
                'capacity' => 36,
            ]
        );

        // 5. Daftarkan Siswa Default ke Rombel X TBSM A (Auto-Inherit Demo)
        $defaultStudent = User::where('email', 'siswa@smk.sch.id')->first();
        if ($defaultStudent) {
            $rombel10TbsmA->students()->syncWithoutDetaching([
                $defaultStudent->id => ['enrollment_status' => 'active'],
            ]);
        }

        // 6. Mata Pelajaran (Subjects)
        $mapelPmsm = Subject::firstOrCreate(
            ['code' => 'PMSM'],
            [
                'name' => 'Pemeliharaan Mesin Sepeda Motor',
                'category' => 'Kejuruan Produktif',
                'department_id' => $tbsm->id,
                'target_grade' => '10',
                'hours_per_week' => 6,
                'kkm' => 75,
            ]
        );

        $mapelLps = Subject::firstOrCreate(
            ['code' => 'LPS'],
            [
                'name' => 'Layanan Lembaga Perbankan Syariah',
                'category' => 'Kejuruan Produktif',
                'department_id' => $pbs->id,
                'target_grade' => '10',
                'hours_per_week' => 6,
                'kkm' => 78,
            ]
        );

        $mapelBin = Subject::firstOrCreate(
            ['code' => 'BIN'],
            [
                'name' => 'Bahasa Indonesia',
                'category' => 'Muatan Umum',
                'department_id' => null,
                'target_grade' => 'all',
                'hours_per_week' => 4,
                'kkm' => 75,
            ]
        );

        // 7. Penugasan Guru (Teaching Assignments)
        $assign1 = TeachingAssignment::firstOrCreate(
            [
                'academic_year_id' => $activeTa->id,
                'rombel_id' => $rombel10TbsmA->id,
                'subject_id' => $mapelPmsm->id,
                'teacher_id' => $guruTbsm->id,
            ],
            [
                'hours_per_week' => 6,
                'room' => 'Bengkel TBSM 1',
            ]
        );

        $assign2 = TeachingAssignment::firstOrCreate(
            [
                'academic_year_id' => $activeTa->id,
                'rombel_id' => $rombel10TbsmB->id,
                'subject_id' => $mapelPmsm->id,
                'teacher_id' => $guruTbsm->id,
            ],
            [
                'hours_per_week' => 6,
                'room' => 'Bengkel TBSM 2',
            ]
        );

        $assign3 = TeachingAssignment::firstOrCreate(
            [
                'academic_year_id' => $activeTa->id,
                'rombel_id' => $rombel10Pbs->id,
                'subject_id' => $mapelLps->id,
                'teacher_id' => $guruPbs->id,
            ],
            [
                'hours_per_week' => 6,
                'room' => 'Mini Bank Syariah',
            ]
        );

        $assign4 = TeachingAssignment::firstOrCreate(
            [
                'academic_year_id' => $activeTa->id,
                'rombel_id' => $rombel10TbsmA->id,
                'subject_id' => $mapelBin->id,
                'teacher_id' => $guruBin->id,
            ],
            [
                'hours_per_week' => 4,
                'room' => 'Ruang Teori 1',
            ]
        );

        // 8. Jadwal Pelajaran Mingguan (Schedules)
        Schedule::firstOrCreate(
            [
                'teaching_assignment_id' => $assign1->id,
                'day' => 'Senin',
                'time_slot' => 'Jam 1 - 3 (07:15 - 09:30)',
            ],
            [
                'start_time' => '07:15:00',
                'end_time' => '09:30:00',
                'room' => 'Bengkel TBSM 1',
                'status' => 'Terjadwal',
            ]
        );

        Schedule::firstOrCreate(
            [
                'teaching_assignment_id' => $assign2->id,
                'day' => 'Senin',
                'time_slot' => 'Jam 4 - 6 (09:45 - 12:00)',
            ],
            [
                'start_time' => '09:45:00',
                'end_time' => '12:00:00',
                'room' => 'Bengkel TBSM 2',
                'status' => 'Terjadwal',
            ]
        );

        Schedule::firstOrCreate(
            [
                'teaching_assignment_id' => $assign3->id,
                'day' => 'Senin',
                'time_slot' => 'Jam 1 - 3 (07:15 - 09:30)',
            ],
            [
                'start_time' => '07:15:00',
                'end_time' => '09:30:00',
                'room' => 'Mini Bank Syariah',
                'status' => 'Terjadwal',
            ]
        );

        Schedule::firstOrCreate(
            [
                'teaching_assignment_id' => $assign4->id,
                'day' => 'Senin',
                'time_slot' => 'Jam 7 - 8 (12:45 - 14:15)',
            ],
            [
                'start_time' => '12:45:00',
                'end_time' => '14:15:00',
                'room' => 'Ruang Teori 1',
                'status' => 'Terjadwal',
            ]
        );
    }
}
