<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div id="sidebar" class="sticky left-0 top-0 h-screen z-40 min-w-64 bg-secondary shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-20 bg-accent">
            <div class="text-center flex items-center gap-2">
                <img src="img/logo_white.png" alt="" class="size-9">
                <h1 class="text-secondary text-xl font-bold">PILKETOS</h1>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="admin.php" class="flex gap-2 items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $current_page == 'admin.php' ? 'bg-accent text-secondary' : ' text-accent hover:bg-gray-100'; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="<?php echo $current_page == 'admin.php' ? '#fffefe' : '#232322'; ?>" class="size-5 " viewBox="0 0 576 512">
                    <path d="M575.8 255.5c0 18-15 32.1-32 32.1l-32 0 .7 160.2c0 2.7-.2 5.4-.5 8.1l0 16.2c0 22.1-17.9 40-40 40l-16 0c-1.1 0-2.2 0-3.3-.1c-1.4 .1-2.8 .1-4.2 .1L416 512l-24 0c-22.1 0-40-17.9-40-40l0-24 0-64c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32 14.3-32 32l0 64 0 24c0 22.1-17.9 40-40 40l-24 0-31.9 0c-1.5 0-3-.1-4.5-.2c-1.2 .1-2.4 .2-3.6 .2l-16 0c-22.1 0-40-17.9-40-40l0-112c0-.9 0-1.9 .1-2.8l0-69.7-32 0c-18 0-32-14-32-32.1c0-9 3-17 10-24L266.4 8c7-7 15-8 22-8s15 2 21 7L564.8 231.5c8 7 12 15 11 24z" />
                </svg>
                Dashboard
            </a>

            <a href="calon.php" class="flex gap-2 items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $current_page == 'calon.php' ? 'bg-accent text-secondary' : 'text-accent hover:bg-gray-100'; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="<?php echo $current_page == 'calon.php' ? '#fffefe' : '#232322'; ?>" class="size-5" viewBox="0 0 640 512">
                    <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304l91.4 0C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7L29.7 512C13.3 512 0 498.7 0 482.3zM609.3 512l-137.8 0c5.4-9.4 8.6-20.3 8.6-32l0-8c0-60.7-27.1-115.2-69.8-151.8c2.4-.1 4.7-.2 7.1-.2l61.4 0C567.8 320 640 392.2 640 481.3c0 17-13.8 30.7-30.7 30.7zM432 256c-31 0-59-12.6-79.3-32.9C372.4 196.5 384 163.6 384 128c0-26.8-6.6-52.1-18.3-74.3C384.3 40.1 407.2 32 432 32c61.9 0 112 50.1 112 112s-50.1 112-112 112z" />
                </svg>
                Kelola calon
            </a>

            <a href="adminconfig.php" class="flex gap-2 items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $current_page == 'adminconfig.php' ? 'bg-accent text-secondary' : 'text-accent hover:bg-gray-100'; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="<?php echo $current_page == 'adminconfig.php' ? '#fffefe' : '#232322'; ?>" class="size-5" viewBox="0 0 640 512">
                    <path d="M224 0a128 128 0 1 1 0 256A128 128 0 1 1 224 0zM178.3 304l91.4 0c11.8 0 23.4 1.2 34.5 3.3c-2.1 18.5 7.4 35.6 21.8 44.8c-16.6 10.6-26.7 31.6-20 53.3c4 12.9 9.4 25.5 16.4 37.6s15.2 23.1 24.4 33c15.7 16.9 39.6 18.4 57.2 8.7l0 .9c0 9.2 2.7 18.5 7.9 26.3L29.7 512C13.3 512 0 498.7 0 482.3C0 383.8 79.8 304 178.3 304zM436 218.2c0-7 4.5-13.3 11.3-14.8c10.5-2.4 21.5-3.7 32.7-3.7s22.2 1.3 32.7 3.7c6.8 1.5 11.3 7.8 11.3 14.8l0 30.6c7.9 3.4 15.4 7.7 22.3 12.8l24.9-14.3c6.1-3.5 13.7-2.7 18.5 2.4c7.6 8.1 14.3 17.2 20.1 27.2s10.3 20.4 13.5 31c2.1 6.7-1.1 13.7-7.2 17.2l-25 14.4c.4 4 .7 8.1 .7 12.3s-.2 8.2-.7 12.3l25 14.4c6.1 3.5 9.2 10.5 7.2 17.2c-3.3 10.6-7.8 21-13.5 31s-12.5 19.1-20.1 27.2c-4.8 5.1-12.5 5.9-18.5 2.4l-24.9-14.3c-6.9 5.1-14.3 9.4-22.3 12.8l0 30.6c0 7-4.5 13.3-11.3 14.8c-10.5 2.4-21.5 3.7-32.7 3.7s-22.2-1.3-32.7-3.7c-6.8-1.5-11.3-7.8-11.3-14.8l0-30.5c-8-3.4-15.6-7.7-22.5-12.9l-24.7 14.3c-6.1 3.5-13.7 2.7-18.5-2.4c-7.6-8.1-14.3-17.2-20.1-27.2s-10.3-20.4-13.5-31c-2.1-6.7 1.1-13.7 7.2-17.2l24.8-14.3c-.4-4.1-.7-8.2-.7-12.4s.2-8.3 .7-12.4L343.8 325c-6.1-3.5-9.2-10.5-7.2-17.2c3.3-10.6 7.7-21 13.5-31s12.5-19.1 20.1-27.2c4.8-5.1 12.4-5.9 18.5-2.4l24.8 14.3c6.9-5.1 14.5-9.4 22.5-12.9l0-30.5zm92.1 133.5a48.1 48.1 0 1 0 -96.1 0 48.1 48.1 0 1 0 96.1 0z" />
                </svg>
                Kelola Admin
            </a>

            <a href="laporan.php" class="flex gap-2 items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $current_page == 'laporan.php' ? 'bg-accent text-secondary' : 'text-accent hover:bg-gray-100'; ?>">
                <svg xmlns="http://www.w3.org/2000/svg" fill="<?php echo $current_page == 'laporan.php' ? '#fffefe' : '#232322'; ?>" class="size-5" viewBox="0 0 448 512">
                    <path d="M160 80c0-26.5 21.5-48 48-48l32 0c26.5 0 48 21.5 48 48l0 352c0 26.5-21.5 48-48 48l-32 0c-26.5 0-48-21.5-48-48l0-352zM0 272c0-26.5 21.5-48 48-48l32 0c26.5 0 48 21.5 48 48l0 160c0 26.5-21.5 48-48 48l-32 0c-26.5 0-48-21.5-48-48L0 272zM368 96l32 0c26.5 0 48 21.5 48 48l0 288c0 26.5-21.5 48-48 48l-32 0c-26.5 0-48-21.5-48-48l0-288c0-26.5 21.5-48 48-48z" />
                </svg>
                Laporan
            </a>
        </nav>

        <!-- User Info -->
        <div class="p-4 border-t border-gray-200">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-accent rounded-full flex items-center justify-center">
                    <span class="text-secondary font-semibold"><?php echo substr($_SESSION['user_name'], 0, 1); ?></span>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-accent"><?php echo $_SESSION['user_name']; ?></p>
                    <p class="text-xs text-gray-500"><?php echo $_SESSION['user_email']; ?></p>
                </div>
            </div>
            <a href="logout.php" class="mt-3 w-full bg-birupesat text-white py-2 px-4 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Logout
            </a>
        </div>
    </div>
</div>

<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden hidden"></div>

<script>
    // Mobile menu toggle
    document.getElementById('mobile-menu-btn').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    document.getElementById('sidebar-overlay').addEventListener('click', function() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
</script>