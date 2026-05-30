<?php
require "dbconnection.php";
session_start();

$swalScript = '';

// Get book_id from POST and store in session
if(isset($_POST['borrow'])){
    $_SESSION['book_id'] = intval($_POST['book_id']);
}

// Get book_id from session
$book_id = isset($_SESSION['book_id']) ? $_SESSION['book_id'] : 0;

// Fetch book details from database
$book_title = '';
$book_author = '';
$book_genre = '';
$book_cover = '';

if($book_id > 0) {
  $query = "SELECT book_id, title, author, genre, ISBN FROM book_table WHERE book_id = " . $book_id;
  $result = $conn->query($query);
  
  if($result && $result->num_rows > 0) {
    $book = $result->fetch_assoc();
    $book_title = htmlspecialchars($book['title']);
    $book_author = htmlspecialchars($book['author']);
    $book_genre = htmlspecialchars($book['genre']);
    $isbn = $book['ISBN'];
    $book_cover = "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg";
  }
}

if(isset($_POST['confirm'])){
    $return_date = isset($_POST['return_date']) ? $_POST['return_date'] : '';
$swalScript ="
  <script>
    Swal.fire({
        title: 'Book successfully borrowed.',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    }).then(()=>{
        window.location.href = 'index.php';
    });
  </script>
  ";
} ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    
    <title>Borrow Book - Library</title>
</head>
<body class="has-sidebar has-topbar" style="background-color: #f7f7f7;">

<!-- TOP NAVBAR -->
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
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
  </div>
</nav>

<!-- NAVBAR -->
<nav class="navbar shadow-sm sidebar-navbar sidebar-collapsed" id="sidebar">
  <div class="container-fluid d-flex flex-column align-items-start">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      
    <h1>E-Library</h1>
        <li class="nav-item">
          <a class="nav-link" aria-current="page" href="index.php"><i class="bi bi-house me-2"></i>Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-book me-2"></i></i>My Library</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#categories"><i class="bi bi-card-list me-2"></i>Category</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-bookmark-heart me-2"></i>Favourite</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-info-circle me-2"></i>About</a>
        </li>
        <hr>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-gear me-2"></i>Settings</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-box-arrow-left me-2"></i>Logout</a>
        </li>
      </ul>
  </div>
</nav>

<!-- MAIN SECTION -->
<main id="main" class="bg-secondary-subtle p-5 d-flex flex-column gap-5">
  <div class="p-5 shadow-sm rounded-4 bg-white">
    <h2>Confirmation Page</h2>
    <hr>
    <div class="row mt-4">
      <div class="col-md-8 offset-md-2">
        <!-- Book Details Section -->
        <div class="card mb-4 shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-3">Book Details</h5>
            <div class="row mb-3">
              <div class="col-md-4">
                <div id="bookCoverContainer" class="d-flex justify-content-center">
                  <?php if($book_cover): ?>
                    <img src="<?php echo $book_cover; ?>" alt="<?php echo $book_title; ?>" class="img-fluid rounded" style="max-width: 200px;">
                  <?php endif; ?>
                </div>
              </div>
              <div class="col-md-8">
                <h4 id="borrowBookTitle" class="mb-3"><?php echo $book_title; ?></h4>
                <p class="mb-2"><strong>Author:</strong> <span id="borrowBookAuthor"><?php echo $book_author; ?></span></p>
                <p class="mb-2"><strong>Genre:</strong> <span id="borrowBookGenre"><?php echo $book_genre; ?></span></p>
              </div>
            </div>
          </div>
        </div>

        <!-- Return Date Section -->
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-3">Return Date</h5>
            <form id="confirmBookingForm" method="POST" action="borrowpage.php">
              <div class="mb-3">
                <label for="returnDate" class="form-label">Select Return Date:</label>
                <input type="date" class="form-control" id="returnDate" name="return_date" required>
              </div>
              <div class="d-flex gap-2 justify-content-end">
                <a href="index.php" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success" name="confirm">
                  <i class="bi bi-check-circle me-2"></i>Confirm
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<?php echo $swalScript; ?>
<script>
  // Set minimum return date to today
  const returnDateInput = document.getElementById('returnDate');
  const today = new Date().toISOString().split('T')[0];
  returnDateInput.min = today;
  returnDateInput.value = today;

  // Sidebar toggle functionality
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebarToggle');
  const body = document.body;
  const desktopQuery = window.matchMedia('(min-width: 992px)');

  function openSidebar() {
    sidebar.classList.remove('sidebar-collapsed');
    sidebar.classList.add('sidebar-open');
    body.classList.add('sidebar-open');
  }

  function closeSidebar() {
    sidebar.classList.add('sidebar-collapsed');
    sidebar.classList.remove('sidebar-open');
    body.classList.remove('sidebar-open');
  }

  function syncSidebarWithViewport() {
    if (desktopQuery.matches) {
      openSidebar();
    } else {
      closeSidebar();
    }
  }

  sidebarToggle.addEventListener('click', () => {
    const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
    if (isCollapsed) {
      openSidebar();
    } else {
      closeSidebar();
    }
  });

  desktopQuery.addEventListener('change', syncSidebarWithViewport);
  syncSidebarWithViewport();
</script>
</body>
</html>

