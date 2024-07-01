<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<h1>Tambah Tiket Baru</h1>
<br>
<form action="/tiket/store" method="post">
    <div class="form-group">
        <label for="id_bus">Nama Bus</label>
        <select class="form-control" id="id_bus" name="id_bus" required>
            <?php foreach ($bus as $item): ?>
                <option value="<?= $item['id_bus'] ?>"><?= $item['nama'] . '  -  ' . $item['kursi'] .' Kursi'?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="tanggal_berangkat">Tanggal Berangkat</label>
        <input type="date" class="form-control" id="tanggal_berangkat" name="tanggal_berangkat" required>
    </div>
    <div class="form-group">
        <label for="jam_berangkat">Jam Berangkat</label>
        <input type="time" class="form-control" id="jam_berangkat" name="jam_berangkat" required>
    </div>
    <div class="form-group">
        <label for="jam_sampai">Jam Sampai</label>
        <input type="time" class="form-control" id="jam_sampai" name="jam_sampai" required>
    </div>
    <div class="form-group">
        <label for="kelas">Kelas</label>
        <input type="text" class="form-control" id="kelas" name="kelas" required>
    </div>
    <div class="form-group">
        <label for="kursi">Kursi</label>
        <input type="number" class="form-control" id="kursi" name="kursi" required>
    </div>
    <div class="form-group">
        <label for="tarif">Tarif</label>
        <input type="number" class="form-control" id="tarif" name="tarif" required>
    </div>
    <button type="submit" class="btn btn-primary">Tambah</button>
</form>
<br>
<br>
<?= $this->endSection() ?>