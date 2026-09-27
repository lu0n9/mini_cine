<!DOCTYPE html>
<html lang="vi">
<head>
@include('admin.partials.head')
</head>
<body>
<div id="adminProgressBar" class="admin-progress-bar"></div>
<div class="app">
  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <!-- ============ SIDEBAR ============ -->
  @include('admin.partials.sidebar')

  <!-- ============ MAIN ============ -->
  <div class="main">
    @include('admin.partials.topbar')

    <main class="content">
        @yield('content')
    </main>

    @include('admin.partials.footer')
  </div>
</div>
@stack('scripts')
</body>
</html>
