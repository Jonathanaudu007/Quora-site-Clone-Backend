<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class SpaceController extends Controller
{

    /**
     * @OA\Post(
     * path="api/user/create-space",
     * tags={"Space"},
     * summary="user create space and add a logo to it",
     * description="A user create space on the platform with a logo",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *               type="object",
     *               required={"space_name","image"},
     *               @OA\Property(property="space_name", type="required|string"),
     *               @OA\Property(property="image", type="mimes:jpg,bmp,png")

     *            ),
     *        ),
     *    ),
     *      @OA\Response(
     *          response=201,
     *          description="Created Successfully",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *          @OA\JsonContent()
     *       ),
     *      @OA\Response(response=400, description="Bad request"),
     *      @OA\Response(response=404, description="Resource Not Found"),
     * )
     */
    public function createSpace(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'space_name' => 'required|string',
            'image' => 'mimes:jpeg,png,bmp',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validations fails',
                'errors' => $validator->errors(),
            ], 422);
        }
        $space = $req->space_name;
        if($req->file('image')){
            $file = $req->file('image');
            $imagename = 'image-' . time() . '.' . $req->image->extension();
            $file->move(public_path('/uploads/space_image/'), $imagename);
        }
            $validator = $validator->validated();
            $space = new Space;
            $space->space_name = $validator['space_name'];
            $space->image = $imagename;
            $space->user_id = Auth::user()->id;
            $space->save();
        return response()->json(['message' => 'Space Added',], 200);

    }


    /**
     * @OA\Get(
     *      path="api/space/SearchTopic",
     *      tags={"Space"},
     *      summary="Search all spaces",
     *      description="search all spaces",
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(ref="#/components/schemas/ProjectResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unable to get spaces",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function searchAllSpace(Request $req)
    {
        $search_string = $req->search_string;

        $search_result = DB::table('posts')
            ->where('title', 'like', "%$search_string%")
            ->orWhere('body', 'like', "%$search_string%")
            ->get();

        return $search_result;
    }

    /**
     * @OA\Get(
     *      path="api/user/spaces",
     *      tags={"Space"},
     *      summary="Get all spaces",
     *      description="Get all spaces",
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(ref="#/components/schemas/ProjectResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unable to get spaces",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function getAllSpace()
    {
        $space = Space::all();
        return response()->json($space, 200);
    }


    /**
     * @OA\Get(
     *      path="api/user/spaces/{user_id}",
     *      tags={"Space"},
     *      summary="Get all spaces by id",
     *      description="Get all spaces by id",
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(ref="#/components/schemas/ProjectResource")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unable to get spaces",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *     )
     */
    public function getSpace($user_id)
    {
        try {
            $space = Space::where('user_id', $user_id)->get();
            return response()->json($space, 200);
        } catch (Throwable $exception) {
            return response()->json($exception->getMessage(), 500);
        }
    }
}
