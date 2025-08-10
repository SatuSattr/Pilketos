<?php
session_start();
include 'conn.php';

$page_title = 'Kelola Hak Suara';
include 'islogin.php';

// Load PhpSpreadsheet
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

setcookie("last_refresh", time(), time() + 1200, "/");
$last_refresh = 0;
if (isset($_COOKIE["last_refresh"])) $last_refresh = time() - (int) $_COOKIE["last_refresh"];

$total_vote_query = "SELECT COUNT(*) as total FROM vote";
$total_vote_result = mysqli_query($conn, $total_vote_query);
$total_vote = mysqli_fetch_assoc($total_vote_result)['total'];
$total_vote_add = 0;
if (isset($_COOKIE["total_vote"])) {
    $total_vote_add =  $total_vote - (int) $_COOKIE["total_vote"];
}
setcookie("total_vote", $total_vote, time() + 1200, "/");

$total_haksuara_query = "SELECT COUNT(*) as total FROM hak_suara";
$total_haksuara_result = mysqli_query($conn, $total_haksuara_query);
$total_haksuara = mysqli_fetch_assoc($total_haksuara_result)['total'];
$total_haksuara_add = 0;
if (isset($_COOKIE["total_haksuara"])) {
    $total_haksuara_add =  $total_haksuara - (int) $_COOKIE["total_haksuara"];
}
setcookie("total_haksuara", $total_haksuara, time() + 1200, "/");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {

        // Tambah manual
        if ($_POST['action'] == 'add') {
            $nisn = trim($_POST['nisn']);
            $check_query = "SELECT * FROM hak_suara WHERE nisn = '" . mysqli_real_escape_string($conn, $nisn) . "'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error = "Nama sudah terdaftar!";
            } else {
                $query = "INSERT INTO hak_suara (nisn) VALUES ('" . mysqli_real_escape_string($conn, $nisn) . "')";
                if (mysqli_query($conn, $query)) {
                    $success = "Hak Suara berhasil ditambahkan!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }

            // Edit manual
        } elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $nisn = trim($_POST['nisn']);
            $check_query = "SELECT * FROM hak_suara WHERE nisn = '" . mysqli_real_escape_string($conn, $nisn) . "' AND id != '" . intval($id) . "'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error = "Nama sudah terdaftar!";
            } else {
                $query = "UPDATE hak_suara SET nisn='" . mysqli_real_escape_string($conn, $nisn) . "' WHERE id='" . intval($id) . "'";
                if (mysqli_query($conn, $query)) {
                    $success = "Data Hak Suara berhasil diupdate!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }

            // Import Excel
        } elseif ($_POST['action'] == 'import') {
            if (isset($_FILES['file_excel']) && $_FILES['file_excel']['error'] == 0) {
                $filePath = $_FILES['file_excel']['tmp_name'];

                try {
                    $spreadsheet = IOFactory::load($filePath);
                    $sheet = $spreadsheet->getActiveSheet();
                    $rows = $sheet->toArray();

                    $imported = 0;
                    foreach ($rows as $index => $row) {
                        if ($index == 0) continue; // skip header
                        $nama = trim($row[1]); // kolom B

                        if ($nama == '') continue;

                        // Cek duplikat
                        $check_query = "SELECT * FROM hak_suara WHERE nisn = '" . mysqli_real_escape_string($conn, $nama) . "'";
                        $check_result = mysqli_query($conn, $check_query);

                        if (mysqli_num_rows($check_result) == 0) {
                            mysqli_query($conn, "INSERT INTO hak_suara (nisn) VALUES ('" . mysqli_real_escape_string($conn, $nama) . "')");
                            $imported++;
                        }
                    }

                    $success = "Import selesai! $imported data berhasil ditambahkan.";
                } catch (Exception $e) {
                    $error = "Gagal membaca file Excel: " . $e->getMessage();
                }
            } else {
                $error = "Upload file Excel gagal!";
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM hak_suara WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        $success = "Data berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Get data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $query = "SELECT * FROM hak_suara WHERE id='$id'";
    $result = mysqli_query($conn, $query);
    $edit_data = mysqli_fetch_assoc($result);
}

// Get semua data hak suara
$query = "SELECT c.*, COUNT(v.id) as isvoted 
          FROM hak_suara c  
          LEFT JOIN vote v ON c.id = v.id_nisn 
          GROUP BY c.id 
          ORDER BY isvoted DESC";
$nisns = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'header.php'; ?>
    <link rel="stylesheet" href="styles.css" />
</head>

<body class="flex">
    <?php include 'sidebar.php'; ?>

    <div class="flex-1 lg:ml-0 pt-16 lg:pt-0">
        <div class="p-6">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-accent">Data Hak Suara</h1>
                <p class="text-gray-600 mt-2">Kelola hak suara siswa</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
                    <?= $success; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
                    <?= $error; ?>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">

                <div class="rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Jumlah Hak Suara</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?= $total_haksuara; ?></h4>
                            </div>
                            <div class="size-13 p-3 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                                <i class="fas text-[1.2rem] fa-user-group text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <hr class="border-t border-gray-200 my-0">
                    <div class="px-4 py-2">
                        <p class="text-sm text-gray-600 mb-0">
                            <span class="text-green-600 font-semibold">+<?= $total_haksuara_add ?> </span>than last <?= $last_refresh; ?> seconds
                        </p>
                    </div>
                </div>

                <div class="rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Hak Suara Terpakai</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?= $total_vote; ?><span class="ml-2 text-gray-600 text-[1rem] font-normal">/ <?= $total_haksuara; ?></span></h4>
                            </div>
                            <div class="size-13 p-3 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                                <i class="text-[1.3rem] far fa-circle-check text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <hr class="border-t border-gray-200 my-0">
                    <div class="px-4 py-2">
                        <p class="text-sm text-gray-600 mb-0">
                            <span class="text-green-600 font-semibold">+<?= $total_vote_add ?> </span>than last <?= $last_refresh; ?> seconds
                        </p>
                    </div>
                </div>




            </div>

            <div class="flex gap-6">
                <div class="w-[40%] space-y-6">
                    <div class="bg-secondary rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-xl font-bold text-accent mb-6"><?= $edit_data ? 'Edit Hak Suara' : 'Tambah Hak Suara'; ?></h2>
                        <form method="POST" class="space-y-6">
                            <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'add'; ?>">
                            <?php if ($edit_data): ?>
                                <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
                            <?php endif; ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
                                <input type="text" name="nisn" required value="<?= $edit_data ? $edit_data['nisn'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent">
                            </div>
                            <button type="submit" class="bg-accent text-secondary py-3 px-6 rounded-xl font-medium hover:bg-gray-800">
                                <?= $edit_data ? 'Update Hak Suara' : 'Tambah Hak Suara'; ?>
                            </button>
                        </form>
                    </div>

                    <div class="bg-secondary rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-xl font-bold text-accent mb-4">Import Hak Suara</h2>
                        <form method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="import">
                            <input type="file" name="file_excel" accept=".xls,.xlsx" required
                                class="mb-4 block w-full px-4 py-3 border border-gray-200 file:mr-3 file:text-neutral-800 file:py-[2px] file:px-3 file:cursor-pointer file:bg-neutral-200 file:rounded rounded-xl focus:ring-2 focus:ring-accent">
                            <button type="submit" class="bg-accent text-secondary py-3 px-6 rounded-xl font-medium hover:bg-gray-800">
                                Import
                            </button>

                        </form>
                    </div>
                </div>

                <div class="bg-secondary w-[60%] rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-accent">Daftar Hak Suara</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 block w-full">
                                <tr class="table w-full table-fixed">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-1/12">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-4/12">NAMA</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-4/12">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-3/12">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block max-h-114 overflow-y-auto bg-white divide-y divide-gray-200 w-full">
                                <?php $no = 1;
                                while ($nisn = mysqli_fetch_assoc($nisns)): ?>
                                    <tr class="table w-full table-fixed">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 w-1/12"><?php echo $no++; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 w-4/12"><?php echo $nisn['nisn']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap w-4/12">
                                            <?php if ($nisn['isvoted']): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Sudah Memilih
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Belum Memilih
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="?edit=<?php echo $nisn['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>

                                            <a href="?delete=<?php echo $nisn['id']; ?>"
                                                onclick="return confirm('Yakin ingin menghapus admin ini?')"
                                                class="text-red-600 hover:text-red-900">Hapus</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>



                </div>
            </div>
        </div>
    </div>
</body>

</html>