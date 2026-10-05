import SwiftUI

struct HomeView: View {
    @State private var payload: HomePayload?
    @State private var searchText = ""
    @State private var searchResults: [Movie] = []
    @State private var isSearching = false
    @State private var isLoading = true
    @State private var errorMessage: String?
    private let usesPreviewData: Bool
    private let api = APIClient()

    init(previewPayload: HomePayload? = nil) {
        usesPreviewData = previewPayload != nil
        _payload = State(initialValue: previewPayload)
        _isLoading = State(initialValue: previewPayload == nil)
    }

    var body: some View {
        NavigationStack {
            ScrollView(showsIndicators: false) {
                VStack(alignment: .leading, spacing: 30) {
                    header
                    searchField

                    if isSearching {
                        searchSection
                    } else if let payload {
                        if let featured = payload.featured { featuredHero(featured) }
                        movieRail("Top 10 thịnh hành tuần này", note: "Những bộ phim được yêu thích", movies: payload.trending, ranked: true)
                        latestGrid(payload.latest)
                    } else if isLoading {
                        ProgressView("Đang tải phim…")
                            .tint(.cineAccent)
                            .frame(maxWidth: .infinity, minHeight: 240)
                    }

                    if let errorMessage {
                        ContentUnavailableView(errorMessage, systemImage: "wifi.exclamationmark")
                            .frame(maxWidth: .infinity, minHeight: 180)
                    }
                }
                .padding(.top, 12)
                .padding(.bottom, 36)
            }
            .background(Color.cineBackground.ignoresSafeArea())
            .toolbar(.hidden, for: .navigationBar)
            .navigationDestination(for: Movie.self) { movie in
                MovieDetailView(slug: movie.slug)
            }
            .task {
                guard !usesPreviewData else { return }
                await loadHome()
            }
            .onChange(of: searchText) { _, value in
                Task { await search(value) }
            }
        }
        .tint(Color.cineAccent)
        .preferredColorScheme(.dark)
    }

    private var header: some View {
        HStack(spacing: 12) {
            HStack(alignment: .firstTextBaseline, spacing: 4) {
                Text("MINI")
                    .font(.system(size: 22, weight: .black, design: .rounded))
                    .tracking(1.1)
                    .foregroundStyle(.white)
                Text("CINE")
                    .font(.system(size: 22, weight: .black, design: .rounded))
                    .tracking(1.1)
                    .foregroundStyle(Color.cineAccent)
            }
            Spacer()
            Image(systemName: "person.crop.circle")
                .font(.system(size: 23, weight: .regular))
                .foregroundStyle(.white.opacity(0.8))
        }
        .padding(.horizontal, 20)
    }

    private var searchField: some View {
        HStack(spacing: 10) {
            Image(systemName: "magnifyingglass")
                .foregroundStyle(Color.cineMuted)
            TextField("Tìm phim, diễn viên, thể loại…", text: $searchText)
                .textInputAutocapitalization(.never)
                .autocorrectionDisabled()
                .foregroundStyle(.white)
            if !searchText.isEmpty {
                Button { searchText = "" } label: {
                    Image(systemName: "xmark.circle.fill")
                        .foregroundStyle(Color.cineMuted)
                }
                .accessibilityLabel("Xóa nội dung tìm kiếm")
            }
        }
        .padding(.horizontal, 15)
        .frame(height: 48)
        .background(Color.cineSurface, in: Capsule())
        .overlay(Capsule().stroke(Color.cineLine, lineWidth: 1))
        .padding(.horizontal, 20)
    }

