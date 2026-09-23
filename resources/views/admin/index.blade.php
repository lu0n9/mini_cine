<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>CineAdmin — Quản lý web xem phim</title>
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap&subset=vietnamese,latin" rel="stylesheet" />
<style>
  :root {
    --bg: #ffffff;
    --fg: #0a0a0a;
    --muted: #6b6b6b;
    --line: #e6e6e6;
    --panel: #fafafa;
    --panel-2: #f2f2f2;
    --ink: #0a0a0a;
    --ink-inv: #ffffff;
    --radius: 14px;
    --shadow: 0 1px 2px rgba(0,0,0,.06), 0 8px 24px rgba(0,0,0,.04);
  }

  * { box-sizing: border-box; margin: 0; padding: 0; }

  html, body { background: var(--bg); }

  body {
    font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
    color: var(--fg);
    line-height: 1.5;
    -webkit-font-smoothing: antialiased;
  }

  a { color: inherit; text-decoration: none; }
  button { font-family: inherit; cursor: pointer; }
  img { display: block; max-width: 100%; }

  /* ------------- Layout ------------- */
  .app {
    display: grid;
    grid-template-columns: 268px 1fr;
    min-height: 100vh;
  }

  /* ------------- Sidebar ------------- */
  .sidebar {
    background: var(--ink);
    color: var(--ink-inv);
    padding: 22px 14px;
    display: flex;
    flex-direction: column;
    gap: 2px;
    position: sticky;
    top: 0;
    height: 100vh;
    overflow-y: auto;
  }
  .sidebar::-webkit-scrollbar { width: 8px; }
  .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 8px; }

  .brand {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 6px 8px 20px;
    border-bottom: 1px solid rgba(255,255,255,.12);
    margin-bottom: 8px;
  }
  .brand .logo {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: var(--ink-inv);
    color: var(--ink);
    display: grid; place-items: center;
    font-weight: 800; font-size: 20px;
    flex: none;
  }
  .brand h1 { font-size: 17px; letter-spacing: .5px; }
  .brand span { font-size: 11px; color: #9a9a9a; letter-spacing: 2px; text-transform: uppercase; }

  /* Collapsible groups */
  .group { border: none; }
  .group > summary {
    list-style: none;
    display: flex; align-items: center; gap: 10px;
    padding: 10px 10px;
    font-size: 11px; letter-spacing: 1.5px; text-transform: uppercase;
    color: #8a8a8a; font-weight: 600;
    cursor: pointer; border-radius: 8px;
    user-select: none;
  }
  .group > summary::-webkit-details-marker { display: none; }
  .group > summary:hover { color: #fff; background: rgba(255,255,255,.05); }
  .group > summary .chev { margin-left: auto; transition: transform .2s; width: 14px; height: 14px; }
  .group[open] > summary .chev { transform: rotate(90deg); }
  .group > summary .gic { width: 16px; height: 16px; opacity: .8; }

  .nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 12px 9px 34px;
    border-radius: 9px;
    color: #cfcfcf;
    font-size: 13.5px; font-weight: 500;
    transition: background .15s, color .15s;
  }
  .nav-item.solo { padding-left: 12px; }
  .nav-item .ic { width: 17px; height: 17px; flex: none; opacity: .9; }
  .nav-item:hover { background: rgba(255,255,255,.08); color: #fff; }
  .nav-item .badge {
    margin-left: auto;
    background: rgba(255,255,255,.15);
    font-size: 10px; padding: 1px 7px; border-radius: 999px;
  }
  .nav-item .badge.warn { background: #fff; color: #000; font-weight: 700; }

  /* :target based navigation */
  .page { display: none; }
  .page:target { display: block; }
  #dashboard { display: block; }
  body:has(.page:target) #dashboard { display: none; }
  #dashboard:target { display: block; }

  .sidebar a:target { background: var(--ink-inv); color: var(--ink); }
  .sidebar a:target .badge { background: rgba(0,0,0,.12); color: #000; }

  .sidebar-foot {
    margin-top: 12px;
    display: flex; align-items: center; gap: 12px;
    padding: 12px;
    border-top: 1px solid rgba(255,255,255,.12);
  }
  .sidebar-foot img { width: 36px; height: 36px; border-radius: 999px; object-fit: cover; filter: grayscale(1); }
  .sidebar-foot .who { font-size: 13px; font-weight: 600; }
  .sidebar-foot .who small { display: block; color: #9a9a9a; font-weight: 400; font-size: 11px; }

  /* ------------- Main ------------- */
  .main { display: flex; flex-direction: column; min-width: 0; }

  .topbar {
    display: flex; align-items: center; gap: 16px;
    padding: 16px 28px;
    border-bottom: 1px solid var(--line);
    position: sticky; top: 0; background: var(--bg); z-index: 5;
  }
  .topbar h2 { font-size: 18px; font-weight: 700; }
  .search {
    margin-left: auto;
    display: flex; align-items: center; gap: 8px;
    background: var(--panel-2);
    border: 1px solid var(--line);
    border-radius: 10px;
    padding: 9px 12px;
    width: 320px; max-width: 40vw;
  }
  .search input { border: none; background: transparent; outline: none; width: 100%; font-size: 14px; color: var(--fg); }
  .icon-btn {
    width: 40px; height: 40px; border-radius: 10px;
    border: 1px solid var(--line); background: var(--bg);
    display: grid; place-items: center;
  }
  .icon-btn:hover { background: var(--panel-2); }

  .content { padding: 28px; }

  .page-head { margin-bottom: 22px; display: flex; align-items: flex-end; justify-content: space-between; gap: 16px; flex-wrap: wrap; }
  .page-head h3 { font-size: 24px; font-weight: 800; letter-spacing: -.3px; }
  .page-head p { color: var(--muted); font-size: 14px; margin-top: 4px; }

  /* ------------- Stat cards ------------- */
  .stats {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px;
    margin-bottom: 26px;
  }
  .stats.six { grid-template-columns: repeat(3, 1fr); }
  .stat {
    border: 1px solid var(--line); border-radius: var(--radius);
    padding: 20px; background: var(--bg); box-shadow: var(--shadow);
  }
  .stat .top { display: flex; justify-content: space-between; align-items: center; }
  .stat .label { font-size: 12px; color: var(--muted); text-transform: uppercase; letter-spacing: 1px; }
  .stat .chip { width: 38px; height: 38px; border-radius: 10px; background: var(--ink); color: var(--ink-inv); display: grid; place-items: center; }
  .stat .num { font-size: 30px; font-weight: 800; margin-top: 14px; letter-spacing: -1px; }
  .stat .delta { font-size: 12px; margin-top: 4px; color: var(--muted); }
  .stat .delta b { color: var(--ink); }

  /* ------------- Grid panels ------------- */
  .grid-2 { display: grid; grid-template-columns: 1.6fr 1fr; gap: 18px; margin-bottom: 18px; }
  .grid-3 { display: grid; grid-template-columns: repeat(3,1fr); gap: 18px; margin-bottom: 18px; }
  .panel {
    border: 1px solid var(--line); border-radius: var(--radius);
    background: var(--bg); box-shadow: var(--shadow);
  }
  .panel + .panel { margin-top: 18px; }
  .panel-head {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px; border-bottom: 1px solid var(--line);
  }
  .panel-head h4 { font-size: 15px; font-weight: 700; }
  .panel-head .link { font-size: 13px; color: var(--muted); }
  .panel-head .link:hover { color: var(--ink); }
  .panel-body { padding: 20px; }

  /* ------------- Bar chart (pure CSS) ------------- */
  .chart { display: flex; align-items: flex-end; gap: 14px; height: 220px; padding-top: 10px; }
  .bar-wrap { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 8px; height: 100%; justify-content: flex-end; }
  .bar {
    width: 100%; max-width: 34px; border-radius: 8px 8px 0 0;
    background: linear-gradient(180deg, #1a1a1a, #4a4a4a);
    transition: transform .2s;
  }
  .bar.alt { background: repeating-linear-gradient(45deg, #d9d9d9, #d9d9d9 4px, #eaeaea 4px, #eaeaea 8px); }
  .bar-wrap:hover .bar { transform: scaleY(1.03); transform-origin: bottom; }
  .bar-x { font-size: 11px; color: var(--muted); }

  .legend { display: flex; gap: 18px; font-size: 12px; color: var(--muted); }
  .legend i { display: inline-block; width: 12px; height: 12px; border-radius: 3px; margin-right: 6px; vertical-align: -1px; }
  .legend .d { background: #2a2a2a; }
  .legend .l { background: repeating-linear-gradient(45deg, #d9d9d9, #d9d9d9 3px, #eaeaea 3px, #eaeaea 6px); }

  /* ------------- Activity list ------------- */
  .activity { display: flex; flex-direction: column; }
  .act { display: flex; gap: 12px; padding: 12px 0; border-bottom: 1px dashed var(--line); }
  .act:last-child { border-bottom: none; }
  .act .dot { width: 34px; height: 34px; border-radius: 999px; background: var(--panel-2); display: grid; place-items: center; flex: none; font-size: 14px; }
  .act .txt { font-size: 13px; }
  .act .txt b { font-weight: 700; }
  .act .txt small { display: block; color: var(--muted); font-size: 12px; }

  /* ------------- Table ------------- */
  .table-tools { display: flex; gap: 10px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; }
  .table-tools .spacer { margin-left: auto; }
  .filter {
    padding: 9px 14px; border: 1px solid var(--line); border-radius: 999px;
    font-size: 13px; background: var(--bg); color: var(--muted);
  }
  .filter.on { background: var(--ink); color: var(--ink-inv); border-color: var(--ink); }
  .mini-search {
    display: flex; align-items: center; gap: 8px;
    background: var(--panel-2); border: 1px solid var(--line); border-radius: 999px;
    padding: 8px 14px; font-size: 13px; color: var(--muted);
  }
  .mini-search input { border: none; background: transparent; outline: none; font-size: 13px; color: var(--fg); width: 180px; }

  .panel table { width: 100%; border-collapse: collapse; }
  thead th {
    text-align: left; font-size: 11px; letter-spacing: 1px; text-transform: uppercase;
    color: var(--muted); padding: 12px 16px; border-bottom: 1px solid var(--line);
  }
  tbody td { padding: 14px 16px; border-bottom: 1px solid var(--line); font-size: 14px; vertical-align: middle; }
  tbody tr:last-child td { border-bottom: none; }
  tbody tr:hover { background: var(--panel); }

  .movie-cell { display: flex; align-items: center; gap: 12px; }
  .movie-cell img { width: 42px; height: 58px; object-fit: cover; border-radius: 6px; filter: grayscale(1) contrast(1.05); }
  .movie-cell.sm img { width: 34px; height: 34px; border-radius: 8px; }
  .movie-cell .mt { font-weight: 600; }
  .movie-cell .mt small { display: block; color: var(--muted); font-weight: 400; font-size: 12px; }

  .tag { display: inline-block; font-size: 11px; padding: 3px 10px; border-radius: 999px; border: 1px solid var(--line); background: var(--panel-2); }
  .tag.solid { background: var(--ink); color: var(--ink-inv); border-color: var(--ink); }

  .status { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; font-weight: 600; }
  .status::before { content: ""; width: 8px; height: 8px; border-radius: 999px; background: var(--ink); }
  .status.off::before { background: #c9c9c9; }
  .status.off { color: var(--muted); }
  .status.warn::before { background: #9a9a9a; }

  .rating { font-weight: 700; }
  .row-actions { display: flex; gap: 6px; }
  .mini {
    width: 32px; height: 32px; border: 1px solid var(--line); border-radius: 8px;
    background: var(--bg); display: grid; place-items: center;
  }
  .mini:hover { background: var(--ink); color: var(--ink-inv); }

  .btn {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 10px 16px; border-radius: 10px; font-size: 14px; font-weight: 600;
    border: 1px solid var(--ink); background: var(--ink); color: var(--ink-inv);
  }
  .btn:hover { opacity: .88; }
  .btn.ghost { background: var(--bg); color: var(--ink); }
  .btn.ghost:hover { background: var(--panel-2); opacity: 1; }
  .btn.sm { padding: 8px 12px; font-size: 13px; }

  /* ------------- Movie poster grid ------------- */
  .poster-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 18px; }
  .poster {
    border: 1px solid var(--line); border-radius: var(--radius); overflow: hidden;
    background: var(--bg); box-shadow: var(--shadow);
  }
  .poster .pic { position: relative; aspect-ratio: 2/3; overflow: hidden; }
  .poster .pic img { width: 100%; height: 100%; object-fit: cover; filter: grayscale(1) contrast(1.05); transition: transform .3s; }
  .poster:hover .pic img { transform: scale(1.05); }
  .poster .pic .rk { position: absolute; top: 10px; left: 10px; background: var(--ink); color: var(--ink-inv); font-size: 11px; font-weight: 700; padding: 3px 9px; border-radius: 999px; }
  .poster .meta { padding: 14px; }
  .poster .meta h5 { font-size: 14px; font-weight: 700; }
  .poster .meta .sub { font-size: 12px; color: var(--muted); margin-top: 2px; display: flex; justify-content: space-between; }

  /* ------------- Form ------------- */
  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
  .form-grid.three { grid-template-columns: repeat(3,1fr); }
  .field { display: flex; flex-direction: column; gap: 7px; }
  .field.full { grid-column: 1 / -1; }
  .field label { font-size: 13px; font-weight: 600; }
  .field input, .field select, .field textarea {
    padding: 11px 13px; border: 1px solid var(--line); border-radius: 10px;
    font-size: 14px; font-family: inherit; background: var(--bg); color: var(--fg); outline: none;
  }
  .field input:focus, .field select:focus, .field textarea:focus { border-color: var(--ink); }
  .field textarea { resize: vertical; min-height: 110px; }
  .hint { font-size: 12px; color: var(--muted); }
  .form-actions { grid-column: 1 / -1; display: flex; gap: 10px; justify-content: flex-end; margin-top: 6px; }

  .upload {
    grid-column: 1 / -1;
    border: 2px dashed var(--line); border-radius: var(--radius);
    padding: 30px; text-align: center; color: var(--muted);
  }
  .upload b { color: var(--ink); }

  /* ------------- Settings rows / toggles ------------- */
  .setting-row {
    display: flex; align-items: center; justify-content: space-between; gap: 16px;
    padding: 16px 0; border-bottom: 1px solid var(--line);
  }
  .setting-row:last-child { border-bottom: none; }
  .setting-row .st-txt strong { font-size: 14px; }
  .setting-row .st-txt small { display: block; color: var(--muted); font-size: 12.5px; margin-top: 2px; }

  .toggle { position: relative; width: 46px; height: 26px; flex: none; }
  .toggle input { position: absolute; opacity: 0; width: 100%; height: 100%; margin: 0; cursor: pointer; }
  .toggle .track { position: absolute; inset: 0; background: #d4d4d4; border-radius: 999px; transition: background .2s; }
  .toggle .track::after { content: ""; position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; border-radius: 999px; background: #fff; transition: transform .2s; box-shadow: 0 1px 3px rgba(0,0,0,.3); }
  .toggle input:checked + .track { background: var(--ink); }
  .toggle input:checked + .track::after { transform: translateX(20px); }

  /* ------------- Two-column settings ------------- */
  .split { display: grid; grid-template-columns: 240px 1fr; gap: 18px; align-items: start; }
  .subnav { display: flex; flex-direction: column; gap: 2px; }
  .subnav a { padding: 10px 14px; border-radius: 10px; font-size: 14px; color: var(--muted); }
  .subnav a:hover { background: var(--panel-2); color: var(--ink); }
  .subnav a.on { background: var(--ink); color: var(--ink-inv); }

  /* progress bar */
  .prog { height: 8px; border-radius: 999px; background: var(--panel-2); overflow: hidden; }
  .prog i { display: block; height: 100%; background: var(--ink); border-radius: 999px; }

  .kv { display: grid; grid-template-columns: 140px 1fr; gap: 10px 18px; font-size: 14px; }
  .kv dt { color: var(--muted); }
  .kv dd { font-weight: 600; }

  /* ------------- Footer ------------- */
  .foot { padding: 22px 28px; color: var(--muted); font-size: 13px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; }

  /* ------------- Responsive ------------- */
  @media (max-width: 1100px) {
    .stats { grid-template-columns: repeat(2, 1fr); }
    .stats.six { grid-template-columns: repeat(2,1fr); }
    .grid-2, .grid-3 { grid-template-columns: 1fr; }
    .poster-grid { grid-template-columns: repeat(3, 1fr); }
  }
  @media (max-width: 760px) {
    .app { grid-template-columns: 1fr; }
    .sidebar { position: static; height: auto; }
    .poster-grid { grid-template-columns: repeat(2, 1fr); }
    .form-grid, .form-grid.three { grid-template-columns: 1fr; }
    .split { grid-template-columns: 1fr; }
    .search { display: none; }
  }
</style>
</head>
<body>
<div class="app">

  <!-- ============ SIDEBAR ============ -->
  <aside class="sidebar">
    <div class="brand">
      <div class="logo">C</div>
      <div>
        <h1>CineAdmin</h1>
        <span>Streaming</span>
      </div>
    </div>

    <a href="#dashboard" class="nav-item solo">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="9"/><rect x="14" y="3" width="7" height="5"/><rect x="14" y="12" width="7" height="9"/><rect x="3" y="16" width="7" height="5"/></svg>
      Bảng điều khiển
    </a>

    <details class="group" open>
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M2 8h20M7 4v4M17 4v4"/></svg>
        Phim
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#movies" class="nav-item">Tất cả phim <span class="badge">248</span></a>
      <a href="#add" class="nav-item">Thêm phim</a>
      <a href="#featured" class="nav-item">Phim nổi bật</a>
      <a href="#popular" class="nav-item">Phim phổ biến</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2"/><path d="M7 2v20M17 2v20M2 12h20M2 7h5M2 17h5M17 7h5M17 17h5"/></svg>
        Tập phim
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#seasons" class="nav-item">Seasons</a>
      <a href="#episodes" class="nav-item">Episodes</a>
      <a href="#servers" class="nav-item">Video Servers</a>
      <a href="#subtitles" class="nav-item">Subtitles</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16v16H4z"/><path d="M4 9h16M9 4v16"/></svg>
        Nội dung
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#genres" class="nav-item">Thể loại</a>
      <a href="#countries" class="nav-item">Quốc gia</a>
      <a href="#actors" class="nav-item">Diễn viên</a>
      <a href="#directors" class="nav-item">Đạo diễn</a>
      <a href="#tags" class="nav-item">Tags</a>
      <a href="#collections" class="nav-item">Collections</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3-6 7-6s7 2 7 6"/></svg>
        Người dùng
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#users" class="nav-item">Users <span class="badge">12.4k</span></a>
      <a href="#roles" class="nav-item">Roles</a>
      <a href="#permissions" class="nav-item">Permissions</a>
      <a href="#banned" class="nav-item">Banned Users</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        Tương tác
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#comments" class="nav-item">Comments <span class="badge warn">9</span></a>
      <a href="#ratings" class="nav-item">Ratings</a>
      <a href="#reviews" class="nav-item">Reviews</a>
      <a href="#reports" class="nav-item">Reports <span class="badge warn">4</span></a>
      <a href="#requests" class="nav-item">Movie Requests</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="m7 14 3-4 3 3 4-6"/></svg>
        Thống kê
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#views" class="nav-item">Movie Views</a>
      <a href="#history" class="nav-item">Watch History</a>
      <a href="#popular-stats" class="nav-item">Popular Movies</a>
      <a href="#user-stats" class="nav-item">User Statistics</a>
      <a href="#traffic" class="nav-item">Traffic</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>
        Giao diện
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#homepage" class="nav-item">Homepage</a>
      <a href="#banners" class="nav-item">Banners</a>
      <a href="#menus" class="nav-item">Menus</a>
      <a href="#pages" class="nav-item">Pages</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
        Thông báo
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#notifications" class="nav-item">Notifications</a>
      <a href="#email" class="nav-item">Email</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4-4"/></svg>
        SEO
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#seo" class="nav-item">SEO Settings</a>
      <a href="#sitemap" class="nav-item">Sitemap</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2 4 5v6c0 5 3.5 8 8 11 4.5-3 8-6 8-11V5z"/></svg>
        Premium
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#plans" class="nav-item">Plans</a>
      <a href="#subscriptions" class="nav-item">Subscriptions</a>
      <a href="#transactions" class="nav-item">Transactions</a>
      <a href="#coupons" class="nav-item">Coupons</a>
    </details>

    <details class="group">
      <summary>
        <svg class="gic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5v14c0 1.7 4 3 9 3s9-1.3 9-3V5M3 12c0 1.7 4 3 9 3s9-1.3 9-3"/></svg>
        Hệ thống
        <svg class="chev" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 6 6 6-6 6"/></svg>
      </summary>
      <a href="#storage" class="nav-item">Storage</a>
      <a href="#cache" class="nav-item">Cache</a>
      <a href="#backup" class="nav-item">Backup</a>
      <a href="#logs" class="nav-item">Activity Logs</a>
      <a href="#cron" class="nav-item">Cron Jobs</a>
      <a href="#api" class="nav-item">API</a>
    </details>

    <a href="#settings" class="nav-item solo">
      <svg class="ic" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.6 1.6 0 0 0 .3 1.8l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.6 1.6 0 0 0-2.7 1.1V21a2 2 0 1 1-4 0v-.1A1.6 1.6 0 0 0 7 19.4a1.6 1.6 0 0 0-1.8.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.6 1.6 0 0 0-1.1-2.7H1a2 2 0 1 1 0-4h.1A1.6 1.6 0 0 0 2.6 7"/></svg>
      Cài đặt
    </a>

    <div class="sidebar-foot">
      <img src="/posters/silent-echo.png" alt="Ảnh đại diện quản trị viên" />
      <div class="who">Minh Trần<small>Super Admin</small></div>
    </div>
  </aside>

  <!-- ============ MAIN ============ -->
  <div class="main">
    <header class="topbar">
      <h2>CineAdmin</h2>
      <div class="search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--muted)" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4-4"/></svg>
        <input type="text" placeholder="Tìm phim, người dùng, thể loại..." aria-label="Tìm kiếm" />
      </div>
      <button class="icon-btn" aria-label="Thông báo">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
      </button>
      <button class="btn" onclick="location.hash='#add'">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg>
        Thêm phim
      </button>
    </header>

    <main class="content">

      <!-- ===== DASHBOARD ===== -->
      <section id="dashboard" class="page">
        <div class="page-head"><div>
          <h3>Bảng điều khiển</h3>
          <p>Tổng quan hoạt động nền tảng xem phim của bạn hôm nay.</p>
        </div></div>

        <div class="stats">
          <div class="stat"><div class="top"><span class="label">Tổng phim</span><span class="chip"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M7 4v16M17 4v16"/></svg></span></div><div class="num">248</div><div class="delta"><b>+12</b> phim trong tháng</div></div>
          <div class="stat"><div class="top"><span class="label">Tổng tập phim</span><span class="chip"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg></span></div><div class="num">5.312</div><div class="delta"><b>+184</b> tập tuần này</div></div>
          <div class="stat"><div class="top"><span class="label">Người dùng</span><span class="chip"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="8" r="4"/><path d="M2 21c0-4 3-6 7-6s7 2 7 6"/></svg></span></div><div class="num">12.4K</div><div class="delta"><b>+312</b> người mới</div></div>
          <div class="stat"><div class="top"><span class="label">Lượt xem hôm nay</span><span class="chip"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7"/><circle cx="12" cy="12" r="3"/></svg></span></div><div class="num">86.7K</div><div class="delta"><b>+21%</b> giờ cao điểm 20:00</div></div>
        </div>

        <div class="grid-2">
          <div class="panel">
            <div class="panel-head"><h4>Lượt xem theo tháng</h4><span class="legend"><span><i class="d"></i>Premium</span><span><i class="l"></i>Miễn phí</span></span></div>
            <div class="panel-body">
              <div class="chart">
                <div class="bar-wrap"><div class="bar" style="height:45%"></div><span class="bar-x">T1</span></div>
                <div class="bar-wrap"><div class="bar" style="height:62%"></div><span class="bar-x">T2</span></div>
                <div class="bar-wrap"><div class="bar" style="height:38%"></div><span class="bar-x">T3</span></div>
                <div class="bar-wrap"><div class="bar" style="height:74%"></div><span class="bar-x">T4</span></div>
                <div class="bar-wrap"><div class="bar alt" style="height:55%"></div><span class="bar-x">T5</span></div>
                <div class="bar-wrap"><div class="bar" style="height:88%"></div><span class="bar-x">T6</span></div>
                <div class="bar-wrap"><div class="bar" style="height:67%"></div><span class="bar-x">T7</span></div>
                <div class="bar-wrap"><div class="bar alt" style="height:71%"></div><span class="bar-x">T8</span></div>
                <div class="bar-wrap"><div class="bar" style="height:82%"></div><span class="bar-x">T9</span></div>
                <div class="bar-wrap"><div class="bar" style="height:60%"></div><span class="bar-x">T10</span></div>
                <div class="bar-wrap"><div class="bar" style="height:93%"></div><span class="bar-x">T11</span></div>
                <div class="bar-wrap"><div class="bar" style="height:100%"></div><span class="bar-x">T12</span></div>
              </div>
            </div>
          </div>
          <div class="panel">
            <div class="panel-head"><h4>Hoạt động gần đây</h4><a href="#logs" class="link">Xem tất cả</a></div>
            <div class="panel-body activity">
              <div class="act"><div class="dot">+</div><div class="txt"><b>Phim mới</b> "Last Horizon" đã được thêm<small>15 phút trước · Minh Trần</small></div></div>
              <div class="act"><div class="dot">★</div><div class="txt"><b>Đánh giá mới</b> 4.8★ cho "Silent Echo"<small>42 phút trước</small></div></div>
              <div class="act"><div class="dot">!</div><div class="txt"><b>Báo lỗi</b> Link chết tập 4 "Iron Verdict"<small>1 giờ trước</small></div></div>
              <div class="act"><div class="dot">✎</div><div class="txt"><b>Cập nhật</b> Thêm tập 12 "Crimson Vale"<small>2 giờ trước</small></div></div>
              <div class="act"><div class="dot">₫</div><div class="txt"><b>Thanh toán</b> Gói Premium năm — ₫990.000<small>3 giờ trước</small></div></div>
            </div>
          </div>
        </div>

        <div class="panel">
          <div class="panel-head"><h4>Phim được xem nhiều nhất</h4><a href="#popular-stats" class="link">Thống kê</a></div>
          <div class="panel-body">
            <div class="poster-grid">
              <div class="poster"><div class="pic"><span class="rk">#1</span><img src="/posters/neon-nights.png" alt="Neon Nights"></div><div class="meta"><h5>Neon Nights</h5><div class="sub"><span>Hành động</span><span>1.2M</span></div></div></div>
              <div class="poster"><div class="pic"><span class="rk">#2</span><img src="/posters/last-horizon.png" alt="Last Horizon"></div><div class="meta"><h5>Last Horizon</h5><div class="sub"><span>Khoa học</span><span>980K</span></div></div></div>
              <div class="poster"><div class="pic"><span class="rk">#3</span><img src="/posters/silent-echo.png" alt="Silent Echo"></div><div class="meta"><h5>Silent Echo</h5><div class="sub"><span>Chính kịch</span><span>870K</span></div></div></div>
              <div class="poster"><div class="pic"><span class="rk">#4</span><img src="/posters/iron-verdict.png" alt="Iron Verdict"></div><div class="meta"><h5>Iron Verdict</h5><div class="sub"><span>Tội phạm</span><span>760K</span></div></div></div>
            </div>
          </div>
        </div>
      </section>

      <!-- ===== MOVIES (Tất cả phim) ===== -->
      <section id="movies" class="page">
        <div class="page-head">
          <div><h3>Tất cả phim</h3><p>Quản lý toàn bộ kho phim: thêm, sửa, ẩn/hiện, sao chép và trạng thái.</p></div>
          <button class="btn" onclick="location.hash='#add'">+ Thêm phim</button>
        </div>
        <div class="table-tools">
          <span class="filter on">Tất cả</span>
          <span class="filter">Phim lẻ</span>
          <span class="filter">Phim bộ</span>
          <span class="filter">Anime</span>
          <span class="filter">TV Show</span>
          <span class="spacer"></span>
          <span class="mini-search"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m21 21-4-4"/></svg><input placeholder="Tìm phim..."></span>
        </div>
        <div class="panel"><div style="overflow-x:auto"><table>
          <thead><tr><th>Phim</th><th>Thể loại</th><th>Năm</th><th>IMDb</th><th>Lượt xem</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td><div class="movie-cell"><img src="/posters/neon-nights.png" alt=""><span class="mt">Neon Nights<small>neon-nights</small></span></div></td><td><span class="tag">Hành động</span></td><td>2024</td><td class="rating">8.4</td><td>1.2M</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini" title="Sửa">✎</button><button class="mini" title="Ẩn">◑</button><button class="mini" title="Xóa">✕</button></div></td></tr>
            <tr><td><div class="movie-cell"><img src="/posters/last-horizon.png" alt=""><span class="mt">Last Horizon<small>last-horizon</small></span></div></td><td><span class="tag">Khoa học</span></td><td>2024</td><td class="rating">7.9</td><td>980K</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
            <tr><td><div class="movie-cell"><img src="/posters/silent-echo.png" alt=""><span class="mt">Silent Echo<small>silent-echo</small></span></div></td><td><span class="tag">Chính kịch</span></td><td>2023</td><td class="rating">8.1</td><td>870K</td><td><span class="status off">Nháp</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
            <tr><td><div class="movie-cell"><img src="/posters/crimson-vale.png" alt=""><span class="mt">Crimson Vale<small>crimson-vale</small></span></div></td><td><span class="tag">Kinh dị</span></td><td>2023</td><td class="rating">7.2</td><td>640K</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
            <tr><td><div class="movie-cell"><img src="/posters/iron-verdict.png" alt=""><span class="mt">Iron Verdict<small>iron-verdict</small></span></div></td><td><span class="tag">Tội phạm</span></td><td>2022</td><td class="rating">8.6</td><td>760K</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
            <tr><td><div class="movie-cell"><img src="/posters/paper-moons.png" alt=""><span class="mt">Paper Moons<small>paper-moons</small></span></div></td><td><span class="tag">Lãng mạn</span></td><td>2024</td><td class="rating">7.5</td><td>410K</td><td><span class="status off">Ẩn</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div></div>
      </section>

      <!-- ===== ADD MOVIE ===== -->
      <section id="add" class="page">
        <div class="page-head"><div><h3>Thêm phim mới</h3><p>Điền đầy đủ thông tin, media và SEO cho phim.</p></div></div>
        <div class="panel"><div class="panel-head"><h4>Thông tin cơ bản</h4></div><div class="panel-body">
          <form class="form-grid" onsubmit="return false">
            <div class="field"><label>Tên phim</label><input placeholder="VD: Last Horizon"></div>
            <div class="field"><label>Tên gốc</label><input placeholder="Original title"></div>
            <div class="field"><label>Slug</label><input placeholder="last-horizon"></div>
            <div class="field"><label>Loại phim</label><select><option>Phim lẻ</option><option>Phim bộ</option><option>TV Show</option><option>Anime</option><option>Hoạt hình</option></select></div>
            <div class="field"><label>Năm phát hành</label><input type="number" placeholder="2024"></div>
            <div class="field"><label>Ngày phát hành</label><input type="date"></div>
            <div class="field"><label>Thời lượng (phút)</label><input type="number" placeholder="128"></div>
            <div class="field"><label>Chất lượng</label><select><option>4K</option><option>1080p</option><option>720p</option><option>480p</option></select></div>
            <div class="field"><label>Ngôn ngữ</label><input placeholder="Tiếng Anh"></div>
            <div class="field"><label>Quốc gia</label><input placeholder="Mỹ"></div>
            <div class="field"><label>IMDb ID / IMDb Rating</label><input placeholder="tt0000000 · 8.4"></div>
            <div class="field"><label>TMDB ID</label><input placeholder="123456"></div>
            <div class="field full"><label>Thể loại</label><input placeholder="Hành động, Phiêu lưu, Khoa học viễn tưởng"></div>
            <div class="field full"><label>Tags</label><input placeholder="siêu anh hùng, hậu tận thế"></div>
            <div class="field"><label>Đạo diễn</label><input placeholder="Nguyễn Văn A"></div>
            <div class="field"><label>Nhà sản xuất</label><input placeholder="Studio X"></div>
            <div class="field full"><label>Diễn viên</label><input placeholder="Diễn viên 1, Diễn viên 2, ..."></div>
            <div class="field full"><label>Mô tả ngắn</label><textarea placeholder="Tóm tắt nội dung..."></textarea></div>
            <div class="field full"><label>Mô tả đầy đủ</label><textarea placeholder="Nội dung chi tiết..."></textarea></div>
            <div class="field"><label>Trailer URL</label><input placeholder="https://youtube.com/..."></div>
            <div class="field"><label>Trạng thái</label><select><option>Nháp</option><option>Hiển thị</option><option>Ẩn</option></select></div>
          </form>
        </div></div>

        <div class="panel"><div class="panel-head"><h4>Media</h4></div><div class="panel-body">
          <div class="form-grid three">
            <div class="upload"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg><p style="margin-top:8px"><b>Poster</b> (2:3)</p></div>
            <div class="upload"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg><p style="margin-top:8px"><b>Thumbnail</b></p></div>
            <div class="upload"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M17 8l-5-5-5 5M12 3v12"/></svg><p style="margin-top:8px"><b>Banner / Backdrop</b></p></div>
          </div>
          <div style="display:flex;gap:24px;margin-top:18px;flex-wrap:wrap">
            <label style="display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600"><span class="toggle"><input type="checkbox"><span class="track"></span></span> Featured</label>
            <label style="display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600"><span class="toggle"><input type="checkbox" checked><span class="track"></span></span> Popular</label>
            <label style="display:flex;align-items:center;gap:10px;font-size:14px;font-weight:600"><span class="toggle"><input type="checkbox"><span class="track"></span></span> Recommended</label>
          </div>
        </div></div>

        <div class="panel"><div class="panel-head"><h4>SEO</h4></div><div class="panel-body">
          <form class="form-grid" onsubmit="return false">
            <div class="field"><label>SEO Title</label><input></div>
            <div class="field"><label>Canonical URL</label><input placeholder="https://..."></div>
            <div class="field full"><label>SEO Description</label><textarea style="min-height:70px"></textarea></div>
            <div class="field full"><label>SEO Keywords</label><input placeholder="từ khóa 1, từ khóa 2"></div>
            <div class="field"><label>OG Title</label><input></div>
            <div class="field"><label>OG Image URL</label><input></div>
            <div class="field full"><label>OG Description</label><textarea style="min-height:70px"></textarea></div>
            <div class="form-actions"><button class="btn ghost">Lưu nháp</button><button class="btn">Xuất bản phim</button></div>
          </form>
        </div></div>
      </section>

      <!-- ===== FEATURED ===== -->
      <section id="featured" class="page">
        <div class="page-head"><div><h3>Phim nổi bật</h3><p>Các phim được đánh dấu Featured hiển thị ở khu vực nổi bật trang chủ.</p></div><button class="btn ghost">Quản lý thứ tự</button></div>
        <div class="poster-grid">
          <div class="poster"><div class="pic"><span class="rk">Featured</span><img src="/posters/last-horizon.png" alt=""></div><div class="meta"><h5>Last Horizon</h5><div class="sub"><span>2024</span><span>★ 7.9</span></div></div></div>
          <div class="poster"><div class="pic"><span class="rk">Featured</span><img src="/posters/neon-nights.png" alt=""></div><div class="meta"><h5>Neon Nights</h5><div class="sub"><span>2024</span><span>★ 8.4</span></div></div></div>
          <div class="poster"><div class="pic"><span class="rk">Featured</span><img src="/posters/iron-verdict.png" alt=""></div><div class="meta"><h5>Iron Verdict</h5><div class="sub"><span>2022</span><span>★ 8.6</span></div></div></div>
          <div class="poster"><div class="pic"><span class="rk">Featured</span><img src="/posters/paper-moons.png" alt=""></div><div class="meta"><h5>Paper Moons</h5><div class="sub"><span>2024</span><span>★ 7.5</span></div></div></div>
        </div>
      </section>

      <!-- ===== POPULAR ===== -->
      <section id="popular" class="page">
        <div class="page-head"><div><h3>Phim phổ biến</h3><p>Danh sách phim được đánh dấu Popular, sắp theo lượt xem.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>#</th><th>Phim</th><th>Lượt xem tuần</th><th>Yêu thích</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>1</td><td><div class="movie-cell"><img src="/posters/neon-nights.png" alt=""><span class="mt">Neon Nights</span></div></td><td>320K</td><td>48K</td><td><span class="tag solid">Popular</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>2</td><td><div class="movie-cell"><img src="/posters/last-horizon.png" alt=""><span class="mt">Last Horizon</span></div></td><td>280K</td><td>41K</td><td><span class="tag solid">Popular</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>3</td><td><div class="movie-cell"><img src="/posters/iron-verdict.png" alt=""><span class="mt">Iron Verdict</span></div></td><td>210K</td><td>36K</td><td><span class="tag solid">Popular</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== SEASONS ===== -->
      <section id="seasons" class="page">
        <div class="page-head"><div><h3>Seasons</h3><p>Quản lý mùa phim cho các phim bộ và series.</p></div><button class="btn">+ Thêm season</button></div>
        <div class="panel"><table>
          <thead><tr><th>Phim</th><th>Season</th><th>Số tập</th><th>Thứ tự</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Crimson Vale</td><td>Season 1</td><td>12</td><td>1</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Crimson Vale</td><td>Season 2</td><td>10</td><td>2</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Iron Verdict</td><td>Season 1</td><td>8</td><td>1</td><td><span class="status off">Ẩn</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== EPISODES ===== -->
      <section id="episodes" class="page">
        <div class="page-head"><div><h3>Episodes</h3><p>Quản lý tập phim: thêm, sửa, sắp xếp, đánh dấu VIP/mới.</p></div><button class="btn">+ Thêm tập</button></div>
        <div class="table-tools"><span class="filter on">Tất cả</span><span class="filter">Mới</span><span class="filter">VIP</span><span class="filter">Ẩn</span></div>
        <div class="panel"><table>
          <thead><tr><th>Tập</th><th>Phim / Season</th><th>Thời lượng</th><th>Phát hành</th><th>Nhãn</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Tập 12 — Kết thúc</td><td>Crimson Vale · S1</td><td>44:20</td><td>12/09/2026</td><td><span class="tag solid">Mới</span></td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Tập 11</td><td>Crimson Vale · S1</td><td>42:05</td><td>05/09/2026</td><td><span class="tag">VIP</span></td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Tập 08</td><td>Iron Verdict · S1</td><td>39:50</td><td>01/09/2026</td><td>—</td><td><span class="status off">Nháp</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== SERVERS ===== -->
      <section id="servers" class="page">
        <div class="page-head"><div><h3>Video Servers</h3><p>Quản lý nguồn phát: embed, HLS (.m3u8), DASH (.mpd) và server mặc định.</p></div><button class="btn">+ Thêm server</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tên server</th><th>Loại</th><th>Chất lượng</th><th>Thứ tự</th><th>Mặc định</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Server VIP #1</td><td>HLS (.m3u8)</td><td>1080p</td><td>1</td><td><span class="tag solid">Mặc định</span></td><td><span class="status">Bật</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Server Backup</td><td>Embed</td><td>720p</td><td>2</td><td>—</td><td><span class="status">Bật</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Server DASH</td><td>DASH (.mpd)</td><td>4K</td><td>3</td><td>—</td><td><span class="status off">Tắt</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== SUBTITLES ===== -->
      <section id="subtitles" class="page">
        <div class="page-head"><div><h3>Subtitles</h3><p>Upload và quản lý phụ đề (.srt / .vtt) theo tập và ngôn ngữ.</p></div><button class="btn">+ Upload subtitle</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tập</th><th>Ngôn ngữ</th><th>Label</th><th>Định dạng</th><th>Mặc định</th><th></th></tr></thead>
          <tbody>
            <tr><td>Crimson Vale · Tập 12</td><td>Tiếng Việt</td><td>Vietsub</td><td>.vtt</td><td><span class="tag solid">Mặc định</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Crimson Vale · Tập 12</td><td>English</td><td>English</td><td>.srt</td><td>—</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== GENRES ===== -->
      <section id="genres" class="page">
        <div class="page-head"><div><h3>Thể loại</h3><p>Quản lý thể loại phim kèm slug, mô tả và SEO.</p></div><button class="btn">+ Thêm thể loại</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tên</th><th>Slug</th><th>Số phim</th><th>Thứ tự</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Hành động</td><td>hanh-dong</td><td>86</td><td>1</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Khoa học viễn tưởng</td><td>khoa-hoc</td><td>42</td><td>2</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Kinh dị</td><td>kinh-di</td><td>37</td><td>3</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Lãng mạn</td><td>lang-man</td><td>51</td><td>4</td><td><span class="status off">Ẩn</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== COUNTRIES ===== -->
      <section id="countries" class="page">
        <div class="page-head"><div><h3>Quốc gia</h3><p>Quản lý quốc gia sản xuất kèm mã quốc gia và cờ.</p></div><button class="btn">+ Thêm quốc gia</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tên</th><th>Slug</th><th>Mã</th><th>Số phim</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Mỹ</td><td>my</td><td>US</td><td>142</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Hàn Quốc</td><td>han-quoc</td><td>KR</td><td>63</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Nhật Bản</td><td>nhat-ban</td><td>JP</td><td>48</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== ACTORS ===== -->
      <section id="actors" class="page">
        <div class="page-head"><div><h3>Diễn viên</h3><p>Quản lý hồ sơ diễn viên: ảnh, tiểu sử, mạng xã hội.</p></div><button class="btn">+ Thêm diễn viên</button></div>
        <div class="panel"><table>
          <thead><tr><th>Diễn viên</th><th>Quốc tịch</th><th>Ngày sinh</th><th>Số phim</th><th></th></tr></thead>
          <tbody>
            <tr><td><div class="movie-cell sm"><img src="/posters/silent-echo.png" alt=""><span class="mt">Anna Reeves</span></div></td><td>Mỹ</td><td>1990</td><td>24</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><div class="movie-cell sm"><img src="/posters/iron-verdict.png" alt=""><span class="mt">Kenji Sato</span></div></td><td>Nhật Bản</td><td>1985</td><td>31</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== DIRECTORS ===== -->
      <section id="directors" class="page">
        <div class="page-head"><div><h3>Đạo diễn</h3><p>Quản lý hồ sơ đạo diễn: ảnh, tiểu sử, quốc tịch.</p></div><button class="btn">+ Thêm đạo diễn</button></div>
        <div class="panel"><table>
          <thead><tr><th>Đạo diễn</th><th>Quốc tịch</th><th>Ngày sinh</th><th>Số phim</th><th></th></tr></thead>
          <tbody>
            <tr><td><div class="movie-cell sm"><img src="/posters/neon-nights.png" alt=""><span class="mt">Marco Ferra</span></div></td><td>Ý</td><td>1972</td><td>12</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><div class="movie-cell sm"><img src="/posters/last-horizon.png" alt=""><span class="mt">Lena Cross</span></div></td><td>Anh</td><td>1980</td><td>9</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== TAGS ===== -->
      <section id="tags" class="page">
        <div class="page-head"><div><h3>Tags</h3><p>Quản lý thẻ gắn cho phim.</p></div><button class="btn">+ Thêm tag</button></div>
        <div class="panel"><div class="panel-body" style="display:flex;flex-wrap:wrap;gap:10px">
          <span class="tag">siêu anh hùng <b style="margin-left:6px">128</b></span>
          <span class="tag">hậu tận thế <b style="margin-left:6px">64</b></span>
          <span class="tag">trinh thám <b style="margin-left:6px">91</b></span>
          <span class="tag">chuyển thể <b style="margin-left:6px">47</b></span>
          <span class="tag">dựa trên có thật <b style="margin-left:6px">33</b></span>
          <span class="tag">tuổi teen <b style="margin-left:6px">58</b></span>
        </div></div>
      </section>

      <!-- ===== COLLECTIONS ===== -->
      <section id="collections" class="page">
        <div class="page-head"><div><h3>Collections</h3><p>Nhóm phim theo vũ trụ / series như Marvel, DC, Fast &amp; Furious.</p></div><button class="btn">+ Thêm collection</button></div>
        <div class="grid-3">
          <div class="panel"><div class="panel-body"><h4 style="margin-bottom:6px">Marvel Universe</h4><p class="hint">32 phim · 12.4M lượt xem</p><div style="margin-top:12px"><button class="btn ghost sm">Quản lý phim</button></div></div></div>
          <div class="panel"><div class="panel-body"><h4 style="margin-bottom:6px">DC Universe</h4><p class="hint">18 phim · 6.1M lượt xem</p><div style="margin-top:12px"><button class="btn ghost sm">Quản lý phim</button></div></div></div>
          <div class="panel"><div class="panel-body"><h4 style="margin-bottom:6px">Fast &amp; Furious</h4><p class="hint">10 phim · 4.8M lượt xem</p><div style="margin-top:12px"><button class="btn ghost sm">Quản lý phim</button></div></div></div>
        </div>
      </section>

      <!-- ===== USERS ===== -->
      <section id="users" class="page">
        <div class="page-head"><div><h3>Người dùng</h3><p>Quản lý tài khoản: khóa/mở, reset mật khẩu, lịch sử hoạt động.</p></div><button class="btn">+ Thêm user</button></div>
        <div class="table-tools"><span class="filter on">Tất cả</span><span class="filter">Active</span><span class="filter">Bị khóa</span><span class="filter">Chưa xác thực</span></div>
        <div class="panel"><table>
          <thead><tr><th>Người dùng</th><th>Email</th><th>Role</th><th>Đăng nhập cuối</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td><div class="movie-cell sm"><img src="/posters/silent-echo.png" alt=""><span class="mt">Lan Phạm<small>@lanpham</small></span></div></td><td>lan@example.com</td><td><span class="tag">User</span></td><td>Hôm nay</td><td><span class="status">Active</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">🔒</button></div></td></tr>
            <tr><td><div class="movie-cell sm"><img src="/posters/paper-moons.png" alt=""><span class="mt">Huy Đỗ<small>@huydo</small></span></div></td><td>huy@example.com</td><td><span class="tag solid">Editor</span></td><td>Hôm qua</td><td><span class="status">Active</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">🔒</button></div></td></tr>
            <tr><td><div class="movie-cell sm"><img src="/posters/crimson-vale.png" alt=""><span class="mt">Sơn Lê<small>@sonle</small></span></div></td><td>son@example.com</td><td><span class="tag">User</span></td><td>3 ngày trước</td><td><span class="status off">Bị khóa</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">🔓</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== ROLES ===== -->
      <section id="roles" class="page">
        <div class="page-head"><div><h3>Roles</h3><p>Nhóm quyền cho quản trị viên và người dùng.</p></div><button class="btn">+ Thêm role</button></div>
        <div class="panel"><table>
          <thead><tr><th>Role</th><th>Số quyền</th><th>Số người</th><th></th></tr></thead>
          <tbody>
            <tr><td><span class="tag solid">Super Admin</span></td><td>Toàn quyền</td><td>2</td><td><div class="row-actions"><button class="mini">✎</button></div></td></tr>
            <tr><td><span class="tag">Admin</span></td><td>24</td><td>5</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><span class="tag">Movie Manager</span></td><td>12</td><td>8</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><span class="tag">Content Manager</span></td><td>10</td><td>6</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><span class="tag">Editor</span></td><td>7</td><td>14</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><span class="tag">Moderator</span></td><td>5</td><td>9</td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== PERMISSIONS ===== -->
      <section id="permissions" class="page">
        <div class="page-head"><div><h3>Permissions</h3><p>Ma trận quyền theo module cho từng role.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>Permission</th><th>Admin</th><th>Movie Manager</th><th>Editor</th><th>Moderator</th></tr></thead>
          <tbody>
            <tr><td>movies.view</td><td>✔</td><td>✔</td><td>✔</td><td>✔</td></tr>
            <tr><td>movies.create</td><td>✔</td><td>✔</td><td>✔</td><td>—</td></tr>
            <tr><td>movies.update</td><td>✔</td><td>✔</td><td>✔</td><td>—</td></tr>
            <tr><td>movies.delete</td><td>✔</td><td>✔</td><td>—</td><td>—</td></tr>
            <tr><td>users.manage</td><td>✔</td><td>—</td><td>—</td><td>—</td></tr>
            <tr><td>comments.moderate</td><td>✔</td><td>—</td><td>—</td><td>✔</td></tr>
            <tr><td>settings.update</td><td>✔</td><td>—</td><td>—</td><td>—</td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== BANNED ===== -->
      <section id="banned" class="page">
        <div class="page-head"><div><h3>Banned Users</h3><p>Danh sách tài khoản bị khóa và lý do.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>Người dùng</th><th>Lý do</th><th>Khóa lúc</th><th>Bởi</th><th></th></tr></thead>
          <tbody>
            <tr><td>@sonle</td><td>Spam bình luận</td><td>05/09/2026</td><td>Minh Trần</td><td><div class="row-actions"><button class="mini">🔓</button></div></td></tr>
            <tr><td>@fakeuser</td><td>Vi phạm bản quyền</td><td>28/08/2026</td><td>Huy Đỗ</td><td><div class="row-actions"><button class="mini">🔓</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== COMMENTS ===== -->
      <section id="comments" class="page">
        <div class="page-head"><div><h3>Bình luận</h3><p>Kiểm duyệt bình luận: ẩn, xóa, đánh dấu spam, khóa user.</p></div></div>
        <div class="table-tools"><span class="filter on">Tất cả</span><span class="filter">Chờ duyệt</span><span class="filter">Spam</span><span class="filter">Đã ẩn</span></div>
        <div class="panel"><table>
          <thead><tr><th>Người dùng</th><th>Nội dung</th><th>Phim</th><th>Thời gian</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>@lanpham</td><td>Phim quá hay, cái kết bất ngờ thật!</td><td>Neon Nights</td><td>10 phút trước</td><td><span class="status warn">Chờ duyệt</span></td><td><div class="row-actions"><button class="mini">✔</button><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
            <tr><td>@spam01</td><td>Mua ngay tại link abc.xyz...</td><td>Last Horizon</td><td>25 phút trước</td><td><span class="status off">Spam</span></td><td><div class="row-actions"><button class="mini">🔒</button><button class="mini">✕</button></div></td></tr>
            <tr><td>@huydo</td><td>Nhạc phim đỉnh của chóp.</td><td>Silent Echo</td><td>1 giờ trước</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== RATINGS ===== -->
      <section id="ratings" class="page">
        <div class="page-head"><div><h3>Ratings</h3><p>Quản lý điểm đánh giá của người dùng cho từng phim.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>User</th><th>Phim</th><th>Điểm</th><th>Thời gian</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>@lanpham</td><td>Neon Nights</td><td class="rating">9.0</td><td>Hôm nay</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
            <tr><td>@huydo</td><td>Iron Verdict</td><td class="rating">8.5</td><td>Hôm qua</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== REVIEWS ===== -->
      <section id="reviews" class="page">
        <div class="page-head"><div><h3>Reviews</h3><p>Đánh giá dạng bài viết của người dùng, xử lý spam và khôi phục.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>User</th><th>Phim</th><th>Review</th><th>Điểm</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>@lanpham</td><td>Silent Echo</td><td>Diễn xuất tuyệt vời, hình ảnh đen trắng đầy cảm xúc...</td><td class="rating">4.8</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">◑</button><button class="mini">✕</button></div></td></tr>
            <tr><td>@guest22</td><td>Crimson Vale</td><td>Nội dung hơi chậm ở giữa phim.</td><td class="rating">3.5</td><td><span class="status warn">Chờ duyệt</span></td><td><div class="row-actions"><button class="mini">✔</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== REPORTS ===== -->
      <section id="reports" class="page">
        <div class="page-head"><div><h3>Reports / Báo lỗi</h3><p>Xử lý báo lỗi từ người xem: link chết, mất tiếng, sai phụ đề...</p></div></div>
        <div class="table-tools"><span class="filter on">Pending</span><span class="filter">Processing</span><span class="filter">Resolved</span><span class="filter">Rejected</span></div>
        <div class="panel"><table>
          <thead><tr><th>Loại lỗi</th><th>Phim / Tập</th><th>User</th><th>Thời gian</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Video không phát được</td><td>Iron Verdict · Tập 4</td><td>@lanpham</td><td>1 giờ trước</td><td><span class="status warn">Pending</span></td><td><div class="row-actions"><button class="mini">✔</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Sai phụ đề</td><td>Neon Nights</td><td>@huydo</td><td>3 giờ trước</td><td><span class="status warn">Processing</span></td><td><div class="row-actions"><button class="mini">✔</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Không có tiếng</td><td>Crimson Vale · Tập 9</td><td>@guest22</td><td>Hôm qua</td><td><span class="status">Resolved</span></td><td><div class="row-actions"><button class="mini">✎</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== MOVIE REQUESTS ===== -->
      <section id="requests" class="page">
        <div class="page-head"><div><h3>Movie Requests</h3><p>Yêu cầu phim từ người dùng — duyệt, xử lý, từ chối.</p></div></div>
        <div class="table-tools"><span class="filter on">Pending</span><span class="filter">Processing</span><span class="filter">Completed</span><span class="filter">Rejected</span></div>
        <div class="panel"><table>
          <thead><tr><th>Tên phim</th><th>IMDb / TMDB</th><th>User</th><th>Ghi chú</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Dune: Part Three</td><td>tt1234567</td><td>@lanpham</td><td>Rất mong chờ phần này</td><td><span class="status warn">Pending</span></td><td><div class="row-actions"><button class="mini">✔</button><button class="mini">✕</button></div></td></tr>
            <tr><td>The Batman 2</td><td>tt7654321</td><td>@huydo</td><td>—</td><td><span class="status warn">Processing</span></td><td><div class="row-actions"><button class="mini">✔</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== VIEWS ===== -->
      <section id="views" class="page">
        <div class="page-head"><div><h3>Movie Views</h3><p>Log lượt xem chi tiết: thiết bị, trình duyệt, quốc gia, thời gian xem.</p></div></div>
        <div class="stats"><div class="stat"><span class="label">Tổng lượt xem</span><div class="num">42.8M</div></div><div class="stat"><span class="label">Hôm nay</span><div class="num">86.7K</div></div><div class="stat"><span class="label">Tuần này</span><div class="num">612K</div></div><div class="stat"><span class="label">Tháng này</span><div class="num">2.4M</div></div></div>
        <div class="panel"><table>
          <thead><tr><th>Phim / Tập</th><th>User</th><th>Thiết bị</th><th>Trình duyệt</th><th>Quốc gia</th><th>Thời gian xem</th></tr></thead>
          <tbody>
            <tr><td>Neon Nights</td><td>@lanpham</td><td>Mobile</td><td>Chrome</td><td>VN</td><td>01:42:10</td></tr>
            <tr><td>Crimson Vale · Tập 12</td><td>Guest</td><td>Desktop</td><td>Safari</td><td>US</td><td>00:44:20</td></tr>
            <tr><td>Iron Verdict</td><td>@huydo</td><td>Smart TV</td><td>WebOS</td><td>KR</td><td>02:05:33</td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== WATCH HISTORY ===== -->
      <section id="history" class="page">
        <div class="page-head"><div><h3>Watch History</h3><p>Lịch sử xem và tiến độ của người dùng.</p></div></div>
        <div class="stats"><div class="stat"><span class="label">Phim xem dở</span><div class="num">8.2K</div></div><div class="stat"><span class="label">Đã xem xong</span><div class="num">31K</div></div><div class="stat"><span class="label">Tiến độ TB</span><div class="num">68%</div></div><div class="stat"><span class="label">Completion rate</span><div class="num">54%</div></div></div>
        <div class="panel"><table>
          <thead><tr><th>User</th><th>Phim / Tập</th><th>Tiến độ</th><th>Xem lần cuối</th><th>Hoàn thành</th></tr></thead>
          <tbody>
            <tr><td>@lanpham</td><td>Neon Nights</td><td style="width:160px"><div class="prog"><i style="width:82%"></i></div></td><td>Hôm nay</td><td>—</td></tr>
            <tr><td>@huydo</td><td>Iron Verdict</td><td style="width:160px"><div class="prog"><i style="width:100%"></i></div></td><td>Hôm qua</td><td>✔</td></tr>
            <tr><td>@sonle</td><td>Crimson Vale · Tập 3</td><td style="width:160px"><div class="prog"><i style="width:35%"></i></div></td><td>3 ngày trước</td><td>—</td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== POPULAR STATS ===== -->
      <section id="popular-stats" class="page">
        <div class="page-head"><div><h3>Popular Movies</h3><p>Thống kê phim theo lượt xem và yêu thích.</p></div></div>
        <div class="grid-2">
          <div class="panel"><div class="panel-head"><h4>Top lượt xem 30 ngày</h4></div><div class="panel-body">
            <div class="chart">
              <div class="bar-wrap"><div class="bar" style="height:100%"></div><span class="bar-x">Neon</span></div>
              <div class="bar-wrap"><div class="bar" style="height:82%"></div><span class="bar-x">Horizon</span></div>
              <div class="bar-wrap"><div class="bar" style="height:70%"></div><span class="bar-x">Iron</span></div>
              <div class="bar-wrap"><div class="bar" style="height:58%"></div><span class="bar-x">Echo</span></div>
              <div class="bar-wrap"><div class="bar" style="height:44%"></div><span class="bar-x">Vale</span></div>
              <div class="bar-wrap"><div class="bar" style="height:33%"></div><span class="bar-x">Moons</span></div>
            </div>
          </div></div>
          <div class="panel"><div class="panel-head"><h4>Top yêu thích</h4></div><div class="panel-body activity">
            <div class="act"><div class="dot">1</div><div class="txt"><b>Neon Nights</b><small>48.2K lượt yêu thích</small></div></div>
            <div class="act"><div class="dot">2</div><div class="txt"><b>Last Horizon</b><small>41.0K lượt yêu thích</small></div></div>
            <div class="act"><div class="dot">3</div><div class="txt"><b>Iron Verdict</b><small>36.4K lượt yêu thích</small></div></div>
            <div class="act"><div class="dot">4</div><div class="txt"><b>Silent Echo</b><small>29.9K lượt yêu thích</small></div></div>
          </div></div>
        </div>
      </section>

      <!-- ===== USER STATS ===== -->
      <section id="user-stats" class="page">
        <div class="page-head"><div><h3>User Statistics</h3><p>Tăng trưởng và phân bố người dùng.</p></div></div>
        <div class="stats"><div class="stat"><span class="label">Tổng user</span><div class="num">12.4K</div></div><div class="stat"><span class="label">User mới (tháng)</span><div class="num">312</div></div><div class="stat"><span class="label">Premium</span><div class="num">2.1K</div></div><div class="stat"><span class="label">Hoạt động/ngày</span><div class="num">4.8K</div></div></div>
        <div class="panel"><div class="panel-head"><h4>Người dùng mới theo tháng</h4></div><div class="panel-body"><div class="chart">
          <div class="bar-wrap"><div class="bar" style="height:40%"></div><span class="bar-x">T1</span></div>
          <div class="bar-wrap"><div class="bar" style="height:55%"></div><span class="bar-x">T2</span></div>
          <div class="bar-wrap"><div class="bar" style="height:48%"></div><span class="bar-x">T3</span></div>
          <div class="bar-wrap"><div class="bar" style="height:70%"></div><span class="bar-x">T4</span></div>
          <div class="bar-wrap"><div class="bar" style="height:66%"></div><span class="bar-x">T5</span></div>
          <div class="bar-wrap"><div class="bar" style="height:88%"></div><span class="bar-x">T6</span></div>
          <div class="bar-wrap"><div class="bar" style="height:100%"></div><span class="bar-x">T7</span></div>
        </div></div></div>
      </section>

      <!-- ===== TRAFFIC ===== -->
      <section id="traffic" class="page">
        <div class="page-head"><div><h3>Traffic</h3><p>Nguồn truy cập theo thiết bị, trình duyệt và quốc gia.</p></div></div>
        <div class="grid-3">
          <div class="panel"><div class="panel-head"><h4>Thiết bị</h4></div><div class="panel-body activity"><div class="act"><div class="txt"><b>Mobile</b> — 62%<small><div class="prog" style="margin-top:6px"><i style="width:62%"></i></div></small></div></div><div class="act"><div class="txt"><b>Desktop</b> — 28%<small><div class="prog" style="margin-top:6px"><i style="width:28%"></i></div></small></div></div><div class="act"><div class="txt"><b>Smart TV</b> — 10%<small><div class="prog" style="margin-top:6px"><i style="width:10%"></i></div></small></div></div></div></div>
          <div class="panel"><div class="panel-head"><h4>Trình duyệt</h4></div><div class="panel-body activity"><div class="act"><div class="txt"><b>Chrome</b> — 54%</div></div><div class="act"><div class="txt"><b>Safari</b> — 31%</div></div><div class="act"><div class="txt"><b>Firefox</b> — 8%</div></div><div class="act"><div class="txt"><b>Khác</b> — 7%</div></div></div></div>
          <div class="panel"><div class="panel-head"><h4>Quốc gia</h4></div><div class="panel-body activity"><div class="act"><div class="txt"><b>Việt Nam</b> — 71%</div></div><div class="act"><div class="txt"><b>Mỹ</b> — 12%</div></div><div class="act"><div class="txt"><b>Hàn Quốc</b> — 9%</div></div><div class="act"><div class="txt"><b>Khác</b> — 8%</div></div></div></div>
        </div>
      </section>

      <!-- ===== HOMEPAGE ===== -->
      <section id="homepage" class="page">
        <div class="page-head"><div><h3>Homepage</h3><p>Quản lý các section hiển thị trên trang chủ và thứ tự.</p></div><button class="btn">+ Thêm section</button></div>
        <div class="panel"><table>
          <thead><tr><th>Section</th><th>Kiểu</th><th>Giới hạn</th><th>Thứ tự</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Phim mới cập nhật</td><td>Tự động</td><td>12</td><td>1</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Phim nổi bật</td><td>Thủ công</td><td>8</td><td>2</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Anime mới</td><td>Theo thể loại</td><td>10</td><td>3</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Phim bộ Hàn Quốc</td><td>Theo quốc gia</td><td>10</td><td>4</td><td><span class="status off">Ẩn</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== BANNERS ===== -->
      <section id="banners" class="page">
        <div class="page-head"><div><h3>Banners / Slider</h3><p>Quản lý banner trang chủ với ưu tiên và thời gian hiển thị.</p></div><button class="btn">+ Thêm banner</button></div>
        <div class="panel"><table>
          <thead><tr><th>Banner</th><th>Phim</th><th>Thời gian</th><th>Ưu tiên</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td><div class="movie-cell sm"><img src="/posters/last-horizon.png" alt=""><span class="mt">Ra mắt Last Horizon</span></div></td><td>Last Horizon</td><td>01/09 – 30/09</td><td>1</td><td><span class="status">Hiển thị</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><div class="movie-cell sm"><img src="/posters/neon-nights.png" alt=""><span class="mt">Ưu đãi Premium</span></div></td><td>—</td><td>10/09 – 20/09</td><td>2</td><td><span class="status off">Hết hạn</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== MENUS ===== -->
      <section id="menus" class="page">
        <div class="page-head"><div><h3>Menus</h3><p>Quản lý Main menu, Footer menu và Mobile menu.</p></div><button class="btn">+ Thêm menu</button></div>
        <div class="grid-3">
          <div class="panel"><div class="panel-head"><h4>Main menu</h4></div><div class="panel-body activity"><div class="act"><div class="txt"><b>Trang chủ</b><small>/</small></div></div><div class="act"><div class="txt"><b>Phim lẻ</b><small>/phim-le</small></div></div><div class="act"><div class="txt"><b>Phim bộ</b><small>/phim-bo</small></div></div><div class="act"><div class="txt"><b>Thể loại</b><small>menu con · 12 mục</small></div></div></div></div>
          <div class="panel"><div class="panel-head"><h4>Footer menu</h4></div><div class="panel-body activity"><div class="act"><div class="txt"><b>Về chúng tôi</b><small>/about</small></div></div><div class="act"><div class="txt"><b>Liên hệ</b><small>/contact</small></div></div><div class="act"><div class="txt"><b>Điều khoản</b><small>/terms</small></div></div></div></div>
          <div class="panel"><div class="panel-head"><h4>Mobile menu</h4></div><div class="panel-body activity"><div class="act"><div class="txt"><b>Trang chủ</b></div></div><div class="act"><div class="txt"><b>Tìm kiếm</b></div></div><div class="act"><div class="txt"><b>Yêu thích</b></div></div><div class="act"><div class="txt"><b>Tài khoản</b></div></div></div></div>
        </div>
      </section>

      <!-- ===== PAGES ===== -->
      <section id="pages" class="page">
        <div class="page-head"><div><h3>Trang tĩnh</h3><p>About, Contact, Privacy, Terms, DMCA, FAQ — publish/draft và SEO.</p></div><button class="btn">+ Thêm trang</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tiêu đề</th><th>Slug</th><th>Cập nhật</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Về chúng tôi</td><td>/about</td><td>02/09/2026</td><td><span class="status">Published</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Chính sách bảo mật</td><td>/privacy</td><td>28/08/2026</td><td><span class="status">Published</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>Điều khoản dịch vụ</td><td>/terms</td><td>28/08/2026</td><td><span class="status">Published</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>DMCA</td><td>/dmca</td><td>—</td><td><span class="status off">Draft</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td>FAQ</td><td>/faq</td><td>15/08/2026</td><td><span class="status">Published</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== NOTIFICATIONS ===== -->
      <section id="notifications" class="page">
        <div class="page-head"><div><h3>Notifications</h3><p>Tạo và gửi thông báo tới tất cả user, theo role hoặc user cụ thể.</p></div></div>
        <div class="grid-2">
          <div class="panel"><div class="panel-head"><h4>Tạo thông báo</h4></div><div class="panel-body"><form class="form-grid" onsubmit="return false" style="grid-template-columns:1fr">
            <div class="field"><label>Tiêu đề</label><input placeholder="VD: Tập mới đã lên sóng"></div>
            <div class="field"><label>Loại</label><select><option>New episode</option><option>New movie</option><option>System</option><option>Promotion</option><option>Maintenance</option></select></div>
            <div class="field"><label>Gửi tới</label><select><option>Tất cả user</option><option>Theo role</option><option>User cụ thể</option></select></div>
            <div class="field"><label>Nội dung</label><textarea placeholder="Nội dung thông báo..."></textarea></div>
            <div class="form-actions"><button class="btn">Gửi thông báo</button></div>
          </form></div></div>
          <div class="panel"><div class="panel-head"><h4>Lịch sử gửi</h4></div><div class="panel-body activity">
            <div class="act"><div class="dot">✉</div><div class="txt"><b>Tập 12 Crimson Vale</b> đã lên sóng<small>Gửi 12.4K user · hôm nay</small></div></div>
            <div class="act"><div class="dot">%</div><div class="txt"><b>Ưu đãi Premium -30%</b><small>Gửi Free users · hôm qua</small></div></div>
            <div class="act"><div class="dot">⚙</div><div class="txt"><b>Bảo trì hệ thống 02:00</b><small>Gửi tất cả · 3 ngày trước</small></div></div>
          </div></div>
        </div>
      </section>

      <!-- ===== EMAIL ===== -->
      <section id="email" class="page">
        <div class="page-head"><div><h3>Email</h3><p>Cấu hình SMTP và mẫu email hệ thống.</p></div></div>
        <div class="grid-2">
          <div class="panel"><div class="panel-head"><h4>SMTP Settings</h4></div><div class="panel-body"><form class="form-grid" onsubmit="return false" style="grid-template-columns:1fr 1fr">
            <div class="field"><label>SMTP Host</label><input placeholder="smtp.example.com"></div>
            <div class="field"><label>Port</label><input placeholder="587"></div>
            <div class="field"><label>Username</label><input></div>
            <div class="field"><label>Password</label><input type="password"></div>
            <div class="field full"><label>From Email</label><input placeholder="no-reply@cineadmin.vn"></div>
            <div class="form-actions"><button class="btn ghost">Gửi thử</button><button class="btn">Lưu</button></div>
          </form></div></div>
          <div class="panel"><div class="panel-head"><h4>Email Templates</h4></div><div class="panel-body activity">
            <div class="act"><div class="txt"><b>Welcome email</b><small>Kích hoạt khi đăng ký</small></div></div>
            <div class="act"><div class="txt"><b>Verify email</b><small>Xác thực tài khoản</small></div></div>
            <div class="act"><div class="txt"><b>Reset password</b><small>Khôi phục mật khẩu</small></div></div>
            <div class="act"><div class="txt"><b>New episode notification</b><small>Tập mới của phim theo dõi</small></div></div>
          </div></div>
        </div>
      </section>

      <!-- ===== SEO ===== -->
      <section id="seo" class="page">
        <div class="page-head"><div><h3>SEO Settings</h3><p>Cấu hình SEO toàn website.</p></div></div>
        <div class="panel"><div class="panel-body"><form class="form-grid" onsubmit="return false">
          <div class="field"><label>Site Title</label><input placeholder="CineAdmin - Xem phim online"></div>
          <div class="field"><label>Canonical URL</label><input placeholder="https://cineadmin.vn"></div>
          <div class="field full"><label>Site Description</label><textarea style="min-height:70px"></textarea></div>
          <div class="field full"><label>Keywords</label><input placeholder="xem phim, phim online, phim hd"></div>
          <div class="field"><label>Google Verification</label><input></div>
          <div class="field"><label>Bing Verification</label><input></div>
          <div class="upload"><b>Logo &amp; Favicon</b> — kéo thả file vào đây</div>
          <div class="form-actions"><button class="btn">Lưu SEO</button></div>
        </form></div></div>
      </section>

      <!-- ===== SITEMAP ===== -->
      <section id="sitemap" class="page">
        <div class="page-head"><div><h3>Sitemap</h3><p>Tạo và quản lý sitemap cho từng loại nội dung.</p></div><button class="btn">Generate toàn bộ</button></div>
        <div class="panel"><table>
          <thead><tr><th>Sitemap</th><th>URL</th><th>Số URL</th><th>Cập nhật</th><th></th></tr></thead>
          <tbody>
            <tr><td>Movie sitemap</td><td>/sitemap-movies.xml</td><td>248</td><td>Hôm nay</td><td><div class="row-actions"><button class="mini">↻</button></div></td></tr>
            <tr><td>Genre sitemap</td><td>/sitemap-genres.xml</td><td>18</td><td>Hôm nay</td><td><div class="row-actions"><button class="mini">↻</button></div></td></tr>
            <tr><td>Episode sitemap</td><td>/sitemap-episodes.xml</td><td>5.312</td><td>Hôm qua</td><td><div class="row-actions"><button class="mini">↻</button></div></td></tr>
            <tr><td>Actor sitemap</td><td>/sitemap-actors.xml</td><td>640</td><td>3 ngày trước</td><td><div class="row-actions"><button class="mini">↻</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== PLANS ===== -->
      <section id="plans" class="page">
        <div class="page-head"><div><h3>Plans</h3><p>Các gói Premium và quyền lợi.</p></div><button class="btn">+ Thêm plan</button></div>
        <div class="grid-3">
          <div class="panel"><div class="panel-body"><span class="tag">Free</span><div class="num" style="font-size:26px;font-weight:800;margin:12px 0">₫0</div><p class="hint">Quảng cáo · 720p · 1 thiết bị</p></div></div>
          <div class="panel"><div class="panel-body"><span class="tag solid">Premium Tháng</span><div class="num" style="font-size:26px;font-weight:800;margin:12px 0">₫99K</div><p class="hint">Không quảng cáo · 4K · 4 thiết bị</p></div></div>
          <div class="panel"><div class="panel-body"><span class="tag solid">Premium Năm</span><div class="num" style="font-size:26px;font-weight:800;margin:12px 0">₫990K</div><p class="hint">Tất cả quyền lợi · tiết kiệm 17%</p></div></div>
        </div>
      </section>

      <!-- ===== SUBSCRIPTIONS ===== -->
      <section id="subscriptions" class="page">
        <div class="page-head"><div><h3>Subscriptions</h3><p>Đăng ký của người dùng: active, expired, cancelled.</p></div></div>
        <div class="table-tools"><span class="filter on">Active</span><span class="filter">Expired</span><span class="filter">Cancelled</span></div>
        <div class="panel"><table>
          <thead><tr><th>User</th><th>Plan</th><th>Bắt đầu</th><th>Kết thúc</th><th>Trạng thái</th></tr></thead>
          <tbody>
            <tr><td>@lanpham</td><td>Premium Năm</td><td>01/01/2026</td><td>01/01/2027</td><td><span class="status">Active</span></td></tr>
            <tr><td>@huydo</td><td>Premium Tháng</td><td>05/09/2026</td><td>05/10/2026</td><td><span class="status">Active</span></td></tr>
            <tr><td>@sonle</td><td>Premium Tháng</td><td>01/07/2026</td><td>01/08/2026</td><td><span class="status off">Expired</span></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== TRANSACTIONS ===== -->
      <section id="transactions" class="page">
        <div class="page-head"><div><h3>Transactions</h3><p>Giao dịch, hóa đơn và hoàn tiền.</p></div></div>
        <div class="stats"><div class="stat"><span class="label">Doanh thu tháng</span><div class="num">₫420M</div></div><div class="stat"><span class="label">Giao dịch</span><div class="num">1.284</div></div><div class="stat"><span class="label">Hoàn tiền</span><div class="num">12</div></div><div class="stat"><span class="label">Thành công</span><div class="num">98.4%</div></div></div>
        <div class="panel"><table>
          <thead><tr><th>Mã GD</th><th>User</th><th>Số tiền</th><th>Phương thức</th><th>Trạng thái</th><th>Thời gian</th></tr></thead>
          <tbody>
            <tr><td>#TX-90231</td><td>@lanpham</td><td>₫990.000</td><td>Momo</td><td><span class="status">Thành công</span></td><td>Hôm nay</td></tr>
            <tr><td>#TX-90230</td><td>@huydo</td><td>₫99.000</td><td>VNPay</td><td><span class="status">Thành công</span></td><td>Hôm nay</td></tr>
            <tr><td>#TX-90228</td><td>@guest22</td><td>₫99.000</td><td>Thẻ</td><td><span class="status off">Thất bại</span></td><td>Hôm qua</td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== COUPONS ===== -->
      <section id="coupons" class="page">
        <div class="page-head"><div><h3>Coupons</h3><p>Mã giảm giá kèm điều kiện và giới hạn sử dụng.</p></div><button class="btn">+ Thêm coupon</button></div>
        <div class="panel"><table>
          <thead><tr><th>Code</th><th>Giảm</th><th>Thời gian</th><th>Đã dùng / Giới hạn</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td><span class="tag solid">WELCOME30</span></td><td>30%</td><td>01/09 – 30/09</td><td>420 / 1000</td><td><span class="status">Bật</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
            <tr><td><span class="tag">TET2026</span></td><td>₫50.000</td><td>Hết hạn</td><td>980 / 1000</td><td><span class="status off">Tắt</span></td><td><div class="row-actions"><button class="mini">✎</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== STORAGE ===== -->
      <section id="storage" class="page">
        <div class="page-head"><div><h3>Storage</h3><p>Quản lý file: ảnh, video, phụ đề, poster, backdrop.</p></div><button class="btn">+ Upload file</button></div>
        <div class="panel"><div class="panel-body">
          <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:14px"><b>Dung lượng đã dùng</b><span>612 GB / 1 TB</span></div>
          <div class="prog"><i style="width:61%"></i></div>
        </div></div>
        <div class="grid-3" style="margin-top:18px">
          <div class="panel"><div class="panel-body"><h4>Posters</h4><p class="hint">248 file · 3.2 GB</p></div></div>
          <div class="panel"><div class="panel-body"><h4>Videos</h4><p class="hint">5.312 file · 588 GB</p></div></div>
          <div class="panel"><div class="panel-body"><h4>Subtitles</h4><p class="hint">9.140 file · 420 MB</p></div></div>
        </div>
        <div class="panel"><div class="panel-head"><h4>File chưa dùng</h4><a class="link">Dọn dẹp</a></div><div class="panel-body activity"><div class="act"><div class="txt"><b>old-banner-2024.jpg</b><small>2.1 MB · không còn liên kết</small></div></div><div class="act"><div class="txt"><b>temp-upload-441.mp4</b><small>1.2 GB · file tạm</small></div></div></div></div>
      </section>

      <!-- ===== CACHE ===== -->
      <section id="cache" class="page">
        <div class="page-head"><div><h3>Cache</h3><p>Xóa các loại cache của ứng dụng.</p></div></div>
        <div class="panel"><div class="panel-body">
          <div class="setting-row"><div class="st-txt"><strong>Application cache</strong><small>Dữ liệu ứng dụng tạm</small></div><button class="btn ghost sm">Clear</button></div>
          <div class="setting-row"><div class="st-txt"><strong>Config cache</strong><small>Cấu hình hệ thống</small></div><button class="btn ghost sm">Clear</button></div>
          <div class="setting-row"><div class="st-txt"><strong>Route cache</strong><small>Định tuyến</small></div><button class="btn ghost sm">Clear</button></div>
          <div class="setting-row"><div class="st-txt"><strong>View cache</strong><small>Template đã biên dịch</small></div><button class="btn ghost sm">Clear</button></div>
          <div class="setting-row"><div class="st-txt"><strong>Toàn bộ cache</strong><small>Xóa tất cả các loại cache</small></div><button class="btn sm">Clear All</button></div>
        </div></div>
      </section>

      <!-- ===== BACKUP ===== -->
      <section id="backup" class="page">
        <div class="page-head"><div><h3>Backup</h3><p>Sao lưu database và file, khôi phục khi cần.</p></div><button class="btn">+ Tạo backup</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tên backup</th><th>Loại</th><th>Kích thước</th><th>Thời gian</th><th></th></tr></thead>
          <tbody>
            <tr><td>backup-2026-09-08.sql</td><td>Database</td><td>842 MB</td><td>Hôm nay 02:00</td><td><div class="row-actions"><button class="mini" title="Tải">↓</button><button class="mini" title="Khôi phục">↻</button><button class="mini">✕</button></div></td></tr>
            <tr><td>backup-files-2026-09-01.zip</td><td>Files</td><td>58 GB</td><td>01/09 02:00</td><td><div class="row-actions"><button class="mini">↓</button><button class="mini">↻</button><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== ACTIVITY LOGS ===== -->
      <section id="logs" class="page">
        <div class="page-head"><div><h3>Activity Logs</h3><p>Nhật ký thao tác của quản trị viên.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>Người thực hiện</th><th>Hành động</th><th>Đối tượng</th><th>IP</th><th>Thời gian</th></tr></thead>
          <tbody>
            <tr><td>Minh Trần</td><td>Thêm phim</td><td>Movie #248 (Last Horizon)</td><td>103.21.x.x</td><td>15 phút trước</td></tr>
            <tr><td>Huy Đỗ</td><td>Cập nhật tập</td><td>Episode #5312</td><td>118.70.x.x</td><td>1 giờ trước</td></tr>
            <tr><td>Minh Trần</td><td>Đổi role user</td><td>User @huydo → Editor</td><td>103.21.x.x</td><td>2 giờ trước</td></tr>
            <tr><td>System</td><td>Xóa file tạm</td><td>temp-upload-441.mp4</td><td>—</td><td>3 giờ trước</td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== CRON ===== -->
      <section id="cron" class="page">
        <div class="page-head"><div><h3>Cron Jobs</h3><p>Tác vụ định kỳ của hệ thống.</p></div></div>
        <div class="panel"><table>
          <thead><tr><th>Job</th><th>Lịch</th><th>Lần chạy cuối</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Backup database</td><td>Hằng ngày 02:00</td><td>Hôm nay 02:00</td><td><span class="status">OK</span></td><td><div class="row-actions"><button class="mini" title="Chạy ngay">▶</button></div></td></tr>
            <tr><td>Update movie metadata</td><td>Mỗi 6 giờ</td><td>4 giờ trước</td><td><span class="status">OK</span></td><td><div class="row-actions"><button class="mini">▶</button></div></td></tr>
            <tr><td>Generate sitemap</td><td>Hằng ngày 03:00</td><td>Hôm nay 03:00</td><td><span class="status">OK</span></td><td><div class="row-actions"><button class="mini">▶</button></div></td></tr>
            <tr><td>Clean expired sessions</td><td>Mỗi giờ</td><td>20 phút trước</td><td><span class="status warn">Đang chạy</span></td><td><div class="row-actions"><button class="mini">▶</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== API ===== -->
      <section id="api" class="page">
        <div class="page-head"><div><h3>API Management</h3><p>Quản lý API keys, giới hạn tần suất và nhật ký sử dụng.</p></div><button class="btn">+ Tạo API key</button></div>
        <div class="panel"><table>
          <thead><tr><th>Tên</th><th>Key</th><th>Rate limit</th><th>Sử dụng hôm nay</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
            <tr><td>Mobile App</td><td>sk_live_••••4f2a</td><td>1000/phút</td><td>42.1K</td><td><span class="status">Active</span></td><td><div class="row-actions"><button class="mini" title="Thu hồi">✕</button></div></td></tr>
            <tr><td>Partner CDN</td><td>sk_live_••••9b1c</td><td>500/phút</td><td>18.4K</td><td><span class="status">Active</span></td><td><div class="row-actions"><button class="mini">✕</button></div></td></tr>
          </tbody>
        </table></div>
      </section>

      <!-- ===== SETTINGS ===== -->
      <section id="settings" class="page">
        <div class="page-head"><div><h3>Cài đặt</h3><p>Cấu hình chung, phim, trình phát, đăng ký, bình luận, đăng nhập xã hội và bảo mật.</p></div></div>
        <div class="split">
          <nav class="subnav">
            <a class="on">General</a>
            <a>Movie</a>
            <a>Player</a>
            <a>Registration</a>
            <a>Comments</a>
            <a>Email</a>
            <a>Social Login</a>
            <a>Security</a>
          </nav>
          <div>
            <div class="panel"><div class="panel-head"><h4>General Settings</h4></div><div class="panel-body">
              <form class="form-grid" onsubmit="return false">
                <div class="field"><label>Website name</label><input value="CineAdmin"></div>
                <div class="field"><label>Website URL</label><input value="https://cineadmin.vn"></div>
                <div class="field"><label>Email liên hệ</label><input value="contact@cineadmin.vn"></div>
                <div class="field"><label>Hotline</label><input value="1900 0000"></div>
                <div class="field"><label>Timezone</label><select><option>GMT+7 (Việt Nam)</option><option>UTC</option></select></div>
                <div class="field"><label>Ngôn ngữ mặc định</label><select><option>Tiếng Việt</option><option>English</option></select></div>
              </form>
              <div class="setting-row" style="margin-top:8px"><div class="st-txt"><strong>Maintenance mode</strong><small>Tạm khóa website để bảo trì</small></div><span class="toggle"><input type="checkbox"><span class="track"></span></span></div>
            </div></div>

            <div class="panel"><div class="panel-head"><h4>Player Settings</h4></div><div class="panel-body">
              <div class="setting-row"><div class="st-txt"><strong>Autoplay</strong><small>Tự phát khi mở trang xem</small></div><span class="toggle"><input type="checkbox" checked><span class="track"></span></span></div>
              <div class="setting-row"><div class="st-txt"><strong>Auto next episode</strong><small>Tự chuyển tập kế tiếp</small></div><span class="toggle"><input type="checkbox" checked><span class="track"></span></span></div>
              <div class="setting-row"><div class="st-txt"><strong>Skip intro / outro</strong><small>Nút bỏ qua giới thiệu</small></div><span class="toggle"><input type="checkbox" checked><span class="track"></span></span></div>
              <div class="setting-row"><div class="st-txt"><strong>Picture in Picture</strong><small>Cho phép thu nhỏ trình phát</small></div><span class="toggle"><input type="checkbox"><span class="track"></span></span></div>
            </div></div>

            <div class="panel"><div class="panel-head"><h4>Registration &amp; Security</h4></div><div class="panel-body">
              <div class="setting-row"><div class="st-txt"><strong>Cho phép đăng ký</strong><small>Người dùng mới có thể tạo tài khoản</small></div><span class="toggle"><input type="checkbox" checked><span class="track"></span></span></div>
              <div class="setting-row"><div class="st-txt"><strong>Xác thực email</strong><small>Bắt buộc verify email khi đăng ký</small></div><span class="toggle"><input type="checkbox" checked><span class="track"></span></span></div>
              <div class="setting-row"><div class="st-txt"><strong>Yêu cầu duyệt bình luận</strong><small>Bình luận cần admin duyệt trước</small></div><span class="toggle"><input type="checkbox"><span class="track"></span></span></div>
              <div class="setting-row"><div class="st-txt"><strong>Two-factor authentication</strong><small>Bảo mật 2 lớp cho quản trị viên</small></div><span class="toggle"><input type="checkbox" checked><span class="track"></span></span></div>
              <div class="setting-row"><div class="st-txt"><strong>Giới hạn đăng nhập sai</strong><small>Khóa tạm sau 5 lần sai</small></div><span class="toggle"><input type="checkbox" checked><span class="track"></span></span></div>
              <div class="form-actions" style="margin-top:14px"><button class="btn ghost">Hủy</button><button class="btn">Lưu cài đặt</button></div>
            </div></div>

            <div class="panel"><div class="panel-head"><h4>Social Login</h4></div><div class="panel-body">
              <div class="setting-row"><div class="st-txt"><strong>Google Login</strong><small>Client ID · Callback URL</small></div><span class="toggle"><input type="checkbox" checked><span class="track"></span></span></div>
              <div class="setting-row"><div class="st-txt"><strong>Facebook Login</strong><small>App ID · App Secret</small></div><span class="toggle"><input type="checkbox"><span class="track"></span></span></div>
              <div class="setting-row"><div class="st-txt"><strong>GitHub Login</strong><small>Client ID · Client Secret</small></div><span class="toggle"><input type="checkbox"><span class="track"></span></span></div>
            </div></div>
          </div>
        </div>
      </section>

    </main>

    <footer class="foot">
      <span>© 2026 CineAdmin — Hệ thống quản lý web xem phim.</span>
      <span>Phiên bản 2.0</span>
    </footer>
  </div>
</div>
</body>
</html>
