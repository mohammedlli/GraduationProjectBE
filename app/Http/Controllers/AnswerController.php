<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnswerRequest;
use App\Models\Answer;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class AnswerController extends Controller
{
    //

     public function store(Request $request){
         $request->validate([
        'file' => 'required|file|mimes:js,php,java,txt|max:2048',
        'task_id' => 'required|exists:tasks,id',
        'user_id' => 'required|exists:users,id',
    ]);

    // رفع الملف
    $file = $request->file('file');
    $path = $file->store('answers', 'public');

    // حفظ في DB
    $answer = Answer::create([
    'file_path' => $path,
    'task_id' => $request->task_id,
    'user_id' => $request->user_id,
    'answer' => $request->answer ?? '',
    ]);

    return response()->json([
        "message" => "File uploaded successfully",
        "data" => $answer
    ]);
    }

public function analyzeUploadedFile(Request $request, $id)
{
    // 1️⃣ جلب الـ answer من DB
    $answer = Answer::findOrFail($id);

    // 2️⃣ قراءة الملف
    $fileContent = Storage::disk('public')->get($answer->file_path);

    // 3️⃣ التحقق إذا كان الملف يبدو ككود برمجي
    // يمكننا استخدام امتداد الملف أو وجود كلمات مفتاحية بسيطة
    $extension = pathinfo($answer->file_path, PATHINFO_EXTENSION);
    $codeExtensions = ['js', 'php', 'java', 'py', 'cpp'];

    if (!in_array(strtolower($extension), $codeExtensions)) {
        // الملف ليس كود → درجة صفر
        return response()->json([
            "isCorrect" => false,
            "score" => 0,
            "message" => "This file does not contain code.",
            "content_preview" => substr($fileContent, 0, 200) // عرض أول 200 حرف فقط
        ]);
    }

    // 4️⃣ إرسال الكود للـ OpenAI
$prompt = "
You are a senior developer.

Return ONLY valid JSON. Do not write anything else.

{
  \"score\": number,
  \"accuracy\": number,
  \"isCorrect\": true/false,
  \"errors\": [
    {\"line\": number, \"message\": \"text\"}
  ],
  \"suggestion\": \"short text\"
}

Rules:
- score must be from 0 to 100
- accuracy must be a percentage (0 to 100)
- suggestion must be very short
- if code is correct, errors should be empty []

Analyze this code:

$fileContent
";

    $response = Http::withToken(env('OPENAI_API_KEY'))
        ->post('https://api.openai.com/v1/responses', [
            "model" => "gpt-4.1-mini",
            "input" => $prompt,
        ]);

    $data = $response->json();

    // 5️⃣ تنظيف النص وارجاع JSON
    $text = $data['output'][0]['content'][0]['text'] ?? '{}';
    $text = preg_replace('/^```json\s*/', '', $text);
    $text = preg_replace('/\s*```$/', '', $text);

    $parsed = json_decode($text, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return response()->json([
            "error" => "Invalid JSON from AI",
            "raw" => $text
        ]);
    }

    return response()->json($parsed);
}


public function getFileContent($id) {
    $answer = Answer::findOrFail($id);

    if (!Storage::disk('public')->exists($answer->file_path)) {
        return response()->json(['error' => 'File not found'], 404);
    }

    $content = Storage::disk('public')->get($answer->file_path);

    return response()->json([
        'file_name' => basename($answer->file_path),
        'content' => $content,
        'language' => $answer->language ?? 'javascript' // أو خذها من قاعدة البيانات
    ]);
}

public function testAI(Request $request)
{
    $request->validate([
        'code' => 'required|string',
    ]);

    $code = $request->code;

    $prompt = "
You are a senior developer.

Analyze this code and return ONLY valid JSON:

{
  \"isCorrect\": true/false,
  \"errors\": [
    {\"line\": number, \"message\": \"text\"}
  ],
  \"suggestions\": [\"text\"]
}

Code:
$code
";

    $response = Http::withToken(env('OPENAI_API_KEY'))
        ->post('https://api.openai.com/v1/responses', [
            "model" => "gpt-4.1-mini",
            "input" => $prompt,
        ]);

    $data = $response->json();

    // حاول إيجاد النص الحقيقي في الرد
    $text = $data['output'][0]['content'][0]['text'] ?? '{}';

    $parsed = json_decode($text, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return response()->json([
            "error" => "Invalid JSON from AI",
            "raw" => $text
        ]);
    }

    return response()->json($parsed);
}


     public function update(Request $request , $id){

     $answer = Answer::findOrFail($id);
        $answer ->update([$request->all()]);
        return response()->json($answer , 200);
    }


         public function destroy(string $id){

     $answer = Answer::findOrFail($id);
        $answer ->delete();
        return response()->json($answer , 200);
    }


         public function show(string $id){
        $answer = Answer::find($id);
        return response()->json($answer , 200);
    }

           public function getAll(){
        $answer = Answer::all();
        return response()->json($answer , 200);
    }

      public function getAllByTaskId($taskId){
    $answers = Answer::where('task_id', $taskId)->get();
    return response()->json($answers);
    }
}



























