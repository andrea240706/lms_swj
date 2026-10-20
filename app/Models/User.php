<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'username', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Determine if user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin();
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isTeacher(): bool
    {
        return in_array($this->role, ['teacher', 'guru']);
    }

    public function isStudent(): bool
    {
        return in_array($this->role, ['student', 'siswa']);
    }

    public function isHomeroomTeacher(): bool
    {
        return in_array($this->role, ['wali_kelas', 'wali']);
    }

    public function isPrincipal(): bool
    {
        return in_array($this->role, ['kepsek', 'principal']);
    }

    /**
     * Get the standard role key matching the 6 LMS UI roles.
     */
    public function getRoleKey(): string
    {
        return match ($this->role) {
            'student', 'siswa' => 'siswa',
            'teacher', 'guru' => 'guru',
            'wali', 'wali_kelas' => 'wali_kelas',
            'kepsek', 'principal' => 'kepsek',
            'super_admin' => 'super_admin',
            default => 'admin',
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
