<?php
require_once __DIR__ . '/BaseController.php';

class SpamController extends BaseController {
    public function check() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['spam' => false, 'error' => 'Method not allowed']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        $text = trim($input['text'] ?? '');
        if (empty($text)) {
            echo json_encode(['spam' => false]);
            return;
        }
        $spam = $this->detectSpam($text);
        echo json_encode(['spam' => $spam]);
    }

    private function detectSpam($text) {
        $text = strtolower($text);
        $spamPatterns = [
            '/\b(buy\s+now|click\s+here|limited\s+time|act\s+now|urgent|guaranteed|free\s+money|make\s+money|work\s+from\s+home)\b/i',
            '/\b(http|https|www\.|\.com|\.net|\.org)\b/i',
            '/\b\d{10,}\b/',
            '/\b([a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})\b/i',
            '/\b(viagra|cialis|pills|pharmacy|prescription)\b/i',
            '/\b(casino|poker|bet|gambling|lottery|jackpot)\b/i',
            '/\b(loan|credit|debt|mortgage|refinance)\b/i',
            '/\b(weight\s+loss|diet\s+pills|lose\s+weight)\b/i',
        ];
        foreach ($spamPatterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }
        $words = explode(' ', $text);
        $wordCounts = array_count_values($words);
        foreach ($wordCounts as $word => $count) {
            if (strlen($word) > 3 && $count > 3) {
                return true;
            }
        }
        if (strlen($text) > 20 && $text === strtoupper($text) && preg_match('/[a-z]/', $text)) {
            return true;
        }
        return false;
    }
}
?>
