<?php $isEdit = !empty($account); ?>
<!DOCTYPE html>
<html>
<head>
    <title><?= $isEdit ? 'Edit' : 'Add' ?> Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(135deg,#667eea,#764ba2); min-height:100vh;">
<div class="container py-5" style="max-width:650px;">
    <div class="card shadow">
        <div class="card-body p-4">
            <h4 class="mb-4"><?= $isEdit ? 'Edit Account' : 'Add New Account' ?></h4>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger"><ul class="mb-0">
                    <?php foreach (session()->getFlashdata('errors') as $e): ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul></div>
            <?php endif; ?>

            <form method="post"
                  action="<?= $isEdit ? base_url('accounts/update/' . $account['id']) : base_url('accounts/store') ?>">
                <?= csrf_field() ?>

                <?php if ($isEdit): ?>
                <div class="mb-3">
                    <label class="form-label">Account Number</label>
                    <input type="text" class="form-control" value="<?= esc($account['account_number']) ?>" disabled>
                </div>
                <?php endif; ?>

                <div class="mb-3">
                    <label class="form-label">Customer Name</label>
                    <input type="text" name="customer_name" class="form-control"
                           value="<?= esc(old('customer_name', $account['customer_name'] ?? '')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control"
                           value="<?= esc(old('address', $account['address'] ?? '')) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="<?= esc(old('phone', $account['phone'] ?? '')) ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= esc(old('email', $account['email'] ?? '')) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Meter Number</label>
                    <input type="text" name="meter_number" class="form-control"
                           value="<?= esc(old('meter_number', $account['meter_number'] ?? '')) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Connection Type</label>
                        <select name="connection_type" class="form-select" required>
                            <?php foreach (['residential', 'commercial', 'industrial'] as $t): ?>
                                <option value="<?= $t ?>" <?= old('connection_type', $account['connection_type'] ?? '') === $t ? 'selected' : '' ?>><?= ucfirst($t) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <?php foreach (['active', 'inactive', 'suspended'] as $s): ?>
                                <option value="<?= $s ?>" <?= old('status', $account['status'] ?? 'active') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <a href="<?= base_url('accounts') ?>" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Account' : 'Save Account' ?></button>
            </form>
        </div>
    </div>
</div>
</body>
</html>