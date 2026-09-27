
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\admin\IndexController;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\client\MovieController;
use App\Http\Controllers\Client\PremiumController;
use App\Http\Controllers\client\PeopleController;
use App\Http\Controllers\client\RatingController;
use App\Http\Controllers\client\CommentController;
use App\Http\Controllers\client\FavoriteController;
use App\Http\Controllers\client\ForumController;
use App\Http\Controllers\Client\NewsController as ClientNewsController;
use App\Http\Controllers\Client\RandomMovieController;
use App\Http\Controllers\Client\SearchController;
use App\Http\Controllers\admin\VideoUploadController;
use App\Http\Controllers\admin\MenuController;
use App\Http\Controllers\admin\PageController;
use App\Models\Admin;


/*
|--------------------------------------------------------------------------
| CLIENT
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// Danh sách phim
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

Route::get('/news', [ClientNewsController::class, 'index'])->name('news.index');
Route::get('/news/{slug}', [ClientNewsController::class, 'show'])->name('news.show');

// Random suggestions page opens its movie picker as a modal.
Route::get('/random-movies', RandomMovieController::class)
    ->name('movies.random');

// Chi tiết phim
Route::get('/movie/{slug}', [MovieController::class,'movieDetail'])
->name('movie.detail');

// Phim
Route::get(
    '/the-loai/{slug}',
    [MovieController::class, 'showGenre']
)->name('genres.show');

// Xem phim
Route::get('/movie/{slug}/watch', [MovieController::class, 'watch'])
    ->name('movies.watch');

Route::get('/premium', [PremiumController::class, 'index'])->name('premium.index');
Route::get('/premium/vnpay/return', [PremiumController::class, 'vnpayReturn'])->name('premium.vnpay.return');

Route::get('/dien-vien/{slug}', [PeopleController::class, 'show'])
    ->name('people.show');

// Public Sitemaps
Route::get('/sitemap.xml', [\App\Http\Controllers\admin\SeoController::class, 'serveSitemapIndex'])->name('sitemap.index');
Route::get('/sitemap-{type}.xml', [\App\Http\Controllers\admin\SeoController::class, 'serveSitemapType'])->name('sitemap.type');


/*
|--------------------------------------------------------------------------
| USER AUTHENTICATION
|--------------------------------------------------------------------------
*/

