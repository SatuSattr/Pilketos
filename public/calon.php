<?php
session_start();
include 'conn.php';

$page_title = 'Data Calon';
include 'header.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $nama = $_POST['nama'];
            $visi = $_POST['visi'];
            $misi = $_POST['misi'];
            $id_kelas = $_POST['id_kelas'];
            $url_foto = $_POST['url_foto'];
            
            $query = "INSERT INTO calon_ketua (nama, visi, misi, id_kelas, url_foto) VALUES ('$nama', '$visi', '$misi', '$id_kelas', '$url_foto')";
            
            if (mysqli_query($conn, $query)) {
                $success = "Calon berhasil ditambahkan!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        } elseif ($_POST['action'] == 'edit') {
            $id = $_POST['id'];
            $nama = $_POST['nama'];
            $visi = $_POST['visi'];
            $misi = $_POST['misi'];
            $id_kelas = $_POST['id_kelas'];
            $url_foto = $_POST['url_foto'];
            
            $query = "UPDATE calon_ketua SET nama='$nama', visi='$visi', misi='$misi', id_kelas='$id_kelas', url_foto='$url_foto' WHERE id='$id'";
            
            if (mysqli_query($conn, $query)) {
                $success = "Data calon berhasil diupdate!";
            } else {
                $error = "Error: " . mysqli_error($conn);
            }
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $query = "DELETE FROM calon_ketua WHERE id='$id'";
    
    if (mysqli_query($conn, $query)) {
        $success = "Calon berhasil dihapus!";
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

<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<div class="flex-1 lg:ml-0 pt-16 lg:pt-0">
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
        
        <!-- Form -->
        <div class="bg-secondary rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
            <h2 class="text-xl font-bold text-accent mb-6">
                <?php echo $edit_data ? 'Edit Calon' : 'Tambah Calon Baru'; ?>
            </h2>
            
            <form method="POST" class="space-y-6">
                <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                <?php if ($edit_data): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                </div>
                
                <div>
                    <label for="url_foto" class="block text-sm font-medium text-gray-700 mb-2">URL Foto</label>
                    <input type="url" id="url_foto" name="url_foto"
                           value="<?php echo $edit_data ? $edit_data['url_foto'] : ''; ?>"
                           placeholder="https://example.com/foto.jpg"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200">
                </div>
                
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
                    <button type="submit" class="bg-accent text-secondary py-3 px-6 rounded-xl font-medium hover:bg-gray-800 transition-colors">
                        <?php echo $edit_data ? 'Update Calon' : 'Tambah Calon'; ?>
                    </button>
                    
                    <?php if ($edit_data): ?>
                        <a href="calon.php" class="bg-gray-500 text-white py-3 px-6 rounded-xl font-medium hover:bg-gray-600 transition-colors">
                            Batal
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Data Table -->
        <div class="bg-secondary rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-accent">Daftar Calon</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Foto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $no = 1;
                        while ($calon = mysqli_fetch_assoc($calon_result)): 
                        ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $no++; ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (!empty($calon['url_foto'])): ?>
                                        <img src="<?php echo $calon['url_foto']; ?>" alt="<?php echo $calon['nama']; ?>" class="w-12 h-12 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo $calon['nama']; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?php echo $calon['nama_kelas']; ?></td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate"><?php echo substr($calon['visi'], 0, 50) . '...'; ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="?edit=<?php echo $calon['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">Edit</a>
                                    <a href="?delete=<?php echo $calon['id']; ?>" 
                                       onclick="return confirm('Yakin ingin menghapus calon ini?')"
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

</body>
</html>
