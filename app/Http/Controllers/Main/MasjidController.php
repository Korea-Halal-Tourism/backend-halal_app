<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Masjid;
use App\Models\MasjidReview;
use App\Models\MasjidReviewImage;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MasjidController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|between:6,100',
                'type_id' => 'required',
                'facilities' => 'required|min:3|max:100',
                'phone' => 'required|string|min:11',
                'operating_start' => 'string|min:4',
                'operating_end' =>'string|min:4',
                'address' => 'required|string|min:10|max:100',
                'lat' => 'required|between:-90,90',
                'long' => 'required|between:-180,180',
                'img' => 'image:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'name.required' => 'name cannot be empty',
                'type.id.required' => 'type_id cannot be empty',
                'facilities.required' => 'facilities cannot be empty',
                'phone.required' => 'phone cannot be empty',
                'operating_start.ming:4' => 'operating_start must 4 or more',
                'operating_end.min:4' => 'operating_end must 4 or more',
                'lat.between' => 'The latitude must be in range between -90 and 90',
                'long.between' => 'The longitude must be in range between -100 and 100',
                'img.image' => 'Image must be and image',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if ($request->hasFile('img')) {
            $file = $request->file('img');
            $ekstension = $file->getClientOriginalExtension();
            $name = time() . '_' . $request->name . '.' . $ekstension;
            $request->img->move(public_path('storage'), $name);

            $masjid = Masjid::create([
                'name' => $request->name,
                'type_id' => $request->type_id,
                'facilities' => $request->facilities,
                'phone' => '+82 '.$request->phone,
                'operating_start' => $request->operating_start,
                'operating_end' => $request->operating_end,
                'address' => $request->address,
                'lat' => $request->lat,
                'long' => $request->long,
                'img' => $name
            ]);

            if (!$masjid) {
                return response()->json([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Failed store masjid data',
                    'data' => null
                ],400);
            } else {
                return response()->json([
                    'success' => true,
                    'code' => 200,
                    'message' => 'success store masjid',
                    'data' => $masjid
                ],200);
            }
        } else {
            $masjid = Masjid::create([
                'name' => $request->name,
                'type_id' => $request->type_id,
                'facilities' => $request->facilities,
                'phone' => '+82 '.$request->phone,
                'operating_start' => $request->operating_start,
                'operating_end' => $request->operating_end,
                'address' => $request->address,
                'lat' => $request->lat,
                'long' => $request->long,
            ]);

            if (!$masjid) {
                return response()->json([
                    'success' => false,
                    'code' => 400,
                    'message' => 'Failed store masjid data',
                    'data' => null
                ],400);
            } else {
                return response()->json([
                    'success' => true,
                    'code' => 200,
                    'message' => 'success store masjid',
                    'data' => $masjid
                ],200);
            }
        }
    }

    public function show(Request $request)
    {
        $isPaginate = $request->isPaginate === 'true'? true: false;

        if (!$isPaginate) {
            $masjids = Masjid::all();

            if ($masjids == null) {
                return response()->json([
                    'success' => false,
                    'code' => 404,
                    'message' => 'Masjid Not Found',
                    'data' => null
                ],404);
            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'success get all masjid data',
                'data' => $masjids
            ],200);
        } else {
            $paginate = DB::table('masjids')->orderBy('name', 'asc')->paginate(4);
            if (!$paginate) {
                return response()->json([
                    'success' => false,
                    'code' => 400,
                    'message' => 'failed pagination masjid',
                    'data' => null
                ],400);
            } else {
                return response()->json([
                    'success' => true,
                    'code' => 200,
                    'message' => 'success pagination masjid',
                    'data' => $paginate
                ],200);
            }
        }
    }

    public function getByType(Request $request, $typeId)
    {
        $isPaginate = $request->isPaginate === 'true'? true: false;
        $perPage = $request->perPage;
        $page = $request->page;

        if (!$isPaginate) {
            $masjids = Masjid::where('type_id', $typeId)->get();

            if ($masjids == null) {
                return response()->json([
                    'success' => false,
                    'code' => 404,
                    'message' => 'Masjid Not Found',
                    'data' => null
                ],404);
            }

            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'success get all masjid data',
                'data' => $masjids
            ],200);
        } else {
            $paginate = Masjid::where('type_id', $typeId)->paginate($perPage, ['*'], 'page', $page);
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'success pagination masjid',
                'data' => $paginate
            ],200);
        }
    }
    

    public function index($id)
    {
        $masjid = Masjid::where('id', $id)->get();

        if (!$masjid) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Masjid Not Found',
                'data' => null
            ],404);
        } else {
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'success get detail masjid',
                'data' => $masjid
            ],200);
        }
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'string|between:6,100',
                'type_id' => 'required',
                'facilities' => 'min:3|max:100',
                'phone' => 'string|min:11',
                'operating_start' => 'string|min:4',
                'operating_end' =>'string|min:4',
                'address' => 'string|min:10|max:100',
                'lat' => 'between:-90,90',
                'long' => 'between:-180,180',
                'img' => 'image:jpeg,png,jpg,gif,svg|max:2048',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }


        $masjid = Masjid::findOrFail($id);
        $masjid->name = $request->name;
        $masjid->lat = $request->lat;
        $masjid->long = $request->long;
        $masjid->type_id = $request->type_id;
        $masjid->facilities = $request->facilities;
        $masjid->phone = $request->phone;
        $masjid->operating_start = $request->operating_start;
        $masjid->operating_end = $request->operating_end;
        $masjid->address = $request->address;

        if ($request->hasFile('img')) {
            $path = public_path('storage') . $masjid->img;

            if (file_exists($path)) {
                try {
                    unlink($path);
                } catch (Exception $e) {
                    return response()->json([
                        'success' => false,
                        'code' => 400,
                        'message' => $e
                    ],400);
                }
            }

            $file = $request->file('img');
            $ekstension = $file->getClientOriginalExtension();
            $name = time() . '_' . $request->name . '.' . $ekstension;
            $request->img->move(public_path('storage'), $name);

            $masjid->img = $name;
        }

        if ($masjid->save()) {
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'success update masjid',
                'data' => $masjid
            ],200);
        } else {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'failed update masjid',
                'data' => null
            ],400);
        }
    }

    public function getMasjidPhoto($masjidId)
    {
        $masjid = Masjid::where('id', $masjidId)->first();
        $masjidReview = MasjidReview::where('masjid_id', $masjidId)->get();

        if ($masjid == null) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'masjid_review not found',
                'data' => null
            ],404);
        }

        $arrPath = array();
        array_push($arrPath, url('/').'/'. $masjid->img);
        foreach($masjidReview as $item)
        {
            $masjidPhotos = MasjidReviewImage::where('masjid_review_id', $item->id)->get();
            foreach ($masjidPhotos as $img) {
                array_push($arrPath, url('/').'/'. $img->path);
            }
        }

        if (!$arrPath) {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'failed get masjid photos',
                'data' => null
            ],400);
        }else{
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'success get masjid photos',
                'data' => $arrPath
            ],200);
        }
        

    }
}
