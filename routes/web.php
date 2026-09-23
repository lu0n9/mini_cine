<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\admin\IndexController;
use App\Http\Controllers\client\HomeController;
use App\Http\Controllers\client\MovieController;
use App\Http\Controllers\client\PeopleController;
use App\Http\Controllers\client\RatingController;
use App\Http\Controllers\client\CommentController;
use App\Http\Controllers\client\FavoriteController;
use App\Http\Controllers\admin\VideoUploadController;
use App\Http\Controllers\admin\MenuController;
use App\Models\Admin;


Route::get('/test-admin', function () {
    return view('admin.index');
});

/*
|--------------------------------------------------------------------------
| CLIENT
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// Danh sách phim
Route::get('/movies', function () {
    return view('client.pages.movies.movies');
})->name('movies.index');

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

Route::get('/dien-vien/{slug}', [PeopleController::class, 'show'])
    ->name('people.show');

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
    Route::get('/danh-sach-cua-toi',[FavoriteController::class, 'index'])
    ->name('favorites.index');

    Route::post('/favorites/{movie}/toggle',[FavoriteController::class, 'toggle'])
    ->name('favorites.toggle');


    // report
    Route::post('/reports', [App\Http\Controllers\client\ReportController::class, 'store'])
        ->name('reports.store');

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

        Route::middleware('auth:admin')->group(function () {

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
                PermissionController::class,
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
            ])->name('matrix.index');

            Route::put('/permissions/role/{role}', [
                \App\Http\Controllers\admin\MatrixController::class,
                'update'
            ])->name('matrix.update');

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
            ])->name('comments.index');

            Route::post('/comments/{comment}/approve', [
                \App\Http\Controllers\admin\CommentController::class,
                'approve'
            ])->name('comments.approve');

            Route::post('/comments/{comment}/spam', [
                \App\Http\Controllers\admin\CommentController::class,
                'spam'
            ])->name('comments.spam');

            Route::post('/comments/{comment}/hide', [
                \App\Http\Controllers\admin\CommentController::class,
                'hide'
            ])->name('comments.hide');

            Route::post('/comments/{comment}/show', [
                \App\Http\Controllers\admin\CommentController::class,
                'show'
            ])->name('comments.show');

            Route::delete('/comments/{comment}', [
                \App\Http\Controllers\admin\CommentController::class,
                'destroy'
            ])->name('comments.destroy');


            // Ratings
            Route::get('/ratings', [\App\Http\Controllers\admin\RatingController::class, 'index'])
            ->middleware('admin.permission:ratings.view')
                ->name('ratings.index');

            Route::delete('/ratings/{rating}', [\App\Http\Controllers\admin\RatingController::class, 'destroy'])
            ->middleware('admin.permission:ratings.view')
                ->name('ratings.destroy');
            // Reports
            Route::get('/reports', [\App\Http\Controllers\admin\ReportController::class, 'index'])
                ->name('reports.index');

            Route::patch('/reports/{report}/status', [\App\Http\Controllers\admin\ReportController::class, 'updateStatus'])
                ->name('reports.update-status');

            Route::delete('/reports/{report}', [\App\Http\Controllers\admin\ReportController::class, 'destroy'])
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
                    ->name('menus.create');

                Route::post('/menus', [MenuController::class, 'store'])
                    ->name('menus.store');

                Route::get('/menus/{menu}/edit', [MenuController::class, 'edit'])
                    ->name('menus.edit');

                Route::put('/menus/{menu}', [MenuController::class, 'update'])
                    ->name('menus.update');

                Route::delete('/menus/{menu}', [MenuController::class, 'destroy'])
                    ->name('menus.destroy');

                Route::patch('/menus/{menu}/toggle', [MenuController::class, 'toggle'])
                    ->name('menus.toggle');

            Route::get(
                '/interface/pages',
                [\App\Http\Controllers\admin\InterfaceController::class, 'pages']
            )
                ->middleware('admin.permission:interface.pages')
                ->name('interface.pages');


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

            Route::get(
                '/email',
                [\App\Http\Controllers\admin\NotificationController::class, 'email']
            )
                ->middleware('admin.permission:email.manage')
                ->name('email');


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

            Route::get(
                '/sitemap',
                [\App\Http\Controllers\admin\SeoController::class, 'siteMap']
            )
                ->middleware('admin.permission:sitemap.manage')
                ->name('sitemap');


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

            Route::get(
                '/system/cache',
                [\App\Http\Controllers\admin\SystemController::class, 'cache']
            )
                ->middleware('admin.permission:system.cache')
                ->name('system.cache');

            Route::get(
                '/system/backup',
                [\App\Http\Controllers\admin\SystemController::class, 'backup']
            )
                ->middleware('admin.permission:system.backup')
                ->name('system.backup');

            Route::get(
                '/system/cron',
                [\App\Http\Controllers\admin\SystemController::class, 'cron']
            )
                ->middleware('admin.permission:system.cron')
                ->name('system.cron');

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
                // ->middleware('admin.permission:settings.manage')
                ->name('setting.index');


            Route::get(
                '/videos/upload',
                [VideoUploadController::class, 'create']
            )
                // ->middleware('admin.permission:servers.create')
                ->name('videos.upload');

            Route::post(
                '/videos/upload',
                [VideoUploadController::class, 'store']
            )
                // ->middleware('admin.permission:servers.create')
                ->name('videos.upload.store');

            Route::get(
                '/videos/processing/{processingJob}/status',
                [VideoUploadController::class, 'status']
            )
                // ->middleware('admin.permission:servers.view')
                ->name('videos.processing.status');

        });

    });