    private func featuredHero(_ movie: Movie) -> some View {
        NavigationLink(value: movie) {
            ZStack(alignment: .bottomLeading) {
                PosterImage(url: movie.backdropURL ?? movie.posterURL)
                    .frame(height: 490)
                    .frame(maxWidth: .infinity)
                    .clipped()
                    .overlay {
                        LinearGradient(
                            stops: [
                                .init(color: .black.opacity(0.04), location: 0),
                                .init(color: .black.opacity(0.28), location: 0.38),
                                .init(color: Color.cineBackground.opacity(0.98), location: 1)
                            ],
                            startPoint: .top,
                            endPoint: .bottom
                        )
                    }
                VStack(alignment: .leading, spacing: 13) {
                    Label("ĐỀ XUẤT HÔM NAY", systemImage: "sparkle")
                        .font(.caption.weight(.bold))
                        .tracking(1.1)
                        .foregroundStyle(Color.cineAccent)
                    Text(movie.title)
                        .font(.system(size: 34, weight: .heavy, design: .rounded))
                        .foregroundStyle(.white)
                        .lineLimit(2)
                        .multilineTextAlignment(.leading)
                    metadata(movie)
                    HStack(spacing: 9) {
                        Image(systemName: "play.fill")
                        Text("Xem chi tiết")
                    }
                    .font(.subheadline.weight(.bold))
                    .foregroundStyle(Color.cineBackground)
                    .padding(.horizontal, 19)
                    .frame(height: 44)
                    .background(Color.cineAccent, in: Capsule())
                    .padding(.top, 4)
                }
                .padding(.horizontal, 22)
                .padding(.bottom, 28)
                .frame(maxWidth: .infinity, alignment: .leading)
            }
            .contentShape(Rectangle())
        }
        .buttonStyle(.plain)
        .accessibilityLabel("Xem chi tiết phim \(movie.title)")
    }

    private func movieRail(_ title: String, note: String, movies: [Movie], ranked: Bool = false) -> some View {
        VStack(alignment: .leading, spacing: 15) {
            sectionHeading(title, note: note)
            if movies.isEmpty {
                emptyMessage("Chưa có phim để hiển thị.")
            } else {
                ScrollView(.horizontal, showsIndicators: false) {
                    HStack(alignment: .top, spacing: 15) {
                        ForEach(Array(movies.enumerated()), id: \.element.id) { index, movie in
                            NavigationLink(value: movie) {
                                MoviePosterCard(movie: movie, rank: ranked ? index + 1 : nil, width: 166)
                            }
                            .buttonStyle(.plain)
                        }
                    }
                    .padding(.horizontal, 20)
                    .padding(.bottom, 4)
                }
            }
        }
    }

    private func latestGrid(_ movies: [Movie]) -> some View {
        VStack(alignment: .leading, spacing: 15) {
            sectionHeading("Mới cập nhật", note: "Phim mới nhất trên Mini Cine")
            if movies.isEmpty {
                emptyMessage("Chưa có phim mới.")
            } else {
                LazyVGrid(columns: [GridItem(.flexible(), spacing: 14), GridItem(.flexible(), spacing: 14)], alignment: .leading, spacing: 22) {
                    ForEach(movies) { movie in
                        NavigationLink(value: movie) {
                            MoviePosterCard(movie: movie, width: nil)
                        }
                        .buttonStyle(.plain)
                    }
                }
                .padding(.horizontal, 20)
            }
        }
    }

    private var searchSection: some View {
        VStack(alignment: .leading, spacing: 15) {
            sectionHeading("Kết quả tìm kiếm", note: "Phim phù hợp với từ khóa của bạn")
            if searchResults.isEmpty {
                emptyMessage("Không tìm thấy phim phù hợp.")
            } else {
                LazyVGrid(columns: [GridItem(.flexible(), spacing: 14), GridItem(.flexible(), spacing: 14)], alignment: .leading, spacing: 22) {
                    ForEach(searchResults) { movie in
                        NavigationLink(value: movie) {
                            MoviePosterCard(movie: movie, width: nil)
                        }
                        .buttonStyle(.plain)
                    }
                }
                .padding(.horizontal, 20)
            }
        }
    }

    private func sectionHeading(_ title: String, note: String) -> some View {
        HStack(alignment: .center, spacing: 10) {
            RoundedRectangle(cornerRadius: 2)
                .fill(Color.cineAccent)
                .frame(width: 3, height: 34)
            VStack(alignment: .leading, spacing: 2) {
                Text(title)
                    .font(.system(size: 19, weight: .bold, design: .rounded))
                    .foregroundStyle(.white)
                Text(note)
                    .font(.caption)
                    .foregroundStyle(Color.cineMuted)
            }
            Spacer(minLength: 0)
        }
        .padding(.horizontal, 20)
    }