// <?php

// namespace App\Http\Controllers;

// use App\Models\Answer;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Http;

// class AnswerController extends Controller
// {
//     public function store(Request $request)
//     {
//         $request->validate([
//             'file' => 'required|file|mimes:js,php,java,txt,py,cpp|max:2048',
//             'task_id' => 'required|exists:tasks,id',
//             'user_id' => 'required|exists:users,id',
//             'answer' => 'nullable|string',
//         ]);

//         try {
//             $file = $request->file('file');

//             // Save file to S3 / Laravel Cloud Object Storage
//             $path = $file->store('answers', 's3');

//             $answer = Answer::create([
//                 'file_path' => $path,
//                 'task_id' => $request->task_id,
//                 'user_id' => $request->user_id,
//                 'answer' => $request->answer ?? '',
//             ]);

//             return response()->json([
//                 'message' => 'File uploaded successfully',
//                 'data' => $answer,
//             ], 201);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'message' => 'File upload failed',
//                 'error' => $e->getMessage(),
//             ], 500);
//         }
//     }

//     public function analyzeUploadedFile(Request $request, $id)
//     {
//         try {
//             $answer = Answer::findOrFail($id);

//             if (!Storage::disk('s3')->exists($answer->file_path)) {
//                 return response()->json([
//                     'error' => 'File not found',
//                 ], 404);
//             }

//             $fileContent = Storage::disk('s3')->get($answer->file_path);

//             $extension = strtolower(pathinfo($answer->file_path, PATHINFO_EXTENSION));
//             $codeExtensions = ['js', 'php', 'java', 'py', 'cpp', 'txt'];

//             if (!in_array($extension, $codeExtensions)) {
//                 return response()->json([
//                     'isCorrect' => false,
//                     'score' => 0,
//                     'message' => 'This file does not contain code.',
//                     'content_preview' => substr($fileContent, 0, 200),
//                 ]);
//             }

//             $prompt = <<<PROMPT
// You are a senior developer.

// Return ONLY valid JSON. Do not write anything else.

// {
//   "score": number,
//   "accuracy": number,
//   "isCorrect": true/false,
//   "errors": [
//     {"line": number, "message": "text"}
//   ],
//   "suggestion": "short text"
// }

// Rules:
// - score must be from 0 to 100
// - accuracy must be a percentage from 0 to 100
// - suggestion must be very short
// - if code is correct, errors should be empty []

// Analyze this code:

// $fileContent
// PROMPT;

//             $response = Http::withToken(env('OPENAI_API_KEY'))
//                 ->post('https://api.openai.com/v1/responses', [
//                     'model' => 'gpt-4.1-mini',
//                     'input' => $prompt,
//                 ]);

//             if (!$response->successful()) {
//                 return response()->json([
//                     'error' => 'AI request failed',
//                     'details' => $response->json(),
//                 ], 500);
//             }

