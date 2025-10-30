<?php
require_once 'config/database.php';
require_once 'classes/Produk.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$produkObj = new Produk();
$produkData = $produkObj->readOne($id);

if (!$produkData) {
    header("Location: index.php");
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $produkObj->setNama($_POST['nama']);
    $produkObj->setDeskripsi($_POST['deskripsi']);
    $produkObj->setHarga($_POST['harga']);

    $foto_name = $produkData['foto']; // default

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $new_foto = $produkObj->uploadFoto($_FILES['foto']);
        if ($new_foto) {
            // hapus foto lama
            if ($foto_name && file_exists("uploads/" . $foto_name)) {
                unlink("uploads/" . $foto_name);
            }
            $foto_name = $new_foto;
        } else {
            $error = "Upload gagal. File harus gambar <2MB.";
        }
    }

    $produkObj->setFoto($foto_name);

    if (empty($error)) {
        if ($produkObj->update($id)) {
            $message = "Produk berhasil diperbarui!";
            header("refresh:2;url=index.php");
        } else {
            $error = "Gagal memperbarui produk.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - <?php echo htmlspecialchars($produkData['nama']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Edit Produk</h1>

        <?php if($message): ?><div class="alert alert-success"><?php echo $message; ?></div><?php endif; ?>
        <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="form">
            <div class="form-group">
                <label>Nama Produk:</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($produkData['nama']); ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi:</label>
                <textarea name="deskripsi" rows="5"><?php echo htmlspecialchars($produkData['deskripsi']); ?></textarea>
            </div>

            <div class="form-group">
                <label>Harga (Rp):</label>
                <input type="number" name="harga" step="0.01" value="<?php echo htmlspecialchars($produkData['harga']); ?>" required>
            </div>

            <div class="form-group">
                <label>Foto Produk:</label><br>
                <?php if($produkData['foto']): ?>
                    <img src="uploads/<?php echo $produkData['foto']; ?>" width="100"><br>
                <?php endif; ?>
                <input type="file" name="foto" accept="image/*">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>
