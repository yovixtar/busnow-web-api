<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<h1>Daftar Pesanan</h1>
<br>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama</th>
            <th>Nama Bus</th>
            <th>Asal - Tujuan</th>
            <th>Tanggal Berangkat</th>
            <th>Jam Berangkat</th>
            <th>Jam Sampai</th>
            <th>Kelas</th>
            <th>Kursi</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pesanan as $item) : ?>
            <tr>
                <td><?= $item['id_pesanan'] ?></td>
                <td><?= $item['nama'] ?></td>
                <td><?= $item['nama_bus'] ?></td>
                <td><?= $item['asal_tujuan'] ?></td>
                <td><?= $item['tanggal_berangkat'] ?></td>
                <td><?= $item['jam_berangkat'] ?></td>
                <td><?= $item['jam_sampai'] ?></td>
                <td><?= $item['kelas'] ?></td>
                <td><?= $item['kursi'] ?></td>
                <td><?= $item['total'] ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
