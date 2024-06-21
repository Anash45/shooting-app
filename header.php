<header class="px-4 py-3 border-b-2 border-blue-900 glass sticky top-0 z-10">
    <div class="container flex justify-between">
        <div class="flex items-center gap-2">
            <img src="<?php echo $_SESSION['user_picture']; ?>" class="w-14 h-14 rounded-full" alt="Profile Picture">
            <div class="flex flex-col">
                <h2 class="font-bold text-lg text-blue-700"><?php echo $_SESSION['user_name']; ?></h2>
                <h5 class="font-medium text-blue-900"><?php echo $_SESSION['user_email']; ?></h5>
            </div>
        </div>
        <nav class="flex items-center gap-3">
            <!-- <span
                class="text-black-600 cursor-pointer hover:text-black-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"><i
                    class="fa-regular fa-plus"></i> Add Note</span> -->
            <a href="./create-event.php"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"><i
                    class="fa-regular fa-plus"></i> Create Event</a>
            <a href="./logout.php"
                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"><i
                    class="fa fa-sign-out"></i> Logout</a>
        </nav>
    </div>
</header>