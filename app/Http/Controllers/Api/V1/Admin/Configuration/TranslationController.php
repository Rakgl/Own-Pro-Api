<?php

namespace App\Http\Controllers\Api\V1\Admin\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\Admin\TranslationRequest;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TranslationController extends Controller
{
    /**
     * Display a paginated list of translations for the admin panel.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);

        // Fetch paginated translations from the database
        $translationsPaginator = Translation::query()
            ->where(function ($query) use ($request) {
                // General search across key and value columns
                if ($request->filled('search')) {
                    $searchTerm = '%' . $request->search . '%';
                    $query->where('key', 'LIKE', $searchTerm)
                        ->orWhere('value', 'LIKE', $searchTerm);
                }

                // Specific search for the 'key' column
                if ($request->filled('key')) {
                    $query->where('key', 'LIKE', '%' . $request->key . '%');
                }

                if ($request->filled('platform')) {
                    if($request->platform != 'ALL') {
                        $query->where('platform', $request->platform);
                    }
                }
            })
            ->where('status', '!=', 'DELETED')
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage);

        // Manually structure the response to match the format the Vue component expects,
        // which was previously created by the API Resource Collection.
        return response()->json([
            'success' => true,
            'message' => 'Translations retrieved successfully.',
            'data' => [
                'data' => $translationsPaginator->items(),
                'links' => [
                    'first' => $translationsPaginator->url(1),
                    'last' => $translationsPaginator->url($translationsPaginator->lastPage()),
                    'prev' => $translationsPaginator->previousPageUrl(),
                    'next' => $translationsPaginator->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $translationsPaginator->currentPage(),
                    'from' => $translationsPaginator->firstItem(),
                    'last_page' => $translationsPaginator->lastPage(),
                    'path' => $translationsPaginator->path(),
                    'per_page' => $translationsPaginator->perPage(),
                    'to' => $translationsPaginator->lastItem(),
                    'total' => $translationsPaginator->total(),
                ],
            ]
        ]);
    }

    /**
     * Store a newly created translation in storage.
     *
     * @param TranslationRequest $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:100|unique:translations,key',
            'value' => 'required|array',
            'platform' => ['required', 'string', Rule::in(['ADMIN', 'MOBILE'])],
            'status' => ['required', 'string', Rule::in(['ACTIVE', 'INACTIVE'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validatedData = $validator->validated();

            // **FIX:** Manually encode the 'value' array to a JSON string.
            $validatedData['value'] = json_encode($validatedData['value']);

            $translation = Translation::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Translation created successfully.',
                'data' => $translation,
            ], 201);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create translation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified translation.
     *
     * @param Translation $translation
     * @return JsonResponse
     */
    public function show(Translation $translation): JsonResponse
    {
        // Manually decode the value before sending the response.
        $translation->value = json_decode($translation->value);

        return response()->json([
            'success' => true,
            'message' => 'Translation retrieved successfully.',
            'data' => $translation,
        ]);
    }

    /**
     * Update the specified translation in storage.
     *
     * @param Request $request
     * @param Translation $translation
     * @return JsonResponse
     */
    public function update(Request $request, Translation $translation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'key' => ['required', 'string', 'max:100', Rule::unique('translations')->ignore($translation->id)],
            'value' => 'required|array',
            'platform' => ['required', 'string', Rule::in(['ADMIN', 'MOBILE'])],
            'status' => ['required', 'string', Rule::in(['ACTIVE', 'INACTIVE'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validatedData = $validator->validated();

            // **FIX:** Manually encode the 'value' array to a JSON string.
            $validatedData['value'] = json_encode($validatedData['value']);

            $translation->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Translation updated successfully.',
                'data' => $translation,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update translation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified translation from storage by changing its status to DELETED.
     *
     * @param Translation $translation
     * @return JsonResponse
     */
    public function destroy(Translation $translation): JsonResponse
    {
        try {
            if ($translation->status !== 'DELETED') {
                $translation->status = 'DELETED';
                $translation->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Translation deleted successfully.'
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete translation.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all active translations formatted for a specific locale.
     *
     * @param string $locale The language code (e.g., 'en', 'fr').
     * @return JsonResponse
     */
    public function getTranslationsByLocale(string $locale): JsonResponse
    {
        Log::info($locale);
        try {
            $translations = Translation::where('status', 'ACTIVE')->get(['key', 'value']);
            $formattedTranslations = [];

            foreach ($translations as $translation) {
                $valueDecoded = json_decode($translation->value, true);
                if (isset($valueDecoded[$locale])) {
                    $this->setNestedArrayValue($formattedTranslations, $translation->key, $valueDecoded[$locale]);
                }
            }

            return response()->json($formattedTranslations);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Could not fetch translations.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper function to set a value in a nested array using a dot-notation key.
     *
     * @param array &$array The array to modify.
     * @param string $key The dot-notation key (e.g., 'nav.headings.core_administration').
     * @param mixed $value The value to set.
     * @return void
     */
    private function setNestedArrayValue(array &$array, string $key, $value): void
    {
        $keys = explode('.', $key);
        $temp = &$array;

        foreach ($keys as $nestedKey) {
            if (!isset($temp[$nestedKey]) || !is_array($temp[$nestedKey])) {
                $temp[$nestedKey] = [];
            }
            $temp = &$temp[$nestedKey];
        }

        $temp = $value;
    }
}
