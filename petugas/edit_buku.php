<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Edit Buku</h2>
        <?php
        include "../config/controller.php";

        if (isset($_GET['id_buku'])) {
            $id = $_GET['id_buku'];
            $buku = get_buku_by_id($id);
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_buku'])) {
            if (edit_buku($_POST) > 0) {
                echo "<script>
                alert('Data berhasil diubah.');
                document.location.href='index.php';</script>";
            } else {
                echo "<script>
                alert('Data gagal diubah.');
                document.location.href='index.php';</script>";
            }
        }
        ?>

        <form action="" method="POST">
            <input type="hidden" name="id_buku" value="<?php echo htmlspecialchars($buku['id_buku']); ?>">
            <div class="mb-3">
                <label for="judul" class="form-label">Judul</label>
                <input type="text" class="form-control" id="judul" name="judul" value="<?php echo htmlspecialchars($buku['judul']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="penulis" class="form-label">Penulis</label>
                <input type="text" class="form-control" id="penulis" name="penulis" value="<?php echo htmlspecialchars($buku['penulis']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="penerbit" class="form-label">Penerbit</label>
                <input type="text" class="form-control" id="penerbit" name="penerbit" value="<?php echo htmlspecialchars($buku['penerbit']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                <input type="number" class="form-control" id="tahun_terbit" name="tahun_terbit" value="<?php echo htmlspecialchars($buku['tahun_terbit']); ?>" required>
            </div>
            <button type="submit" name="edit_buku" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>

