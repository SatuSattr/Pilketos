<?php
session_start();
include 'conn.php';

$page_title = 'Data Calon';
include 'islogin.php';

$file = 'config.json';
$config = json_decode(file_get_contents($file), true);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {

        if ($_POST['action'] != 'haksuara') {
            $upload_folder = "foto_calon/";
            $url_foto = null;

            // Handle upload foto baru
            if (isset($_FILES['foto_calon']) && $_FILES['foto_calon']['error'] === 0) {
                $ext = pathinfo($_FILES['foto_calon']['name'], PATHINFO_EXTENSION);
                $unique_name = uniqid() . '.' . $ext;
                $upload_path = $upload_folder . $unique_name;

                if (move_uploaded_file($_FILES['foto_calon']['tmp_name'], $upload_path)) {
                    $url_foto = $upload_path;
                } else {
                    $error = "Gagal upload foto.";
                }
            }

            if ($_POST['action'] == 'add') {
                $nama = $_POST['nama'];
                $visi = $_POST['visi'];
                $misi = $_POST['misi'];
                $id_kelas = $_POST['id_kelas'];
                $nomor_urut = $_POST['nomor'];

                if (!$url_foto) {
                    $error = "Foto wajib diunggah saat menambahkan data baru.";
                } else {
                    $query = "INSERT INTO calon_ketua (nama, nomor, visi, misi, id_kelas, url_foto)
                  VALUES ('$nama', '$nomor_urut', '$visi', '$misi', '$id_kelas', '$url_foto')";

                    if (mysqli_query($conn, $query)) {
                        $success = "Calon berhasil ditambahkan!";
                    } else {
                        $error = "Error: " . mysqli_error($conn);
                    }
                }
            } elseif ($_POST['action'] == 'edit') {
                $id = $_POST['id'];
                $nama = $_POST['nama'];
                $visi = $_POST['visi'];
                $misi = $_POST['misi'];
                $id_kelas = $_POST['id_kelas'];
                $nomor_urut = $_POST['nomor'];


                if (!$url_foto && isset($_POST['old_url_foto'])) {
                    $url_foto = $_POST['old_url_foto'];
                } else if ($url_foto) {
                    $img = $_POST['old_url_foto'];
                    if (file_exists($img)) {
                        unlink($img);
                    }
                }

                $query = "UPDATE calon_ketua SET 
                      nama='$nama', 
                      nomor='$nomor_urut', 
                      visi='$visi', 
                      misi='$misi', 
                      id_kelas='$id_kelas', 
                      url_foto='$url_foto'
                      WHERE id='$id'";

                if (mysqli_query($conn, $query)) {
                    $success = "Data calon berhasil diupdate!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        } else {
            $config['haksuara'] = (int) $_POST['haksuara'];
            try {
                file_put_contents($file, json_encode($config, JSON_PRETTY_PRINT));
                $success = "Data hak suara berhasil diupdate!";
            } catch (Exception $e) {
                $error = "Error: " . $e;
            }

            $config = json_decode(file_get_contents($file), true);
        }
    }
}


if (isset($_GET['delete']) && isset($_GET['img'])) {
    $id = $_GET['delete'];
    $img = $_GET['img'];

    $query = "DELETE FROM calon_ketua WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        if (file_exists($img)) {
            unlink($img);
        }

        $success = "Calon dan foto berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}



// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $query = "SELECT * FROM calon_ketua WHERE id='$id'";
    $result = mysqli_query($conn, $query);
    $edit_data = mysqli_fetch_assoc($result);
}

// Get all calon
$query = "SELECT c.*, k.name as nama_kelas FROM calon_ketua c LEFT JOIN kelas k ON c.id_kelas = k.id ORDER BY c.id DESC";
$calon_result = mysqli_query($conn, $query);

// Get all kelas
$kelas_query = "SELECT * FROM kelas ORDER BY name";
$kelas_result = mysqli_query($conn, $kelas_query);



?>



<!-- Main Content -->

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'header.php'; ?>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="styles.css" />
</head>

