<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!-- Sidebar -->
<div id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 w-64 bg-secondary shadow-lg transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full">
        <!-- Logo -->
        <div class="flex items-center justify-center h-20 bg-accent">
            <div class="text-center">
                <h1 class="text-secondary text-xl font-bold">PILKETOS</h1>
                <p class="text-secondary/80 text-sm">Dashboard v1.0</p>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="dashboard.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $current_page == 'dashboard.php' ? 'bg-accent text-secondary' : 'text-accent hover:bg-gray-100'; ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                </svg>
                Dashboard
            </a>

            <a href="calon.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $current_page == 'calon.php' ? 'bg-accent text-secondary' : 'text-accent hover:bg-gray-100'; ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                </svg>
                Data Calon
            </a>

            <a href="laporan.php" class="flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors <?php echo $current_page == 'laporan.php' ? 'bg-accent text-secondary' : 'text-accent hover:bg-gray-100'; ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
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

<!-- Overlay untuk mobile -->
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