<?php
require "dbconnection.php";
session_start();

if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'Librarian') {
    if(isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Administrator') {
        header('Location: admindashboard.php');
    } else {
        header('Location: index.php');
    }
    exit();
}

if(isset($_GET['logout']) && $_GET['logout'] == '1') {
    session_destroy();
    header('Location: login.php');
    exit();
}

function writeLog($conn, $action) {
    $userId = isset($_SESSION['id']) ? intval($_SESSION['id']) : 'NULL';
    $safeAction = $conn->real_escape_string($action);
    $logSql = "INSERT INTO logs_table (user_id, action, datetime) VALUES ($userId, '$safeAction', NOW())";
    $conn->query($logSql);
}

$activeSection = 'booksSection';
if(isset($_GET['section']) && $_GET['section'] === 'booksSection') {
    $activeSection = 'booksSection';
}

// Handle Delete Book
if(isset($_POST['deleteBook'])) {
    $book_id = intval($_POST['book_id']);
    $conn->query("DELETE FROM borrowing_record_table WHERE book_id = $book_id");
    $conn->query("DELETE FROM book_table WHERE book_id = $book_id");
    writeLog($conn, "Deleted Book ID: $book_id");
    echo "<script>Swal.fire('Success', 'Book deleted successfully', 'success');</script>";
    $activeSection = 'booksSection';
}

// Handle Add/Edit Book
if(isset($_POST['saveBook'])) {
    $book_id = isset($_POST['book_id']) && $_POST['book_id'] != '' ? intval($_POST['book_id']) : null;
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $isbn = $conn->real_escape_string($_POST['isbn']);
    $genre = $conn->real_escape_string($_POST['genre']);
    $publication_date = $conn->real_escape_string($_POST['publication_date']);
    $availability_status = $conn->real_escape_string($_POST['availability_status']);
    $category_id = trim($_POST['category_id']);
    $categorySql = $category_id === '' ? 'NULL' : intval($category_id);

    if($book_id) {
        $updateBookSql = "UPDATE book_table SET title='$title', author='$author', ISBN='$isbn', genre='$genre', publication_date='$publication_date', availability_status='$availability_status', category_id=$categorySql WHERE book_id=$book_id";
        $conn->query($updateBookSql);
        writeLog($conn, "Updated Book ID: $book_id");
        echo "<script>Swal.fire('Success', 'Book updated successfully', 'success');</script>";
    } else {
        $insertBookSql = "INSERT INTO book_table (title, author, ISBN, genre, publication_date, availability_status, category_id) VALUES ('$title', '$author', '$isbn', '$genre', '$publication_date', '$availability_status', $categorySql)";
        $conn->query($insertBookSql);
        writeLog($conn, "Added Book: $title");
        echo "<script>Swal.fire('Success', 'Book added successfully', 'success');</script>";
    }
    $activeSection = 'booksSection';
}

// Handle Delete Borrowing Record
if(isset($_POST['deleteBorrow'])) {
    $record_id = intval($_POST['record_id']);
    $borrowRes = $conn->query("SELECT book_id, status FROM borrowing_record_table WHERE record_id = $record_id");
    if($borrowRes && $borrowRes->num_rows == 1) {
        $borrowRow = $borrowRes->fetch_assoc();
        $conn->query("DELETE FROM borrowing_record_table WHERE record_id = $record_id");
        if($borrowRow['status'] == 'borrowed') {
            $conn->query("UPDATE book_table SET availability_status = 'Available' WHERE book_id = " . intval($borrowRow['book_id']));
        }
    }
    writeLog($conn, "Deleted Borrow Record ID: $record_id");
    echo "<script>Swal.fire('Success', 'Borrowed book record deleted successfully', 'success');</script>";
    $activeSection = 'booksSection';
}

