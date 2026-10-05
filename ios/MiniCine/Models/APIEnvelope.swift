import Foundation

struct APIEnvelope<Value: Decodable>: Decodable {
    let data: Value
}
