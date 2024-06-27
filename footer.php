<div class="note-floater">
    <ul
        class="notes-ul border-2 border-blue-600 border-r-0 rounded-lg rounded-r-none rounded-br-none text-lg overflow-hidden">
        <!-- <li class="note-1">
            <a href="notes.php" class="px-3 py-1 text-blue-600 bg-white hover:bg-blue-600 hover:text-white"><i
                    class="fa fa-note-sticky mr-1"></i> Notes</a>
        </li> -->
        <li class="note-2">
            <a href="#" id="open-popup" class="px-3 py-1 text-blue-600 bg-white hover:bg-blue-600 hover:text-white"><i
                    class="fa fa-plus mr-1"></i> Add Note</a>
        </li>
    </ul>
</div>
<!-- Popup backdrop -->
<div id="popup-backdrop" class="fixed inset-0 flex items-center justify-center z-50 hidden popup-backdrop">
    <!-- Popup container -->
    <div id="popup" class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg mx-4">
        <h2 class="text-xl font-bold mb-4 text-center">Add Note</h2>
        <form method="post" action="?eventID=<?php echo $eventID; ?>" id="popup-form" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="title" class="block text-gray-700">Title</label>
                <input type="text" id="title" name="title" class="w-full px-3 py-2 border rounded">
            </div>
            <div class="mb-4">
                <label for="description" class="block text-gray-700">Description</label>
                <textarea id="description" name="description" class="w-full px-3 py-2 border rounded"
                    rows="4"></textarea>
            </div>
            <div class="mb-4">
                <label for="file-upload" class="block text-gray-700">Upload PDF</label>
                <input type="file" id="file-upload" name="file-upload" accept=".pdf,.png,.jpg,.jpeg,.gif,.svg"
                    class="w-full px-3 py-2 border rounded">
                <p class="text-xs text-gray-600">Only PDF files are allowed.</p>
            </div>
            <div class="flex justify-end">
                <button type="button" id="close-popup"
                    class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600 mr-2">Cancel</button>
                <button type="submit"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Submit</button>
            </div>
        </form>
    </div>
</div>