// Handle Add/Edit Borrowing Record
if(isset($_POST['saveBorrow'])) {
    $record_id = isset($_POST['record_id']) && $_POST['record_id'] != '' ? intval($_POST['record_id']) : null;
    $book_id = intval($_POST['book_id']);
    $member_id = intval($_POST['member_id']);
    $borrow_date = $conn->real_escape_string($_POST['borrow_date']);
    $return_date = $conn->real_escape_string($_POST['return_date']);
    $status = $conn->real_escape_string($_POST['status']);

    if($record_id) {
        $updateBorrowSql = "UPDATE borrowing_record_table SET book_id=$book_id, member_id=$member_id, borrow_date='$borrow_date', return_date='$return_date', status='$status' WHERE record_id=$record_id";
        $conn->query($updateBorrowSql);
        writeLog($conn, "Updated Borrow Record ID: $record_id");
    } else {
        $insertBorrowSql = "INSERT INTO borrowing_record_table (book_id, member_id, borrow_date, return_date, status) VALUES ($book_id, $member_id, '$borrow_date', '$return_date', '$status')";
        $conn->query($insertBorrowSql);
        writeLog($conn, "Added Borrow Record for Book ID: $book_id");
    }

    if($status === 'borrowed') {
        $conn->query("UPDATE book_table SET availability_status = 'Borrowed' WHERE book_id = $book_id");
    } elseif($status === 'returned') {
        $conn->query("UPDATE book_table SET availability_status = 'Available' WHERE book_id = $book_id");
    }

    echo "<script>Swal.fire('Success', 'Borrow record saved successfully', 'success');</script>";
    $activeSection = 'booksSection';
}

$bookSearchInput = '';
$bookDisplaySQL = "SELECT * FROM book_table";
if(isset($_POST['btnBookSearch'])) {
    $bookSearchInput = $conn->real_escape_string($_POST['bookSearchInput']);
    if ($bookSearchInput !== '') {
        $bookDisplaySQL = "SELECT * FROM book_table WHERE (
            book_id LIKE '%$bookSearchInput%' OR
            title LIKE '%$bookSearchInput%' OR
            author LIKE '%$bookSearchInput%' OR
            ISBN LIKE '%$bookSearchInput%' OR
            genre LIKE '%$bookSearchInput%' OR
            publication_date LIKE '%$bookSearchInput%' OR
            availability_status LIKE '%$bookSearchInput%'
        )";
    }
}
$bookResult = $conn->query($bookDisplaySQL);

$borrowSearchInput = '';
$borrowDisplaySQL = "SELECT br.record_id, br.book_id, br.member_id, br.borrow_date, br.return_date, br.status, b.title AS book_title, m.member_name 
FROM borrowing_record_table br 
LEFT JOIN book_table b ON br.book_id = b.book_id 
LEFT JOIN member_table m ON br.member_id = m.member_id";
if(isset($_POST['btnBorrowSearch'])) {
    $borrowSearchInput = $conn->real_escape_string($_POST['borrowSearchInput']);
    if ($borrowSearchInput !== '') {
        $borrowDisplaySQL = "SELECT br.record_id, br.book_id, br.member_id, br.borrow_date, br.return_date, br.status, b.title AS book_title, m.member_name 
        FROM borrowing_record_table br 
        LEFT JOIN book_table b ON br.book_id = b.book_id 
        LEFT JOIN member_table m ON br.member_id = m.member_id 
        WHERE (
            br.record_id LIKE '%$borrowSearchInput%' OR
            b.title LIKE '%$borrowSearchInput%' OR
            m.member_name LIKE '%$borrowSearchInput%' OR
            br.status LIKE '%$borrowSearchInput%'
        )";
    }
}
$borrowResult = $conn->query($borrowDisplaySQL);