//             $data = $response->json();

//             $text = $data['output'][0]['content'][0]['text'] ?? '{}';

//             $text = preg_replace('/^```json\s*/', '', $text);
//             $text = preg_replace('/^```\s*/', '', $text);
//             $text = preg_replace('/\s*```$/', '', $text);

//             $parsed = json_decode($text, true);

//             if (json_last_error() !== JSON_ERROR_NONE) {
//                 return response()->json([
//                     'error' => 'Invalid JSON from AI',
//                     'raw' => $text,
//                 ], 500);
//             }

//             return response()->json($parsed);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'message' => 'Analyze failed',
//                 'error' => $e->getMessage(),
//             ], 500);
//         }
//     }

//     public function getFileContent($id)
//     {
//         try {
//             $answer = Answer::findOrFail($id);

//             if (!Storage::disk('s3')->exists($answer->file_path)) {
//                 return response()->json([
//                     'error' => 'File not found',
//                 ], 404);
//             }

//             $content = Storage::disk('s3')->get($answer->file_path);

//             return response()->json([
//                 'file_name' => basename($answer->file_path),
//                 'content' => $content,
//                 'language' => $answer->language ?? 'javascript',
//             ]);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'message' => 'Could not read file',
//                 'error' => $e->getMessage(),
//             ], 500);
//         }
//     }

//     public function testAI(Request $request)
//     {
//         $request->validate([
//             'code' => 'required|string',
//         ]);

//         try {
//             $code = $request->code;

//             $prompt = <<<PROMPT
// You are a senior developer.

// Analyze this code and return ONLY valid JSON:

// {
//   "isCorrect": true/false,
//   "errors": [
//     {"line": number, "message": "text"}
//   ],
//   "suggestions": ["text"]
// }

// Code:
// $code
// PROMPT;

//             $response = Http::withToken(env('OPENAI_API_KEY'))
//                 ->post('https://api.openai.com/v1/responses', [
//                     'model' => 'gpt-4.1-mini',
//                     'input' => $prompt,
//                 ]);

//             if (!$response->successful()) {
//                 return response()->json([
//                     'error' => 'AI request failed',
//                     'details' => $response->json(),
//                 ], 500);
//             }

//             $data = $response->json();

//             $text = $data['output'][0]['content'][0]['text'] ?? '{}';

//             $text = preg_replace('/^```json\s*/', '', $text);
//             $text = preg_replace('/^```\s*/', '', $text);
//             $text = preg_replace('/\s*```$/', '', $text);

//             $parsed = json_decode($text, true);

//             if (json_last_error() !== JSON_ERROR_NONE) {
//                 return response()->json([
//                     'error' => 'Invalid JSON from AI',
//                     'raw' => $text,
//                 ], 500);
//             }

//             return response()->json($parsed);

//         } catch (\Exception $e) {
//             return response()->json([
//                 'message' => 'AI test failed',
//                 'error' => $e->getMessage(),
//             ], 500);
//         }
//     }

//     public function update(Request $request, $id)
//     {
//         $answer = Answer::findOrFail($id);

//         $answer->update($request->all());

//         return response()->json($answer, 200);
//     }

//     public function destroy(string $id)
//     {
//         $answer = Answer::findOrFail($id);

//         if ($answer->file_path && Storage::disk('s3')->exists($answer->file_path)) {
//             Storage::disk('s3')->delete($answer->file_path);
//         }

//         $answer->delete();

//         return response()->json([
//             'message' => 'Answer deleted successfully',
//             'data' => $answer,
//         ], 200);
//     }

//     public function show(string $id)
//     {
//         $answer = Answer::findOrFail($id);

//         return response()->json($answer, 200);
//     }

//     public function getAll()
//     {
//         $answers = Answer::all();

//         return response()->json($answers, 200);
//     }

//     public function getAllByTaskId($taskId)
//     {
//         $answers = Answer::where('task_id', $taskId)->get();

//         return response()->json($answers, 200);
//     }
// }
