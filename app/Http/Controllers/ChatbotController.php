<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Movie;
use App\Models\Show;
use Carbon\Carbon;

class ChatbotController extends Controller
{
    public function chat(Request $request)
    {
        $userMessage = $request->input('message');

        if (!$userMessage) {
            return response()->json(['error' => 'Message is required'], 400);
        }

        // 1. Retrieval: Lấy một số thông tin cơ bản làm ngữ cảnh
        $movies = Movie::where('status', 'showing')
                       ->orWhere('status', 'coming_soon')
                       ->get(['id', 'title', 'genre', 'age_rating', 'duration_minutes', 'description']);
        
        $shows = Show::with(['movie:id,title', 'room:id,name'])
                     ->where('start_time', '>=', Carbon::now())
                     ->where('start_time', '<=', Carbon::now()->addDays(7))
                     ->get(['id', 'movie_id', 'room_id', 'start_time', 'price']);

        $contextData = [
            'available_movies' => $movies,
            'upcoming_shows' => $shows,
        ];

        $contextString = json_encode($contextData, JSON_UNESCAPED_UNICODE);

        // 2. System Prompt
        $systemPrompt = <<<PROMPT
Bạn là trợ lý AI thông minh của rạp chiếu phim (Cinema Ticket Management).
Nhiệm vụ của bạn là tư vấn phim, lịch chiếu, giá vé cho khách hàng.
Dưới đây là thông tin về các phim đang chiếu/sắp chiếu và lịch chiếu trong 7 ngày tới (định dạng JSON):
{$contextString}

Quy tắc trả lời:
- Luôn thân thiện, xưng hô bằng "tôi" và gọi khách hàng là "bạn".
- Chỉ trả lời dựa trên thông tin được cung cấp trong JSON. Nếu khách hỏi thông tin không có, hãy lịch sự báo không biết hoặc chưa có thông tin.
- TRẢ LỜI BẰNG VĂN BẢN THƯỜNG, TUYỆT ĐỐI KHÔNG SỬ DỤNG MARKDOWN (như in đậm **, in nghiêng). Không chèn ký tự ** vào câu trả lời.
- BẮT BUỘC Ở CUỐI CÂU TRẢ LỜI, bạn phải gợi ý thêm 2 câu hỏi liên quan tiếp theo mà người dùng có thể hỏi. 
- Mỗi câu gợi ý phải đặt trên một dòng mới và bắt đầu bằng cờ [SUGGEST].
Ví dụ:
[SUGGEST]Lịch chiếu phim Mai hôm nay thế nào?
[SUGGEST]Giá vé cuối tuần là bao nhiêu?
PROMPT;

        // 3. Call Gemini API
        $apiKey = config('services.gemini.api_key');
        $model = 'gemini-3.6-flash';
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::post($url, [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => $systemPrompt . "\n\nCâu hỏi của người dùng: " . $userMessage]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Xin lỗi, tôi không thể trả lời lúc này.';
                
                return response()->json([
                    'reply' => $reply
                ]);
            } else {
                return response()->json(['error' => 'API Error: ' . $response->body()], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
