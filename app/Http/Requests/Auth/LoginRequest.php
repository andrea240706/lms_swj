<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('email') && ! $this->has('username')) {
            $this->merge(['username' => $this->input('email')]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $login = trim($this->string('username')->toString());
        $password = $this->string('password')->toString();
        $remember = $this->boolean('remember');

        // 1. Direct match on username or email
        $user = User::where('username', $login)
            ->orWhere('email', $login)
            ->first();

        // 2. Convenience aliases for testing & quick access
        if (! $user) {
            $normalized = strtolower($login);
            $user = match ($normalized) {
                'siswa', 'student', '008492104' => User::whereIn('role', ['siswa', 'student'])->first(),
                'guru', 'teacher', '198804122015041002' => User::whereIn('role', ['guru', 'teacher'])->first(),
                'wali', 'wali_kelas', 'wali.xdkv1@smk.sch.id' => User::whereIn('role', ['wali', 'wali_kelas'])->first(),
                'kepsek', 'principal', 'kepsek@smk.sch.id' => User::whereIn('role', ['kepsek', 'principal'])->first(),
                'admin', 'admin@smk.sch.id' => User::where('role', 'admin')->first(),
                'superadmin', 'super_admin', 'superadmin@smk.sch.id' => User::where('role', 'super_admin')->first(),
                default => null,
            };
        }

        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, $remember);

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}
