<?php
if (isset($_SESSION['notifications'])) {
    // print_r($_SESSION['notifications']);
    foreach ($_SESSION['notifications'] as $notification) {
        foreach ($notification as $type => $message) {
            $classes = ($type == 'error') ? 'bg-red-200 border-red-800 text-red-800 ' : 'bg-green-200 border-green-800 text-green-800 ';
            $info .= '<div class="px-3 py-2 mb-3 ' . $classes . ' border rounded">'.$message.'</div>';
        }
    }
    $_SESSION['notifications'] = array();
}
?>
<header class="px-4 py-3 border-b-2 border-blue-900 glass sticky top-0 z-10">
    <div class="container flex justify-between items-center">
        <div class="flex items-center gap-2">
            <img src="<?php echo $_SESSION['user_picture']; ?>" class="w-14 h-14 rounded-full" alt="Profile Picture">
            <div class="flex flex-col">
                <h2 class="font-bold text-lg text-blue-700"><?php echo $_SESSION['user_name']; ?></h2>
                <h5 class="font-medium text-blue-900"><?php echo $_SESSION['user_email']; ?></h5>
            </div>
        </div>
        <nav>
            <div class="flex items-center gap-3 p-5 lg:p-0">
                <a href="./dashboard.php"
                    class="text-gray-700 cursor-pointer hover:text-gray-800 font-bold py-2 px-0 rounded focus:outline-none focus:shadow-outline"><i
                        class="fa fa-home"></i> Home</a>
                <a href="./create-event.php"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"><i
                        class="fa-regular fa-plus"></i> Create Event</a>
                <a href="./logout.php"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"><i
                        class="fa fa-sign-out"></i> Logout</a>
            </div>
        </nav>
        <span class="rounded-sm border border-gray-300 px-2 py-0 cursor-pointer block lg:hidden menu-btn">
            <i class="fa fa-bars"></i>
        </span>
    </div>
</header>