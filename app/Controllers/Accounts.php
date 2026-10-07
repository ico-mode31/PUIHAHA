<?php

namespace App\Controllers;

use App\Models\CustomerAccountModel;

class Accounts extends BaseController
{
    protected $customerModel;

    public function __construct()
    {
        $this->customerModel = new CustomerAccountModel();
        helper(['form']);
    }

    // ---------------- READ (list) ----------------
    public function index()
    {
        $keyword = $this->request->getGet('search');
        $status  = $this->request->getGet('status');
        $type    = $this->request->getGet('type');
        $perPage = 10;

        if ($keyword) {
            $accounts = $this->customerModel->searchAccounts($keyword, $perPage);
        } elseif ($status) {
            $accounts = $this->customerModel->getAccountsByStatus($status, $perPage);
        } elseif ($type) {
            $accounts = $this->customerModel->getAccountsByType($type, $perPage);
        } else {
            $accounts = $this->customerModel->getAccountsPaginated($perPage);
        }

        $data = [
            'accounts'            => $accounts,
            'pager'               => $this->customerModel->pager,
            'total_accounts'      => $this->customerModel->getTotalAccounts(),
            'active_accounts'     => $this->customerModel->getCountByStatus('active'),
            'inactive_accounts'   => $this->customerModel->getCountByStatus('inactive'),
            'suspended_accounts'  => $this->customerModel->getCountByStatus('suspended'),
            'current_page'        => $this->request->getGet('page') ?? 1,
            'search_keyword'      => $keyword,
            'filter_status'       => $status,
            'filter_type'         => $type,
        ];

        return view('accounts/index', $data);
    }

    // ---------------- READ (single) ----------------
    public function viewAccount($id)
    {
        $account = $this->customerModel->find($id);

        if (!$account) {
            return redirect()->to(base_url('accounts'))->with('error', 'Account not found');
        }

        return view('accounts/view_account', ['account' => $account]);
    }

    // ---------------- CREATE ----------------
    public function create()
    {
        return view('accounts/form', ['account' => null]);
    }

    public function store()
    {
        if (!$this->validate($this->validationRules())) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->customerModel->insert([
            'account_number'  => $this->customerModel->generateAccountNumber(),
            'customer_name'   => $this->request->getPost('customer_name'),
            'address'         => $this->request->getPost('address'),
            'phone'           => $this->request->getPost('phone'),
            'email'           => $this->request->getPost('email'),
            'meter_number'    => $this->request->getPost('meter_number'),
            'connection_type' => $this->request->getPost('connection_type'),
            'status'          => $this->request->getPost('status'),
        ]);

        return redirect()->to(base_url('accounts'))->with('success', 'Account created successfully.');
    }

    // ---------------- UPDATE ----------------
    public function edit($id)
    {
        $account = $this->customerModel->find($id);

        if (!$account) {
            return redirect()->to(base_url('accounts'))->with('error', 'Account not found');
        }

        return view('accounts/form', ['account' => $account]);
    }

    public function update($id)
    {
        if (!$this->customerModel->find($id)) {
            return redirect()->to(base_url('accounts'))->with('error', 'Account not found');
        }

        if (!$this->validate($this->validationRules((int) $id))) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // account_number is intentionally not updated
        $this->customerModel->update($id, [
            'customer_name'   => $this->request->getPost('customer_name'),
            'address'         => $this->request->getPost('address'),
            'phone'           => $this->request->getPost('phone'),
            'email'           => $this->request->getPost('email'),
            'meter_number'    => $this->request->getPost('meter_number'),
            'connection_type' => $this->request->getPost('connection_type'),
            'status'          => $this->request->getPost('status'),
        ]);

        return redirect()->to(base_url('accounts'))->with('success', 'Account updated successfully.');
    }

    // ---------------- DELETE ----------------
    public function delete($id)
    {
        if (!$this->customerModel->find($id)) {
            return redirect()->to(base_url('accounts'))->with('error', 'Account not found');
        }

        $this->customerModel->delete($id);

        return redirect()->to(base_url('accounts'))->with('success', 'Account deleted successfully.');
    }

    // ---------------- Validation ----------------
    private function validationRules(?int $id = null): array
    {
        $ignore = $id ?? 0;

        return [
            'customer_name'   => 'required|min_length[3]|max_length[100]',
            'address'         => 'required|max_length[255]',
            'phone'           => 'required|max_length[20]',
            'email'           => "required|valid_email|is_unique[customer_accounts.email,id,{$ignore}]",
            'meter_number'    => "required|max_length[50]|is_unique[customer_accounts.meter_number,id,{$ignore}]",
            'connection_type' => 'required|in_list[residential,commercial,industrial]',
            'status'          => 'required|in_list[active,inactive,suspended]',
        ];
    }
}