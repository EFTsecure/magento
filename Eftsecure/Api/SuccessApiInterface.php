<?php

namespace Callpay\Eftsecure\Api;

interface SuccessApiInterface
{
    /**
     *
     * POST method to handle success webbhook from Callpay.
     *
     * @return bool|string
     */
    public function execute(): bool|string;
}
