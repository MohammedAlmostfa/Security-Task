<?php

namespace App\Http\Controllers;

use App\Http\Requests\fileuploadformrequest;
use App\Models\Attachment;
use Illuminate\Http\Request;
use App\Service\ApiResponseService;
use App\Service\assetsService;

class AttachmentController extends Controller
{
    protected $assetsService;
    protected $apiResponseService;

    public function __construct(assetsService $assetsService, ApiResponseService $apiResponseService)
    {
        $this->assetsService = $assetsService;
        $this->apiResponseService = $apiResponseService;
    }

    public function store(fileuploadformrequest $request, $taskId)
    {
        $validateData = $request->validated();
        $file = $request->file('file');
        $url = $this->assetsService->storeFile($file, $taskId);

        return $this->apiResponseService->success('File uploaded successfully', ['file_url' => $url]);
    }

    public function listFiles()
    {
        $files = Attachment::all();
        return $this->apiResponseService->success('Files retrieved successfully', $files);
    }

    public function download($id)
    {
        try {
            return $this->assetsService->download($id);
        } catch (\Exception $e) {
            return $this->apiResponseService->error('Failed to download file: ' . $e->getMessage());
        }
    }
}
