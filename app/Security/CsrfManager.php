<?php

declare(strict_types = 1);

namespace App\Security;

use App\Auth\SessionManager;

class CsrfManager
{
    public function __construct(private SessionManager $session)
    {}

    /**
     * Method to store and return csrf token
     *
     * @return string
     */
    public function token(): string
    {
        $this->session->start();
        if(!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['_csrf_token'];
    }

    /**
     * Method to verify incoming csrf
     *
     * @param string $token
     * @return boolean
     */
    public function validate(string $token): bool
    {
        $this->session->start();
        if(!isset($_SESSION['_csrf_token'])) {
            return false;
        }

        return hash_equals($_SESSION['_csrf_token'], $token);
    }
}