$bookOptionsRes = $conn->query("SELECT book_id, title FROM book_table ORDER BY title");
$memberOptionsRes = $conn->query("SELECT member_id, member_name FROM member_table ORDER BY member_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <title>Librarian Dashboard</title>
</head>
<body class="has-sidebar has-topbar" style="background-color: #f7f7f7;">
<nav class="navbar navbar-expand-lg top-navbar shadow-sm">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-2">
            <button class="btn btn-light shadow-sm sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand d-flex align-items-center gap-2" href="#">
                <img src="./assets/headerLogo.png" alt="Logo" width="220" height="45" class="d-inline-block align-text-top" draggable="false">
            </a>
        </div>
    </div>
</nav>
<nav class="navbar shadow-sm sidebar-navbar sidebar-collapsed" id="sidebar">
    <div class="container-fluid d-flex flex-column align-items-start">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_type']); ?></h2>
            <li class="nav-item"><a class="nav-link" href="librariandashboard.php?section=booksSection"><i class="bi bi-book me-2"></i>Modify Books</a></li>
            <li class="nav-item"><a class="nav-link" href="librariandashboard.php?logout=1"><i class="bi bi-box-arrow-left me-2"></i>Logout</a></li>
        </ul>
    </div>
</nav>
<main id="main" class="bg-secondary-subtle p-5 d-flex flex-column gap-5">
    <div id="homeSection" class="p-5 shadow-sm rounded-4 bg-white" <?php echo $activeSection === 'homeSection' ? '' : 'style="display:none;"'; ?>>
        <h2>Welcome to Librarian Dashboard</h2>
        <p class="mt-3">Select Modify Books to manage the catalog and borrowing records.</p>
    </div>

    <div id="booksSection" class="p-5 shadow-sm rounded-4 bg-white" <?php echo $activeSection === 'booksSection' ? '' : 'style="display:none;"'; ?>>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Modify Books</h2>
            <div class="d-flex gap-2">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addBookModal"><i class="bi bi-plus-circle me-2"></i>Add Book</button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBorrowModal"><i class="bi bi-journal-plus me-2"></i>Add Borrowed Book</button>
            </div>
        </div>

        <form action="librariandashboard.php?section=booksSection" method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-9"><input type="search" name="bookSearchInput" placeholder="Search books" class="form-control" value="<?php echo htmlspecialchars($bookSearchInput); ?>"></div>
                <div class="col-md-3"><input type="submit" name="btnBookSearch" value="Search Books" class="btn btn-primary w-100"></div>
            </div>
        </form>

        <div class="table-responsive mb-5">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark"><tr><th>ID</th><th>Title</th><th>Author</th><th>ISBN</th><th>Genre</th><th>Publication Date</th><th>Availability</th><th>Category ID</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if($bookResult && $bookResult->num_rows > 0) { foreach($bookResult as $book) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($book['book_id']); ?></td>
                            <td><?php echo htmlspecialchars($book['title']); ?></td>
                            <td><?php echo htmlspecialchars($book['author']); ?></td>
                            <td><?php echo htmlspecialchars($book['ISBN']); ?></td>
                            <td><?php echo htmlspecialchars($book['genre']); ?></td>
                            <td><?php echo htmlspecialchars($book['publication_date']); ?></td>
                            <td><?php echo htmlspecialchars($book['availability_status']); ?></td>
                            <td><?php echo htmlspecialchars($book['category_id']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editBookModal" onclick='editBook(<?php echo json_encode($book); ?>)'><i class="bi bi-pencil"></i> Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteBook(<?php echo intval($book['book_id']); ?>)"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                    <?php } } else { ?>
                        <tr><td colspan="9" class="text-center">No books found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <form action="librariandashboard.php?section=booksSection" method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-9"><input type="search" name="borrowSearchInput" placeholder="Search borrowed books" class="form-control" value="<?php echo htmlspecialchars($borrowSearchInput); ?>"></div>
                <div class="col-md-3"><input type="submit" name="btnBorrowSearch" value="Search Borrowed Books" class="btn btn-primary w-100"></div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark"><tr><th>Record ID</th><th>Book</th><th>Member</th><th>Borrow Date</th><th>Return Date</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if($borrowResult && $borrowResult->num_rows > 0) { foreach($borrowResult as $borrow) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($borrow['record_id']); ?></td>
                            <td><?php echo htmlspecialchars($borrow['book_title']); ?></td>
                            <td><?php echo htmlspecialchars($borrow['member_name']); ?></td>
                            <td><?php echo htmlspecialchars($borrow['borrow_date']); ?></td>
                            <td><?php echo htmlspecialchars($borrow['return_date']); ?></td>
                            <td><?php echo htmlspecialchars($borrow['status']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning me-2" data-bs-toggle="modal" data-bs-target="#editBorrowModal" onclick='editBorrow(<?php echo json_encode($borrow); ?>)'><i class="bi bi-pencil"></i> Edit</button>
                                <button class="btn btn-sm btn-danger" onclick="deleteBorrow(<?php echo intval($borrow['record_id']); ?>)"><i class="bi bi-trash"></i> Delete</button>
                            </td>
                        </tr>
                    <?php } } else { ?>
                        <tr><td colspan="7" class="text-center">No borrowed book records found.</td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Add Book Modal -->
<div class="modal fade" id="addBookModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Add New Book</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="librariandashboard.php?section=booksSection"><div class="modal-body">
        <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Author</label><input type="text" name="author" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">ISBN</label><input type="text" name="isbn" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Genre</label><input type="text" name="genre" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Publication Date</label><input type="date" name="publication_date" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Availability</label><select name="availability_status" class="form-control"><option value="Available">Available</option><option value="Borrowed">Borrowed</option><option value="Reserved">Reserved</option></select></div>
        <div class="mb-3"><label class="form-label">Category ID</label><input type="number" name="category_id" class="form-control" min="1"></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="saveBook" class="btn btn-primary">Save Book</button></div></form></div></div></div>

<!-- Edit Book Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Edit Book</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="librariandashboard.php?section=booksSection"><div class="modal-body">
        <input type="hidden" name="book_id" id="editBookId">
        <div class="mb-3"><label class="form-label">Title</label><input type="text" name="title" id="editBookTitle" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Author</label><input type="text" name="author" id="editBookAuthor" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">ISBN</label><input type="text" name="isbn" id="editBookISBN" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Genre</label><input type="text" name="genre" id="editBookGenre" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Publication Date</label><input type="date" name="publication_date" id="editBookPublicationDate" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Availability</label><select name="availability_status" id="editBookAvailability" class="form-control"><option value="Available">Available</option><option value="Borrowed">Borrowed</option><option value="Reserved">Reserved</option></select></div>
        <div class="mb-3"><label class="form-label">Category ID</label><input type="number" name="category_id" id="editBookCategory" class="form-control" min="1"></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="saveBook" class="btn btn-primary">Update Book</button></div></form></div></div></div>

<!-- Add Borrow Modal -->
<div class="modal fade" id="addBorrowModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Add Borrowed Book Record</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="librariandashboard.php?section=booksSection"><div class="modal-body">
        <div class="mb-3"><label class="form-label">Book</label><select name="book_id" class="form-control" required><option value="">Select Book</option><?php if($bookOptionsRes && $bookOptionsRes->num_rows > 0) { foreach($bookOptionsRes as $bookOption) { ?><option value="<?php echo intval($bookOption['book_id']); ?>"><?php echo htmlspecialchars($bookOption['title']); ?></option><?php } } ?></select></div>
        <div class="mb-3"><label class="form-label">Member</label><select name="member_id" class="form-control" required><option value="">Select Member</option><?php if($memberOptionsRes && $memberOptionsRes->num_rows > 0) { foreach($memberOptionsRes as $memberOption) { ?><option value="<?php echo intval($memberOption['member_id']); ?>"><?php echo htmlspecialchars($memberOption['member_name']); ?></option><?php } } ?></select></div>
        <div class="mb-3"><label class="form-label">Borrow Date</label><input type="date" name="borrow_date" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Return Date</label><input type="date" name="return_date" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Status</label><select name="status" class="form-control" required><option value="borrowed">Borrowed</option><option value="returned">Returned</option><option value="reserved">Reserved</option></select></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="saveBorrow" class="btn btn-primary">Save Record</button></div></form></div></div></div>

<!-- Edit Borrow Modal -->
<div class="modal fade" id="editBorrowModal" tabindex="-1">
    <div class="modal-dialog modal-lg"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Edit Borrowed Book Record</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <form method="POST" action="librariandashboard.php?section=booksSection"><div class="modal-body">
        <input type="hidden" name="record_id" id="editBorrowRecordId">
        <div class="mb-3"><label class="form-label">Book</label><select name="book_id" id="editBorrowBookId" class="form-control" required><option value="">Select Book</option><?php if($bookOptionsRes && $bookOptionsRes->num_rows > 0) { foreach($bookOptionsRes as $bookOption) { ?><option value="<?php echo intval($bookOption['book_id']); ?>"><?php echo htmlspecialchars($bookOption['title']); ?></option><?php } } ?></select></div>
        <div class="mb-3"><label class="form-label">Member</label><select name="member_id" id="editBorrowMemberId" class="form-control" required><option value="">Select Member</option><?php if($memberOptionsRes && $memberOptionsRes->num_rows > 0) { foreach($memberOptionsRes as $memberOption) { ?><option value="<?php echo intval($memberOption['member_id']); ?>"><?php echo htmlspecialchars($memberOption['member_name']); ?></option><?php } } ?></select></div>
        <div class="mb-3"><label class="form-label">Borrow Date</label><input type="date" name="borrow_date" id="editBorrowDate" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Return Date</label><input type="date" name="return_date" id="editBorrowReturnDate" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Status</label><select name="status" id="editBorrowStatus" class="form-control" required><option value="borrowed">Borrowed</option><option value="returned">Returned</option><option value="reserved">Reserved</option></select></div>
    </div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" name="saveBorrow" class="btn btn-primary">Update Record</button></div></form></div></div></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const sidebar = document.getElementById('sidebar');
const sidebarToggle = document.getElementById('sidebarToggle');
const body = document.body;
const desktopQuery = window.matchMedia('(min-width: 992px)');

function openSidebar() { sidebar.classList.remove('sidebar-collapsed'); sidebar.classList.add('sidebar-open'); body.classList.add('sidebar-open'); }
function closeSidebar() { sidebar.classList.add('sidebar-collapsed'); sidebar.classList.remove('sidebar-open'); body.classList.remove('sidebar-open'); }
function syncSidebarWithViewport() { if (desktopQuery.matches) { openSidebar(); } else { closeSidebar(); } }
sidebarToggle.addEventListener('click', () => { const isCollapsed = sidebar.classList.contains('sidebar-collapsed'); if (isCollapsed) { openSidebar(); } else { closeSidebar(); } });
desktopQuery.addEventListener('change', syncSidebarWithViewport);
syncSidebarWithViewport();

function editBook(book) {
    document.getElementById('editBookId').value = book.book_id;
    document.getElementById('editBookTitle').value = book.title || '';
    document.getElementById('editBookAuthor').value = book.author || '';
    document.getElementById('editBookISBN').value = book.ISBN || '';
    document.getElementById('editBookGenre').value = book.genre || '';
    document.getElementById('editBookPublicationDate').value = book.publication_date || '';
    document.getElementById('editBookAvailability').value = book.availability_status || 'Available';
    document.getElementById('editBookCategory').value = book.category_id || '';
}

function editBorrow(borrow) {
    document.getElementById('editBorrowRecordId').value = borrow.record_id;
    document.getElementById('editBorrowBookId').value = borrow.book_id;
    document.getElementById('editBorrowMemberId').value = borrow.member_id;
    document.getElementById('editBorrowDate').value = borrow.borrow_date || '';
    document.getElementById('editBorrowReturnDate').value = borrow.return_date || '';
    document.getElementById('editBorrowStatus').value = borrow.status || 'borrowed';
}

function deleteBook(bookId) {
    Swal.fire({ title: 'Delete book?', text: 'This will also remove related borrowing records.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, delete it!' }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'librariandashboard.php?section=booksSection';
            const input = document.createElement('input'); input.type = 'hidden'; input.name = 'book_id'; input.value = bookId;
            const submitInput = document.createElement('input'); submitInput.type = 'hidden'; submitInput.name = 'deleteBook'; submitInput.value = '1';
            form.appendChild(input); form.appendChild(submitInput); document.body.appendChild(form); form.submit();
        }
    });
}

function deleteBorrow(recordId) {
    Swal.fire({ title: 'Delete borrowed book record?', text: 'This action cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6', confirmButtonText: 'Yes, delete it!' }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'librariandashboard.php?section=booksSection';
            const input = document.createElement('input'); input.type = 'hidden'; input.name = 'record_id'; input.value = recordId;
            const submitInput = document.createElement('input'); submitInput.type = 'hidden'; submitInput.name = 'deleteBorrow'; submitInput.value = '1';
            form.appendChild(input); form.appendChild(submitInput); document.body.appendChild(form); form.submit();
        }
    });
}
</script>
</body>
</html>
