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
    <a class="navbar-brand d-flex align-items-center gap-2" href="#">
      <img src="../assets/headerLogo.png" alt="Logo" width="200" height="44" class="d-inline-block align-text-top">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbar" aria-controls="topNavbar" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="topNavbar">
      <form class="d-flex" role="search">
        <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success" type="submit">Search</button>
      </form>
    </div>
  </div>
</nav>
<!-- TOP NAVBAR -->
<button class="btn btn-light shadow-sm sidebar-toggle" id="sidebarToggle" type="button" aria-label="Toggle navigation">
  <i class="bi bi-list"></i>
</button>
<!-- NAVBAR -->
<nav class="navbar shadow-sm sidebar-navbar sidebar-collapsed" id="sidebar">
  <div class="container-fluid d-flex flex-column align-items-start">
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">About</a>
        </li>
      </ul>
  </div>
</nav>
<!-- NAVBAR -->

<!-- MAIN SECTION -->
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