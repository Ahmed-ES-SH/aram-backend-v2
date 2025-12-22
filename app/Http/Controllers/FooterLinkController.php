<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFooterListLinkRequest;
use App\Http\Traits\ApiResponse;
use App\Models\FooterLink;
use App\Models\FooterList;
use Illuminate\Http\Request;

class FooterLinkController extends Controller
{
    use ApiResponse;

    public function store(StoreFooterListLinkRequest $request)
    {
        try {
            $data = $request->validated();
            $link = FooterLink::create($data);
            return $this->successResponse($link, 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function getLinksByList()
    {
        try {
            $lists = FooterList::with('links')->get();
            return $this->successResponse($lists, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function show($linkId)
    {
        try {
            $link = FooterLink::findOrFail($linkId);
            return $this->successResponse($link, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function update(StoreFooterListLinkRequest $request, $linkId)
    {
        try {
            $data = $request->validated();
            $link = FooterLink::findOrFail($linkId);
            $link->update($data);
            return $this->successResponse($link, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }


    public function destroy($linkId)
    {
        try {
            $link = FooterLink::findOrFail($linkId);
            $link->delete();
            return $this->successResponse($link, 200);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
