<?php
include "../config/controller.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if (delete_member($id) > 0) {
        echo "<script>
        alert('Data berhasil dihapus.');
        document.location.href='index_datamember.php';</script>";
    } else {
        echo "<script>
        alert('Data gagal dihapus.');
        document.location.href='index_datamember.php';</script>";
    }
}