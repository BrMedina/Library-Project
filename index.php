<?php
require "dbconnection.php";
session_start();

// Handle logout
if(isset($_GET['logout']) && $_GET['logout'] == '1') {
    session_destroy();
    header('Location: login.php');
    exit();
}

$query = 'SELECT * FROM book_table ORDER BY RAND() LIMIT 4';

$res = $conn->query($query);

// Query to get all unique genres
$genreQuery = 'SELECT DISTINCT genre FROM book_table ORDER BY genre';
$genreRes = $conn->query($genreQuery);

// Query to get all books
$allBooksQuery = 'SELECT * FROM book_table ORDER BY title';
$allBooksRes = $conn->query($allBooksQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <title>Library</title>
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
          <a class="nav-link" aria-current="page" href="#"><i class="bi bi-house me-2"></i>Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-book me-2"></i></i>My Library</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#categories"><i class="bi bi-card-list me-2"></i>Category</a>
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
          <a class="nav-link" href="index.php?logout=1"><i class="bi bi-box-arrow-left me-2"></i>Logout</a>
        </li>
      </ul>
  </div>
</nav>
<!-- NAVBAR -->

<!-- MAIN SECTION (Display list of Recommended books, Books by category, etc.) -->
<main id="main" class="bg-secondary-subtle p-5 d-flex flex-column gap-5">
  <div class="p-5 shadow-sm rounded-4 bg-white">
    <h2>Recommended</h2>
    <div class="row g-4 mt-2">
      <?php
      if($res->num_rows > 0){
        foreach($res as $field){
          $isbn = $field['ISBN'];
          $coverUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg";
          ?>
          <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100 shadow-sm book-card" onclick="showBookModal(this)" data-book-id="<?php echo $field['book_id']; ?>" data-title="<?php echo htmlspecialchars($field['title']); ?>" data-author="<?php echo htmlspecialchars($field['author']); ?>" data-genre="<?php echo htmlspecialchars($field['genre']); ?>" data-date="<?php echo htmlspecialchars($field['publication_date']); ?>" data-cover="<?php echo $coverUrl; ?>" data-availability="<?php echo isset($field['availability']) ? htmlspecialchars($field['availability']) : 'Available'; ?>">
              <div class="book-cover-container">
                <img src="<?php echo $coverUrl; ?>" alt="<?php echo htmlspecialchars($field['title']); ?>" class="card-img-top h-100 object-fit-cover" style="object-fit: cover; width: 100%;">
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($field['title']); ?></h5>
                <p class="card-text text-muted small mb-2">
                  <?php echo htmlspecialchars($field['author']); ?>
                </p>
                <p class="card-text small mb-2">
                  <span class="badge bg-secondary"><?php echo htmlspecialchars($field['genre']); ?></span>
                </p>
                <p class="card-text text-muted small mb-2">
                  <?php echo htmlspecialchars($field['publication_date']); ?>
                </p>
              </div>
            </div>
          </div>
          <?php
        }
      } else {
        echo "<p class='text-danger'>No record found</p>";
      }
      ?>
    </div>  
  </div>
  <div class="p-5 shadow-sm rounded-4 bg-white" id="categories">
    <h2>Categories</h2>
    <div class="mb-4 d-flex flex-nowrap overflow-x-auto pb-2" style="gap: 0.5rem;">
      <button class="btn btn-outline-primary mb-2 genre-filter rounded-pill" data-genre="all" style="white-space: nowrap;">All</button>
      <?php
      if($genreRes->num_rows > 0){
        foreach($genreRes as $genre){
          $genreName = htmlspecialchars($genre['genre']);
          echo "<button class='btn btn-outline-primary mb-2 genre-filter' data-genre='{$genreName}' style='white-space: nowrap;'>{$genreName}</button>";
        }
      }
      ?>
    </div>
    <div class="row g-4" id="genreBooks">
      <?php
      if($allBooksRes->num_rows > 0){
        foreach($allBooksRes as $field){
          $isbn = $field['ISBN'];
          $coverUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg";
          $genre = htmlspecialchars($field['genre']);
          ?>
          <div class="col-md-6 col-lg-4 col-xl-3 book-item" data-genre="<?php echo $genre; ?>">
            <div class="card h-100 shadow-sm book-card" onclick="showBookModal(this)" 
            data-book-id="<?php echo $field['book_id']; ?>" 
            data-title="<?php echo htmlspecialchars($field['title']); ?>" 
            data-author="<?php echo htmlspecialchars($field['author']); ?>" 
            data-genre="<?php echo $genre; ?>" 
            data-date="<?php echo htmlspecialchars($field['publication_date']); ?>" 
            data-cover="<?php echo $coverUrl; ?>" data-availability="<?php echo $field['availability_status']; ?>">
              <div class="book-cover-container">
                <img src="<?php echo $coverUrl; ?>" alt="<?php echo htmlspecialchars($field['title']); ?>" class="card-img-top h-100 object-fit-cover" style="object-fit: cover; width: 100%;">
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($field['title']); ?></h5>
                <p class="card-text text-muted small mb-2">
                  <?php echo htmlspecialchars($field['author']); ?>
                </p>
                <p class="card-text small mb-2">
                  <span class="badge bg-secondary"><?php echo $genre; ?></span>
                </p>
                <p class="card-text text-muted small mb-2">
                  <?php echo htmlspecialchars($field['publication_date']); ?>
                </p>
              </div>
            </div>
          </div>
          <?php
        }
      } else {
        echo "<p class='text-danger'>No record found</p>";
      }
      ?>
    <!-- Book Modal -->
    <div class="modal fade" id="bookModal" tabindex="-1" aria-labelledby="bookModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h1 class="modal-title fs-5" id="bookModalLabel">Details</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="bookingForm" method="POST" action="borrowpage.php">
            <input type="hidden" id="bookIdInput" name="book_id">
            <div class="modal-body">
              <div class="row">
                <div class="col-md-4">
                  <img id="modalBookCover" src="" alt="Book Cover" class="img-fluid rounded">
                </div>
                <div class="col-md-8">
                  <h3 id="modalBookTitle"></h3>
                  <p class="text-muted mb-2"><strong>Author:</strong> <span id="modalBookAuthor"></span></p>
                  <p class="text-muted mb-2"><strong>Genre:</strong> <span id="modalBookGenre"></span></p>
                  <p class="text-muted mb-2"><strong>Publication Date:</strong> <span id="modalBookDate"></span></p>
                  <p class="text-muted mb-2"><strong>Availability:</strong> <span id="modalBookAvailability"></span></p>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary borrow" name="borrow">Borrow Book</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    </div>  
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script>
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

  // Genre filtering functionality
  const genreFilters = document.querySelectorAll('.genre-filter');
  const bookItems = document.querySelectorAll('.book-item');

  genreFilters.forEach(filter => {
    filter.addEventListener('click', () => {
      const selectedGenre = filter.getAttribute('data-genre');
      
      // Update active button
      genreFilters.forEach(btn => btn.classList.remove('btn-primary'));
      genreFilters.forEach(btn => btn.classList.add('btn-outline-primary'));
      filter.classList.remove('btn-outline-primary');
      filter.classList.add('btn-primary');
      
      // Filter books
      bookItems.forEach(book => {
        if (selectedGenre === 'all' || book.getAttribute('data-genre') === selectedGenre) {
          book.style.display = '';
        } else {
          book.style.display = 'none';
        }
      });
    });
  });

  // Show book modal
  function showBookModal(cardElement) {
    const bookId = cardElement.getAttribute('data-book-id');
    const title = cardElement.getAttribute('data-title');
    const author = cardElement.getAttribute('data-author');
    const genre = cardElement.getAttribute('data-genre');
    const date = cardElement.getAttribute('data-date');
    const cover = cardElement.getAttribute('data-cover');
    const availability = cardElement.getAttribute('data-availability');
    const borrowbtn = document.querySelector('.modal-footer .borrow');

    // Update modal content
    document.getElementById('modalBookTitle').textContent = title;
    document.getElementById('modalBookAuthor').textContent = author;
    document.getElementById('modalBookGenre').textContent = genre;
    document.getElementById('modalBookDate').textContent = date;
    document.getElementById('modalBookCover').src = cover;
    document.getElementById('modalBookAvailability').textContent = availability;
    
    // Update form action with book_id query parameter
    document.getElementById('bookingForm').action = 'borrowpage.php';
    document.getElementById('bookIdInput').value = bookId;
    
    // Disable button if book is borrowed or reserved
    if(availability === 'Borrowed' || availability === 'Reserved'){
      borrowbtn.disabled = true;
    } else {
      borrowbtn.disabled = false;
    }

    // Show modal
    const bookModal = new bootstrap.Modal(document.getElementById('bookModal'));
    bookModal.show();
  }
</script>
</body>
</html>