// Đăng nhập
Route::middleware('guest')->group(function () {

    Route::get('/login', [UserAuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [UserAuthController::class, 'login'])
        ->name('login.submit');

    // Quên và đặt lại mật khẩu
    Route::get('/forgot-password', [UserAuthController::class, 'showForgotPassword'])
        ->name('password.request');
    Route::post('/forgot-password', [UserAuthController::class, 'sendPasswordResetLink'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [UserAuthController::class, 'showResetPassword'])
        ->name('password.reset');
    Route::post('/reset-password', [UserAuthController::class, 'resetPassword'])
        ->name('password.update');

    // Đăng ký
    Route::get('/register', [UserAuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [UserAuthController::class, 'register'])
        ->name('register.submit');
});


// Đăng xuất
Route::post('/logout', [UserAuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| USER
|--------------------------------------------------------------------------
*/

Route::middleware('auth', 'user.ban')->group(function () {

    Route::get('/notifications', [\App\Http\Controllers\Client\NotificationController::class, 'index'])
        ->name('notifications.index');
    Route::get('/notifications/{notification}', [\App\Http\Controllers\Client\NotificationController::class, 'show'])
        ->whereNumber('notification')
        ->name('notifications.show');

    Route::post('/premium/checkout/{plan}', [PremiumController::class, 'checkout'])->name('premium.checkout');
    Route::get('/premium/invoices/{transaction}', [PremiumController::class, 'invoice'])->name('premium.invoice');
    Route::post('/premium/shares', [PremiumController::class, 'addShare'])->name('premium.shares.store');
    Route::delete('/premium/shares/{share}', [PremiumController::class, 'removeShare'])->name('premium.shares.destroy');

    // Profile
    Route::get('/profile', [UserAuthController::class, 'showProfile'])
        ->name('profile');

    // Cập nhật tên
    Route::put('/profile/update-name', [UserAuthController::class, 'updateName'])
        ->name('profile.updateName');

    // Đổi mật khẩu
    Route::post('/change-password', [UserAuthController::class, 'changePassword'])
        ->name('password.change');

    // Lưu tiến trình xem phim
    Route::post('/api/save-history', [MovieController::class, 'saveHistory'])
        ->name('api.save_history');
    
    Route::get('/history', [MovieController::class, 'history'])
        ->name('history.index');

    // Đánh giá
    Route::post('/ratings', [RatingController::class, 'store'])
        ->name('ratings.store');

    // comment
    Route::post('/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::post('/comments/{comment}/like',[CommentController::class, 'toggleLike'])
    ->name('comments.like');

    // favorite
    Route::get('/my-list',[FavoriteController::class, 'index'])
    ->name('favorites.index');

    Route::post('/favorites/{movie}/toggle',[FavoriteController::class, 'toggle'])
    ->name('favorites.toggle');


    // report
    Route::post('/reports', [App\Http\Controllers\client\ReportController::class, 'store'])
        ->name('reports.store');

    /*
    |--------------------------------------------------------------------------
    | Forum
    |--------------------------------------------------------------------------
    */

    Route::get('/forum', [ForumController::class, 'index'])
        ->name('forum.index');

    Route::get('/forum/category/{slug}', [ForumController::class, 'category'])
        ->name('forum.category');

    Route::get('/forum/post/{slug}', [ForumController::class, 'show'])
        ->name('forum.show');


    Route::get('/forum/create', [ForumController::class, 'create'])
        ->name('forum.create');

    Route::post('/forum/post', [ForumController::class, 'store'])
        ->name('forum.store');

    Route::get('/forum/post/{post}/edit', [ForumController::class, 'edit'])
        ->name('forum.edit');

    Route::put('/forum/post/{post}', [ForumController::class, 'update'])
        ->name('forum.update');

    Route::delete('/forum/post/{post}', [ForumController::class, 'destroy'])
        ->name('forum.destroy');

    Route::post(
        '/forum/post/{post}/comment',
        [ForumController::class, 'commentStore']
    )->name('forum.comment.store');
    Route::get(
        '/forum/my-posts',
        [ForumController::class, 'myPosts']
    )->name('forum.my-posts');



});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | ADMIN GUEST
        |--------------------------------------------------------------------------
        */

        // Trang đăng nhập
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])
            ->name('login');

        // Xử lý đăng nhập
        Route::post('/login', [AdminAuthController::class, 'login'])
            ->name('login.submit');

        

        /*
        |--------------------------------------------------------------------------
        | ADMIN AUTHENTICATED
        |--------------------------------------------------------------------------
        */

        Route::middleware(['auth:admin', 'admin.activity-log'])->group(function () {

            /*
            |--------------------------------------------------------------------------
            | DASHBOARD
            |--------------------------------------------------------------------------
            */

            Route::get('/dashboard', [IndexController::class, 'dashboard'])
                ->middleware('admin.permission:dashboard.view')
                ->name('dashboard');


            /*
            |--------------------------------------------------------------------------
            | LOGOUT
            |--------------------------------------------------------------------------
            */

            Route::post('/logout', [AdminAuthController::class, 'logout'])
                ->name('logout');

            Route::post('/profile', [\App\Http\Controllers\admin\AdminController::class, 'updateProfile'])
                ->middleware('admin.permission:profile.update')
                ->name('profile.update');


            /*
            |--------------------------------------------------------------------------
            | MOVIE MANAGEMENT
            |--------------------------------------------------------------------------
            */

            // Danh sách phim
            Route::get(
                '/movies',
                [\App\Http\Controllers\admin\MovieController::class, 'index']
            )
                ->middleware('admin.permission:movies.view')
                ->name('movies.index');

            // Featured
            Route::get(
                '/movies/featured',
                [\App\Http\Controllers\admin\MovieController::class, 'featured']
            )
                ->middleware('admin.permission:movies.view')
                ->name('movies.featured');
            
            Route::post('/featured/{movie}/unfeatured', [\App\Http\Controllers\admin\MovieController::class, 'unfeatured'])
                ->middleware('admin.permission:movies.edit')
                ->name('featured.unfeatured');

            // Popular
            Route::get(
                '/movies/popular',
                [\App\Http\Controllers\admin\MovieController::class, 'popular']
            )
                ->middleware('admin.permission:movies.view')
                ->name('movies.popular');

            Route::post('/popular/{movie}/unpopular', [\App\Http\Controllers\admin\MovieController::class, 'unpopular'])
                ->middleware('admin.permission:movies.edit')
                ->name('popular.unpopular');

            // Thêm phim
            Route::get(
                '/movies/create',
                [\App\Http\Controllers\admin\MovieController::class, 'add']
            )
                ->middleware('admin.permission:movies.create')
                ->name('movies.create');

            // Lưu phim
            Route::post(
                '/movies',
                [\App\Http\Controllers\admin\MovieController::class, 'store']
            )
                ->middleware('admin.permission:movies.create')
                ->name('movies.store');

            // Sửa phim
            Route::get(
                '/movies/{id}/edit',
                [\App\Http\Controllers\admin\MovieController::class, 'edit']
            )
                ->middleware('admin.permission:movies.edit')
                ->name('movies.edit');

            // Cập nhật phim
            Route::put(
                '/movies/{id}',
                [\App\Http\Controllers\admin\MovieController::class, 'update']
            )
                ->middleware('admin.permission:movies.edit')
                ->name('movies.update');

            // Xóa phim
            Route::delete(
                '/movies/{id}',
                [\App\Http\Controllers\admin\MovieController::class, 'destroy']
            )
                ->middleware('admin.permission:movies.delete')
                ->name('movies.destroy');

            // Đổi trạng thái
            Route::patch(
                '/movies/{id}/toggle-status',
                [\App\Http\Controllers\admin\MovieController::class, 'toggleStatus']
            )
                ->middleware('admin.permission:movies.edit')
                ->name('movies.toggle-status');


            /*
            |--------------------------------------------------------------------------
            | EPISODE MANAGEMENT
            |--------------------------------------------------------------------------
            */

            // Danh sách tập phim
            Route::get(
                '/episodes',
                [\App\Http\Controllers\admin\EpisodeController::class, 'episodes']
            )
                ->middleware('admin.permission:episodes.view')
                ->name('episodes');

            // Thêm tập
            Route::get(
                '/episodes/create',
                [\App\Http\Controllers\admin\EpisodeController::class, 'create']
            )
                ->middleware('admin.permission:episodes.create')
                ->name('episodes.create');

            // Lưu tập
            Route::post(
                '/episodes',
                [\App\Http\Controllers\admin\EpisodeController::class, 'store']
            )
                ->middleware('admin.permission:episodes.create')
                ->name('episodes.store');

            // Sửa tập
            Route::get(
                '/episodes/{id}/edit',
                [\App\Http\Controllers\admin\EpisodeController::class, 'edit']
            )
                ->middleware('admin.permission:episodes.edit')
                ->name('episodes.edit');

            // Cập nhật tập
            Route::put(
                '/episodes/{id}',
                [\App\Http\Controllers\admin\EpisodeController::class, 'update']
            )
                ->middleware('admin.permission:episodes.edit')
                ->name('episodes.update');

            // Xóa tập
            Route::delete(
                '/episodes/{id}',
                [\App\Http\Controllers\admin\EpisodeController::class, 'destroy']
            )
                ->middleware('admin.permission:episodes.delete')
                ->name('episodes.destroy');


            /*
            |--------------------------------------------------------------------------
            | SEASON MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/seasons',
                [\App\Http\Controllers\admin\SeasonController::class, 'seasons']
            )
                ->middleware('admin.permission:seasons.view')
                ->name('seasons');

            Route::get(
                '/seasons/create',
                [\App\Http\Controllers\admin\SeasonController::class, 'createSeason']
            )
                ->middleware('admin.permission:seasons.create')
                ->name('seasons.create');

            Route::post(
                '/seasons',
                [\App\Http\Controllers\admin\SeasonController::class, 'store']
            )
                ->middleware('admin.permission:seasons.create')
                ->name('seasons.store');

            Route::get(
                '/seasons/{id}/edit',
                [\App\Http\Controllers\admin\SeasonController::class, 'edit']
            )
                ->middleware('admin.permission:seasons.edit')
                ->name('seasons.edit');

            Route::put(
                '/seasons/{id}',
                [\App\Http\Controllers\admin\SeasonController::class, 'update']
            )
                ->middleware('admin.permission:seasons.edit')
                ->name('seasons.update');

            Route::delete(
                '/seasons/{id}',
                [\App\Http\Controllers\admin\SeasonController::class, 'destroy']
            )
                ->middleware('admin.permission:seasons.delete')
                ->name('seasons.destroy');


            /*
            |--------------------------------------------------------------------------
            | SERVER MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/servers',
                [\App\Http\Controllers\admin\ServerController::class, 'index']
            )
                ->middleware('admin.permission:servers.view')
                ->name('servers');

            Route::get(
                '/servers/create',
                [\App\Http\Controllers\admin\ServerController::class, 'create']
            )
                ->middleware('admin.permission:servers.create')
                ->name('servers.create');

            Route::post(
                '/servers',
                [\App\Http\Controllers\admin\ServerController::class, 'store']
            )
                ->middleware('admin.permission:servers.create')
                ->name('servers.store');

            Route::get(
                '/servers/{id}/edit',
                [\App\Http\Controllers\admin\ServerController::class, 'edit']
            )
                ->middleware('admin.permission:servers.edit')
                ->name('servers.edit');

            Route::put(
                '/servers/{id}',
                [\App\Http\Controllers\admin\ServerController::class, 'update']
            )
                ->middleware('admin.permission:servers.edit')
                ->name('servers.update');

            Route::delete(
                '/servers/{id}',
                [\App\Http\Controllers\admin\ServerController::class, 'destroy']
            )
                ->middleware('admin.permission:servers.delete')
                ->name('servers.destroy');

            Route::patch(
                '/servers/{id}/toggle-status',
                [\App\Http\Controllers\admin\ServerController::class, 'toggleStatus']
            )
                ->middleware('admin.permission:servers.edit')
                ->name('servers.toggle-status');

            Route::get(
                '/servers/movie/{movieId}/episodes',
                [\App\Http\Controllers\admin\ServerController::class, 'getEpisodes']
            )
                ->middleware('admin.permission:servers.view')
                ->name('servers.episodes');


            /*
            |--------------------------------------------------------------------------
            | SUBTITLE MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/subtitles',
                [\App\Http\Controllers\admin\SubtitleController::class, 'index']
            )
                ->middleware('admin.permission:subtitles.view')
                ->name('subtitles');

            Route::get(
                '/subtitles/create',
                [\App\Http\Controllers\admin\SubtitleController::class, 'create']
            )
                ->middleware('admin.permission:subtitles.create')
                ->name('subtitles.create');

            Route::post(
                '/subtitles',
                [\App\Http\Controllers\admin\SubtitleController::class, 'store']
            )
                ->middleware('admin.permission:subtitles.create')
                ->name('subtitles.store');

            Route::get(
                '/subtitles/{id}/edit',
                [\App\Http\Controllers\admin\SubtitleController::class, 'edit']
            )
                ->middleware('admin.permission:subtitles.edit')
                ->name('subtitles.edit');

            Route::put(
                '/subtitles/{id}',
                [\App\Http\Controllers\admin\SubtitleController::class, 'update']
            )
                ->middleware('admin.permission:subtitles.edit')
                ->name('subtitles.update');

            Route::delete(
                '/subtitles/{id}',
                [\App\Http\Controllers\admin\SubtitleController::class, 'destroy']
            )
                ->middleware('admin.permission:subtitles.delete')
                ->name('subtitles.destroy');

            Route::patch(
                '/subtitles/{id}/toggle-status',
                [\App\Http\Controllers\admin\SubtitleController::class, 'toggleStatus']
            )
                ->middleware('admin.permission:subtitles.edit')
                ->name('subtitles.toggle-status');

            Route::patch(
                '/subtitles/{id}/set-default',
                [\App\Http\Controllers\admin\SubtitleController::class, 'setDefault']
            )
                ->middleware('admin.permission:subtitles.edit')
                ->name('subtitles.set-default');

            Route::get(
                '/subtitles/movie/{movieId}/episodes',
                [\App\Http\Controllers\admin\SubtitleController::class, 'getEpisodes']
            )
                ->middleware('admin.permission:subtitles.view')
                ->name('subtitles.episodes');


            /*
            |--------------------------------------------------------------------------
            | CONTENT MANAGEMENT
            |--------------------------------------------------------------------------
            */

            // Genres
            Route::get(
                '/content/genres',
                [\App\Http\Controllers\admin\GenreController::class, 'index']
            )
                ->middleware('admin.permission:genres.view')
                ->name('content.genres');

            Route::get(
                '/genres/create',
                [\App\Http\Controllers\admin\GenreController::class, 'create']
            )
                ->middleware('admin.permission:genres.create')
                ->name('genres.create');

            Route::post(
                '/genres/store',
                [\App\Http\Controllers\admin\GenreController::class, 'store']
            )
                ->middleware('admin.permission:genres.create')
                ->name('genres.store');

            Route::get(
                '/genres/{id}/edit',
                [\App\Http\Controllers\admin\GenreController::class, 'edit']
            )
                ->middleware('admin.permission:genres.edit')
                ->name('genres.edit');

            Route::put(
                '/genres/{id}',
                [\App\Http\Controllers\admin\GenreController::class, 'update']
            )
                ->middleware('admin.permission:genres.edit')
                ->name('genres.update');

            Route::delete(
                '/genres/{id}',
                [\App\Http\Controllers\admin\GenreController::class, 'destroy']
            )
                ->middleware('admin.permission:genres.delete')
                ->name('genres.destroy');


            // Countries
            Route::get(
                '/countries',
                [\App\Http\Controllers\admin\CountryController::class, 'index']
            )
                ->middleware('admin.permission:countries.view')
                ->name('countries');

            Route::get(
                '/countries/create',
                [\App\Http\Controllers\admin\CountryController::class, 'create']
            )
                ->middleware('admin.permission:countries.create')
                ->name('countries.create');

            Route::post(
                '/countries',
                [\App\Http\Controllers\admin\CountryController::class, 'store']
            )
                ->middleware('admin.permission:countries.create')
                ->name('countries.store');

            Route::get(
                '/countries/{id}/edit',
                [\App\Http\Controllers\admin\CountryController::class, 'edit']
            )
                ->middleware('admin.permission:countries.edit')
                ->name('countries.edit');

            Route::put(
                '/countries/{id}',
                [\App\Http\Controllers\admin\CountryController::class, 'update']
            )
                ->middleware('admin.permission:countries.edit')
                ->name('countries.update');

            Route::delete(
                '/countries/{id}',
                [\App\Http\Controllers\admin\CountryController::class, 'destroy']
            )
                ->middleware('admin.permission:countries.delete')
                ->name('countries.destroy');


            // Tags
             Route::get('/tags', [\App\Http\Controllers\admin\TagController::class, 'index'])
                ->middleware('admin.permission:movies.view')
                ->name('content.tags');

            Route::get('/tags/create', [\App\Http\Controllers\admin\TagController::class, 'create'])
                ->middleware('admin.permission:movies.create')
                ->name('tags.create');

            Route::post('/tags', [\App\Http\Controllers\admin\TagController::class, 'store'])
                ->middleware('admin.permission:movies.create')
                ->name('tags.store');

            Route::get('/tags/{tag}/edit', [\App\Http\Controllers\admin\TagController::class, 'edit'])
                ->middleware('admin.permission:movies.edit')
                ->name('tags.edit');

            Route::put('/tags/{tag}', [\App\Http\Controllers\admin\TagController::class, 'update'])
                ->middleware('admin.permission:movies.edit')
                ->name('tags.update');

            Route::delete('/tags/{tag}', [\App\Http\Controllers\admin\TagController::class, 'destroy'])
                ->middleware('admin.permission:movies.delete')
                ->name('tags.destroy');


            // People
            Route::get(
                '/people',
                [\App\Http\Controllers\admin\PeopleController::class, 'index']
            )
                ->middleware('admin.permission:people.view')
                ->name('people');

            Route::get(
                '/people/create',
                [\App\Http\Controllers\admin\PeopleController::class, 'create']
            )
                ->middleware('admin.permission:people.create')
                ->name('people.create');

            Route::post(
                '/people',
                [\App\Http\Controllers\admin\PeopleController::class, 'store']
            )
                ->middleware('admin.permission:people.create')
                ->name('people.store');

            Route::get(
                '/people/{id}/edit',
                [\App\Http\Controllers\admin\PeopleController::class, 'edit']
            )
                ->middleware('admin.permission:people.edit')
                ->name('people.edit');

            Route::put(
                '/people/{id}',
                [\App\Http\Controllers\admin\PeopleController::class, 'update']
            )
                ->middleware('admin.permission:people.edit')
                ->name('people.update');

            Route::delete(
                '/people/{id}',
                [\App\Http\Controllers\admin\PeopleController::class, 'destroy']
            )
                ->middleware('admin.permission:people.delete')
                ->name('people.destroy');


            // Collections
           
            Route::get('/collections', [\App\Http\Controllers\admin\CollectionController::class, 'index'])
                ->middleware('admin.permission:movies.view')
                ->name('collections');

            Route::get('/collections/create', [\App\Http\Controllers\admin\CollectionController::class, 'create'])
                ->middleware('admin.permission:movies.create')
                ->name('collections.create');

            Route::post('/collections', [\App\Http\Controllers\admin\CollectionController::class, 'store'])
                ->middleware('admin.permission:movies.create')
                ->name('collections.store');

            Route::get('/collections/{collection}/edit', [\App\Http\Controllers\admin\CollectionController::class, 'edit'])
                ->middleware('admin.permission:movies.edit')
                ->name('collections.edit');

            Route::put('/collections/{collection}', [\App\Http\Controllers\admin\CollectionController::class, 'update'])
                ->middleware('admin.permission:movies.edit')
                ->name('collections.update');

            
            // Xem Collection
            Route::get('/collections/{collection}', [\App\Http\Controllers\admin\CollectionController::class, 'show'])
                ->middleware('admin.permission:movies.view')
                ->name('collections.show');
            // Xóa phim khỏi Collection
            Route::delete(
                '/collections/{collection}/movies/{movie}',
                [\App\Http\Controllers\admin\CollectionController::class, 'removeMovie']
            )
                ->middleware('admin.permission:movies.edit')
                ->name('collections.movies.remove');

            Route::delete('/collections/{collection}', [\App\Http\Controllers\admin\CollectionController::class, 'destroy'])
                ->middleware('admin.permission:movies.delete')
                ->name('collections.destroy');

            /*
            |--------------------------------------------------------------------------
            | USER MANAGEMENT
            |--------------------------------------------------------------------------
            */

            // Danh sách user
            Route::get(
                '/users',
                [\App\Http\Controllers\admin\UserController::class, 'users']
            )
                ->middleware('admin.permission:users.view')
                ->name('users');

            // User detail
            Route::get(
                '/users/{user}',
                [\App\Http\Controllers\admin\UserController::class, 'show']
            )
                ->middleware('admin.permission:users.view')
                ->name('users.show');

            // Ban
            Route::post(
                '/users/{user}/ban',
                [\App\Http\Controllers\admin\UserController::class, 'ban']
            )
                ->middleware('admin.permission:users.ban')
                ->name('users.ban');

            // Unban
            Route::patch(
                '/users/{user}/unban',
                [\App\Http\Controllers\admin\UserController::class, 'unban']
            )
                ->middleware('admin.permission:users.unban')
                ->name('users.unban');

            // Banned users
            Route::get(
                '/banned',
                [\App\Http\Controllers\admin\UserController::class, 'banned']
            )
                ->middleware('admin.permission:users.view')
                ->name('banned');


            /*
            |--------------------------------------------------------------------------
            | ROLE MANAGEMENT
            |--------------------------------------------------------------------------
            */

            // Danh sách Role
            Route::get(
                '/roles',
                [\App\Http\Controllers\admin\RoleController::class, 'index']
            )
                ->middleware('admin.permission:roles.view')
                ->name('roles.index');

            // Tạo Role
            Route::get(
                '/roles/create',
                [\App\Http\Controllers\admin\RoleController::class, 'create']
            )
                ->middleware('admin.permission:roles.create')
                ->name('roles.create');

            Route::post(
                '/roles',
                [\App\Http\Controllers\admin\RoleController::class, 'store']
            )
                ->middleware('admin.permission:roles.create')
                ->name('roles.store');

            // Sửa Role
            Route::get(
                '/roles/{role}/edit',
                [\App\Http\Controllers\admin\RoleController::class, 'edit']
            )
                ->middleware('admin.permission:roles.edit')
                ->name('roles.edit');

            Route::put(
                '/roles/{role}',
                [\App\Http\Controllers\admin\RoleController::class, 'update']
            )
                ->middleware('admin.permission:roles.edit')
                ->name('roles.update');

            // Xóa Role
            Route::delete(
                '/roles/{role}',
                [\App\Http\Controllers\admin\RoleController::class, 'destroy']
            )
                ->middleware('admin.permission:roles.delete')
                ->name('roles.destroy');


            // Danh sách Permission
            // Route::get(
            //     '/permissions',
            //     [\App\Http\Controllers\admin\UserController::class, 'permissions']
            // )
            //     ->middleware('admin.permission:roles.view')
            //     ->name('permissions');

            /*
            |--------------------------------------------------------------------------
            | Permissions
            |--------------------------------------------------------------------------
            */

            Route::get('/permissions', [
                \App\Http\Controllers\admin\PermissionController::class,
                'index'
            ])
                ->middleware('admin.permission:roles.view')
                ->name('permissions');


            Route::get('/permissions/create', [
                \App\Http\Controllers\admin\PermissionController::class,
                'create'
            ])
                ->middleware('admin.permission:roles.create')
                ->name('permissions.create');


            Route::post('/permissions', [
                \App\Http\Controllers\admin\PermissionController::class,
                'store'
            ])
                ->middleware('admin.permission:roles.create')
                ->name('permissions.store');


            Route::get('/permissions/{permission}', [
                \App\Http\Controllers\admin\PermissionController::class,
                'show'
            ])
                ->middleware('admin.permission:roles.view')
                ->name('permissions.show');


            Route::get('/permissions/{permission}/edit', [
                \App\Http\Controllers\admin\PermissionController::class,
                'edit'
            ])
                ->middleware('admin.permission:roles.edit')
                ->name('permissions.edit');


            Route::put('/permissions/{permission}', [
                \App\Http\Controllers\admin\PermissionController::class,
                'update'
            ])
                ->middleware('admin.permission:roles.edit')
                ->name('permissions.update');


            Route::delete('/permissions/{permission}', [
               \App\Http\Controllers\admin\PermissionController::class,
                'destroy'
            ])
                ->middleware('admin.permission:roles.delete')
                ->name('permissions.destroy');

            Route::get('/matrix', [
                \App\Http\Controllers\admin\MatrixController::class,
                'index'
            ])->middleware('admin.permission:roles.view')->name('matrix.index');

            Route::put('/permissions/role/{role}', [
                \App\Http\Controllers\admin\MatrixController::class,
                'update'
            ])->middleware('admin.permission:roles.edit')->name('matrix.update');

            /*
            |--------------------------------------------------------------------------
            | ADMINS MANAGEMENT
            |--------------------------------------------------------------------------
            */

            
            Route::get('/admins', [
                \App\Http\Controllers\admin\AdminController::class,
                'index'
                ])
                ->middleware('admin.permission:staff.view')
                ->name('admins');
            
            // Form tạo Admin
            Route::get('/admins/create', [
                \App\Http\Controllers\admin\AdminController::class,
                'create'
            ])
                ->middleware('admin.permission:staff.create')
                ->name('admins.create');


            // Tạo Admin
            Route::post('/admins', [
                \App\Http\Controllers\admin\AdminController::class,
                'store'
            ])
                ->middleware('admin.permission:staff.create')
                ->name('admins.store');


            // Form sửa Admin
            Route::get('/admins/{admin}/edit', [
                \App\Http\Controllers\admin\AdminController::class,
                'edit'
            ])
                ->middleware('admin.permission:staff.edit')
                ->name('admins.edit');


            // Cập nhật Admin
            Route::put('/admins/{admin}', [
                \App\Http\Controllers\admin\AdminController::class,
                'update'
            ])
                ->middleware('admin.permission:staff.edit')
                ->name('admins.update');


            // Xóa Admin
            Route::delete('/admins/{admin}', [
                \App\Http\Controllers\admin\AdminController::class,
                'destroy'
            ])
                ->middleware('admin.permission:staff.delete')
                ->name('admins.destroy');

            /*
            |--------------------------------------------------------------------------
            | INTERACT MANAGEMENT
            |--------------------------------------------------------------------------
            */

            // Comments
            Route::get('/comments', [
                \App\Http\Controllers\admin\CommentController::class,
                'index'
            ])->middleware('admin.permission:comments.view')->name('comments.index');

            Route::post('/comments/{comment}/approve', [
                \App\Http\Controllers\admin\CommentController::class,
                'approve'
            ])->middleware('admin.permission:comments.moderate')->name('comments.approve');

            Route::post('/comments/{comment}/spam', [
                \App\Http\Controllers\admin\CommentController::class,
                'spam'
            ])->middleware('admin.permission:comments.moderate')->name('comments.spam');

            Route::post('/comments/{comment}/hide', [
                \App\Http\Controllers\admin\CommentController::class,
                'hide'
            ])->middleware('admin.permission:comments.moderate')->name('comments.hide');

            Route::post('/comments/{comment}/show', [
                \App\Http\Controllers\admin\CommentController::class,
                'show'
            ])->middleware('admin.permission:comments.moderate')->name('comments.show');

            Route::delete('/comments/{comment}', [
                \App\Http\Controllers\admin\CommentController::class,
                'destroy'
            ])->middleware('admin.permission:comments.delete')->name('comments.destroy');


            // Ratings
            Route::get('/ratings', [\App\Http\Controllers\admin\RatingController::class, 'index'])
            ->middleware('admin.permission:ratings.view')
                ->name('ratings.index');

            Route::delete('/ratings/{rating}', [\App\Http\Controllers\admin\RatingController::class, 'destroy'])
            ->middleware('admin.permission:ratings.delete')
                ->name('ratings.destroy');
            // Reports
            Route::get('/reports', [\App\Http\Controllers\admin\ReportController::class, 'index'])
                ->middleware('admin.permission:reports.view')
                ->name('reports.index');

            Route::patch('/reports/{report}/status', [\App\Http\Controllers\admin\ReportController::class, 'updateStatus'])
                ->middleware('admin.permission:reports.manage')
                ->name('reports.update-status');

            Route::delete('/reports/{report}', [\App\Http\Controllers\admin\ReportController::class, 'destroy'])
                ->middleware('admin.permission:reports.manage')
                ->name('reports.destroy');

            /*
            |--------------------------------------------------------------------------
            | STATISTICAL MANAGEMENT
            |--------------------------------------------------------------------------
            */
            
            Route::get('/views', [\App\Http\Controllers\admin\MovieViewController::class, 'index'])
                ->middleware('admin.permission:statistics.view')
                ->name('views.index');  
                
            Route::get('/traffic', [\App\Http\Controllers\admin\TrafficController::class, 'index'])
                ->middleware('admin.permission:statistics.view')
                ->name('statistical.traffic');

            Route::get('/history', [\App\Http\Controllers\admin\WatchHistoryController::class, 'index'])
                ->middleware('admin.permission:statistics.view')
                ->name('history.index');
                
            Route::get('/popular-movies', [\App\Http\Controllers\admin\PopularMovieController::class, 'index'])
                ->middleware('admin.permission:statistics.view')
                ->name('statistical.popular_stats');

            Route::get('/user-statistics', [
                \App\Http\Controllers\admin\UserStatisticsController::class,'index'])
                ->middleware('admin.permission:statistics.view')
                ->name('statistical.user_stats');


            /*
            |--------------------------------------------------------------------------
            | INTERFACE MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/interface/banners',
                [\App\Http\Controllers\admin\InterfaceController::class, 'banners']
            )
                ->middleware('admin.permission:interface.banners')
                ->name('interface.banners');

            Route::get(
                '/interface/home-page',
                [\App\Http\Controllers\admin\InterfaceController::class, 'homepage']
            )
                ->middleware('admin.permission:interface.homepage')
                ->name('interface.homepage');

            Route::get('/menus', [MenuController::class, 'index'])
                ->middleware('admin.permission:interface.menus')
                ->name('menus.index');

            Route::get('/menus/create', [MenuController::class, 'create'])
                ->middleware('admin.permission:interface.menus')
                ->name('menus.create');

            Route::post('/menus', [MenuController::class, 'store'])
                ->middleware('admin.permission:interface.menus')
                ->name('menus.store');

            Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])
                ->middleware('admin.permission:interface.menus')
                ->name('menus.edit');

            Route::put('/menus/{menu}', [MenuController::class, 'update'])
                ->middleware('admin.permission:interface.menus')
                ->name('menus.update');

            Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])
                ->middleware('admin.permission:interface.menus')
                ->name('menus.destroy');

            Route::patch('/menus/{menu}/toggle', [MenuController::class, 'toggle'])
                ->middleware('admin.permission:interface.menus')
                ->name('menus.toggle');

        
            Route::post('/pages/upload-image', [PageController::class, 'uploadImage'])
                ->middleware('admin.permission:interface.pages')
                ->name('pages.upload-image');
            Route::patch('/pages/{page}/toggle-status', [PageController::class, 'toggleStatus'])
                ->middleware('admin.permission:interface.pages')
                ->name('pages.toggle-status');

            Route::resource('pages', PageController::class)
                ->middleware('admin.permission:interface.pages')
                ->except(['show']);

            Route::resource('news', \App\Http\Controllers\Admin\NewsController::class)
                ->middleware('admin.permission:news.manage')
                ->except(['show']);


            /*
            |--------------------------------------------------------------------------
            | NOTIFICATION MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/notifications',
                [\App\Http\Controllers\admin\NotificationController::class, 'notification']
            )
                ->middleware('admin.permission:notifications.view')
                ->name('notifications');

            Route::post(
                '/notifications/send',
                [\App\Http\Controllers\admin\NotificationController::class, 'sendNotification']
            )
                ->middleware('admin.permission:notifications.create')
                ->name('notifications.send');

            Route::get(
                '/notifications/user-count',
                [\App\Http\Controllers\admin\NotificationController::class, 'previewUserCount']
            )
                ->middleware('admin.permission:notifications.view')
                ->name('notifications.user-count');

            Route::get(
                '/notifications/search-users',
                [\App\Http\Controllers\admin\NotificationController::class, 'searchUsers']
            )
                ->middleware('admin.permission:notifications.view')
                ->name('notifications.search-users');

            Route::delete(
                '/notifications/campaigns/{id}',
                [\App\Http\Controllers\admin\NotificationController::class, 'deleteCampaign']
            )
                ->middleware('admin.permission:notifications.delete')
                ->name('notifications.campaigns.delete');

            Route::get(
                '/email',
                [\App\Http\Controllers\admin\NotificationController::class, 'email']
            )
                ->middleware('admin.permission:email.view')
                ->name('email');

            Route::post(
                '/email/settings',
                [\App\Http\Controllers\admin\NotificationController::class, 'updateSmtp']
            )
                ->middleware('admin.permission:email.update')
                ->name('email.settings');

            Route::post(
                '/email/test',
                [\App\Http\Controllers\admin\NotificationController::class, 'sendTestEmail']
            )
                ->middleware('admin.permission:email.update')
                ->name('email.test');

            Route::get(
                '/email/preview-template',
                [\App\Http\Controllers\admin\NotificationController::class, 'previewTemplate']
            )
                ->middleware('admin.permission:email.view')
                ->name('email.preview');


            /*
            |--------------------------------------------------------------------------
            | SEO MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/seo',
                [\App\Http\Controllers\admin\SeoController::class, 'seo']
            )
                ->middleware('admin.permission:seo.manage')
                ->name('seo');

            Route::post(
                '/seo',
                [\App\Http\Controllers\admin\SeoController::class, 'updateSeo']
            )
                ->middleware('admin.permission:seo.manage')
                ->name('seo.update');

            Route::get(
                '/sitemap',
                [\App\Http\Controllers\admin\SeoController::class, 'siteMap']
            )
                ->middleware('admin.permission:sitemap.manage')
                ->name('sitemap');

            Route::post(
                '/sitemap/generate-all',
                [\App\Http\Controllers\admin\SeoController::class, 'generateAllSitemaps']
            )
                ->middleware('admin.permission:sitemap.manage')
                ->name('sitemap.generate-all');

            Route::post(
                '/sitemap/generate/{type}',
                [\App\Http\Controllers\admin\SeoController::class, 'generateSingleSitemap']
            )
                ->middleware('admin.permission:sitemap.manage')
                ->name('sitemap.generate-single');


            /*
            |--------------------------------------------------------------------------
            | PREMIUM MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/premium/coupons',
                [\App\Http\Controllers\admin\PremiumController::class, 'coupons']
            )
                ->middleware('admin.permission:premium.coupons')
                ->name('premium.coupons');

            Route::post(
                '/premium/coupons',
                [\App\Http\Controllers\admin\PremiumController::class, 'storeCoupon']
            )
                ->middleware('admin.permission:premium.coupons')
                ->name('premium.coupons.store');

            Route::patch(
                '/premium/coupons/{coupon}/toggle',
                [\App\Http\Controllers\admin\PremiumController::class, 'toggleCoupon']
            )
                ->middleware('admin.permission:premium.coupons')
                ->name('premium.coupons.toggle');

            Route::delete(
                '/premium/coupons/{coupon}',
                [\App\Http\Controllers\admin\PremiumController::class, 'deleteCoupon']
            )
                ->middleware('admin.permission:premium.coupons')
                ->name('premium.coupons.delete');

            Route::get(
                '/premium/promotions',
                [\App\Http\Controllers\admin\PremiumController::class, 'promotions']
            )
                ->middleware('admin.permission:premium.promotions')
                ->name('premium.promotions');

            Route::post(
                '/premium/promotions',
                [\App\Http\Controllers\admin\PremiumController::class, 'storePromotion']
            )
                ->middleware('admin.permission:premium.promotions')
                ->name('premium.promotions.store');

            Route::patch(
                '/premium/promotions/{promotion}/toggle',
                [\App\Http\Controllers\admin\PremiumController::class, 'togglePromotion']
            )
                ->middleware('admin.permission:premium.promotions')
                ->name('premium.promotions.toggle');

            Route::delete(
                '/premium/promotions/{promotion}',
                [\App\Http\Controllers\admin\PremiumController::class, 'deletePromotion']
            )
                ->middleware('admin.permission:premium.promotions')
                ->name('premium.promotions.delete');

            Route::get(
                '/premium/subscriptions',
                [\App\Http\Controllers\admin\PremiumController::class, 'subscriptions']
            )
                ->middleware('admin.permission:premium.subscriptions')
                ->name('premium.subscriptions');

            Route::get(
                '/premium/transactions',
                [\App\Http\Controllers\admin\PremiumController::class, 'transactions']
            )
                ->middleware('admin.permission:premium.transactions')
                ->name('premium.transactions');

            Route::get(
                '/premium/plans',
                [\App\Http\Controllers\admin\PremiumController::class, 'plans']
            )
                ->middleware('admin.permission:premium.plans')
                ->name('premium.plans');

            Route::post(
                '/premium/plans/{code}',
                [\App\Http\Controllers\admin\PremiumController::class, 'storePlan']
            )
                ->middleware('admin.permission:premium.plans')
                ->name('premium.plans.store');

            Route::put(
                '/premium/plans/{plan}',
                [\App\Http\Controllers\admin\PremiumController::class, 'updatePlan']
            )
                ->middleware('admin.permission:premium.plans')
                ->name('premium.plans.update');


            /*
            |--------------------------------------------------------------------------
            | SYSTEM MANAGEMENT
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/system/activity-log',
                [\App\Http\Controllers\admin\SystemController::class, 'activityLog']
            )
                ->middleware('admin.permission:system.activity_log')
                ->name('system.activity-log');

            Route::get(
                '/system/api',
                [\App\Http\Controllers\admin\SystemController::class, 'api']
            )
                ->middleware('admin.permission:system.api')
                ->name('system.api');

            Route::post(
                '/system/api',
                [\App\Http\Controllers\admin\SystemController::class, 'storeApiKey']
            )
                ->middleware('admin.permission:system.api')
                ->name('system.api.store');

            Route::post(
                '/system/api/payment-config',
                [\App\Http\Controllers\admin\SystemController::class, 'savePaymentConfig']
            )
                ->middleware('admin.permission:system.api')
                ->name('system.api.payment-config');

            Route::put(
                '/system/api/{id}',
                [\App\Http\Controllers\admin\SystemController::class, 'updateApiKey']
            )
                ->middleware('admin.permission:system.api')
                ->name('system.api.update');

            Route::post(
                '/system/api/{id}/revoke',
                [\App\Http\Controllers\admin\SystemController::class, 'revokeApiKey']
            )
                ->middleware('admin.permission:system.api')
                ->name('system.api.revoke');

            Route::post(
                '/system/api/{id}/activate',
                [\App\Http\Controllers\admin\SystemController::class, 'activateApiKey']
            )
                ->middleware('admin.permission:system.api')
                ->name('system.api.activate');

            Route::delete(
                '/system/api/{id}',
                [\App\Http\Controllers\admin\SystemController::class, 'deleteApiKey']
            )
                ->middleware('admin.permission:system.api')
                ->name('system.api.delete');

            // Bulk toggle status (active ↔ revoked) for selected API keys
            Route::post(
                '/system/api/bulk-toggle',
                [\App\Http\Controllers\admin\SystemController::class, 'bulkToggle']
            )
                ->middleware('admin.permission:system.api')
                ->name('system.api.bulk-toggle');

            Route::post(
                '/system/api/sync',
                [\App\Http\Controllers\admin\SystemController::class, 'syncProjectApis']
            )
                ->middleware('admin.permission:system.api')
                ->name('system.api.sync');

            Route::get(
                '/system/cache',
                [\App\Http\Controllers\admin\SystemController::class, 'cache']
            )
                ->middleware('admin.permission:system.cache')
                ->name('system.cache');

            Route::post(
                '/system/cache/clear',
                [\App\Http\Controllers\admin\SystemController::class, 'clearCache']
            )
                ->middleware('admin.permission:system.cache')
                ->name('system.cache.clear');

            Route::post(
                '/system/cache/clear-all',
                [\App\Http\Controllers\admin\SystemController::class, 'clearAllCache']
            )
                ->middleware('admin.permission:system.cache')
                ->name('system.cache.clear-all');

            Route::get(
                '/system/backup',
                [\App\Http\Controllers\admin\SystemController::class, 'backup']
            )
                ->middleware('admin.permission:system.backup')
                ->name('system.backup');

            Route::post(
                '/system/backup',
                [\App\Http\Controllers\admin\SystemController::class, 'createBackup']
            )
                ->middleware('admin.permission:system.backup')
                ->name('system.backup.create');

            Route::get(
                '/system/backup/{filename}/download',
                [\App\Http\Controllers\admin\SystemController::class, 'downloadBackup']
            )
                ->middleware('admin.permission:system.backup')
                ->name('system.backup.download');

            Route::post(
                '/system/backup/{filename}/restore',
                [\App\Http\Controllers\admin\SystemController::class, 'restoreBackup']
            )
                ->middleware('admin.permission:system.backup')
                ->name('system.backup.restore');

            Route::delete(
                '/system/backup/{filename}',
                [\App\Http\Controllers\admin\SystemController::class, 'deleteBackup']
            )
                ->middleware('admin.permission:system.backup')
                ->name('system.backup.delete');

            Route::get(
                '/system/cron',
                [\App\Http\Controllers\admin\SystemController::class, 'cron']
            )
                ->middleware('admin.permission:system.cron')
                ->name('system.cron');

            Route::post(
                '/system/cron/{job}/run',
                [\App\Http\Controllers\admin\SystemController::class, 'runCronJob']
            )
                ->middleware('admin.permission:system.cron')
                ->name('system.cron.run');

            Route::get(
                '/system/storage',
                [\App\Http\Controllers\admin\SystemController::class, 'storage']
            )
                ->middleware('admin.permission:system.storage')
                ->name('system.storage');


            /*
            |--------------------------------------------------------------------------
            | SETTINGS
            |--------------------------------------------------------------------------
            */

            Route::get(
                '/setting',
                [\App\Http\Controllers\admin\SettingController::class, 'index']
            )
                ->middleware('admin.permission:settings.manage')
                ->name('setting.index');

            Route::post(
                '/setting',
                [\App\Http\Controllers\admin\SettingController::class, 'update']
            )
                ->middleware('admin.permission:settings.manage')
                ->name('setting.update');


            Route::get(
                '/videos/upload',
                [VideoUploadController::class, 'create']
            )
                ->middleware('admin.permission:videos.upload')
                ->name('videos.upload');

            Route::post(
                '/videos/upload',
                [VideoUploadController::class, 'store']
            )
                ->middleware('admin.permission:videos.upload')
                ->name('videos.upload.store');

            Route::post(
                '/videos/upload/seasons',
                [VideoUploadController::class, 'storeUploadSeason']
            )->middleware('admin.permission:videos.upload')->name('videos.upload.seasons.store');

            Route::post(
                '/videos/upload/episodes',
                [VideoUploadController::class, 'storeUploadEpisode']
            )->middleware('admin.permission:videos.upload')->name('videos.upload.episodes.store');

            Route::get(
                '/videos/processing/{processingJob}/status',
                [VideoUploadController::class, 'status']
            )
                ->middleware('admin.permission:videos.view')
                ->name('videos.processing.status');


            // forum manage

            Route::prefix('forum')
                ->name('forum.')
                ->group(function () {

                    /*
                    |--------------------------------------------------------------------------
                    | Forum Dashboard
                    |--------------------------------------------------------------------------
                    */

                    Route::get('/', [App\Http\Controllers\admin\ForumController::class, 'index'])
                        ->middleware('admin.permission:forum.view')
                        ->name('index');


                    /*
                    |--------------------------------------------------------------------------
                    | Forum Categories
                    |--------------------------------------------------------------------------
                    */

                    Route::get('/categories', [App\Http\Controllers\admin\ForumController::class, 'categories'])
                        ->middleware('admin.permission:forum.categories.view')
                        ->name('categories');

                    Route::get('/categories/create', [App\Http\Controllers\admin\ForumController::class, 'createCategory'])
                        ->middleware('admin.permission:forum.categories.manage')
                        ->name('categories.create');

                    Route::post('/categories', [App\Http\Controllers\admin\ForumController::class, 'storeCategory'])
                        ->middleware('admin.permission:forum.categories.manage')
                        ->name('categories.store');

                    Route::get('/categories/{category}/edit', [App\Http\Controllers\admin\ForumController::class, 'editCategory'])
                        ->middleware('admin.permission:forum.categories.manage')
                        ->name('categories.edit');

                    Route::put('/categories/{category}', [App\Http\Controllers\admin\ForumController::class, 'updateCategory'])
                        ->middleware('admin.permission:forum.categories.manage')
                        ->name('categories.update');

                    Route::patch('/categories/{category}/toggle', [App\Http\Controllers\admin\ForumController::class, 'toggleCategory'])
                        ->middleware('admin.permission:forum.categories.manage')
                        ->name('categories.toggle');

                    Route::delete('/categories/{category}', [App\Http\Controllers\admin\ForumController::class, 'destroyCategory'])
                        ->middleware('admin.permission:forum.categories.manage')
                        ->name('categories.destroy');


                    /*
                    |--------------------------------------------------------------------------
                    | Forum Posts
                    |--------------------------------------------------------------------------
                    */

                    Route::get('/posts', [App\Http\Controllers\admin\ForumController::class, 'posts'])
                        ->middleware('admin.permission:forum.posts.view')
                        ->name('posts');

                    Route::get('/posts/{post}/edit', [App\Http\Controllers\admin\ForumController::class, 'editPost'])
                        ->middleware('admin.permission:forum.posts.manage')
                        ->name('posts.edit');

                    Route::put('/posts/{post}', [App\Http\Controllers\admin\ForumController::class, 'updatePost'])
                        ->middleware('admin.permission:forum.posts.manage')
                        ->name('posts.update');

                    Route::patch('/posts/{post}/pin', [App\Http\Controllers\admin\ForumController::class, 'togglePostPin'])
                        ->middleware('admin.permission:forum.posts.manage')
                        ->name('posts.pin');

                    Route::patch('/posts/{post}/lock', [App\Http\Controllers\admin\ForumController::class, 'togglePostLock'])
                        ->middleware('admin.permission:forum.posts.manage')
                        ->name('posts.lock');

                    Route::delete('/posts/{post}', [App\Http\Controllers\admin\ForumController::class, 'deletePost'])
                        ->middleware('admin.permission:forum.posts.manage')
                        ->name('posts.destroy');


                    /*
                    |--------------------------------------------------------------------------
                    | Forum Comments
                    |--------------------------------------------------------------------------
                    */

                    Route::get('/comments', [App\Http\Controllers\admin\ForumController::class, 'comments'])
                        ->middleware('admin.permission:forum.comments.view')
                        ->name('comments');

                    Route::patch('/comments/{comment}/status', [App\Http\Controllers\admin\ForumController::class, 'updateCommentStatus'])
                        ->middleware('admin.permission:forum.comments.manage')
                        ->name('comments.status');

                    Route::delete('/comments/{comment}', [App\Http\Controllers\admin\ForumController::class, 'deleteComment'])
                        ->middleware('admin.permission:forum.comments.manage')
                        ->name('comments.destroy');
                });

        });

    });


Route::get('/{slug}', [ \App\Http\Controllers\client\PageController::class, 'show'])
    ->name('client.pages.show');
