<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Category;
use App\Models\User;
use App\Models\PhotoRequest;
use App\Models\PhotoResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class RequestController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function createThumbnail($path, $width, $height)
    {
        $img = Image::make($path)->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($path);
    }

    public function make_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'category_id' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $user = PhotoRequest::create($input);
        $success =  $input;

        return $this->sendResponse($success, 'Request made successfully.');
    }

    public function get_category(Request $request)
    {
        $categories = Category::all();
        $success['data'] =  $categories;
        return $this->sendResponse($success, 'Fetched successfully.');
    }

    public function get_my_requests(Request $request)
    {
        $all_requests = PhotoRequest::where(['id' => Auth::id()]);
        return $this->sendResponse($all_requests, 'request fetched successfully.');
    }

    public function get_requests(Request $request)
    {
        $categories = Auth::user()->category_ids;
        if ($categories !== null) {
            $all_categories = explode(',', $categories);
            foreach ($all_categories as $single) {
                $category = Category::find($single);
                $photo = PhotoRequest::where(['category_id' => $single, 'status' => 0])->first();
                if ($photo !== null) {
                    $name = User::find($photo->user_id);
                    $photo['id'] = $photo->id;
                    $photo['user_id'] = $photo->user_id;
                    $photo['name'] = $name->name;
                    $photo['description'] = $photo->description;
                    $photo['category'] = $category->name;
                    $all[] = $photo;
                }
            }
        }
        return $this->sendResponse($all, 'Fetched successfully.');
    }

    public function respond_to_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'photo_request_id' => 'required',
            'user_id' => 'required',
            'image' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        if ($request->hasFile('image')) {
            //get filename with extension
            $filenamewithextension = $request->file('image')->getClientOriginalName();

            //get filename without extension
            $filename = pathinfo($filenamewithextension, PATHINFO_FILENAME);

            //get file extension
            $extension = $request->file('image')->getClientOriginalExtension();

            //filename to store
            $filenametostore = $filename . '_' . time() . '.' . $extension;

            //small thumbnail name
            $smallthumbnail = $filename . '_small_' . time() . '.' . $extension;

            //Upload File
            $request->file('image')->storeAs('public/original', $filenametostore);
            $request->file('image')->storeAs('public/thumbnails', $smallthumbnail);

            //create small thumbnail
            $smallthumbnailpath = public_path('storage/thumbnails/' . $smallthumbnail);
            $this->createThumbnail($smallthumbnailpath, 150, 93);

            $resp['thumbnail'] = $smallthumbnail;
            $request->merge([
                'thumbnail' => env('APP_URL') . env('STORAGE_PATH') . 'thumbnail/' . $smallthumbnail,
                'high_resolution' => env('APP_URL') . env('STORAGE_PATH') . 'original/' . $filenametostore,
            ]);
        }


        $input = $request->all();
        $the_response = PhotoResponse::create($input);
        $success =  $the_response;
        return $this->sendResponse($success, 'Request made successfully.');
    }

    public function view_response(Request $request, $id, $response_id, $status = null)
    {
        if ($response_id !== null && $status !== null) {
            $resp = PhotoResponse::find($response_id);
            if ($status == 'accept') {
                $status = 1;
                $msg = 'Accepted';
            } else {
                $status = 2;
                $msg = 'Rejected';
            }
            $resp['status'] = $status;
            $resp->save();
            if ($status == 1) {
                $photo_req = PhotoRequest::find($id);
                $photo_req['status'] = 1;
                $photo_req->save();
            }
            return $this->sendResponse($resp, $msg);
        }
        $resp = PhotoResponse::select('photo_request_id', 'user_id', 'thumbnail', 'comment')->where(['photo_request_id' => $id])->get();
        return $this->sendResponse($resp, 'Fetched successfully.');
    }

    public function get_pending_responses(Request $request)
    {
        $id = Auth::id();
        $resp = PhotoResponse::select('photo_request_id', 'user_id', 'thumbnail', 'comment', 'name')
            ->join('users', 'user_id', '=', 'users.id')
            ->where(['photo_request_id' => $id, 'status' => 0])->get();
        return $this->sendResponse($resp, 'Fetched successfully.');
    }

    public function get_my_photos(Request $request)
    {
        $id = Auth::id();
        $resp = PhotoResponse::join('users', 'user_id', '=', 'users.id')
            ->where(['user_id' => $id, 'status' => 1])->get();
        return $this->sendResponse($resp, 'Fetched successfully.');
    }
}
