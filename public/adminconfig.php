<?php
session_start();
include 'conn.php';

$page_title = 'Konfigurasi Admin';
include 'islogin.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $nama_lengkap = $_POST['nama_lengkap'];

            // Hash password dengan SHA256
            $hashed_password = hash('sha256', $password);

            // Check if email already exists
            $check_query = "SELECT * FROM users WHERE email = '$email'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error = "Email sudah terdaftar!";
            } else {
                $query = "INSERT INTO users (email, psw, nama_lengkap) VALUES ('$email', '$hashed_password', '$nama_lengkap')";

                if (mysqli_query($conn, $query)) {
                    $success = "Admin berhasil ditambahkan!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $email = $_POST['email'];
            $nama_lengkap = $_POST['nama_lengkap'];

            // Check if email already exists for other users
            $check_query = "SELECT * FROM users WHERE email = '$email' AND id != '$id'";
            $check_result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($check_result) > 0) {
                $error = "Email sudah digunakan oleh admin lain!";
            } else {
                if (!empty($_POST['password'])) {
                    // Update with new password
                    $hashed_password = hash('sha256', $_POST['password']);
                    $query = "UPDATE users SET email='$email', psw='$hashed_password', nama_lengkap='$nama_lengkap' WHERE id='$id'";
                } else {
                    // Update without changing password
                    $query = "UPDATE users SET email='$email', nama_lengkap='$nama_lengkap' WHERE id='$id'";
                }

                if (mysqli_query($conn, $query)) {
                    $success = "Data admin berhasil diupdate!";
                } else {
                    $error = "Error: " . mysqli_error($conn);
                }
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    // Prevent deleting current user
    if ($id == $_SESSION['user_id']) {
        $error = "Tidak dapat menghapus akun yang sedang digunakan!";
    } else {
        $query = "DELETE FROM users WHERE id='$id'";

        if (mysqli_query($conn, $query)) {
            $success = "Admin berhasil dihapus!";
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// Get data for edit
$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $query = "SELECT * FROM users WHERE id='$id'";
    $result = mysqli_query($conn, $query);
    $edit_data = mysqli_fetch_assoc($result);
}

// Get all users
$query = "SELECT * FROM users ORDER BY id DESC";
$users_result = mysqli_query($conn, $query);
?>



<!-- Main Content -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'header.php'; ?>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="styles.css" />
</head>

<body class="flex">

    <script>
        if (window.innerWidth < 1024) {
            window.location.href = "no-access.php";
        }
    </script>

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 lg:ml-0 pt-16 lg:pt-0">
        <div class="p-6">
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-accent">Konfigurasi Admin</h1>
                <p class="text-gray-600 mt-2">Kelola akun administrator sistem</p>
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

            <div class="flex gap-6">
                <!-- Form -->
                <div class="bg-secondary w-[40%] rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-bold text-accent mb-6">
                        <?php echo $edit_data ? 'Edit Admin' : 'Tambah Admin Baru'; ?>
                    </h2>

                    <form method="POST" class="space-y-6">
                        <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                        <?php if ($edit_data): ?>
                            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                        <?php endif; ?>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" required
                                    value="<?php echo $edit_data ? $edit_data['nama_lengkap'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" required
                                    value="<?php echo $edit_data ? $edit_data['email'] : ''; ?>"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                                <?php if ($edit_data): ?>
                                    <span class="text-gray-500 font-normal">(Kosongkan jika tidak ingin mengubah password)</span>
                                <?php endif; ?>
                            </label>
                            <input type="password" id="password" name="password"
                                <?php echo !$edit_data ? 'required' : ''; ?>
                                placeholder="<?php echo $edit_data ? 'Masukkan password baru (opsional)' : 'Masukkan password'; ?>"
                                class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200">
                            <p class="text-xs text-gray-500 mt-1">Password minimal 6 karakter</p>
                        </div>

                        <?php if (!$edit_data): ?>
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" required
                                    placeholder="Ulangi password"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200">
                            </div>
                        <?php endif; ?>

                        <div class="flex gap-4">
                            <button type="submit" class="bg-accent cursor-pointer text-secondary py-3 px-6 rounded-xl font-medium hover:bg-gray-800 transition-colors">
                                <?php echo $edit_data ? 'Update Admin' : 'Tambah Admin'; ?>
                            </button>

                            <?php if ($edit_data): ?>
                                <a href="adminconfig.php" class="bg-gray-500 cursor-pointer text-white py-3 px-6 rounded-xl font-medium hover:bg-gray-600 transition-colors">
                                    Batal
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>

                <!-- Data Table -->
                <div class="bg-secondary w-[60%] rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h2 class="text-xl font-bold text-accent">Daftar Administrator</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php
                                $no = 1;
                                while ($user = mysqli_fetch_assoc($users_result)):
                                ?>
                                    <tr class="<?php echo $user['id'] == $_SESSION['user_id'] ? 'bg-blue-50' : ''; ?>">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $no++; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-accent text-secondary rounded-full flex items-center justify-center font-semibold mr-3">
                                                    <?php echo substr($user['nama_lengkap'], 0, 1); ?>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900"><?php echo $user['nama_lengkap']; ?></div>
                                                    <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                        <div class="text-xs text-blue-600">Akun Anda</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $user['email']; ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($user['id'] == $_SESSION['user_id']): ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Aktif (Anda)
                                                </span>
                                            <?php else: ?>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Administrator
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="?edit=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <a href="?delete=<?php echo $user['id']; ?>"
                                                    onclick="return confirm('Yakin ingin menghapus admin ini?')"
                                                    class="text-red-600 hover:text-red-900">Hapus</a>
                                            <?php else: ?>
                                                <span class="text-gray-400 cursor-not-allowed">Hapus</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-yellow-800">Peringatan Keamanan</h3>
                        <div class="mt-2 text-sm text-yellow-700">
                            <ul class="list-disc list-inside space-y-1">
                                <li>Gunakan password yang kuat (minimal 8 karakter dengan kombinasi huruf, angka, dan simbol)</li>
                                <li>Jangan bagikan informasi login kepada orang lain</li>
                                <li>Pastikan logout setelah selesai menggunakan sistem</li>
                                <li>Hanya berikan akses admin kepada orang yang dipercaya</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password confirmation validation
        document.getElementById('confirm_password')?.addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;

            if (password !== confirmPassword) {
                this.setCustomValidity('Password tidak cocok');
            } else {
                this.setCustomValidity('');
            }
        });

        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strength = getPasswordStrength(password);

            // You can add visual feedback here
        });

        function getPasswordStrength(password) {
            let strength = 0;
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            return strength;
        }
    </script>

</body>

</html>