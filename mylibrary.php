<?php
require "dbconnection.php";
session_start();

// Redirect to login if user is not authenticated or has no member ID
if (!isset($_SESSION['memberid'])) {
    header('Location: login.php');
    exit();
}

$memberId = intval($_SESSION['memberid']);

// Fetch books currently borrowed by this user
$query = "SELECT br.record_id, br.borrow_date, br.return_date, br.status, b.book_id, b.title, b.author, b.ISBN, b.genre, b.publication_date
          FROM borrowing_record_table br
          INNER JOIN book_table b ON br.book_id = b.book_id
          WHERE br.member_id = $memberId AND br.status = 'borrowed'
          ORDER BY br.borrow_date DESC";

$res = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css">
    <title>My Library</title>
</head>
<body class="has-sidebar has-topbar" style="background-color: #f7f7f7;">
<!-- TOP NAVBAR -->
<nav class="navbar navbar-expand-lg top-navbar shadow-sm">
  <div class="container-fluid">
    <div class="d-flex align-items-center gap-2">
      <button class="btn btn-light shadow-sm sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle navigation">
        <i class="bi bi-list"></i>
      </button>
      <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
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
          <a class="nav-link" href="index.php"><i class="bi bi-house me-2"></i>Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="mylibrary.php"><i class="bi bi-book me-2"></i>My Library</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="index.php#categories"><i class="bi bi-card-list me-2"></i>Category</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#"><i class="bi bi-info-circle me-2"></i>About</a>
        </li>
        <hr>
        <li class="nav-item">
          <a class="nav-link" href="index.php?logout=1"><i class="bi bi-box-arrow-left me-2"></i>Logout</a>
        </li>
      </ul>
  </div>
</nav>

<!-- MAIN SECTION -->
<main id="main" class="bg-secondary-subtle p-5 d-flex flex-column gap-5">
  <div class="p-5 shadow-sm rounded-4 bg-white">
    <h2>My Library</h2>
    <p class="text-muted">Below are the books you currently have borrowed.</p>
    <div class="row g-4 mt-2">
      <?php
      if($res && $res->num_rows > 0){
        foreach($res as $field){
          $isbn = $field['ISBN'];
          $coverUrl = "https://covers.openlibrary.org/b/isbn/{$isbn}-M.jpg";
          ?>
          <div class="col-md-6 col-lg-4 col-xl-3">
            <div class="card h-100 shadow-sm book-card">
              <div class="book-cover-container">
                <img src="<?php echo $coverUrl; ?>" alt="<?php echo htmlspecialchars($field['title']); ?>" class="card-img-top h-100 object-fit-cover" style="object-fit: cover; width: 100%;">
              </div>
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?php echo htmlspecialchars($field['title']); ?></h5>
                <p class="card-text text-muted small mb-2">
                  <strong>Author:</strong> <?php echo htmlspecialchars($field['author']); ?>
                </p>
                <p class="card-text small mb-2">
                  <span class="badge bg-secondary"><?php echo htmlspecialchars($field['genre']); ?></span>
                  <span class="badge bg-success">Borrowed</span>
                </p>
                <hr class="my-2">
                <div class="mt-auto">
                  <p class="card-text small mb-1">
                    <i class="bi bi-calendar2-check text-primary me-1"></i>
                    <strong>Borrowed:</strong> <?php echo date('M d, Y', strtotime($field['borrow_date'])); ?>
                  </p>
                  <p class="card-text small mb-0">
                    <i class="bi bi-calendar-event text-danger me-1"></i>
                    <strong>Return By:</strong> <?php echo date('M d, Y', strtotime($field['return_date'])); ?>
                  </p>
                </div>
              </div>
            </div>
          </div>
          <?php
        }
      } else {
        ?>
        <div class="col-12 text-center py-5">
          <div class="mb-3">
            <i class="bi bi-book-half text-secondary" style="font-size: 4rem;"></i>
          </div>
          <h4>No Borrowed Books</h4>
          <p class="text-muted">You do not currently have any borrowed books in your library.</p>
          <a href="index.php" class="btn btn-primary mt-2">Browse Books</a>
        </div>
        <?php
      }
      ?>
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
</script>
</body>
</html>
