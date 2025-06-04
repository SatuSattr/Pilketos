<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: admin.php');
    exit();
}

include 'conn.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash password dengan SHA256 (sesuai dengan data yang ada)
    $hashed_password = hash('sha256', $password);

    $query = "SELECT * FROM users WHERE email = '$email' AND psw = '$hashed_password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['nama_lengkap'];
        $_SESSION['user_email'] = $user['email'];

        header('Location: admin.php');
        exit();
    } else {
        $error = "Email atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include 'header.php'; ?>
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="styles.css" />
</head>

<body class="relative bg-primary font-montserrat min-h-screen flex items-center justify-center p-4 overflow-hidden">

    <div
        class="absolute inset-0 bg-[url('img/back.png')] bg-cover bg-center opacity-10 saturate-0 z-0"></div>



    <div class="relative z-10 w-full max-w-sm mx-auto">
        <div class="bg-secondary rounded-3xl shadow-xl overflow-hidden">
            <div class="bg-accent h-48 flex gap-3 items-center justify-center relative">
                <img src="img/logo_white.png" alt="" class="object-cover h-18">
                <h1 class="text-primary uppercase leading-6 text-2xl font-semibold">
                    Pilketos
                    <br>
                    <span class="font-normal text-[0.9rem] normal-case">v1.0</span>
                </h1>

            </div>

            <div class="p-8">
                <h1 class="text-2xl font-semibold text-accent text-center mb-8">Login</h1>

                <?php if (isset($error)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" placeholder="yourrmail@gmail.com" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200 text-accent placeholder-gray-400">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" placeholder="••••••••" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent outline-none transition-all duration-200 text-accent placeholder-gray-400">
                    </div>

                    <button type="submit" class="w-full bg-accent text-secondary py-3 px-4 rounded-xl font-medium hover:bg-gray-800 focus:ring-2 focus:ring-accent focus:ring-offset-2 transition-all duration-200">
                        Login
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>