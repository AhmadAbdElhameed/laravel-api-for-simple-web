<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Models\Ad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdRequest;
use App\Http\Resources\AdResource;

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

    }

    public function update(AdRequest $request, $adId)
    {
    }

    public function delete(Request $request, $adId)
    {

    }

    public function myads(Request $request)
    {

    }
}
