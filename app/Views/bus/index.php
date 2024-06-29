<?= $this->extend('template') ?>

<?= $this->section('content') ?>
    <h1>Daftar Bus</h1>
    <br>
    <a href="<?= base_url('bus/create') ?>" class="btn btn-primary mb-3">Tambah Bus Baru</a>
    <br>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama</th>
                <th>Asal</th>
                <th>Tujuan</th>
                <th>Kursi</th>
                <th>Gambar</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($buses as $bus): ?>
                <tr>
                    <td><?= $bus['id_bus'] ?></td>
                    <td><?= $bus['nama'] ?></td>
                    <td><?= $bus['asal'] ?></td>
                    <td><?= $bus['tujuan'] ?></td>
                    <td><?= $bus['kursi'] ?></td>
                    <td><img src="<?= $bus['gambar'] ?>" alt="<?= $bus['nama'] ?>" width="50"></td>
                    <td>
                        <a href="<?= base_url('bus/edit/'.$bus['id_bus']) ?>" class="btn btn-warning btn-sm">Edit</a>
                        <form action="<?= base_url('bus/delete/'.$bus['id_bus']) ?>" method="post" style="display:inline-block;">
                            <?= csrf_field() ?>
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?= $this->endSection() ?>
