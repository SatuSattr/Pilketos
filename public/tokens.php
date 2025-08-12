<?php
session_start();
include 'conn.php';

$page_title = 'Display Tokens';
include 'islogin.php';

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {

        // Tambah
        if ($_POST['action'] == 'add') {
            $token = trim($_POST['token']);
            $check_query = "SELECT * FROM tokens WHERE token = '" . mysqli_real_escape_string($conn, $token) . "'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error = "Token sudah ada!";
            } else {
                $query = "INSERT INTO tokens (token) VALUES ('" . mysqli_real_escape_string($conn, $token) . "')";
                if (mysqli_query($conn, $query)) {
                    $success = "Token berhasil ditambahkan!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }

            // Edit
        } elseif ($_POST['action'] == 'edit') {
            $id = intval($_POST['id']);
            $token = trim($_POST['token']);
            $check_query = "SELECT * FROM tokens WHERE token = '" . mysqli_real_escape_string($conn, $token) . "' AND id != '$id'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error = "Token sudah ada!";
            } else {
                $query = "UPDATE tokens SET token='" . mysqli_real_escape_string($conn, $token) . "' WHERE id='$id'";
                if (mysqli_query($conn, $query)) {
                    $success = "Token berhasil diupdate!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Hapus
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM tokens WHERE id='$id'";
    if (mysqli_query($conn, $query)) {
        $success = "Token berhasil dihapus!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// Ambil data untuk edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $query = "SELECT * FROM tokens WHERE id='$id'";
    $result = mysqli_query($conn, $query);
    $edit_data = mysqli_fetch_assoc($result);
}

// Ambil semua data token
$query = "SELECT * FROM tokens ORDER BY id DESC";
$tokens = mysqli_query($conn, $query);

setcookie("last_refresh", time(), time() + 1200, "/");
$last_refresh = 0;
if (isset($_COOKIE["last_refresh"])) $last_refresh = time() - (int) $_COOKIE["last_refresh"];

$total_token_query = "SELECT COUNT(*) as total FROM tokens";
$total_token_result = mysqli_query($conn, $total_token_query);
$total_token = mysqli_fetch_assoc($total_token_result)['total'];
$total_token_add = 0;
if (isset($_COOKIE["total_token"])) {
    $total_token_add = $total_token - (int) $_COOKIE["total_token"];
}
setcookie("total_token", $total_token, time() + 1200, "/");

$total_active_query = "SELECT COUNT(*) as total FROM tokens WHERE active = 1";
$total_active_result = mysqli_query($conn, $total_active_query);
$total_active = mysqli_fetch_assoc($total_active_result)['total'];
$total_active_add = 0;
if (isset($_COOKIE["total_active"])) {
    $total_active_add = $total_active - (int) $_COOKIE["total_active"];
}
setcookie("total_active", $total_active, time() + 1200, "/");


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
                <h1 class="text-3xl font-bold text-accent">Display Tokens</h1>
                <p class="text-gray-600 mt-2">Kelola token yang terdaftar</p>
            </div>

            <?php if (isset($success)): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6"><?= $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6"><?= $error; ?></div>
            <?php endif; ?>


            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Jumlah Token</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?= $total_token; ?></h4>
                            </div>
                            <div class="size-13 p-3 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                                <i class="fas text-[1.2rem] fa-key text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <hr class="border-t border-gray-200 my-0">
                    <div class="px-4 py-2">
                        <p class="text-sm text-gray-600 mb-0">
                            <span class="text-green-600 font-semibold">+<?= $total_token_add ?> </span>than last <?= $last_refresh; ?> seconds
                        </p>
                    </div>
                </div>

                <div class="rounded-xl shadow-md bg-white">
                    <div class="px-4 py-2">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm mb-1 capitalize text-gray-600">Token Aktif</p>
                                <h4 class="text-3xl font-semibold text-gray-800"><?= $total_active; ?><span class="ml-2 text-gray-600 text-[1rem] font-normal">/ <?= $total_token; ?></span></h4>
                            </div>
                            <div class="size-13 p-3 flex items-center justify-center bg-accent shadow-lg rounded-lg">
                                <i class="text-[1.3rem] far fa-check-circle text-primary"></i>
                            </div>
                        </div>
                    </div>
                    <hr class="border-t border-gray-200 my-0">
                    <div class="px-4 py-2">
                        <p class="text-sm text-gray-600 mb-0">
                            <span class="text-green-600 font-semibold">+<?= $total_active_add ?> </span>than last <?= $last_refresh; ?> seconds
                        </p>
                    </div>
                </div>
            </div>


            <div class="flex gap-6">
                <!-- Form -->
                <div class="w-[40%] bg-secondary rounded-xl h-fit shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-accent mb-6"><?= $edit_data ? 'Edit Token' : 'Tambah Token'; ?></h2>
                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="<?= $edit_data ? 'edit' : 'add'; ?>">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?= $edit_data['id']; ?>">
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Token</label>
                            <div class="flex">
                                <input type="text" id="tokenInput" name="token" required
                                    value="<?= $edit_data ? $edit_data['token'] : ''; ?>"
                                    class="flex-1 px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent">
                                <button type="button" id="generateToken"
                                    class="ml-2 bg-accent text-secondary px-4 rounded-xl hover:bg-gray-800">
                                    <i class="fas fa-dice"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="bg-accent text-secondary py-3 px-6 rounded-xl font-medium hover:bg-gray-800">
                            <?= $edit_data ? 'Update Token' : 'Tambah Token'; ?>
                        </button>
                    </form>
                </div>

                <!-- Script Random Token -->
                <script>
                    document.getElementById('generateToken').addEventListener('click', function() {
                        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                        let token = '';
                        for (let i = 0; i < 6; i++) {
                            token += chars.charAt(Math.floor(Math.random() * chars.length));
                        }
                        document.getElementById('tokenInput').value = token;
                    });
                </script>


                <!-- Tabel -->
                <div class="bg-secondary w-[60%] rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-accent">Daftar Token</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 block w-full">
                                <tr class="table w-full table-fixed">
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-1/12">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-7/12">Token</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase w-4/12">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="block max-h-114 overflow-y-auto bg-white divide-y divide-gray-200 w-full">
                                <?php $no = 1;
                                while ($row = mysqli_fetch_assoc($tokens)): ?>
                                    <tr class="table w-full table-fixed">
                                        <td class="px-6 py-4 text-sm text-gray-900 w-1/12"><?= $no++; ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-900 w-7/12"><?= htmlspecialchars($row['token']); ?></td>
                                        <td class="px-6 py-4 text-sm font-medium w-4/12">
                                            <a href="?edit=<?= $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                            <a href="?delete=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus token ini?')" class="text-red-600 hover:text-red-900">Hapus</a>
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