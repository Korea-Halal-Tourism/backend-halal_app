<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\MasjidReview;
use App\Models\MasjidReviewImage;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class MasjidReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reviews = MasjidReview::all();

        if ($reviews->count() > 0) {
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'success add review masjid',
                'data' => $reviews
            ]);
        } else {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'data not found',
                'data' => null
            ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $masjidId)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'rating_id' => 'required|integer', Rule::in([1, 2, 3, 4]),
                'comment' => 'required|string|min:3|max:1000',
                'img.*' => 'mimes:jpeg,png,jpg,gif,svg|max:12048',
            ],
            [
                'rating_id' => 'rating_id cannot be empty',
                'comment.required' => 'comment cannot be empty',
                'img.image' => 'image must be an image',
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $image = $request->file('img');
        $files = array();

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Login First',
            ]);
        } else {

            //ARRAY FOR SAVING IMAGE
            $dataFile = array();

            if ($image != null) {
                foreach ($image as $files) {
                    $destinationPath = 'uploads/img/masjid_reviews/';
                    $file_name = $request->user . "_" . uniqid() . $files->getClientOriginalName();
                    if ($files->move($destinationPath, $file_name))
                        $dataFile[] = $destinationPath . $file_name;
                }
            }

            $review = new MasjidReview();

            $review->masjid_id = $masjidId;
            $review->user_id = Auth::id();
            $review->rating_id = $request->rating_id;
            $review->comment = $request->comment;

            if ($review->save()) {

                foreach ($dataFile as $file) {
                    $reviewImage = new MasjidReviewImage();
                    $reviewImage->masjid_review_id = $review->id;
                    $reviewImage->path = $file;
                    $reviewImage->save();
                }
                return response()->json([
                    'success' => true,
                    'code' => 200,
                    'message' => 'success add review masjid',
                    'data' => $review
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'code' => 400,
                    'message' => 'failed add review masjid',
                    'data' => null
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($reviewId)
    {
        $review = MasjidReview::find($reviewId);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Login First',
            ]);
        }

        if ($review == null) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'review not found',
                'data' => null
            ]);
        } else {
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'success get data review',
                'data' => $review
            ]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $reviewId)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'rating_id' => 'integer', Rule::in([1, 2]),
                'comment' => 'string|min:3|max:1000',
                'img.*' => 'mimes:jpeg,png,jpg,gif,svg|max:12048',
            ],
            [
                'rating_id.required' => 'rating_id cannot be empty',
                'comment.required' => 'comment cannot be empty',
                'img.image' => 'image must be an image'
            ]
        );

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Login First',
            ]);
        }

        $review = MasjidReview::find($reviewId);

        if ($review == null) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => "masjid review not found"
            ]);
        }

        $review->rating_id = $request->rating_id;
        $review->comment = $request->comment;

        if ($review->save()) {
            return response()->json([
                'success' => true,
                'code' => 200,
                'message' => 'success update review masjid',
                'data' => $review
            ]);
        } else {
            return response()->json([
                'success' => false,
                'code' => 400,
                'message' => 'failed update review masjid',
                'data' => null
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($reviewId)
    {
        $review = MasjidReview::find($reviewId);
        $image = MasjidReviewImage::where('masjid_review_id', $reviewId);
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Login First',
            ]);
        } else {
            if ($review == null) {
                return response()->json([
                    'success' => false,
                    'code' => 404,
                    'message' => "masjid review not found"
                ]);
            } else {
                try {
                    $image->delete();
                } catch (\Throwable $th) {
                    return response()->json([
                        'success' => false,
                        'code' => 400,
                        'message' => $th->getMessage(),
                    ]);
                }
                if ($review->delete()) {
                    return response()->json([
                        'success' => true,
                        'code' => 200,
                        'message' => 'success delete review masjid',
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'code' => 400,
                        'message' => 'failed delete review masjid',
                    ]);
                }
            }
        }
    }
}
