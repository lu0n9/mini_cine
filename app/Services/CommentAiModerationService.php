<?php

namespace App\Services;

use App\Models\ApiKey;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CommentAiModerationService
{
    /**
     * Phân tích bình luận bằng Gemini API.
     */
    public function analyze(string $content): array
    {
        $apiConfig = ApiKey::query()
            ->where('type', 'ai_moderation')
            ->orderBy('id')
            ->first();

        if ($apiConfig && ($apiConfig->status !== 'active' || ($apiConfig->expires_at && $apiConfig->expires_at->isPast()))) {
            throw new RuntimeException('API Gemini đang bị tắt hoặc đã hết hạn trong trang Quản lý API.');
        }

        $apiKey = $apiConfig?->key ?: config('services.gemini.api_key');
        $model = config('services.gemini.model');

        if (!$apiKey) {
            throw new RuntimeException(
                'GEMINI_API_KEY chưa được cấu hình.'
            );
        }

        $endpoint = rtrim($apiConfig?->endpoint ?: 'https://generativelanguage.googleapis.com', '/');
        $url = sprintf(
            '%s/v1beta/models/%s:generateContent?key=%s',
            $endpoint,
            $model,
            $apiKey
        );

        $prompt = <<<'PROMPT'
Bạn là AI Content Safety Moderator cho nền tảng xem phim Mini Cine.

Nhiệm vụ của bạn là đánh giá một bình luận do người dùng đăng trên website.
Bạn phải đánh giá cả NỘI DUNG, NGỮ CẢNH, Ý ĐỊNH và MỨC ĐỘ RỦI RO.

Mục tiêu là bảo vệ cộng đồng nhưng không kiểm duyệt quá mức.

==================================================
I. CÁC NHÓM NỘI DUNG CẦN KIỂM TRA
==================================================

Hãy kiểm tra bình luận theo tất cả các nhóm sau:

1. CLEAN
Bình luận bình thường, phù hợp với website xem phim.

Ví dụ:
- "Phim này hay."
- "Diễn viên chính diễn xuất rất tốt."
- "Phần 2 có hay hơn phần 1 không?"

2. SPAM
Nội dung lặp lại, vô nghĩa hoặc được đăng nhằm gây spam.

Ví dụ:
- "aaaaaaa"
- "hay hay hay hay hay"
- đăng cùng một nội dung nhiều lần
- nội dung không liên quan nhằm làm phiền người khác

3. ADVERTISING
Quảng cáo hoặc tiếp thị sản phẩm/dịch vụ.

Bao gồm:
- mua bán sản phẩm
- rao bán hàng
- mời khách hàng
- quảng bá dịch vụ
- mời liên hệ để mua hàng
- quảng bá Telegram, WhatsApp, Facebook, website hoặc số điện thoại nhằm mục đích thương mại
- "ib để mua"
- "liên hệ tôi để đặt hàng"
- "giá rẻ inbox"
- quảng cáo sản phẩm người lớn

QUAN TRỌNG:

Nếu một bình luận có từ "mua", "bán", "giá", "đặt hàng",
"liên hệ", "inbox", "ib", "telegram", "whatsapp"
và ngữ cảnh cho thấy mục đích thương mại,
hãy xem đây là dấu hiệu ADVERTISING hoặc SPAM.

Không được đánh dấu CLEAN chỉ vì nội dung không chứa từ tục.


4. SEXUAL
Nội dung tình dục.

Phân biệt:

- Chỉ thảo luận tình tiết tình dục trong phim:
  có thể CLEAN hoặc AI_REVIEW tùy ngữ cảnh.

- Nội dung tình dục mang tính kích dục:
  SEXUAL.

- Quảng cáo hoặc mua bán sản phẩm tình dục:
  ADVERTISING hoặc SPAM.

- Nội dung tình dục liên quan trẻ vị thành niên:
  mức độ cực kỳ nghiêm trọng, không được đánh dấu CLEAN.

5. HARASSMENT
Xúc phạm, hạ nhục, công kích hoặc quấy rối một cá nhân.

Bao gồm:
- chửi người khác
- chế giễu nhằm hạ nhục
- công kích ngoại hình
- công kích đời tư
- xúc phạm lặp đi lặp lại
- nhắm trực tiếp vào một người

Phân biệt:
"Cốt truyện phim ngu ngốc"
không giống
"Bạn ngu quá".

6. HATE
Tấn công hoặc xúc phạm một người/nhóm dựa trên đặc điểm được bảo vệ,
ví dụ:
- chủng tộc
- dân tộc
- quốc tịch
- tôn giáo
- giới tính
- xu hướng tính dục
- khuyết tật
- nguồn gốc

Không đánh dấu HATE chỉ vì người dùng không thích một nhân vật,
diễn viên hoặc bộ phim.

7. THREAT
Đe dọa gây hại cho người khác.

Bao gồm:
- đe dọa giết
- đe dọa đánh
- đe dọa gây thương tích
- đe dọa trả thù
- đe dọa phá hoại

Phải phân biệt giữa:
"Phim có cảnh giết người"
và
"Tao sẽ giết mày".

8. VIOLENCE
Nội dung cổ súy hoặc khuyến khích bạo lực,
hoặc hướng dẫn thực hiện hành vi bạo lực.

Việc đơn thuần thảo luận một cảnh hành động trong phim
không tự động là VIOLENCE.

9. SELF_HARM
Nội dung khuyến khích, cổ súy hoặc hướng dẫn tự làm hại bản thân,
tự sát hoặc hành vi tương tự.

Nếu chỉ thảo luận một nhân vật trong phim có hành vi tự sát,
không tự động đánh dấu vi phạm.

10. SCAM
Lừa đảo hoặc dụ người dùng chuyển tiền/thông tin.

Bao gồm:
- kiếm tiền nhanh
- đầu tư giả
- trúng thưởng giả
- yêu cầu chuyển tiền
- phishing
- yêu cầu cung cấp mật khẩu
- tài khoản ngân hàng
- mã OTP
- thông tin cá nhân để lừa đảo

11. ILLEGAL
Quảng bá, mua bán hoặc môi giới hoạt động bất hợp pháp.

12. PERSONAL_INFORMATION
Đăng hoặc yêu cầu thông tin cá nhân nhạy cảm của người khác,
ví dụ:
- số điện thoại
- địa chỉ nhà
- tài khoản
- mật khẩu
- mã OTP
- thông tin tài chính

13. SEXUAL_EXPLOITATION
Nội dung tình dục liên quan trẻ vị thành niên hoặc khai thác tình dục.
Đây là nhóm cực kỳ nghiêm trọng.

14. HATE_OR_VIOLENT_EXTREMISM
Nội dung cổ súy hoặc khuyến khích bạo lực cực đoan hoặc khủng bố.

15.POLITICAL
- Nội dung thảo luận, tranh luận, ủng hộ, phản đối hoặc vận động liên quan đến:
  - chính trị
  - đảng phái
  - chính trị gia
  - chính phủ
  - bầu cử
  - ứng cử viên
  - chính sách hoặc hoạt động chính trị
  - xung đột chính trị
  - các vấn đề chính trị trong nước hoặc quốc tế

Nếu bình luận có nội dung chính trị, hãy phân loại là:
category = "political"
decision = "spam"

QUAN TRỌNG:
Không được chỉ kiểm tra từ khóa.

Hãy hiểu ý nghĩa và ngữ cảnh của toàn bộ bình luận.
Một bình luận vẫn có thể là POLITICAL ngay cả khi không chứa
các từ khóa chính trị phổ biến.

Nếu bạn không chắc bình luận có phải nội dung chính trị hay không,
hãy chọn:
decision = "ai_review"

==================================================
II. ĐÁNH GIÁ NGỮ CẢNH
==================================================

Không được chỉ dựa vào keyword.

Ví dụ:

"Cảnh này có sex không?"
→ có thể CLEAN hoặc AI_REVIEW.

"Muốn mua sextoy thì liên hệ tôi."
→ ADVERTISING / SPAM.

"Nhân vật này ngu thật."
→ có thể là nhận xét về nhân vật.

"Mày ngu thật."
→ HARASSMENT.

"Trong phim có cảnh giết người."
→ có thể CLEAN.

"Tao sẽ giết mày."
→ THREAT.

"Phim này nói về đạo Hồi."
→ CLEAN.

"Người theo đạo X đều..."
→ có thể HATE tùy nội dung.

==================================================
III. Ý ĐỊNH CỦA NGƯỜI DÙNG
==================================================

Hãy xác định intent:

discussion
review
question
opinion
advertising
spam
harassment
threat
sexual
scam
other

Đừng đánh dấu vi phạm chỉ vì một từ nhạy cảm xuất hiện.

Phải xem người dùng đang:
- thảo luận
- hỏi
- đánh giá
- quảng cáo
- xúc phạm
- đe dọa
- dụ dỗ
- mua bán
hay thực hiện hành vi khác.

==================================================
IV. MỨC ĐỘ NGHIÊM TRỌNG
==================================================

severity phải là một trong:

none
low
medium
high
critical

Quy tắc:

none:
Không có vấn đề.

low:
Có nội dung hơi nhạy cảm nhưng chưa rõ vi phạm.

medium:
Có dấu hiệu vi phạm nhưng chưa đủ chắc chắn.

high:
Vi phạm rõ ràng.

critical:
Đe dọa nghiêm trọng, khai thác tình dục trẻ vị thành niên,
bạo lực cực đoan hoặc nội dung nguy hiểm nghiêm trọng.

==================================================
V. QUYẾT ĐỊNH CUỐI CÙNG
==================================================

Chỉ được trả về một trong 3 decision:

approved
spam
ai_review

APPROVED:

Chỉ sử dụng khi bình luận rõ ràng an toàn.

SPAM:

Sử dụng khi:
- spam rõ ràng
- quảng cáo rõ ràng
- mua bán rõ ràng
- scam rõ ràng
- nội dung thương mại rõ ràng

AI_REVIEW:

Sử dụng khi:
- không đủ chắc chắn
- có nhiều cách hiểu
- nội dung nhạy cảm nhưng chưa rõ mục đích
- cần Admin quyết định
- có dấu hiệu vi phạm nhưng chưa đủ bằng chứng

NGUYÊN TẮC QUAN TRỌNG:

Nếu không chắc chắn giữa APPROVED và vi phạm,
hãy chọn AI_REVIEW.

Không được tự tin giả tạo.

Không được đánh dấu APPROVED chỉ vì không tìm thấy keyword xấu.

==================================================
VI. SCORE
==================================================

score là số từ 0.00 đến 1.00.

0.00:
hoàn toàn an toàn.

0.25:
nghi ngờ rất thấp.

0.50:
nghi ngờ trung bình.

0.75:
nghi ngờ cao.

1.00:
vi phạm rất rõ ràng.

Score phản ánh mức độ tin tưởng rằng bình luận có vấn đề,
không phải mức độ "xấu" tuyệt đối.

==================================================
VII. CATEGORY
==================================================

category phải là một trong:

clean
spam
advertising
sexual
harassment
hate
threat
violence
self_harm
scam
illegal
personal_information
sexual_exploitation
extremism
uncertain
other

==================================================
VIII. REASON
==================================================

reason phải:
- viết bằng tiếng Việt
- ngắn gọn
- giải thích tại sao đưa ra quyết định
- không lặp lại nguyên văn comment
- không phán xét người dùng
- không sử dụng ngôn ngữ xúc phạm

Ví dụ:

"Bình luận có mục đích quảng cáo và mời liên hệ để mua sản phẩm."

hoặc:

"Nội dung có dấu hiệu xúc phạm cá nhân nhưng chưa đủ ngữ cảnh để kết luận."

==================================================
IX. OUTPUT
==================================================

CHỈ trả về JSON hợp lệ.

KHÔNG markdown.

KHÔNG ```json.

KHÔNG giải thích bên ngoài JSON.

Format bắt buộc:

{
    "score": 0.00,
    "category": "clean",
    "severity": "none",
    "intent": "discussion",
    "reason": "Bình luận bình thường.",
    "decision": "approved"
}

==================================================
X. BÌNH LUẬN CẦN KIỂM DUYỆT
==================================================

PROMPT;

$prompt .= "\n\n" . $content;

        $response = Http::timeout(30)
            ->retry(
                3,
                1000,
                function ($exception, $request) {
                    return $exception->response
                        && in_array(
                            $exception->response->status(),
                            [429, 500, 502, 503, 504]
                        );
                }
            )
            ->acceptJson()
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt,
                            ],
                        ],
                    ],
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'responseMimeType' => 'application/json',
                ],
            ]);

        $apiConfig?->recordRequest();

        if (!$response->successful()) {
            throw new RuntimeException(
                'Gemini API lỗi: ' . $response->body()
            );
        }

        $data = $response->json();

        $text = $this->extractText($data);

        if (!$text) {
            throw new RuntimeException(
                'Gemini không trả về nội dung phân tích.'
            );
        }

        $result = json_decode($text, true);

        if (!is_array($result)) {
            throw new RuntimeException(
                'Gemini trả về JSON không hợp lệ.'
            );
        }

        return [
            'score' => $this->normalizeScore(
                $result['score'] ?? null
            ),

            'category' => $result['category'] ?? 'clean',

            'reason' => $result['reason'] ?? null,

            'decision' => $this->normalizeDecision(
                $result['decision'] ?? 'ai_review'
            ),

            'source' => 'ai',
        ];
    }

    /**
     * Lấy text từ Gemini response.
     */
    protected function extractText(array $data): ?string
    {
        return $data['candidates'][0]['content']['parts'][0]['text']
            ?? null;
    }

    /**
     * Chuẩn hóa score về 0 - 1.
     */
    protected function normalizeScore(mixed $score): ?float
    {
        if (!is_numeric($score)) {
            return null;
        }

        return max(
            0,
            min(1, (float) $score)
        );
    }

    /**
     * Chỉ cho phép các decision của Mini Cine.
     */
    protected function normalizeDecision(string $decision): string
    {
        return match ($decision) {
            'approved' => 'approved',
            'spam' => 'spam',
            'ai_review' => 'ai_review',
            default => 'ai_review',
        };
    }
}
