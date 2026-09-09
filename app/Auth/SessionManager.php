<?php

declare(strict_types = 1);

namespace App\Auth;

class SessionManager
{
    /**
     * Method to start the session
     *
     * @return void
     */
    public function start(): void
    {
        if(session_status() === PHP_SESSION_NONE) {
            /**
             * Setting up the session cookie params
             */
            session_set_cookie_params([
                'httponly' => true,
                'secure' => false,
                'samesite' => 'Lax',
                'path' => '/'
            ]);

            session_start();
        }
    }

    /**
     * Method to login the user with user id
     *
     * @param integer $user_id
     * @return void
     */
    public function login(int $user_id): void
    {
        $this->start();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
    }

    /**
     * Method to getting user id
     *
     * @return integer|null
     */
    public function userId(): ?int
    {
        $this->start();
        if(!isset($_SESSION['user_id'])) {
            return null;
        }

        return (int) $_SESSION['user_id'];
    }

    /**
     * Method to logout the user
     *
     * @return void
     */
    public function logout(): void
    {
        $this->start();
        $_SESSION = [];

        if(ini_get('session.use_cookies')) {
            /**
             * Getting the session cookie params
             */
            $params = session_get_cookie_params();
            /** Setting cookie befor logout */
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'] ?? '',
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }
}