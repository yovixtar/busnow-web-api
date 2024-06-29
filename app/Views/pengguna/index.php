<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<h1>Daftar Pengguna</h1>
<br>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pengguna as $user) : ?>
            <tr>
                <td><?= $user['id_user'] ?></td>
                <td><?= $user['nama'] ?></td>
                <td><?= $user['username'] ?></td>
                <td><?= $user['email'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
