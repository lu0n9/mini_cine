import Foundation

enum APIError: LocalizedError {
    case invalidURL
    case badResponse

    var errorDescription: String? {
        switch self {
        case .invalidURL: "Địa chỉ API không hợp lệ. Hãy chỉnh API_BASE_URL trong APIClient.swift."
        case .badResponse: "Không thể tải dữ liệu từ Mini Cine. Hãy kiểm tra Laravel đang chạy."
        }
    }
}

struct APIClient {
    // Simulator: use http://127.0.0.1:8000/api/v1 with `php artisan serve`.
    // Physical iPhone: replace 127.0.0.1 with the Mac's LAN IP address.
    private let baseURL = "http://127.0.0.1:8000/api/v1"

    func home() async throws -> HomePayload {
        try await get("/home").data
    }

    func movies(search: String = "") async throws -> [Movie] {
        let path = "/movies"
        if !search.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty {
            var components = URLComponents(string: baseURL + path)
            components?.queryItems = [URLQueryItem(name: "search", value: search)]
            guard let url = components?.url else { throw APIError.invalidURL }
            let (data, response) = try await URLSession.shared.data(from: url)
            guard (response as? HTTPURLResponse)?.statusCode == 200 else { throw APIError.badResponse }
            return try JSONDecoder().decode(APIEnvelope<[Movie]>.self, from: data).data
        }
        return try await get(path).data
    }

    func movie(slug: String) async throws -> Movie {
        try await get("/movies/\(slug)").data
    }

    private func get<Value: Decodable>(_ path: String) async throws -> APIEnvelope<Value> {
        guard let url = URL(string: baseURL + path) else { throw APIError.invalidURL }
        let (data, response) = try await URLSession.shared.data(from: url)
        guard (response as? HTTPURLResponse)?.statusCode == 200 else { throw APIError.badResponse }
        return try JSONDecoder().decode(APIEnvelope<Value>.self, from: data)
    }
}
