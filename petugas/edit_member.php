<?php
session_start();
include "../config/controller.php";

// Tambahkan error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$member = [];
// Cek apakah ada ID member di URL atau di POST data (jika form sudah disubmit)
if (isset($_GET['id_member']) || (isset($_POST['id_member']) && !isset($_POST['edit_member']))) {
    $id = isset($_GET['id_member']) ? $_GET['id_member'] : $_POST['id_member'];
    
    // Pastikan ID tidak kosong
    if (empty($id)) {
        echo "<script>
        alert('ID member kosong.');
        document.location.href='index_datamember.php';</script>";
        exit();
    }
    
    $member = get_member_by_id($id);
    
    // Debug: cek hasil dari fungsi get_member_by_id()
    // echo "<pre>"; print_r($member); echo "</pre>";
    
    if (!$member) {
        echo "<script>
        alert('Data member dengan ID ".$id." tidak ditemukan.');
        document.location.href='index_datamember.php';</script>";
        exit();
    }
} else {
    // Jika tidak ada ID member di URL dan bukan form yang disubmit, redirect ke halaman daftar member
    if (!isset($_POST['edit_member'])) {
        echo "<script>
        alert('ID member tidak diberikan.');
        document.location.href='index_datamember.php';</script>";
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_member'])) {
    // Perbaikan: Menggunakan $_POST langsung sebagai parameter sesuai dengan definisi di app.php
    if (edit_member($_POST) > 0) {
        echo "<script>
        alert('Data berhasil diubah.');
        document.location.href='index_datamember.php';</script>";
    } else {
        echo "<script>
        alert('Data gagal diubah.');
        document.location.href='index_datamember.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Aplikasi Perpustakaan</title>
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3" style="font-size: 30px;">UniLibrary</a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Cari Buku..." aria-label="Cari Buku..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <div class="small" style="color: white">
                <?php
                $conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');
                if (!$conn) {
                    die("Connection failed: " . mysqli_connect_error());
                }

                $user_id = $_SESSION['user_id'];
                $sql = "SELECT username FROM users WHERE id = $user_id";
                $result = mysqli_query($conn, $sql);

                if ($row = mysqli_fetch_assoc($result)) {
                    echo $row['username'];
                } else {
                    echo 'Guest';
                }

                mysqli_close($conn);
                ?>
            </div>
            <div class="sb-sidenav-footer">
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="../login.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading"></div>
                            <a class="nav-link" href="../petugas/index_databuku.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                                Data Buku
                            </a>
                            <a class="nav-link" href="../petugas/index_datamember.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Data Pelanggan
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                </nav>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
            <!-- Isi halaman -->
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
            <h2 class="mb-4">Edit Member</h2>
            <form action="" method="POST">
                <input type="hidden" name="id_member" value="<?php echo isset($member['id_member']) ? htmlspecialchars($member['id_member']) : ''; ?>">
                <div class="mb-3">
                    <label for="nama" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" 
                           value="<?php echo isset($member['nama']) ? htmlspecialchars($member['nama']) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="telp" class="form-label">No. Telp</label>
                    <input type="text" class="form-control" id="telp" name="telp" 
                           value="<?php echo isset($member['telp']) ? htmlspecialchars($member['telp']) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo isset($member['email']) ? htmlspecialchars($member['email']) : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="alamat" class="form-label">Alamat</label>
                    <textarea class="form-control" id="alamat" name="alamat" required><?php echo isset($member['alamat']) ? htmlspecialchars($member['alamat']) : ''; ?></textarea>
                </div>
            <button type="submit" name="edit_member" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    </body>
</html>