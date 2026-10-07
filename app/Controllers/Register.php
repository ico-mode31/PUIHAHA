<?php

namespace App\Controllers;

use App\Models\User;

class Register extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index(): string
    {
        $data = [
            'title'      => 'Register - PowerFlow Electric',
            'page'       => 'register',
            'success'    => session()->getFlashdata('success'),
            'error'      => session()->getFlashdata('error'),
            'validation' => session()->getFlashdata('validation'),
        ];

        return view('register', $data);
    }

    public function create()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'first_name'       => 'required|min_length[2]|max_length[100]',
            'last_name'        => 'required|min_length[2]|max_length[100]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'phone'            => 'required|min_length[10]|max_length[20]',
            'street_address'   => 'required|min_length[5]|max_length[255]',
            'city'             => 'required|min_length[2]|max_length[100]',
            'state'            => 'required|min_length[2]|max_length[50]',
            'zip_code'         => 'required|min_length[5]|max_length[10]',
            'password'         => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
            'terms'            => 'required',
        ]);

        if (! $validation->withRequest($this->request)->run()) {
            session()->setFlashdata(
                'validation',
                $validation->getErrors()
            );

            return redirect()->back()->withInput();
        }

        $userData = [
            'first_name'     => trim($this->request->getPost('first_name')),
            'last_name'      => trim($this->request->getPost('last_name')),
            'email'          => trim($this->request->getPost('email')),
            'phone'          => trim($this->request->getPost('phone')),
            'street_address' => trim($this->request->getPost('street_address')),
            'city'           => trim($this->request->getPost('city')),
            'state'          => trim($this->request->getPost('state')),
            'zip_code'       => trim($this->request->getPost('zip_code')),

            // Store a hashed password, never plain text
            'password'       => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),

            'newsletter'     => $this->request->getPost('newsletter') ? 1 : 0,
        ];

       try {
    $userId = $this->userModel->insert($userData);

    if ($userId === false) {
        $modelErrors = $this->userModel->errors();
        $dbError = \Config\Database::connect()->error();

        $errorMessage = ! empty($modelErrors)
            ? implode(' ', $modelErrors)
            : ($dbError['message'] ?? 'Unknown database error');

        session()->setFlashdata(
            'error',
            'Registration failed: ' . $errorMessage
        );

        return redirect()->back()->withInput();
    }

    session()->setFlashdata(
        'success',
        'Registration successful! Welcome to PowerFlow Electric.'
    );

    return redirect()->to(base_url('register'));

} catch (\Throwable $e) {
    session()->setFlashdata(
        'error',
        'Registration failed: ' . $e->getMessage()
    );

    return redirect()->back()->withInput();
}
    }
}