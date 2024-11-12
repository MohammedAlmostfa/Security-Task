<?php

namespace App\Service;

use Exception;
use App\Models\Task;
use App\Models\Attachment;
use Illuminate\Support\Str;
use League\Flysystem\Visibility;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class assetsService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('VIRUSTOTAL_API_KEY'); // Add your API key to .env
    }

    public function scanFile($filePath)
    {
        $url = 'https://www.virustotal.com/api/v3/files';

        // Upload the file to VirusTotal
        $response = Http::withHeaders([
            'x-apikey' => $this->apiKey,
        ])->attach('file', fopen($filePath, 'r'), basename($filePath))->post($url);

        // Check if the file was uploaded successfully
        if ($response->successful()) {
            // Extract the analysis ID from the response
            $analysisId = $response->json()['data']['id'];
            return $this->pollScanResult($analysisId);
        } else {
            Log::error('VirusTotal API error:', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            throw new Exception('Failed to scan file: ' . $response->body(), $response->status());
        }
    }

    public function pollScanResult($analysisId)
    {
        $url = "https://www.virustotal.com/api/v3/analyses/{$analysisId}";
        $maxAttempts = 10;
        $attempt = 0;

        // Poll every 10 seconds for the result until the scan is complete
        do {
            sleep(10); // wait 10 seconds between polling

            $response = Http::withHeaders([
                'x-apikey' => $this->apiKey,
            ])->get($url);

            $scanResult = $response->json();

            // Check if the scan is completed
            if (isset($scanResult['data']['attributes']['status']) && $scanResult['data']['attributes']['status'] === 'completed') {
                return $scanResult;
            }

            $attempt++;
        } while ($attempt < $maxAttempts);

        throw new Exception('Scan timeout or failed to complete after polling.');
    }
    /**
     * Store a file securely and associate it with a task.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param int $taskId
     * @return string
     * @throws Exception
     */


    public function storeFile($file, $taskId)
    {
        try {
            $message = '';

            // Scan the file
            $scanResult = $this->scanFile($file->path());

            // Check scan results for malicious content
            if (isset($scanResult['data']['attributes']['stats'])) {
                $maliciousCount = $scanResult['data']['attributes']['stats']['malicious'] ?? 0;
                if ($maliciousCount > 0) {
                    throw new Exception('File contains a virus!', 400);
                } else {
                    $message = 'Scan completed successfully, no virus found :)';
                }
            }

            // العثور على المهمة بواسطة معرفها
            $task = Task::findOrFail($taskId);

            // الحصول على اسم الملف الأصلي
            $originalName = $file->getClientOriginalName();

            // التأكد من أن الامتداد صالح ولا يوجد تحايل على المسار في اسم الملف
            if (preg_match('/\.[^.]+\./', $originalName)) {
                throw new Exception(trans('general.notAllowedAction'), 403);
            }

            // التحقق من تحايل المسار (باستخدام ../ أو ..\ أو / للصعود إلى الدلائل العليا)
            if (strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
                throw new Exception(trans('general.pathTraversalDetected'), 403);
            }

            // توليد اسم ملف عشوائي وآمن
            $fileName = Str::random(32);
            $extension = $file->getClientOriginalExtension(); // طريقة آمنة للحصول على الامتداد
            $filePath = "Files/{$fileName}.{$extension}";

            // تخزين الملف بشكل آمن
            $path = Storage::disk('public')->putFileAs('Files', $file, $filePath);

            // ضمان أن الملف يمكن الوصول إليه بشكل عام
            Storage::disk('public')->setVisibility($path, Visibility::PUBLIC);

            // الحصول على رابط URL الكامل للملف المخزن
            $url = Storage::url($path);

            // تخزين معلومات الملف في قاعدة البيانات
            $attachment = new Attachment();
            $attachment->name = $originalName; // حفظ الاسم الأصلي للملف
            $attachment->path = $url;
            $task->attachments()->save($attachment);

            // إعادة رابط URL للملف المرفوع
            return $url;
        } catch (ModelNotFoundException $e) {
            // تسجيل الخطأ وإلقاء استثناء إذا لم يتم العثور على المهمة
            Log::error('Task not found: ' . $e->getMessage());
            throw new \Exception('Task not found: ' . $e->getMessage());
        } catch (\Exception $e) {
            // تسجيل أي أخطاء أخرى وإلقاء استثناء
            Log::error('Error uploading attachment: ' . $e->getMessage());
            throw new \Exception('Error uploading attachment: ' . $e->getMessage());
        }
    }

    /**
     * Download the specified file.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        try {
            $attachment = Attachment::findOrFail($id);

            if (Storage::disk('public')->exists($attachment->path)) {
                return Storage::disk('public')->download($attachment->path, $attachment->name);
            } else {
                return response()->json(['message' => 'File not found'], 404);
            }
        } catch (ModelNotFoundException $e) {
            Log::error('Attachment not found: ' . $e->getMessage());
            return response()->json(['message' => 'Attachment not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error downloading attachment: ' . $e->getMessage());
            return response()->json(['message' => 'Error downloading attachment'], 500);
        }
    }

}
