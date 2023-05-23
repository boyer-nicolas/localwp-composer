<?php

final class EnvGenerator
{
    private $env_file;
    private $example_env_file;
    private $interactive;

    public function __construct($argumentCount, $arguments)
    {
        $this->example_env_file = '.env.example';
        $this->env_file = '.env';

        if ($argumentCount > 1 && in_array('generate', $arguments))
        {
            $this->generate();
        }

        if ($argumentCount > 1 && in_array('fill', $arguments))
        {
            $this->fill($arguments[2]);
        }
    }

    private function generate()
    {
        // Generate strings
        $secure_auth_key = $this->generateRandomString();
        $auth_key = $this->generateRandomString();
        $logged_in_key = $this->generateRandomString();
        $nonce_key = $this->generateRandomString();
        $secure_auth_salt = $this->generateRandomString();
        $auth_salt = $this->generateRandomString();
        $logged_in_salt = $this->generateRandomString();
        $nonce_salt = $this->generateRandomString();

        $this->info("String generation completed.");


        $this->info("Replacing keys...");
        $this->set_variable('secure_auth_key', $secure_auth_key);
        $this->set_variable('auth_key', $auth_key);
        $this->set_variable('logged_in_key', $logged_in_key);
        $this->set_variable('nonce_key', $nonce_key);
        $this->set_variable('secure_auth_salt', $secure_auth_salt);
        $this->set_variable('auth_salt', $auth_salt);
        $this->set_variable('logged_in_salt', $logged_in_salt);
        $this->set_variable('nonce_salt', $nonce_salt);

        $this->info("Generated .env file.");
    }

    private function fill(string $variable)
    {
        $this->info("Filling $variable...");
        $this->set_variable($variable, $this->ask($variable));
    }

    private function ask(string $question)
    {
        echo "\n\e[35m=>\e[0m Please enter a value for: " . $question . "\n";
        $answer = $this->get_input();
        if (empty($answer))
        {
            echo "\e[31m[ERROR]\e[0m Please enter a value.\n";
            return $this->ask($question);
        }
        else
        {
            echo "\e[32m=>\e[0m Using provided value: \e[32m$answer\e[0m\n";
        }

        return $answer;
    }

    private function get_input()
    {
        $handle = fopen("php://stdin", "e");
        $line = fgets($handle);
        return trim($line);
    }

    private function info(string $message)
    {
        echo "\n\e[34m==>\e[0m " . $message;
    }

    private function generateRandomString(int $length = 45, bool $special_chars = true)
    {
        $extra_chars = "";

        if ($special_chars)
        {
            $extra_chars = "!?$#&^*()_+-=|{}[]:;<>.,";
        }


        $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ$extra_chars";
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++)
        {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    private function set_variable(string $key, string $value)
    {
        // Set formatting
        $key = strtoupper($key);
        $key = "$key=";
        $value = "$key\"$value\"";

        // echo value in .env
        $dotenv_contents = file_get_contents($this->env_file);
        $dotenv_contents .= "\n$value";
        $dotenv = fopen($this->env_file, "w");
        fwrite($dotenv, $dotenv_contents);
        fclose($dotenv);
    }
}

new EnvGenerator($argc, $argv);
