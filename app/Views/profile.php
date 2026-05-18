<?= $this->extend('layout'); ?> 

<?= $this->section('content'); ?>
        
<h4 class="mt-3">Profil Pengguna</h4>

<ul>
    <li><strong>Username:</strong> <?= esc($username); ?></li>
    <li><strong>Role:</strong> <?= esc($role); ?></li>
    <li><strong>Email:</strong> <?= esc($email); ?></li>
    <li><strong>Waktu Login:</strong> <?= esc($waktu_login); ?></li>
    <li><strong>Status Login:</strong> <?= esc($status_login); ?></li>
</ul>

<?= $this->endSection(); ?>