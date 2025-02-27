<?php
session_start(); // Mulai session login
if (isset($_SESSION['level'])) { 
    if ($_SESSION['level'] == 1) { // Jika level 1 = Admin - level 2 = pengunjung
        header("Location: petugas/index_databuku.php");
    } elseif ($_SESSION['level'] == 2) {
        header("Location: pengunjung/index_pengunjung.php");
    }
    exit();
}

include '../config/controller.php';
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
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="display-8 mt-4 fw-bolder">
                            Selamat Datang,
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
                        </h1>
                        <p style="font-size: large;">Mau pinjam buku apa?</p>
                        <br>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple" class="table table-bordered">
                                    <div class="d-flex justify-content-end mb-3">
                                        
                                    </div>
                                    
                                        <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Judul</th>
                                            <th>Penulis</th>
                                            <th>Penerbit</th>
                                            <th>Tahun Terbit</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            $conn = mysqli_connect('localhost', 'root', '', 'perpustakaan');
                                            $sql = "SELECT * FROM buku";
                                            $result = mysqli_query($conn, $sql);
                                            
                                            if (mysqli_num_rows($result) > 0) {
                                                while($row = mysqli_fetch_assoc($result)) {
                                                    echo "<tr>
                                                        <td>" . (isset($row["judul"]) ? $row["judul"] : '') . "</td>
                                                        <td>" . (isset($row["penulis"]) ? $row["penulis"] : '') . "</td>
                                                        <td>" . (isset($row["penerbit"]) ? $row["penerbit"] : '') . "</td>
                                                        <td>" . (isset($row["tahun_terbit"]) ? $row["tahun_terbit"] : '') . "</td>
                                                        <td>" . (isset($row["status"]) ? $row["status"] : '') . "</td>
                                                        <td>
                                                            <a href='?id=" . $row['id_buku'] . "' class='btn btn-success'>Pinjam</a>
                                                        </td>
                                                    </tr>";
                                                }
                                            } else {
                                                echo "0 results";
                                            }
                                            mysqli_close($conn);
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; Your Website 2023</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>

                                            