import Foundation

struct Movie: Decodable, Identifiable, Hashable {
    let id: Int
    let slug: String
    let title: String

    let posterURL: URL?
    let backdropURL: URL?

    let releaseYear: Int?
    let rating: Double?
    let type: String?

    let genres: [String]

    var description: String?
    var originalTitle: String?
    var countries: [String]?
    var episodes: [Episode]?

    enum CodingKeys: String, CodingKey {
        case id
        case slug
        case title
        case type
        case rating
        case genres
        case description
        case countries
        case episodes

        case posterURL = "poster_url"
        case backdropURL = "backdrop_url"
        case releaseYear = "release_year"
        case originalTitle = "original_title"
    }
}
