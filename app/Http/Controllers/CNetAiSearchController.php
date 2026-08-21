<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class CNetAiSearchController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $data = $request->validate(['query' => ['required', 'string', 'min:3', 'max:500']]);
        $apiKey = (string) env('GEMINI_API_KEY', '');

        if ($apiKey === '') {
            return response()->json(['message' => 'C-Net AI Search is being configured. Please try again later.'], 503);
        }

        try {
            $response = Http::timeout(45)->retry(1, 500)->withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent', [
                'systemInstruction' => ['parts' => [['text' => 'You are C-Net AI Search, a helpful educational web search assistant for students, parents and teachers. Answer clearly in the same language as the user. Prefer concise, age-appropriate explanations. Use Google Search when current information is useful. Never claim to be Google. Mention uncertainty when appropriate.']]],
                'contents' => [['role' => 'user', 'parts' => [['text' => trim($data['query'])]]]],
                'tools' => [['google_search' => (object) []]],
                'generationConfig' => ['temperature' => 0.35, 'maxOutputTokens' => 1800],
            ]);

            if (! $response->successful()) {
                Log::warning('C-Net AI Search Gemini request failed.', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                ]);

                $message = match ($response->status()) {
                    400 => 'Gemini request configuration was rejected. Please check the latest Laravel log.',
                    401, 403 => 'Gemini API key is invalid, restricted, or the required API access is not enabled.',
                    429 => 'Gemini usage limit has been reached. Please try again later or check API quota.',
                    default => 'AI service is temporarily unavailable. Please try again.',
                };

                return response()->json(['message' => $message], 502);
            }

            $payload = $response->json();
            $candidate = $payload['candidates'][0] ?? [];
            $answer = trim(collect($candidate['content']['parts'] ?? [])->pluck('text')->filter()->implode("\n\n"));
            $metadata = $candidate['groundingMetadata'] ?? [];
            $sources = collect($metadata['groundingChunks'] ?? [])->map(fn ($chunk) => $chunk['web'] ?? null)
                ->filter(fn ($web) => is_array($web) && ! empty($web['uri']))
                ->map(fn ($web) => ['title' => $web['title'] ?? 'Web source', 'url' => $web['uri']])
                ->unique('url')->take(8)->values()->all();

            if ($answer === '') {
                return response()->json(['message' => 'No answer was generated. Please rephrase your question.'], 422);
            }

            return response()->json(['answer' => $answer, 'sources' => $sources, 'search_suggestions' => $metadata['searchEntryPoint']['renderedContent'] ?? null]);
        } catch (Throwable $exception) {
            report($exception);
            return response()->json(['message' => 'C-Net AI Search could not complete this request. Please try again.'], 500);
        }
    }
}
