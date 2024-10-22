<?php

namespace Callpay\Eftsecure\Service;

class AuthService
{
    /**
     * @param $salt
     * @param $org_id
     * @param $timestamp
     * @return string
     */
    public function generateSha256($salt, $org_id, $timestamp): string
    {
        // Concatenate the salt, org_id, and timestamp
        $data = $salt .'_'. $org_id .'_'. $timestamp;

        // Generate the SHA256 hash
        $sha256 = hash('sha256', $data);

        return strtolower($sha256);
    }
}
