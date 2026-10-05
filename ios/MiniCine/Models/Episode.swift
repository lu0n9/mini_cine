import Foundation

struct Episode: Decodable, Identifiable, Hashable {
    let id: Int
    let name: String?
    let episodeNumber: Int
    let seasonNumber: Int?
    let duration: Int?

    enum CodingKeys: String, CodingKey {
        case id
        case name
        case duration

        case episodeNumber = "episode_number"
        case seasonNumber = "season_number"
    }
}
