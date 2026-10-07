<?php

namespace App\Controllers;

use App\Models\User;

class Login extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(): string
    {
        return view('login', [
            'title' => 'Login - Puihaha Electric',
        ]);
    }

    public function authenticate()
    {
        $email = trim((string) $this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');

        $user = $this->userModel
            ->where('email', $email)
            ->first();

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid email or password.');
        }

        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'email'      => $user['email'],
            'first_name' => $user['first_name'],
            'last_name'  => $user['last_name'],
        ]);

        return redirect()->to(base_url('/'));
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url('login'));
    }
}