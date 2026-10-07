<?php

namespace App\Controllers;

use App\Models\User;

class Auth extends BaseController
{
    public function index()
    {
        return view('auth/login', [
            'title' => 'Login - Puihaha Electric',
        ]);
    }

    public function login()
    {
        // Remove any previous login session first
        session()->destroy();

        $email = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        $userModel = new User();

        $user = $userModel
            ->where('email', $email)
            ->first();

        // Reject wrong email or wrong password
        if ($user === null || ! password_verify($password, $user['password'])) {
            return redirect()
                ->to(base_url('login'))
                ->with('error', 'Invalid email or password.');
        }

        // Login only if the database credentials are correct
        session()->set([
            'logged_in'    => true,
            'display_name' => $user['first_name'] . ' ' . $user['last_name'],
            'user_id'      => $user['id'],
            'email'        => $user['email'],
        ]);

        return redirect()->to(base_url('accounts'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url('login'));
    }
}