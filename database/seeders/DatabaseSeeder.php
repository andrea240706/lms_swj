<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'siswa@lms-swj.test'],
            [
                'name' => 'Nadia Putri',
                'username' => '008492104',
                'password' => bcrypt('password'),
                'role' => 'siswa',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher@lms-swj.test'],
            [
                'name' => 'Budi Santoso, S.Kom',
                'username' => '198804122015041002',
                'password' => bcrypt('password'),
                'role' => 'guru',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'wali@lms-swj.test'],
            [
                'name' => 'Siti Rahma, M.Pd',
                'username' => 'wali.xdkv1@smk.sch.id',
                'password' => bcrypt('password'),
                'role' => 'wali_kelas',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'kepsek@lms-swj.test'],
            [
                'name' => 'Drs. Hendra Wijaya',
                'username' => 'kepsek@smk.sch.id',
                'password' => bcrypt('password'),
                'role' => 'kepsek',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@lms-swj.test'],
            [
                'name' => 'Rizky Pratama',
                'username' => 'admin@smk.sch.id',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'superadmin@lms-swj.test'],
            [
                'name' => 'Arif Darmawan',
                'username' => 'superadmin@smk.sch.id',
                'password' => bcrypt('password'),
                'role' => 'super_admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
