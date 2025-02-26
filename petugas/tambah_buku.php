<?php
function create_buku($post)
{
    global $db;
    $id_buku = strip_tags($post['id_buku']);
    $judul = strip_tags($post['judul']);
    $pengarang = strip_tags($post['pengarang']);
    $penerbit = strip_tags($post['penerbit']);
    $tahun_terbit = strip_tags($post['tahun_terbit']);
 
    $query = "INSERT INTO buku (id_buku, judul, pengarang, penerbit, tahun_terbit) VALUES ('$id_buku', '$judul', '$pengarang', '$penerbit', '$tahun_terbit'";
    mysqli_query($db, $query);
    return mysqli_affected_rows($db);
}
?>