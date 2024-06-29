<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="<?= base_url('home') ?>">Admin Panel</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('bus') ?>">Bus</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('tiket') ?>">Tiket</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('pesanan') ?>">Pesanan</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('user') ?>">Pengguna</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('logout') ?>">Logout</a></li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">
        <?= $this->renderSection('content') ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