    private func emptyMessage(_ text: String) -> some View {
        Text(text)
            .font(.subheadline)
            .foregroundStyle(Color.cineMuted)
            .padding(.horizontal, 20)
            .padding(.vertical, 10)
    }

    private func metadata(_ movie: Movie) -> some View {
        HStack(spacing: 9) {
            if let rating = movie.rating, rating > 0 {
                Label(rating.formatted(.number.precision(.fractionLength(1))), systemImage: "star.fill")
                    .foregroundStyle(Color.cineAccent)
            }
            if let year = movie.releaseYear { Text(String(year)) }
            if let type = movie.type { Text(type == "series" ? "Phim bộ" : "Phim lẻ") }
            if let genre = movie.genres.first { Text(genre).lineLimit(1) }
        }
        .font(.caption.weight(.medium))
        .foregroundStyle(.white.opacity(0.76))
    }

    @MainActor private func loadHome() async {
        do { payload = try await api.home(); errorMessage = nil }
        catch { errorMessage = error.localizedDescription }
        isLoading = false
    }

    @MainActor private func search(_ value: String) async {
        let trimmed = value.trimmingCharacters(in: .whitespacesAndNewlines)
        guard !trimmed.isEmpty else { isSearching = false; searchResults = []; return }
        isSearching = true
        do { searchResults = try await api.movies(search: trimmed); errorMessage = nil }
        catch { searchResults = []; errorMessage = error.localizedDescription }
    }
}

private struct MoviePosterCard: View {
    let movie: Movie
    let rank: Int?
    let width: CGFloat?

    init(movie: Movie, rank: Int? = nil, width: CGFloat?) {
        self.movie = movie
        self.rank = rank
        self.width = width
    }

    var body: some View {
        VStack(alignment: .leading, spacing: 7) {
            ZStack(alignment: .topLeading) {
                PosterImage(url: movie.posterURL)
                    .aspectRatio(2 / 3, contentMode: .fit)
                    .frame(maxWidth: .infinity)
                    .clipShape(RoundedRectangle(cornerRadius: 10))
                    .overlay(RoundedRectangle(cornerRadius: 10).stroke(Color.cineLine, lineWidth: 1))
                if let rank {
                    Text("\(rank)")
                        .font(.system(size: 34, weight: .black, design: .rounded))
                        .foregroundStyle(.white)
                        .shadow(color: .black.opacity(0.9), radius: 5, y: 2)
                        .padding(9)
                }
            }
            Text(movie.title)
                .font(.system(size: 14, weight: .semibold))
                .foregroundStyle(.white)
                .lineLimit(1)
            HStack(spacing: 5) {
                if let year = movie.releaseYear { Text(String(year)) }
                if movie.releaseYear != nil && !movie.genres.isEmpty { Text("·") }
                Text(movie.genres.first ?? (movie.type == "series" ? "Phim bộ" : "Phim lẻ"))
                    .lineLimit(1)
            }
            .font(.caption)
            .foregroundStyle(Color.cineMuted)
        }
        .frame(width: width)
        .frame(maxWidth: width == nil ? .infinity : nil, alignment: .leading)
        .contentShape(Rectangle())
    }
}

extension Color {
    static let cineBackground = Color(red: 8 / 255, green: 9 / 255, blue: 10 / 255)
    static let cineSurface = Color(red: 20 / 255, green: 22 / 255, blue: 26 / 255)
    static let cineLine = Color.white.opacity(0.10)
    static let cineMuted = Color(red: 139 / 255, green: 143 / 255, blue: 150 / 255)
    static let cineAccent = Color(red: 169 / 255, green: 133 / 255, blue: 1)
}

struct HomeView_Previews: PreviewProvider {
    static var previews: some View {
        let first = Movie(id: 1, slug: "house-of-the-dragon", title: "House of the Dragon", posterURL: nil, backdropURL: nil, releaseYear: 2026, rating: 8.5, type: "series", genres: ["Phiêu lưu", "Hành động"])
        let second = Movie(id: 2, slug: "breaking-bad", title: "Breaking Bad", posterURL: nil, backdropURL: nil, releaseYear: 2022, rating: 9.0, type: "series", genres: ["Tội phạm"])
        return HomeView(previewPayload: HomePayload(featured: first, trending: [first, second], latest: [second, first]))
    }
}
