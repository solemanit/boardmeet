<?php

namespace App\Services;

class PasswordPolicyService
{
    /**
     * Validate password against policy requirements
     */
    public function validate(string $password): array
    {
        $minLength        = 8;
        $requireUppercase = true;
        $requireLowercase = true;
        $requireNumbers   = true;
        $requireSpecial   = true;

        $requirements = [
            'min_length' => [
                'requirement' => "At least {$minLength} characters",
                'met'         => strlen($password) >= $minLength,
            ],
            'uppercase' => [
                'requirement' => 'At least one uppercase letter (A-Z)',
                'met'         => $requireUppercase ? (bool) preg_match('/[A-Z]/', $password) : true,
            ],
            'lowercase' => [
                'requirement' => 'At least one lowercase letter (a-z)',
                'met'         => $requireLowercase ? (bool) preg_match('/[a-z]/', $password) : true,
            ],
            'numbers' => [
                'requirement' => 'At least one number (0-9)',
                'met'         => $requireNumbers ? (bool) preg_match('/[0-9]/', $password) : true,
            ],
            'special' => [
                'requirement' => 'At least one special character (!@#$%^&*)',
                'met'         => $requireSpecial ? $this->hasSpecialChar($password) : true,
            ],
        ];

        $allMet = collect($requirements)->every(fn($req) => $req['met']);

        // FIX #7: 'failed_requirements' now returns human-readable messages (not just keys),
        // so API consumers can display them directly without extra mapping.
        return [
            'valid'               => $allMet,
            'requirements'        => array_column($requirements, 'requirement'),
            'failed_requirements' => array_values(
                array_map(
                    fn($req) => $req['requirement'],
                    array_filter($requirements, fn($req) => !$req['met'])
                )
            ),
        ];
    }

    /**
     * Check if password contains at least one special character
     */
    private function hasSpecialChar(string $password): bool
    {
        return (bool) preg_match('/[!@#$%^&*()\-_=+\[\]{};\':",.\\/<>?\\\\|`~]/', $password);
    }

    /**
     * Check if password has been used before (for preventing reuse)
     */
    public function hasBeenUsedBefore(string $password, array $oldPasswords): bool
    {
        foreach ($oldPasswords as $oldPassword) {
            if (\Illuminate\Support\Facades\Hash::check($password, $oldPassword)) {
                return true;
            }
        }

        return false;
    }
}
