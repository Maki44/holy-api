<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\API\Content;
use App\Models\API\File;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Twilio\Rest\Client;

class ContentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validatedData = $request->validate([
            'type' => 'required|string',
            'title' => 'required|string',
            'description' => 'required|string',
            'background_image' => 'required|string',
            'content_text' => 'required|string',
            'media_link' => 'required|string',
            'content_category' => 'required|string',
            'lang' => 'required|string',
            'is_original' => 'required|boolean',
            'auto_translate' => 'required|boolean',
            'is_draft' => 'required|boolean',
        ]);

        $content = Content::create([
            'type' => $validatedData['type'],
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'background_image' => $validatedData['background_image'],
            'content_text' => $validatedData['content_text'],
            'media_link' => $validatedData['media_link'],
            'content_category' => $validatedData['content_category'],
            'lang' => $validatedData['lang'],
            'is_original' => $validatedData['is_original'],
            'auto_translate' => $validatedData['auto_translate'],
            'is_draft' => $validatedData['is_draft'],
            'updated_by' => Auth::user()->id,

        ]);

        return response()->json([
            'content' => $content,
            'message' => 'Success'
        ], 200);
    }

    public function update(Request $request): JsonResponse
    {
        

        $validatedData = $request->validate([
            'id' => 'required|integer',
            'type' => 'required|string',
            'title' => 'required|string',
            'description' => 'required|string',
            'background_image' => 'required|string',
            'content_text' => 'required|string',
            'media_link' => 'required|string',
            'content_category' => 'required|string',
            'lang' => 'required|string',
            'is_original' => 'required|boolean',
            'auto_translate' => 'required|boolean',
            'is_draft' => 'required|boolean',
        ]);
        $id = $request->input('id');
        $content = Content::findOrFail($id);
        $content->fill($validatedData);
        $content->save();

        return response()->json([
            'contentConfig' => $content,
            'message' => 'Success, Content Config updated successfully'
        ], 200);

    }

    public function all(Request $request)
    {
        $request->validate([
            'content_category' => 'required|string',
            'lang' => 'required|string',
            'start' => 'nullable|integer'
        ]);
        $content_category = $request->input("content_category");
        $lang = $request->input('lang','english');
        $tenantId = $request->header('tenant_id',0); 
        $start = $request->input('start', 0); // Default to 0 if not provided
        $limit = $request->input('limit', 10); // Default to 10 if not provided

        $content = Content::with('media_link')->where("lang", $lang)
            ->select('content.id', 'content.lang', 'content.type','content.title','content.description','content.background_image','content.content_text','content.content_category',DB::raw('UNIX_TIMESTAMP(created_at)*1000 AS release_date_time'))
            ->where([["content_category", $content_category],['lang',$lang],['tenant_id',$tenantId]])
            ->offset($start)
            ->limit($limit)
            ->get();


        return $content;
    }

    public function one(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'content_category' => 'required|string',
            'lang' => 'required|string',
        ]);
        $content_category = $request->input("content_category");
        $id = $request->input('id');
        $lang = $request->input('lang');

        $content = Content::where("id", $id)
            ->select('content.id', 'content.lang', 'content.type','content.title','content.media_link','content.description','content.background_image','content.content_category',DB::raw('UNIX_TIMESTAMP(created_at)*1000 AS release_date_time'))
            ->where("lang", $lang)
            ->where("content_category", $content_category)
            ->first();

        return $content;
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
        $id = $request->input('id');
        $response = Content::where('id', $id)->delete();
        if ($response)
            return "Content deleted successfully.";
        else return "Content not found";
    }

}
