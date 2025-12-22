<?php

namespace App\Http\Controllers;

use App\Http\Traits\ApiResponse;
use App\Models\Tag;
use Exception;
use Illuminate\Http\Request;

class TagController extends Controller
{

    use ApiResponse;



    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $limit = (int) $request->get('limit', 0);

            if ($limit > 0) {
                $query = Tag::latest()->limit($limit)->get();
                return $this->successResponse($query, 200);
            }

            $query = Tag::latest()->paginate(40);
            return $this->paginationResponse($query, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }




    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'tag' => 'required|string'
            ]);

            $tag = Tag::create([
                'tag' => $request->tag
            ]);

            return $this->successResponse($tag, 201);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {

            $request->validate([
                'tag_id' => 'required|exists:tags,id',
                'tag' => 'required|string'
            ]);
            $tag_id = $request->tag_id;

            $tag = Tag::findOrFail($tag_id);

            $tag->update([
                'tag' => $request->tag
            ]);

            return $this->successResponse($tag, 200);
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        try {
            $request->validate([
                'tag_id' => 'required|exists:tags,id',
            ]);
            $tag_id = $request->tag_id;

            Tag::findOrFail($tag_id)->delete();

            return $this->successResponse([], 200, 'Tag Deleted SuccessFully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
