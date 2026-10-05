import Foundation

struct HomePayload: Decodable {
    let featured: Movie?
    let trending: [Movie]
    let latest: [Movie]
}
