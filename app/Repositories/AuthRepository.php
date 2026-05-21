<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\AuthRepositoryInterface;
use App\Traits\Upload\UplodeImageHelper;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthRepository implements AuthRepositoryInterface
{
    use UplodeImageHelper;
    private function generateUniqueVerificationCode()
    {
        do {
            $code = random_int(100000, 999999);
        } while (User::where('verification_code', $code)->exists());


        return $code;
    }

    public function register(array $data): array
    {
        $verificationCode = $this->generateUniqueVerificationCode();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'password' => Hash::make($data['password']),
            'verification_code' => $verificationCode,
            'role' => 'user',
            'verification_code_expires_at' => now()->addMinutes(5),
        ]);

        if (!empty($data['image'])) {
            $user->image()->create(['path' => $this->uplodeImage($data['image'],'users')]);
        }

        return [
            'user'  => $user,
        ];
    }

    public function verify_code(array $data): array
    {
        return DB::transaction(function () use ($data)
        {
            $user = User::where('email', $data['email'])
                ->lockForUpdate()
                ->first();

            if (now()->gt($user->verification_code_expires_at)) {
                return ['status' => 'expired'];
            }


            if ($user->verification_attempts >= 3) {
                return ['status' => 'max_attempts'];
            }

            if ($user->verification_code !== $data['code']) {
                $user->increment('verification_attempts');
                $remaining = 3 - ($user->verification_attempts + 1);
                // أو
                $user->refresh();
                $remaining = 3 - $user->verification_attempts;

                return [
                    'status'    => 'wrong_code',
                    'remaining' => $remaining,
                ];
            }


            $user->update([
                'verification_code'       => null,
                'verification_code_expires_at' => null,
                'verification_attempts'   => 0,
                'email_verified_at' => now()
            ]);

            $token = $user->createToken("api_token")->plainTextToken;


            return ['status' => 'verified', 'user' => $user->fresh(),'token' => $token];
        });

    }

    public function resend_code(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        $verificationCode = $this->generateUniqueVerificationCode();

        $user->update([
            'verification_code'           => $verificationCode,
            'verification_code_expire_at' => now()->addMinutes(5),
            'verification_attempts'       => 0,
        ]);

        return ['user' => $user];
    }

public function findByEmail(string $email): ?User
{
    return User::where('email', $email)->first();
}

    public function logout(User $user): bool
{
    return $user->currentAccessToken()->delete();
}


}
