<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<h1>Update Bus</h1>
<br>
<form action="/tiket/update/<?= $tiket['id_tiket'] ?>" method="post">
    <div class="form-group">
        <label for="id_bus">Nama Bus</label>
        <select class="form-control" id="id_bus" name="id_bus" disabled>
            <?php foreach ($bus as $item): ?>
                <option value="<?= $item['id_bus'] ?>" <?= $tiket['id_bus'] == $item['id_bus'] ? 'selected' : '' ?>><?= $item['nama'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="tanggal_berangkat">Tanggal Berangkat</label>
        <input type="date" class="form-control" id="tanggal_berangkat" name="tanggal_berangkat" value="<?= $tiket['tanggal_berangkat'] ?>" disabled>
    </div>
    <div class="form-group">
        <label for="jam_berangkat">Jam Berangkat</label>
        <input type="time" class="form-control" id="jam_berangkat" name="jam_berangkat" value="<?= $tiket['jam_berangkat'] ?>">
    </div>
    <div class="form-group">
        <label for="jam_sampai">Jam Sampai</label>
        <input type="time" class="form-control" id="jam_sampai" name="jam_sampai" value="<?= $tiket['jam_sampai'] ?>">
    </div>
    <div class="form-group">
        <label for="kelas">Kelas</label>
        <input type="text" class="form-control" id="kelas" name="kelas" value="<?= $tiket['kelas'] ?>">
    </div>
    <div class="form-group">
        <label for="kursi">Kursi</label>
        <input type="number" class="form-control" id="kursi" name="kursi" value="<?= $tiket['kursi'] ?>">
    </div>
    <div class="form-group">
        <label for="tarif">Tarif</label>
        <input type="number" class="form-control" id="tarif" name="tarif" value="<?= $tiket['tarif'] ?>">
    </div>
    <button type="submit" class="btn btn-primary">Update Ticket</button>
</form>
<br><br>
<?= $this->endSection() ?>