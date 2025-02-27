<?php
session_start(); // Mulai session login
if (isset($_SESSION['level'])) { 
    if ($_SESSION['level'] == 1) { // Jika level 1 = Admin - level 2 = pengunjung
        header("Location: ../login.php?error= unauthorized");
    } elseif ($_SESSION['level'] == 2) {
        header("Location: pengunjung/index_pengunjung.php");
    }
    exit();
}
include '../config/controller.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'pinjam') {
            $id_anggota = $_POST['id_anggota'];
            $id_buku = $_POST['id_buku'];
            $result = pinjamBuku($id_anggota, $id_buku);
            
            echo "<div class='alert " . ($result['status'] ? 'alert-success' : 'alert-danger') . "'>";
            echo $result['message'];
            echo "</div>";
            
            if ($result['status']) {
                echo "<div class='alert alert-info'>";
                echo "ID Peminjaman: " . $result['data']['id_peminjaman'] . "<br>";
                echo "Tanggal Pinjam: " . $result['data']['tgl_pinjam'] . "<br>";
                echo "Tanggal Kembali: " . $result['data']['tgl_kembali'];
                echo "</div>";
            }
        } elseif ($_POST['action'] == 'kembali') {
            $id_peminjaman = $_POST['id_peminjaman'];
            $result = kembalikanBuku($id_peminjaman);
            
            echo "<div class='alert " . ($result['status'] ? 'alert-success' : 'alert-danger') . "'>";
            echo $result['message'];
            echo "</div>";
            
            if ($result['status']) {
                echo "<div class='alert alert-info'>";
                echo "ID Peminjaman: " . $result['data']['id_peminjaman'] . "<br>";
                echo "Tanggal Pengembalian: " . $result['data']['tgl_kembali_actual'];
                echo "</div>";
            }
        }
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
                            <a class="nav-link" href="../pengunjung/index_pengunjung.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-book"></i></div>
                                Peminjaman
                            </a>
                            <div class="collapse" id="collapsePages" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav accordion" id="sidenavAccordionPages">
                                </nav>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div id="layoutSidenav">
            <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="h3 mb-4 text-gray-800">Peminjaman Buku</h1>
                    <form action="tambah_member.php" method="POST">
                        <div class="mb-3">
                            <label for="id_peminjaman" class="form-label">ID Peminjaman</label>
                            <input type="text" class="form-control" id="id_peminjaman" name="id_peminjaman" required>
                        </div>
                        <div class="mb-3">
                            <label for="id_anggota" class="form-label">Anggota</label>
                            <input type="text" class="form-control" id="id_anggota" name="id_anggota" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="tgl_pinjam" class="form-label">Tanggal Peminjaman</label>
                            <input type="date" class="form-control" id="tgl_pinjam" name="tgl_pinjam" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="tgl_kembali" class="form-label">Tanggal Pengembalian</label>
                            <input type="date" class="form-control" id="tgl_kembali" name="tgl_kembali" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <input type="text" class="form-control combobox" id="status" name="status" required>
                        </div>
                        <button type="submit" name="pinjam_buku" class="btn btn-primary">Tambah</button>
                    </form>
                </div>
            </main>
            </div>
        </div>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
