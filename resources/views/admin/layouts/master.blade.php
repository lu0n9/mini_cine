<!DOCTYPE html>
<html lang="vi">
<head>
@include('admin.partials.head')
</head>
<body>
<div class="app">

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
</body>
</html>
