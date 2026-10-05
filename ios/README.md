# Mini Cine iOS

SwiftUI iOS app using the Laravel project as its backend.

## Open and run

1. Open `MiniCine.xcodeproj` in Xcode.
2. Start Laravel from the project root with `php artisan serve`.
3. Run the `MiniCine` scheme on an iOS Simulator.

The app points to `http://127.0.0.1:8000/api/v1` by default. For a physical iPhone, change the host in `MiniCine/APIClient.swift` to the Mac's LAN IP, then start Laravel with `php artisan serve --host=0.0.0.0` while both devices are on the same network.

## Included API

- `GET /api/v1/home` — featured, trending, and latest movies.
- `GET /api/v1/movies?search=...` — published movie list and search.
- `GET /api/v1/movies/{slug}` — movie details and episode list.

This first app slice covers browsing, search, and movie details. Account sign-in and video playback need separate API work before those flows can be used in the native app.
