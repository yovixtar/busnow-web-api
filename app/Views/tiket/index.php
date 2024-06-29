<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<h1>Daftar Tiket</h1>
<br>
<a href="/tiket/create" class="btn btn-primary">Tambah Tiket Baru</a>
<br>
<br>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID Tiket</th>
            <th>Nama Bus</th>
            <th>Asal - Tujuan</th>
            <th>Tanggal Berangkat</th>
            <th>Jam Berangkat</th>
            <th>Jam Sampai</th>
            <th>Kelas</th>
            <th>Kursi</th>
            <th>Tarif</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php
        function tgl_indo($tanggal)
        {
            $bulan = array(
                1 =>   'Januari',
                'Februari',
                'Maret',
                'April',
                'Mei',
                'Juni',
                'Juli',
                'Agustus',
                'September',
                'Oktober',
                'November',
                'Desember'
            );
            $pecahkan = explode('-', $tanggal);

            // variabel pecahkan 0 = tanggal
            // variabel pecahkan 1 = bulan
            // variabel pecahkan 2 = tahun

            return $pecahkan[2] . ' ' . $bulan[(int)$pecahkan[1]] . ' ' . $pecahkan[0];
        }

        foreach ($tikets as $item) :
            $jam_berangkat = date('H:i', strtotime($item['jam_berangkat']));
            $jam_sampai = date('H:i', strtotime($item['jam_sampai']));
        ?>
            <tr>
                <td><?= $item['id_tiket'] ?></td>
                <td><?= $item['nama_bus'] ?></td>
                <td><?= $item['asal_tujuan'] ?></td>
                <td><?= tgl_indo($item['tanggal_berangkat']) ?></td>
                <td><?= $jam_berangkat ?></td>
                <td><?= $jam_sampai ?></td>
                <td><?= $item['kelas'] ?></td>
                <td><?= $item['kursi'] ?></td>
                <td>Rp <?= number_format($item['tarif'], 0, ',', '.') ?></td>
                <td>
                    <a href="/tiket/edit/<?= $item['id_tiket'] ?>" class="btn btn-warning">Edit</a>
                    <a href="/tiket/delete/<?= $item['id_tiket'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this ticket?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>