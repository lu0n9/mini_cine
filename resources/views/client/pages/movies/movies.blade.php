@extends('client.layouts.master')
@section('content')
  <main>
    <section class="section" id="moi-cap-nhat" aria-labelledby="moi-title">
          <div class="shell">
            <div class="chips" role="group" aria-label="Lọc theo loại phim">
              <button class="chip" type="button" aria-pressed="true">
                Tất cả
              </button>
              <button class="chip" type="button" aria-pressed="false">
                Phim lẻ
              </button>
              <button class="chip" type="button" aria-pressed="false">
                Phim bộ
              </button>
              <button class="chip" type="button" aria-pressed="false">
                Chiếu rạp
              </button>
              <button class="chip" type="button" aria-pressed="false">
                Hoạt hình
              </button>
              <button class="chip" type="button" aria-pressed="false">
                Thuyết minh
              </button>
            </div>

            <ul class="grid">
              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-2.png"
                      alt="Áp phích phim Hành Tinh Xám"
                      width="336"
                      height="504"
                    />
                    <span class="poster__badge">Tập 8</span>
                  </div>
                  <p class="card__title">Hành Tinh Xám</p>
                  <p class="card__sub">2026 · Phụ đề Việt</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-4.png"
                      alt="Áp phích phim Căn Nhà Số 7"
                      width="336"
                      height="504"
                    />
                    <span class="poster__badge">Mới</span>
                  </div>
                  <p class="card__title">Căn Nhà Số 7</p>
                  <p class="card__sub">2025 · Thuyết minh</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-5.png"
                      alt="Áp phích phim Dưới Cùng Một Mái Dù"
                      width="336"
                      height="504"
                    />
                  </div>
                  <p class="card__title">Dưới Cùng Một Mái Dù</p>
                  <p class="card__sub">2025 · Phụ đề Việt</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-1.png"
                      alt="Áp phích phim Mưa Trên Phố Vắng"
                      width="336"
                      height="504"
                    />
                    <span class="poster__badge">4K</span>
                  </div>
                  <p class="card__title">Mưa Trên Phố Vắng</p>
                  <p class="card__sub">2026 · Phụ đề Việt</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-6.png"
                      alt="Áp phích phim Phi Vụ Hầm Tối"
                      width="336"
                      height="504"
                    />
                  </div>
                  <p class="card__title">Phi Vụ Hầm Tối</p>
                  <p class="card__sub">2026 · Lồng tiếng</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-3.png"
                      alt="Áp phích phim Trúc Lâm Kiếm Ảnh"
                      width="336"
                      height="504"
                    />
                    <span class="poster__badge">Tập 12</span>
                  </div>
                  <p class="card__title">Trúc Lâm Kiếm Ảnh</p>
                  <p class="card__sub">2025 · Phụ đề Việt</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-2.png"
                      alt="Áp phích phim Hành Tinh Xám"
                      width="336"
                      height="504"
                    />
                    <span class="poster__badge">Tập 8</span>
                  </div>
                  <p class="card__title">Hành Tinh Xám</p>
                  <p class="card__sub">2026 · Phụ đề Việt</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-4.png"
                      alt="Áp phích phim Căn Nhà Số 7"
                      width="336"
                      height="504"
                    />
                    <span class="poster__badge">Mới</span>
                  </div>
                  <p class="card__title">Căn Nhà Số 7</p>
                  <p class="card__sub">2025 · Thuyết minh</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-5.png"
                      alt="Áp phích phim Dưới Cùng Một Mái Dù"
                      width="336"
                      height="504"
                    />
                  </div>
                  <p class="card__title">Dưới Cùng Một Mái Dù</p>
                  <p class="card__sub">2025 · Phụ đề Việt</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-1.png"
                      alt="Áp phích phim Mưa Trên Phố Vắng"
                      width="336"
                      height="504"
                    />
                    <span class="poster__badge">4K</span>
                  </div>
                  <p class="card__title">Mưa Trên Phố Vắng</p>
                  <p class="card__sub">2026 · Phụ đề Việt</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-6.png"
                      alt="Áp phích phim Phi Vụ Hầm Tối"
                      width="336"
                      height="504"
                    />
                  </div>
                  <p class="card__title">Phi Vụ Hầm Tối</p>
                  <p class="card__sub">2026 · Lồng tiếng</p>
                </a>
              </li>

              <li class="card">
                <a href="#">
                  <div class="poster">
                    <img
                      src="/images/poster-3.png"
                      alt="Áp phích phim Trúc Lâm Kiếm Ảnh"
                      width="336"
                      height="504"
                    />
                    <span class="poster__badge">Tập 12</span>
                  </div>
                  <p class="card__title">Trúc Lâm Kiếm Ảnh</p>
                  <p class="card__sub">2025 · Phụ đề Việt</p>
                </a>
              </li>
            </ul>
          </div>
        </section>
  </main>
@endsection