<?php

namespace HasinHayder\TyroLogin\Traits;

use HasinHayder\TyroLogin\Casts\EncryptedOrPlaintext;

trait HasTwoFactorAuth
{
    /**
     * Initialize the trait.
     * 
     * @return void
     */
    public function initializeHasTwoFactorAuth()
    {
        $this->mergeCasts([
            'two_factor_secret' => EncryptedOrPlaintext::class,
            'two_factor_recovery_codes' => EncryptedOrPlaintext::class,
            'two_factor_confirmed_at' => 'datetime',
        ]);
    }

    /**
     * Determine if two-factor authentication has been enabled.
     *
     * @return bool
     */
    public function hasEnabledTwoFactorAuthentication()
    {
        return ! is_null($this->two_factor_secret) &&
               ! is_null($this->two_factor_confirmed_at);
    }

    /**
     * Get the user's two-factor recovery codes.
     *
     * @return array
     */
    public function recoveryCodes()
    {
        return json_decode($this->two_factor_recovery_codes, true) ?? [];
    }
}
