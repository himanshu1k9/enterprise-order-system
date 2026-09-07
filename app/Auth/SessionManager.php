<?php

declare(strict_types = 1);

namespace App\Auth;

class SessionManager
{
    public function start(): void
    {
        if(session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login(int $user_id): void
    {
        $this->start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
    }

    public function userId(): ?int
    {
        $this->start();
        if(!isset($_SESSION['user_id'])) {
            return null;
        }

        return (int) $_SESSION['user_id'];
    }

    public function logout(): void
    {
        $this->start();
        $_SESSION = [];
        session_destroy();
    }
}