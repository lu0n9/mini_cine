<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\CommentModeration;

class CommentModerationService
{
    protected array $blockedWords = [
        'đm',
        'đmm',
        'địt',
        'đụ',
        'đéo',
        'vcl',
        'clm',
        'cặc',
        'lồn',
        'ngu',
    ];

    protected array $spamKeywords = [
        'mua ngay',
        'giá rẻ',
        'khuyến mãi',
        'kiếm tiền',
        'kiếm tiền online',
        'click vào link',
        'liên hệ ngay',
        'ib mình',
        'telegram',
        'whatsapp',
        'casino',
        'bet',
        'forex',
        'crypto',
    ];

    public function __construct(
        protected CommentAiModerationService $aiModeration
    ) {
    }

    /**
     * Phân tích và quyết định trạng thái comment.
     */
    public function moderate(string $content, ?int $userId = null): array
    {
        $content = trim($content);

        $ruleResult = $this->analyzeRules($content, $userId);

        /*
        |--------------------------------------------------------------------------
        | Rule Engine chỉ chặn khi chắc chắn
        |--------------------------------------------------------------------------
        */

        if ($ruleResult['score'] >= 0.60) {
            return [
                'score' => $ruleResult['score'],
                'category' => $ruleResult['category'],
                'reason' => $ruleResult['reason'],
                'decision' => 'spam',
                'source' => 'rule',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Các trường hợp còn lại phải để AI đánh giá
        |--------------------------------------------------------------------------
        */

        try {
            return $this->aiModeration->analyze($content);
        } catch (\Throwable $e) {

            report($e);

            /*
            Nếu AI lỗi thì không nên tự động coi comment là clean.
            Cho Admin xem xét.
            */

            return [
                'score' => $ruleResult['score'],
                'category' => 'uncertain',
                'reason' => 'Không thể hoàn tất kiểm tra AI. Cần Admin xem xét.',
                'decision' => 'ai_review',
                'source' => 'ai',
            ];
        }
    }

    /**
     * Lưu kết quả moderation vào DB.
     */
    public function saveResult(
        Comment $comment,
        array $result
    ): CommentModeration {
        return $comment->moderations()->create([
            'source' => $result['source'] ?? 'rule',
            'score' => $result['score'] ?? null,
            'category' => $result['category'] ?? null,
            'reason' => $result['reason'] ?? null,
            'decision' => $result['decision'] ?? null,
        ]);
    }

    /**
     * Rule Engine.
     */
    protected function analyzeRules(
        string $content,
        ?int $userId = null
    ): array {
        $normalized = $this->normalize($content);

        // Rule score nội bộ: 0 - 100
        $score = 0;

        $reasons = [];

        /*
        |--------------------------------------------------------------------------
        | TỪ NGỮ KHÔNG PHÙ HỢP
        |--------------------------------------------------------------------------
        */

        foreach ($this->blockedWords as $word) {
            if (
                mb_stripos(
                    $normalized,
                    $this->normalize($word)
                ) !== false
            ) {
                $score += 60;

                $reasons[] = 'Chứa từ ngữ không phù hợp.';

                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SPAM KEYWORD
        |--------------------------------------------------------------------------
        */

        foreach ($this->spamKeywords as $keyword) {
            if (
                mb_stripos(
                    $normalized,
                    $this->normalize($keyword)
                ) !== false
            ) {
                $score += 30;

                $reasons[] = 'Có dấu hiệu quảng cáo hoặc spam.';

                break;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | LINK
        |--------------------------------------------------------------------------
        */

        preg_match_all(
            '/https?:\/\/[^\s]+/iu',
            $content,
            $matches
        );

        $urlCount = count($matches[0]);

        if ($urlCount > 0) {
            $score += 40;

            $reasons[] = 'Chứa liên kết.';
        }

        if ($urlCount >= 2) {
            $score += 30;

            $reasons[] = 'Chứa nhiều liên kết.';
        }

        /*
        |--------------------------------------------------------------------------
        | LẶP KÝ TỰ
        |--------------------------------------------------------------------------
        */

        if (preg_match('/(.)\1{5,}/u', $content)) {
            $score += 15;

            $reasons[] = 'Lặp ký tự bất thường.';
        }

        /*
        |--------------------------------------------------------------------------
        | QUÁ NGẮN
        |--------------------------------------------------------------------------
        */

        if (mb_strlen($content) < 3) {
            $score += 5;

            $reasons[] = 'Nội dung quá ngắn.';
        }

        /*
        |--------------------------------------------------------------------------
        | COMMENT TRÙNG
        |--------------------------------------------------------------------------
        */

        if ($userId) {
            $duplicate = Comment::query()
                ->where('user_id', $userId)
                ->where('content', $content)
                ->where(
                    'created_at',
                    '>=',
                    now()->subMinutes(10)
                )
                ->exists();

            if ($duplicate) {
                $score += 50;

                $reasons[] = 'Nội dung trùng lặp.';
            }
        }

        /*
        |--------------------------------------------------------------------------
        | CHUẨN HÓA SCORE ĐỂ LƯU DATABASE
        |--------------------------------------------------------------------------
        |
        | Rule score:
        | 0 - 100
        |
        | Database:
        | 0.0000 - 1.0000
        |
        */

        $normalizedScore = min($score / 100, 1);

        if ($score >= 60) {
            return [
                'score' => $normalizedScore,
                'category' => 'rule_violation',
                'reason' => implode(' ', $reasons),
                'decision' => 'spam',
                'source' => 'rule',
            ];
        }

        if ($score < 20) {
            return [
                'score' => $normalizedScore,
                'category' => 'clean',
                'reason' => 'Không phát hiện dấu hiệu vi phạm.',
                'decision' => 'approved',
                'source' => 'rule',
            ];
        }

        return [
            'score' => $normalizedScore,
            'category' => 'uncertain',
            'reason' => implode(' ', $reasons),
            'decision' => 'ai_review',
            'source' => 'rule',
        ];
    }

    protected function normalize(string $text): string
    {
        $text = mb_strtolower(
            $text,
            'UTF-8'
        );

        $text = preg_replace(
            '/\s+/u',
            ' ',
            $text
        );

        return trim($text);
    }
}