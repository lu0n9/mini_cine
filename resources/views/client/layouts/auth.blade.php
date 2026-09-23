<!DOCTYPE html>
<html lang="vi">
<head>
    @include('client.partials.head')
    <title>@yield('title', 'Đăng nhập') - Hắc Ảnh</title>
</head>
<body class="auth-body">

  <!-- BACKGROUND LƯỚI POSTER PHIM -->
<div class="poster-bg" aria-hidden="true">
  <div class="poster-grid">
    
    <!-- Cột 1: Trượt lên -->
    <div class="poster-col poster-col--up">
      <div class="poster-track">
        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&q=80" alt="Poster 1">
        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=300&q=80" alt="Poster 2">
        <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=300&q=80" alt="Poster 3">
        <img src="https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=300&q=80" alt="Poster 4">
        <img src="https://images.unsplash.com/photo-1518173946687-a4c8a383392e?w=300&q=80" alt="Poster 5">
      </div>
    </div>

    <!-- Cột 2: Trượt xuống -->
    <div class="poster-col poster-col--down">
      <div class="poster-track">
        <img src="https://images.unsplash.com/photo-1518173946687-a4c8a383392e?w=300&q=80" alt="Poster 5">
        <img src="https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=300&q=80" alt="Poster 4">
        <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=300&q=80" alt="Poster 3">
        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=300&q=80" alt="Poster 2">
        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&q=80" alt="Poster 1">
      </div>
    </div>

    <!-- Cột 3: Trượt lên -->
    <div class="poster-col poster-col--up">
      <div class="poster-track">
        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&q=80" alt="Poster 1">
        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=300&q=80" alt="Poster 2">
        <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=300&q=80" alt="Poster 3">
        <img src="https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=300&q=80" alt="Poster 4">
        <img src="https://images.unsplash.com/photo-1518173946687-a4c8a383392e?w=300&q=80" alt="Poster 5">
      </div>
    </div>

    <!-- Cột 4: Trượt xuống -->
    <div class="poster-col poster-col--down">
      <div class="poster-track">
        <img src="https://images.unsplash.com/photo-1518173946687-a4c8a383392e?w=300&q=80" alt="Poster 5">
        <img src="https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=300&q=80" alt="Poster 4">
        <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=300&q=80" alt="Poster 3">
        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=300&q=80" alt="Poster 2">
        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&q=80" alt="Poster 1">
      </div>
    </div>

    <!-- Cột 5: Trượt lên -->
    <div class="poster-col poster-col--up">
      <div class="poster-track">
        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&q=80" alt="Poster 1">
        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=300&q=80" alt="Poster 2">
        <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=300&q=80" alt="Poster 3">
        <img src="https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=300&q=80" alt="Poster 4">
        <img src="https://images.unsplash.com/photo-1518173946687-a4c8a383392e?w=300&q=80" alt="Poster 5">
      </div>
    </div>

    <!-- Cột 6: Trượt xuống -->
    <div class="poster-col poster-col--down">
      <div class="poster-track">
        <img src="https://images.unsplash.com/photo-1518173946687-a4c8a383392e?w=300&q=80" alt="Poster 5">
        <img src="https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=300&q=80" alt="Poster 4">
        <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=300&q=80" alt="Poster 3">
        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=300&q=80" alt="Poster 2">
        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&q=80" alt="Poster 1">
      </div>
    </div>

    <!-- Cột 7: Trượt lên -->
    <div class="poster-col poster-col--up">
      <div class="poster-track">
        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&q=80" alt="Poster 1">
        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=300&q=80" alt="Poster 2">
        <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=300&q=80" alt="Poster 3">
        <img src="https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=300&q=80" alt="Poster 4">
        <img src="https://images.unsplash.com/photo-1518173946687-a4c8a383392e?w=300&q=80" alt="Poster 5">
      </div>
    </div>

    <!-- Cột 8: Trượt xuống -->
    <div class="poster-col poster-col--down">
      <div class="poster-track">
        <img src="https://images.unsplash.com/photo-1518173946687-a4c8a383392e?w=300&q=80" alt="Poster 5">
        <img src="https://images.unsplash.com/photo-1574267432553-4b4628081c31?w=300&q=80" alt="Poster 4">
        <img src="https://images.unsplash.com/photo-1517604931442-7e0c8ed2963c?w=300&q=80" alt="Poster 3">
        <img src="https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?w=300&q=80" alt="Poster 2">
        <img src="https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=300&q=80" alt="Poster 1">
      </div>
    </div>

  </div>

  <!-- Lớp phủ Gradient làm tối và tạo tiêu điểm ở trung tâm -->
  <div class="poster-overlay"></div>
</div>

  <!-- NỘI DUNG FORM ĐĂNG NHẬP CĂN GIỮA -->
  <main class="auth-container">
      @yield('content')
  </main>

</body>
</html>