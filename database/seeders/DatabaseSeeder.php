<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $accounts = [
            [
                'email' => 'siswa@smk.sch.id',
                'name' => 'Nadia Putri',
                'username' => '008492104',
                'role' => 'siswa',
            ],
            [
                'email' => 'guru@smk.sch.id',
                'name' => 'Budi Santoso, S.Kom',
                'username' => '198804122015041002',
                'role' => 'guru',
            ],
            [
                'email' => 'wali.xdkv1@smk.sch.id',
                'name' => 'Siti Rahma, M.Pd',
                'username' => 'wali.xdkv1@smk.sch.id',
                'role' => 'wali_kelas',
            ],
            [
                'email' => 'kepsek@smk.sch.id',
                'name' => 'Drs. Hendra Wijaya',
                'username' => 'kepsek@smk.sch.id',
                'role' => 'kepsek',
            ],
            [
                'email' => 'admin@smk.sch.id',
                'name' => 'Rizky Pratama',
                'username' => 'admin@smk.sch.id',
                'role' => 'admin',
            ],
            [
                'email' => 'superadmin@smk.sch.id',
                'name' => 'Arif Darmawan',
                'username' => 'superadmin@smk.sch.id',
                'role' => 'super_admin',
            ],
        ];

        foreach ($accounts as $account) {
            User::where('email', $account['email'])
                ->orWhere('username', $account['username'])
                ->delete();

            User::create([
                'name' => $account['name'],
                'email' => $account['email'],
                'username' => $account['username'],
                'password' => Hash::make('password'),
                'role' => $account['role'],
                'email_verified_at' => now(),
            ]);
        }

        $this->call(AcademicScheduleSeeder::class);
    }
}
