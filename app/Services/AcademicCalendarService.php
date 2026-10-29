<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AcademicCalendarService
{
    /**
     * Get national holidays for a specific year, combining API with fallback cache.
     *
     * @return array<string, array{name: string, type: string}>
     */
    public function getHolidays(int $year): array
    {
        return Cache::remember("national_holidays_{$year}", 86400, function () use ($year): array {
            try {
                $response = Http::timeout(4)->get("https://tanggalmerah.upset.dev/api/holidays?year={$year}");

                if ($response->successful()) {
                    $json = $response->json();
                    $result = [];

                    if (isset($json['data']) && is_array($json['data'])) {
                        foreach ($json['data'] as $item) {
                            if (! empty($item['date']) && ! empty($item['name'])) {
                                $result[$item['date']] = [
                                    'name' => $item['name'],
                                    'type' => $item['type'] ?? 'holiday',
                                ];
                            }
                        }
                    }

                    if (! empty($result)) {
                        return $result;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Failed to fetch holidays from upset.dev: '.$e->getMessage());
            }

            return $this->getFallbackHolidays($year);
        });
    }

    /**
     * Fallback standard Indonesian National Holidays for 2026/2027 (SKB 3 Menteri).
     *
     * @return array<string, array{name: string, type: string}>
     */
    public function getFallbackHolidays(int $year): array
    {
        $all = [
            '2026-01-01' => ['name' => 'Tahun Baru 2026 Masehi', 'type' => 'holiday'],
            '2026-01-16' => ['name' => 'Isra Mi\'raj Nabi Muhammad SAW', 'type' => 'holiday'],
            '2026-02-16' => ['name' => 'Cuti Bersama Tahun Baru Imlek', 'type' => 'leave'],
            '2026-02-17' => ['name' => 'Tahun Baru Imlek 2577 Kongzili', 'type' => 'holiday'],
            '2026-03-18' => ['name' => 'Cuti Bersama Hari Suci Nyepi', 'type' => 'leave'],
            '2026-03-19' => ['name' => 'Hari Suci Nyepi Tahun Baru Saka 1948', 'type' => 'holiday'],
            '2026-03-20' => ['name' => 'Cuti Bersama Hari Raya Idul Fitri', 'type' => 'leave'],
            '2026-03-21' => ['name' => 'Hari Raya Idul Fitri 1447 H', 'type' => 'holiday'],
            '2026-03-22' => ['name' => 'Hari Raya Idul Fitri 1447 H', 'type' => 'holiday'],
            '2026-03-23' => ['name' => 'Cuti Bersama Hari Raya Idul Fitri', 'type' => 'leave'],
            '2026-03-24' => ['name' => 'Cuti Bersama Hari Raya Idul Fitri', 'type' => 'leave'],
            '2026-04-03' => ['name' => 'Wafat Yesus Kristus (Jumat Agung)', 'type' => 'holiday'],
            '2026-04-05' => ['name' => 'Kebangkitan Yesus Kristus (Paskah)', 'type' => 'holiday'],
            '2026-05-01' => ['name' => 'Hari Buruh Internasional', 'type' => 'holiday'],
            '2026-05-14' => ['name' => 'Kenaikan Yesus Kristus', 'type' => 'holiday'],
            '2026-05-15' => ['name' => 'Cuti Bersama Kenaikan Yesus Kristus', 'type' => 'leave'],
            '2026-05-27' => ['name' => 'Hari Raya Idul Adha 1447 H', 'type' => 'holiday'],
            '2026-05-28' => ['name' => 'Cuti Bersama Hari Raya Idul Adha', 'type' => 'leave'],
            '2026-05-31' => ['name' => 'Hari Raya Waisak 2570 BE', 'type' => 'holiday'],
            '2026-06-01' => ['name' => 'Hari Lahir Pancasila', 'type' => 'holiday'],
            '2026-06-16' => ['name' => 'Tahun Baru Islam 1448 H', 'type' => 'holiday'],
            '2026-08-17' => ['name' => 'HUT Kemerdekaan RI ke-81', 'type' => 'holiday'],
            '2026-08-25' => ['name' => 'Maulid Nabi Muhammad SAW', 'type' => 'holiday'],
            '2026-12-24' => ['name' => 'Cuti Bersama Hari Raya Natal', 'type' => 'leave'],
            '2026-12-25' => ['name' => 'Hari Raya Natal', 'type' => 'holiday'],
            // 2027 entries
            '2027-01-01' => ['name' => 'Tahun Baru 2027 Masehi', 'type' => 'holiday'],
            '2027-02-06' => ['name' => 'Tahun Baru Imlek 2578', 'type' => 'holiday'],
            '2027-03-10' => ['name' => 'Hari Raya Idul Fitri 1448 H', 'type' => 'holiday'],
            '2027-05-01' => ['name' => 'Hari Buruh Internasional', 'type' => 'holiday'],
            '2027-08-17' => ['name' => 'HUT Kemerdekaan RI ke-82', 'type' => 'holiday'],
        ];

        return array_filter($all, fn ($k) => str_starts_with($k, (string) $year), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Get SMK SWJ Academic Calendar Agenda (Kaldik Kejuruan).
     *
     * @return array<string, array{name: string, type: string, description: string}>
     */
    public function getAcademicAgenda(int $year): array
    {
        $agenda = [
            // Semester Ganjil 2026/2027
            '2026-07-13' => ['name' => 'MPLS Peserta Didik Baru', 'type' => 'school_event', 'description' => 'Masa Pengenalan Lingkungan Sekolah SMK SWJ Hari ke-1'],
            '2026-07-14' => ['name' => 'MPLS Peserta Didik Baru', 'type' => 'school_event', 'description' => 'Pengenalan Budaya Kerja Industri & K3LH'],
            '2026-07-15' => ['name' => 'MPLS & Apel Akbar', 'type' => 'school_event', 'description' => 'Penutupan MPLS & Pelantikan Siswa Baru'],
            '2026-07-16' => ['name' => 'Awal Masuk KBM Semester Ganjil', 'type' => 'kbm', 'description' => 'Mulai pembelajaran efektif TP 2026/2027'],
            '2026-09-21' => ['name' => 'PTS Semester Ganjil (Hari 1)', 'type' => 'exam', 'description' => 'Penilaian Tengah Semester Produktif Kejuruan'],
            '2026-09-22' => ['name' => 'PTS Semester Ganjil (Hari 2)', 'type' => 'exam', 'description' => 'Penilaian Tengah Semester Teori Kejuruan'],
            '2026-09-23' => ['name' => 'PTS Semester Ganjil (Hari 3)', 'type' => 'exam', 'description' => 'Penilaian Tengah Semester Umum'],
            '2026-09-24' => ['name' => 'PTS Semester Ganjil (Hari 4)', 'type' => 'exam', 'description' => 'Penilaian Tengah Semester Umum'],
            '2026-09-25' => ['name' => 'PTS Semester Ganjil (Hari 5)', 'type' => 'exam', 'description' => 'Penilaian Praktikum Laboratorium'],
            '2026-09-26' => ['name' => 'PTS Semester Ganjil (Hari 6)', 'type' => 'exam', 'description' => 'Susulan & Evaluasi PTS Ganjil'],
            '2026-10-28' => ['name' => 'Hari Sumpah Pemuda', 'type' => 'school_event', 'description' => 'Upacara Peringatan Hari Sumpah Pemuda di Kampus SWJ'],
            '2026-11-10' => ['name' => 'Hari Pahlawan Nasional', 'type' => 'school_event', 'description' => 'Peringatan Hari Pahlawan & Bedah Sejarah Vokasi'],
            '2026-11-23' => ['name' => 'Pekan Remedial & Pengayaan', 'type' => 'kbm', 'description' => 'Tuntaskan batas ketuntasan minimal (KKM) mata pelajaran'],
            '2026-11-24' => ['name' => 'Pekan Remedial & Pengayaan', 'type' => 'kbm', 'description' => 'Bimbingan remedial kompetensi keahlian'],
            '2026-12-01' => ['name' => 'PAS Semester Ganjil (Hari 1)', 'type' => 'exam', 'description' => 'Penilaian Akhir Semester Ganjil Mata Pelajaran Wajib'],
            '2026-12-02' => ['name' => 'PAS Semester Ganjil (Hari 2)', 'type' => 'exam', 'description' => 'Penilaian Akhir Semester Mapel Kejuruan'],
            '2026-12-03' => ['name' => 'PAS Semester Ganjil (Hari 3)', 'type' => 'exam', 'description' => 'Praktikum Ujian Akhir Semester'],
            '2026-12-04' => ['name' => 'PAS Semester Ganjil (Hari 4)', 'type' => 'exam', 'description' => 'Ujian Akhir Berbasis Komputer (CBT SWJ)'],
            '2026-12-05' => ['name' => 'PAS Semester Ganjil (Hari 5)', 'type' => 'exam', 'description' => 'Ujian Akhir Berbasis Komputer (CBT SWJ)'],
            '2026-12-07' => ['name' => 'PAS Susulan & Remedial', 'type' => 'exam', 'description' => 'Jadwal susulan PAS'],
            '2026-12-15' => ['name' => 'Rapat Pleno Dewan Guru', 'type' => 'school_event', 'description' => 'Rapat Penentuan Nilai Rapor Semester Ganjil'],
            '2026-12-18' => ['name' => 'Pembagian Rapor Semester Ganjil', 'type' => 'school_event', 'description' => 'Penyerahan Laporan Hasil Belajar oleh Wali Kelas'],
            '2026-12-21' => ['name' => 'Libur Akhir Semester Ganjil', 'type' => 'holiday', 'description' => 'Hari pertama libur semester ganjil'],
            '2026-12-22' => ['name' => 'Libur Akhir Semester Ganjil', 'type' => 'holiday', 'description' => 'Libur semester ganjil'],
            '2026-12-23' => ['name' => 'Libur Akhir Semester Ganjil', 'type' => 'holiday', 'description' => 'Libur semester ganjil'],
            '2026-12-26' => ['name' => 'Libur Akhir Semester Ganjil', 'type' => 'holiday', 'description' => 'Libur semester ganjil'],
            '2026-12-28' => ['name' => 'Libur Akhir Semester Ganjil', 'type' => 'holiday', 'description' => 'Libur semester ganjil'],
            '2026-12-29' => ['name' => 'Libur Akhir Semester Ganjil', 'type' => 'holiday', 'description' => 'Libur semester ganjil'],
            '2026-12-30' => ['name' => 'Libur Akhir Semester Ganjil', 'type' => 'holiday', 'description' => 'Libur semester ganjil'],
            '2026-12-31' => ['name' => 'Libur Akhir Semester Ganjil', 'type' => 'holiday', 'description' => 'Libur pergantian tahun ajaran'],

            // Semester Genap 2026/2027
            '2027-01-04' => ['name' => 'Hari Pertama KBM Genap', 'type' => 'kbm', 'description' => 'Awal Masuk Pembelajaran Semester Genap'],
            '2027-02-22' => ['name' => 'Pra-UKK Kejuruan', 'type' => 'exam', 'description' => 'Simulasi Uji Kompetensi Keahlian Rombel XII'],
            '2027-03-08' => ['name' => 'PTS Semester Genap', 'type' => 'exam', 'description' => 'Penilaian Tengah Semester Genap'],
            '2027-04-12' => ['name' => 'UKK SMK Nasional', 'type' => 'exam', 'description' => 'Uji Kompetensi Keahlian Asesor Industri'],
            '2027-05-03' => ['name' => 'Pengumuman Kelulusan XII', 'type' => 'school_event', 'description' => 'Rapat Pleno & Pengumuman Kelulusan'],
            '2027-06-07' => ['name' => 'PAT / PAS Genap', 'type' => 'exam', 'description' => 'Penilaian Akhir Tahun'],
            '2027-06-25' => ['name' => 'Pembagian Rapor Semester Genap', 'type' => 'school_event', 'description' => 'Penyerahan Laporan Hasil Belajar Kenaikan Kelas'],
            '2027-06-28' => ['name' => 'Libur Akhir Tahun Pelajaran', 'type' => 'holiday', 'description' => 'Libur kenaikan kelas'],
        ];

        return array_filter($agenda, fn ($k) => str_starts_with($k, (string) $year), ARRAY_FILTER_USE_KEY);
    }

    /**
     * Get combined event map (holidays + academic agenda) for a specific year.
     *
     * @return array<string, array{name: string, type: string, description: string, badgeColor: string}>
     */
    public function getCombinedEvents(int $year): array
    {
        $holidays = $this->getHolidays($year);
        $academic = $this->getAcademicAgenda($year);
        $combined = [];

        // 1. Process holidays first
        foreach ($holidays as $date => $info) {
            $isLeave = ($info['type'] ?? '') === 'leave';
            $combined[$date] = [
                'name' => $info['name'],
                'type' => $isLeave ? 'leave' : 'holiday',
                'description' => $isLeave ? 'Cuti Bersama Nasional' : 'Hari Libur Nasional Resmi (SKB 3 Menteri)',
                'badgeColor' => $isLeave ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-700',
            ];
        }

        // 2. Process academic agenda (school events take priority on note detail)
        foreach ($academic as $date => $info) {
            if (isset($combined[$date])) {
                // If collision with holiday, merge description
                $combined[$date]['academicNote'] = $info['name'];
            } else {
                $badge = match ($info['type']) {
                    'exam' => 'bg-purple-100 text-purple-800',
                    'school_event' => 'bg-sky-100 text-sky-800',
                    'holiday' => 'bg-rose-100 text-rose-700',
                    default => 'bg-emerald-100 text-emerald-800',
                };

                $combined[$date] = [
                    'name' => $info['name'],
                    'type' => $info['type'],
                    'description' => $info['description'],
                    'badgeColor' => $badge,
                ];
            }
        }

        return $combined;
    }

    /**
     * Get dynamic week days (Senin - Sabtu) with exact dates and calendar markers.
     *
     * @return array<int, array{d: string, n: int, full: string, date: string, isToday: bool, isHoliday: bool, holidayName: ?string, academicEvent: ?string, eventType: string, badgeColor: string}>
     */
    public function getWeekDays(?string $referenceDate = null): array
    {
        $ref = $referenceDate ? Carbon::parse($referenceDate) : Carbon::now();
        $startOfWeek = $ref->copy()->startOfWeek(Carbon::MONDAY);
        $events = $this->getCombinedEvents((int) $ref->year);

        $shortDayNames = [
            1 => 'Sen',
            2 => 'Sel',
            3 => 'Rab',
            4 => 'Kam',
            5 => 'Jum',
            6 => 'Sab',
        ];

        $fullDayNames = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        $days = [];

        for ($i = 0; $i < 6; $i++) {
            $dayCarbon = $startOfWeek->copy()->addDays($i);
            $dateString = $dayCarbon->toDateString();
            $dayNum = $dayCarbon->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
            $isToday = $dayCarbon->isToday();

            $event = $events[$dateString] ?? null;
            $isHoliday = $event && in_array($event['type'], ['holiday', 'leave']);

            $days[] = [
                'd' => $shortDayNames[$dayNum] ?? 'Hari',
                'n' => $dayCarbon->day,
                'full' => $fullDayNames[$dayNum] ?? '',
                'date' => $dateString,
                'isToday' => $isToday,
                'isHoliday' => $isHoliday,
                'holidayName' => $isHoliday ? $event['name'] : null,
                'academicEvent' => $event ? $event['name'] : null,
                'description' => $event['description'] ?? 'Hari Efektif KBM Reguler',
                'eventType' => $event['type'] ?? 'kbm',
                'badgeColor' => $event['badgeColor'] ?? 'bg-charcoal/5 text-charcoal/80',
            ];
        }

        return $days;
    }
}
