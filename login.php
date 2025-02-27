<?php
include 'config/koneksi.php';
session_start();
// Check if already logged in
if (isset($_SESSION['level'])) {
    if ($_SESSION['level'] == 1) {
        header("Location: ./petugas/index_databuku.php");
    } elseif ($_SESSION['level'] == 2) {
        header("Location: ./pengunjung/index_pengunjung.php");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    if ($stmt = $db->prepare("SELECT id, password, level FROM users WHERE username = ?")) {
        $stmt->bind_param("s", $username);

        // Execute the statement
        $stmt->execute();

        // Bind result variables
        $stmt->bind_result($id, $storedPassword, $level);

        // Fetch value
        if ($stmt->fetch()) {
            // Verify password
            if ($password == $storedPassword) { 
                // Store user ID and level in session
                $_SESSION['user_id'] = $id;
                $_SESSION['user_level'] = $level;

                if ($level == 'petugas') {
                    echo "<script>
                    alert('Login sukses. Selamat datang, Admin');
                    document.location.href='petugas/index_databuku.php';</script>";
                } elseif ($level == 'pengunjung') {
                    echo "<script>
                    alert('Login sukses! Selamat Datang di UniLibrary.');
                    document.location.href='pengunjung/index_pengunjung.php';</script>"; 
                }
            } else {
                echo "<script>
                alert('Password salah.');
                document.location.href='login.php';</script>";
            }
        } else {
            echo "<script>
            alert('Username tidak ditemukan.');
            document.location.href='login.php';</script>";
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $db->error;
    }
}
$db->close();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Login Perpustakaan</title>
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>    
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container">
                        <div class="mt-5 row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header">
                                        <h3 class="text-center font-weight-light my-2">Welcome</h3>
                                        <p style="text-align: center;">Silahkan login untuk mengakses perpustakaan</p>
                                    </div>
                                    <div class="card-body">
                                        <form method="post" action="">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputEmail" name="username" type="text" placeholder="name@example.com" required />
                                                <label for="inputEmail">Username</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" id="inputPassword" name="password" type="password" placeholder="Password" required />
                                                <label for="inputPassword">Password</label>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-center mt-4 mb-0">
                                                <button class="btn btn-primary" type="submit">Login</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
    </body>
</html>



