<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdRequest;
use App\Http\Resources\AdResource;
use League\Glide\Api\Api;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::latest()->paginate(1);
        if(count($ads) > 0){
            if($ads->total() > $ads->perPage()){
                $data = [
                    "records" => AdResource::collection($ads),
                    "pagination links" => [
                        'current page' => $ads->currentPage(),
                        'per page' => $ads->perPage(),
                        'total' => $ads->total(),
                        'links'=>[
                            'first'=>$ads->url(1),
                            'last'=>$ads->url($ads->lastPage())
                        ],
                    ],
                ];
            }else{
                $data = AdResource::collection($ads);
            }
            return ApiResponse::sendResponse(200, 'Ads Retrieved Successfully',$data);
        }
        return ApiResponse::sendResponse(200, 'No Ads available', []);
    }

    public function latest()
    {
        $ads = Ad::latest()->take(2)->get();
        if (count($ads) > 0){
            return ApiResponse::sendResponse(200,'Ads Retrieved Successfully',AdResource::collection($ads));
        }
        return ApiResponse::sendResponse(401,'No Ads available',[]);

    }

    public function domain($domain_id)
    {
        $ads = Ad::where("domain_id",$domain_id)->latest()->get();
        if (count($ads) > 0){
            return ApiResponse::sendResponse(200,'Ads By Domain Retrieved Successfully',AdResource::collection($ads));
        }
        return ApiResponse::sendResponse(401,'No Ads available',[]);
    }

    public function search(Request $request)
    {
        $word = $request->has('search') ? $request->input('search') : null;
        $ads = Ad::when($word != null , function($query) use($word){
            $query->where("title","like","%". $word  ."%");
        })->latest()->get();
        if (count($ads) > 0){
            return ApiResponse::sendResponse(200,'Search Results',AdResource::collection($ads));
        }
        return ApiResponse::sendResponse(401,'No matching keywords',[]);
    }

    public function create(AdRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        $record = Ad::create($data);
        if($record) return ApiResponse::sendResponse(201,'Your Ad has been created successfully',
        new AdResource($record));

    }

    public function update(AdRequest $request, $adId)
    {
        $ad = Ad::findOrFail($adId);
        if($ad->user_id != $request->user()->id){
            return ApiResponse::sendResponse(403,"You aren\'t allowed to make this action .",[]);
        }

        $data = $request->validated();
        $updated = $ad->update($data);
        if($updated) return ApiResponse::sendResponse(201,'Your Ad has been updated successfully',
            new AdResource($ad));

    }

    public function delete(Request $request, $adId)
    {
        $ad = Ad::findOrFail($adId);
        if($ad->user_id != $request->user()->id){
            return ApiResponse::sendResponse(403,"You aren\'t allowed to make this action .",[]);
        }

        $success = $ad->delete();
        if($success) return ApiResponse::sendResponse(200,'Your Ad has been deleted successfully',
            []);

    }

    public function myads(Request $request)
    {
        $ads = Ad::where('user_id',$request->user()->id)->latest()->get();
        if(count($ads) > 0){
            return ApiResponse::sendResponse(200,'Your Ads',AdResource::collection($ads));
        }
            return ApiResponse::sendResponse(200,'You don\'t have Ads yet',[]);

    }
}
