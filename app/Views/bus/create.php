<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<h1>Tambah Bus Baru</h1>
<br>
<form action="<?= base_url('bus/store') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-group">
        <div class="form-group">
            <label for="nama">Nama</label>
            <input type="text" class="form-control" id="nama" name="nama" required>
        </div>
        <label for="asal">Asal</label>
        <input type="text" class="form-control" id="asal" name="asal" required>
    </div>
    <div class="form-group">
        <label for="tujuan">Tujuan</label>
        <input type="text" class="form-control" id="tujuan" name="tujuan" required>
    </div>
    <div class="form-group">
        <label for="kursi">Kursi</label>
        <input type="number" class="form-control" id="kursi" name="kursi" required>
    </div>
    <div class="form-group">
        <label for="gambar">Gambar</label>
        <input type="file" class="form-control" id="gambar" name="gambar" required>
    </div>
    <button type="submit" class="btn btn-primary">Submit</button>
</form>
<?= $this->endSection() ?>