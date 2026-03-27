<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CodeEditorController extends Controller
{
    public function run(Request $request)
    {
        $request->validate([
            'language' => 'required|string',
            'version' => 'required|string',
        ]);

        $code = $request->code ?? $request->input('files.0.content');

        if (!$code) {
            return response()->json([
                'message' => 'The code field is required.'
            ], 422);
        }

        $response = Http::post('http://localhost:2000/api/v2/execute', [
            'language' => $request->language,
            'version' => $request->version,
            'files' => [
                [
                    'content' => $code,
                ]
            ],
        ]);

        return response()->json($response->json(), $response->status());
    }
}
