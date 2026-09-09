<?php

declare(strict_types = 1);

namespace App\Security;

class RateLimiter
{
    public function __construct(private string $storagePath)
    {}

    /**
     * Method to check if attempted too many times or not
     *
     * @param string $key
     * @param integer $maxAttempts
     * @param integer $deckaySeconds
     * @return boolean
     */
    public function tooManyAttempts(string $key, int $maxAttempts, int $deckaySeconds): bool
    {
        $data = $this->load($key);
        if($data === null) {
            return false;
        }

        if(time() - $data['first_attempt'] >= $deckaySeconds) {
            $this->clear($key);
            return false;
        }

        return $data['attempts'] >= $maxAttempts;
    }

    /**
     * Method to hit the wrong attempts
     *
     * @param string $key
     * @return void
     */
    public function hit(string $key): void
    {
        $data = $this->load($key);
        if($data === null) {
            $data = [
                'attempts' => 0,
                'first_attempt' => time()
            ];
        }

        $data['attempts']++;
        $this->store($key, $data);
    }

    /**
     * Method to clear the wrong attempts if attemted correct or excedded decay time
     *
     * @param string $key
     * @return void
     */
    public function clear(string $key): void
    {
        $file = $this->file($key);
        if(is_file($file)) {
            unlink($file);
        }
    }

    /**
     * method to load the data from the file
     *
     * @param string $key
     * @return array|null
     */
    public function load(string $key): ?array
    {
        $file = $this->file($key);
        if(!is_file($file)) {
            return null;
        }

        $content = file_get_contents($file);
        if($content === false) {
            return null;
        }

        $data = json_decode($content, true);

        return is_array($data) ? $data : null;
    }

    /**
     * Method to store attempts info
     *
     * @param string $key
     * @param array $data
     * @return void
     */
    public function store(string $key, array $data): void
    {
        $directory = dirname($this->file($key));
        if(!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($this->file($key), json_encode($data), LOCK_EX);
    }

    /**
     * Method to return file along with location
     *
     * @param string $key
     * @return string
     */
    public function file(string $key): string
    {
        return $this->storagePath . '/' . hash('sha256', $key) . '.json';
    }
}