<body>

    <div class="flex">
        <?php include 'sidebar.php'; ?>
        <div class="p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-accent">Data Calon Ketua OSIS</h1>
                <p class="text-gray-600 mt-2">Kelola data calon ketua OSIS</p>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>


            <div class="flex flex-col flex-wrap 2xl:flex-row gap-4">
                <!-- Form -->
                <div class="bg-secondary rounded-xl shadow-sm max-w-[42rem] border border-gray-100">

                    <div class="p-6 bg-neutral-100 rounded-t-lg border-b border-gray-200">
                        <h2 class="text-xl font-bold text-accent"><?php echo $edit_data ? 'Edit Calon' : 'Tambah Calon Baru'; ?></h2>
                    </div>

                    <form method="POST" class="p-6 space-y-6" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" id="nama" name="nama" required
                                    value="<?php echo $edit_data ? $edit_data['nama'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200">
                            </div>

                            <div>
                                <label for="id_kelas" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                                <select id="id_kelas" name="id_kelas" required
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200">
                                    <option value="">Pilih Kelas</option>
                                    <?php
                                    mysqli_data_seek($kelas_result, 0);
                                    while ($kelas = mysqli_fetch_assoc($kelas_result)):
                                    ?>
                                        <option value="<?php echo $kelas['id']; ?>"
                                            <?php echo ($edit_data && $edit_data['id_kelas'] == $kelas['id']) ? 'selected' : ''; ?>>
                                            <?php echo $kelas['name']; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div>
                                <label for="nomor" class="block text-sm font-medium text-gray-700 mb-2">Nomor urut</label>
                                <input type="number" id="nomor" name="nomor" required
                                    value="<?php echo $edit_data ? $edit_data['nomor'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200">
                            </div>
                        </div>

                        <div class="">
                            <label for="foto_upload" class="block text-sm font-medium text-gray-700 mb-2">Upload Foto</label>
                            <div class="flex gap-4">

                                <?php if ($edit_data): ?>
                                    <input type="hidden" name="old_url_foto" value="<?= $edit_data['url_foto'] ?>">
                                    <div id="previewContainer" class="max-w-64 max-h-64 rounded-lg border border-gray-200 shadow overflow-hidden"
                                        style="background-image: linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%, #eee), linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%, #eee); background-size: 20px 20px; background-position: 0 0, 10px 10px;">
                                        <img id="previewImage" src="<?= $edit_data['url_foto'] ?>" alt="Preview" class="w-auto h-auto max-w-full max-h-full object-contain" />
                                    </div>
                                <?php else: ?>
                                    <div id="previewContainer" class="hidden max-w-64 max-h-64 rounded-lg border border-gray-200 shadow overflow-hidden"
                                        style="background-image: linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%, #eee), linear-gradient(45deg, #eee 25%, transparent 25%, transparent 75%, #eee 75%, #eee); background-size: 20px 20px; background-position: 0 0, 10px 10px;">
                                        <img id="previewImage" src="" alt="Preview" class="w-auto h-auto max-w-full max-h-full object-contain" />
                                    </div>
                                <?php endif; ?>

                                <div class="w-full">
                                    <label for="foto_calon"
                                        class="flex flex-col items-center justify-center w-full py-14 h-auto text-gray-400 border-2 border-dashed border-gray-300 rounded-2xl text-sm hover:border-accent hover:text-accent hover:cursor-pointer transition-all duration-300">
                                        Klik untuk memilih foto
                                        <input type="file" name="foto_calon" id="foto_calon" class="hidden" accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </div>



                        <script>
                            const fileInput = document.getElementById('foto_calon');
                            const previewContainer = document.getElementById('previewContainer');
                            const previewImage = document.getElementById('previewImage');

                            fileInput.addEventListener('change', function() {
                                const file = this.files[0];
                                if (file) {
                                    const reader = new FileReader();

                                    reader.onload = function(e) {
                                        previewImage.src = e.target.result;
                                        previewContainer.classList.remove('hidden');
                                    };

                                    reader.readAsDataURL(file);
                                }
                            });
                        </script>

                        <div>
                            <label for="visi" class="block text-sm font-medium text-gray-700 mb-2">Visi</label>
                            <textarea id="visi" name="visi" rows="4" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200"><?php echo $edit_data ? $edit_data['visi'] : ''; ?></textarea>
                        </div>

                        <div>
                            <label for="misi" class="block text-sm font-medium text-gray-700 mb-2">Misi</label>
                            <textarea id="misi" name="misi" rows="4" required
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200"><?php echo $edit_data ? $edit_data['misi'] : ''; ?></textarea>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit" class="bg-accent cursor-pointer text-secondary py-3 px-6 rounded-xl font-medium hover:bg-gray-800 transition-colors">
                                <?php echo $edit_data ? 'Update Calon' : 'Tambah Calon'; ?>
                            </button>

                            <?php if ($edit_data): ?>
                                <a href="calon.php" class="bg-gray-500 cursor-pointer text-white py-3 px-6 rounded-xl font-medium hover:bg-gray-600 transition-colors">
                                    Batal
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="bg-secondary max-w-[51rem] rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b bg-neutral-100 rounded-t-lg border-gray-200">
                        <h2 class="text-xl font-bold text-accent">Daftar Calon</h2>
                    </div>

                    <div class="p-6 flex flex-col gap-4 h-[90%] w-full overflow-y-scroll overflow-x-hidden">
                        <?php
                        while ($calon = mysqli_fetch_assoc($calon_result)):
                        ?>
                            <div class="relative overflow-hidden min-w-[49rem] min-h-[14rem] shadow-lg z-0 card bg-white justify-end rounded-lg w-full flex">
                                <div class="information flex flex-col gap-2 justify-normal w-full">
                                    <div class="namanya p-6 pb-0 w-full items-start rounded-tl-lg flex justify-between h-fit">
                                        <div class="info-tag">
                                            <h3 class="text-accent text-xl font-semibold">
                                                <?= $calon['nama'] ?>
                                            </h3>
                                            <span class=" text-gray-400">Kelas: <?= $calon['nama_kelas'] ?></span>
                                        </div>
                                        <div class="action flex">
                                            <a href="?edit=<?php echo $calon['id']; ?>" class="text-neutral-400 hover:text-accent mr-3"><i class="fa-solid fa-pen-to-square"></i></a>
                                            <a href="?delete=<?php echo $calon['id']; ?>&img=<?= $calon['url_foto'] ?>"
                                                onclick="return confirm('Yakin ingin menghapus calon ini?')"
                                                class="text-neutral-400 hover:text-accent"><i class="fa-solid fa-trash-can"></i></a>
                                        </div>
                                    </div>
                                    <div class="visi-misi w-full p-6 pt-0 items-center flex gap-4">
                                        <div class="visi w-[50%]">
                                            <p class="font-medium text-neutral-800">Visi</p>
                                            <style>

                                            </style>
                                            <h1
                                                class="h-auto text-wrap text-sm text-neutral-700 h-28 max-h-28 overflow-x-auto"
                                                style="scrollbar-width: thin; scrollbar-color: #ebebeb transparent; overflow-y: auto;">
                                                <?= $calon['visi'] ?>
                                            </h1>
                                        </div>
                                        <div class="misi w-[50%]">
                                            <p class="font-medium text-neutral-800">Misi</p>
                                            <h1
                                                class="text-wrap text-neutral-700 text-sm h-28 max-h-28 overflow-x-auto"
                                                style="scrollbar-width: thin; scrollbar-color: #ebebeb transparent; overflow-y: auto;">
                                                <?= $calon['misi'] ?>
                                            </h1>
                                        </div>
                                    </div>

                                </div>
                                <div class="shadow-[-5px_0px_21px_-1px_rgba(0,_0,_0,_0.1)] max-w-[230px] bg-gradient-to-br from-gray-50 to-gray-200">
                                    <h1
                                        class="font-montserrat duration-200 mr-28 ml-4 ease-in-out mt-0 font-lato font-black opacity-20 text-8xl">
                                        <?= "0" . $calon['nomor'] ?>
                                    </h1>
                                </div>
                                <?php if (!empty($calon['url_foto'])): ?>
                                    <img
                                        src="<?= $calon['url_foto'] ?>"
                                        class="absolute bottom-0 -right-10 overflow-y-hidden z-10 object-cover size-64"
                                        alt="<?= $calon['nama'] ?>" />
                                <?php else: ?>
                                    <i class="absolute bottom-0 right-0 far opacity-30 text-9xl fa-user"></i>
                                <?php endif; ?>

                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>


                <!-- Jumlah Siswa Dengan Hak Suara -->
                <div class="bg-secondary max-w-[15rem] max-h-[15rem] rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b bg-neutral-100 rounded-t-lg border-gray-200">
                        <h2 class="text-xl font-bold text-accent">Jumlah Hak Suara</h2>
                    </div>

                    <form method="post" class="p-6 flex flex-col gap-4 h-[90%] w-full">
                        <input type="hidden" name="action" value="haksuara">
                        <input type="number" id="haksuara" name="haksuara" required
                            value="<?= $config['haksuara'] ?? '0' ?>"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200">

                        <button type="submit" class="bg-accent text-secondary py-3 px-6 rounded-xl font-medium hover:bg-gray-800 transition-colors">
                            Update
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>

</html>