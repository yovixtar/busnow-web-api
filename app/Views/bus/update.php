<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<h1>Edit Bus</h1>
<br>
<form action="<?= base_url('bus/update/' . $bus['id_bus']) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="form-group">
        <label for="nama">Nama</label>
        <input type="text" class="form-control" id="nama" name="nama" value="<?= $bus['nama'] ?>">
    </div>
    <div class="form-group">
        <label for="asal">Asal</label>
        <input type="text" class="form-control" id="asal" name="asal" value="<?= $bus['asal'] ?>">
    </div>
    <div class="form-group">
        <label for="tujuan">Tujuan</label>
        <input type="text" class="form-control" id="tujuan" name="tujuan" value="<?= $bus['tujuan'] ?>">
    </div>
    <div class="form-group">
        <label for="kursi">Kursi</label>
        <input type="number" class="form-control" id="kursi" name="kursi" value="<?= $bus['kursi'] ?>">
    </div>
    <div class="form-group">
        <label for="gambar">Gambar</label>
        <br>
        <img src="<?= $bus['gambar'] ?>" alt="" style="height: 200px" />
        <input type="file" class="form-control" id="gambar" name="gambar">
    </div>
    <button type="submit" class="btn btn-primary">Update</button>
</form>
<?= $this->endSection() ?>