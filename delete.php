<?php
require_once 'config/database.php';
require_once 'classes/Produk.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $produkObj = new Produk();
    $data = $produkObj->readOne($id);

    if ($data) {
        // Hapus foto dari folder uploads
        if (!empty($data['foto']) && file_exists("uploads/" . $data['foto'])) {
            unlink("uploads/" . $data['foto']);
        }

        // Hapus data dari database
        if ($produkObj->delete($id)) {
            header("Location: index.php?msg=deleted");
            exit();
        }
    }
}

header("Location: index.php");
exit();
?>
