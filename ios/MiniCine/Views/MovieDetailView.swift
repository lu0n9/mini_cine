import SwiftUI

struct MovieDetailView: View {
    let slug: String
    @State private var movie: Movie?
    @State private var errorMessage: String?
    private let api = APIClient()

    var body: some View {
        ScrollView(showsIndicators: false) {
            VStack(alignment: .leading, spacing: 0) {
                detailHero

                if let movie {
                    VStack(alignment: .leading, spacing: 28) {
                        movieSummary(movie)
                        if let description = movie.description, !description.isEmpty {
                            contentBlock("Nội dung phim") {
                                Text(description)
                                    .font(.subheadline)
                                    .lineSpacing(5)
                                    .foregroundStyle(Color.cineTextSecondary)
                            }
                        }
                        if let episodes = movie.episodes, !episodes.isEmpty {
                            episodesBlock(episodes)
                        }
                    }
                    .padding(.horizontal, 20)
                    .padding(.top, 22)
                    .padding(.bottom, 40)
                } else if let errorMessage {
                    ContentUnavailableView(errorMessage, systemImage: "wifi.exclamationmark")
                        .frame(maxWidth: .infinity, minHeight: 220)
                } else {
                    ProgressView("Đang tải thông tin phim…")
                        .tint(.cineAccent)
                        .frame(maxWidth: .infinity, minHeight: 220)
                }
            }
        }
        .background(Color.cineBackground.ignoresSafeArea())
        .toolbarBackground(Color.cineBackground.opacity(0.92), for: .navigationBar)
        .toolbarColorScheme(.dark, for: .navigationBar)
        .task {
            do { movie = try await api.movie(slug: slug) }
            catch { errorMessage = error.localizedDescription }
        }
    }

    private var detailHero: some View {
        ZStack(alignment: .bottomLeading) {
            PosterImage(url: movie?.backdropURL ?? movie?.posterURL)
                .frame(height: 300)
                .frame(maxWidth: .infinity)
                .clipped()
                .overlay {
                    LinearGradient(
                        stops: [
                            .init(color: .black.opacity(0.18), location: 0),
                            .init(color: Color.cineBackground.opacity(0.55), location: 0.55),
                            .init(color: Color.cineBackground, location: 1)
                        ],
                        startPoint: .top,
                        endPoint: .bottom
                    )
                }
            if movie == nil {
                Text("Đang tải phim…")
                    .font(.caption.weight(.semibold))
                    .foregroundStyle(Color.cineMuted)
                    .padding(20)
                    .padding(.bottom, 18)
            }
        }
        .accessibilityHidden(movie == nil)
    }

    private func movieSummary(_ movie: Movie) -> some View {
        VStack(alignment: .leading, spacing: 18) {
            HStack(alignment: .bottom, spacing: 16) {
                PosterImage(url: movie.posterURL)
                    .aspectRatio(2 / 3, contentMode: .fit)
                    .frame(width: 116)
                    .clipShape(RoundedRectangle(cornerRadius: 10))
                    .overlay(RoundedRectangle(cornerRadius: 10).stroke(Color.cineLine, lineWidth: 1))
                    .shadow(color: .black.opacity(0.4), radius: 18, y: 8)

                VStack(alignment: .leading, spacing: 9) {
                    Text(movie.type == "series" ? "PHIM BỘ" : "PHIM LẺ")
                        .font(.caption2.weight(.bold))
                        .tracking(1.5)
                        .foregroundStyle(Color.cineAccent)
                    Text(movie.title)
                        .font(.system(size: 25, weight: .heavy, design: .rounded))
                        .foregroundStyle(.white)
                        .fixedSize(horizontal: false, vertical: true)
                    if let originalTitle = movie.originalTitle, !originalTitle.isEmpty, originalTitle != movie.title {
                        Text(originalTitle)
                            .font(.caption)
                            .foregroundStyle(Color.cineMuted)
                            .lineLimit(2)
                    }
                    if let rating = movie.rating, rating > 0 {
                        ratingBadge(rating)
                            .padding(.top, 3)
                    }
                }
                .frame(maxWidth: .infinity, alignment: .leading)
            }

            metadata(movie)

        }
    }

    private func ratingBadge(_ rating: Double) -> some View {
        HStack(spacing: 8) {
            ZStack {
                Circle().stroke(Color.white.opacity(0.12), lineWidth: 4)
                Circle()
                    .trim(from: 0, to: min(max(rating / 10, 0), 1))
                    .stroke(Color.cineAccent, style: StrokeStyle(lineWidth: 4, lineCap: .round))
                    .rotationEffect(.degrees(-90))
                Text(rating.formatted(.number.precision(.fractionLength(1))))
                    .font(.system(size: 12, weight: .bold, design: .rounded))
                    .foregroundStyle(.white)
            }
            .frame(width: 42, height: 42)
            Text("IMDb")
                .font(.caption.weight(.semibold))
                .foregroundStyle(Color.cineMuted)
        }
        .accessibilityLabel("Đánh giá \(rating.formatted(.number.precision(.fractionLength(1)))) trên 10")
    }

