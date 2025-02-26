<!DOCTYPE html>
<html>
<head>
    <title>Perpustakaan Sederhana</title>
</head>

<body>
    <header>
        <h2>Daftar Buku</h2>
    </header>
    <nav>
        <a href="tambah_buku.php">[+] Tambah Baru</a>
    </nav><br>
    <table border="1">
        <thead>
            <tr>
                <th>No</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>Tahun Terbit</th>
                <th>Deskripsi</th>
                <th>Tindakan</th>
            </tr>
        </thead>
    <tbody>

    <?php
        include("config.php"); // Pastikan config.php menyertakan koneksi ke database

        $sql = "SELECT * FROM buku";
        $query = mysqli_query($connection, $sql);

        if (!$query) {
            die("Query gagal: " . mysqli_error($connection));
        }

        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>ID</th>";
        echo "<th>Judul</th>";
        echo "<th>Pengarang</th>";
        echo "<th>Tahun Terbit</th>";
        echo "<th>Deskripsi</th>";
        echo "<th>Aksi</th>";
        echo "</tr>";

        while($buku = mysqli_fetch_array($query)){
            echo "<tr>";
            echo "<td>".$buku['id']."</td>";
            echo "<td>".$buku['judul']."</td>";
            echo "<td>".$buku['pengarang']."</td>";
            echo "<td>".$buku['tahun_terbit']."</td>";
            echo "<td>".$buku['deskripsi']."</td>";
            echo "<td>";
            echo "<a href='edit_buku.php?id=".$buku['id']."'>Edit</a> | ";
            echo "<a href='hapus_buku.php?id=".$buku['id']."'>Hapus</a>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</table>";
        mysqli_close($connection);
    ?>


    </tbody>
    </table>

    <p>Total: <?php echo mysqli_num_rows($query) ?></p>

    </body>
</html>