    private func metadata(_ movie: Movie) -> some View {
        VStack(alignment: .leading, spacing: 12) {
            HStack(spacing: 8) {
                if let year = movie.releaseYear { metadataPill(String(year)) }
                metadataPill(movie.type == "series" ? "Phim bộ" : "Phim lẻ")
                if let country = movie.countries?.first { metadataPill(country) }
            }
            if !movie.genres.isEmpty {
                Text(movie.genres.joined(separator: "   ·   "))
                    .font(.caption.weight(.semibold))
                    .foregroundStyle(Color.cineAccent)
            }
        }
    }

    private func metadataPill(_ text: String) -> some View {
        Text(text)
            .font(.caption.weight(.medium))
            .foregroundStyle(Color.cineTextSecondary)
            .padding(.horizontal, 10)
            .padding(.vertical, 6)
            .background(Color.cineSurface, in: RoundedRectangle(cornerRadius: 5))
            .overlay(RoundedRectangle(cornerRadius: 5).stroke(Color.cineLine, lineWidth: 1))
    }

    private func episodesBlock(_ episodes: [Episode]) -> some View {
        let seasonNumbers = Array(Set(episodes.compactMap(\.seasonNumber))).sorted()
        return contentBlock("Danh sách tập") {
            VStack(alignment: .leading, spacing: 12) {
                if seasonNumbers.isEmpty {
                    episodeCards(episodes)
                } else {
                    ForEach(seasonNumbers, id: \.self) { season in
                        VStack(alignment: .leading, spacing: 10) {
                            Text("Mùa \(season)")
                                .font(.subheadline.weight(.bold))
                                .foregroundStyle(.white)
                                .padding(.top, 4)
                            episodeCards(episodes.filter { $0.seasonNumber == season })
                        }
                    }
                    let unassigned = episodes.filter { $0.seasonNumber == nil }
                    if !unassigned.isEmpty {
                        VStack(alignment: .leading, spacing: 10) {
                            Text("Tập phim")
                                .font(.subheadline.weight(.bold))
                                .foregroundStyle(.white)
                            episodeCards(unassigned)
                        }
                    }
                }
            }
        }
    }

    private func episodeCards(_ episodes: [Episode]) -> some View {
        VStack(spacing: 9) {
            ForEach(episodes) { episode in
                HStack(spacing: 12) {
                    Image(systemName: "play.circle.fill")
                        .font(.system(size: 27))
                        .foregroundStyle(Color.cineAccent)
                    VStack(alignment: .leading, spacing: 3) {
                        Text(episode.name ?? "Tập \(episode.episodeNumber)")
                            .font(.subheadline.weight(.semibold))
                            .foregroundStyle(.white)
                        Text("Tập \(episode.episodeNumber)")
                            .font(.caption)
                            .foregroundStyle(Color.cineMuted)
                    }
                    Spacer()
                    if let duration = episode.duration {
                        Text("\(duration) phút")
                            .font(.caption)
                            .foregroundStyle(Color.cineMuted)
                    }
                }
                .padding(13)
                .background(Color.cineSurface, in: RoundedRectangle(cornerRadius: 9))
                .overlay(RoundedRectangle(cornerRadius: 9).stroke(Color.cineLine, lineWidth: 1))
            }
        }
    }

    private func contentBlock<Content: View>(_ title: String, @ViewBuilder content: () -> Content) -> some View {
        VStack(alignment: .leading, spacing: 14) {
            HStack(spacing: 10) {
                RoundedRectangle(cornerRadius: 2)
                    .fill(Color.cineAccent)
                    .frame(width: 3, height: 22)
                Text(title)
                    .font(.system(size: 19, weight: .bold, design: .rounded))
                    .foregroundStyle(.white)
            }
            content()
        }
    }
}

struct PosterImage: View {
    let url: URL?

    var body: some View {
        AsyncImage(url: url) { phase in
            if let image = phase.image {
                image.resizable().scaledToFill()
            } else {
                Rectangle()
                    .fill(Color.cineSurface)
                    .overlay {
                        Image(systemName: "film")
                            .font(.largeTitle)
                            .foregroundStyle(.white.opacity(0.22))
                    }
            }
        }
    }
}

private extension Color {
    static let cineTextSecondary = Color.white.opacity(0.76)
}
