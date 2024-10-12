<?php

namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Services\UserService;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="User",
 *     description=""
 * )
 *
 */

class UserController extends Controller
{
    use ApiResponse;

    protected $userService;
    protected $articleService;

    public function __construct(UserService $userService, ArticleService $articleService)
    {
        $this->userService = $userService;
        $this->articleService = $articleService;
    }
    // Get User Preferences
    /**
     * @OA\Get(
     *     path="/api/user/preferences",
     *     summary="get user preferences",
     *     description="Get the user's preferred news sources, categories, and authors.",
     *     tags={"User"},
     *     security={{"BearerToken":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="User's preferences",
     *                 example={
     *                     "user_id": 1,
     *                     "sources": {"NewsAPI", "BBC"},
     *                     "categories": {"Technology", "Health"},
     *                     "authors": {"Jamiu Jimoh"}
     *                 }
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="null"
     *             )
     *         )
     *     )
     * )
     */

    public function getPreferences(Request $request)
    {
        $user = $request->user();
        $preferences = $this->userService->getPreferences($user);

        return $this->message_success($preferences);
    }

    /**
     * @OA\Post(
     *     path="/api/user/preferences",
     *     summary="Set user preferences",
     *     description="Update the user's preferred news sources, categories, and authors.",
     *     tags={"User"},
     *     security={{"BearerToken":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="sources",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"NewsAPI", "BBC"}
     *             ),
     *             @OA\Property(
     *                 property="categories",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"Technology", "Health"}
     *             ),
     *             @OA\Property(
     *                 property="authors",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"Jamiu Jimoh", "Hannah Smith"},
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 description="User's updated preferences",
     *                 example={
     *                     "user_id": 1,
     *                     "sources": {"NewsAPI", "BBC"},
     *                     "categories": {"Technology", "Health"},
     *                     "authors": {"Jamiu Jimoh"}
     *                 }
     *             ),
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Preferences updated successfully"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="field",
     *                     type="array",
     *                     @OA\Items(type="string"),
     *                     example={"The field must be an array."}
     *                 )
     *             )
     *         )
     *     )
     * )
     */


    public function setPreferences(Request $request)
    {
        $preferences = $request->validate([
            'sources' => 'nullable|array',
            'categories' => 'nullable|array',
            'authors' => 'nullable|array'
        ]);

        $user = $request->user();
        $user_preference = $this->userService->setPreferences($user, $preferences);

        return $this->message_success($user_preference, 'Preferences updated successfully');
    }


    /**
     * @OA\Get(
     *     path="/api/user/personalized-feed",
     *     summary="",
     *     description="Retrive Personalized feeds for user. (Filter included).",
     *     tags={"User"},
     *     security={{"BearerToken":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="category", type="string"),
     *                      @OA\Property(property="author", type="string"),
     *                     @OA\Property(property="source", type="string"),
     *                     @OA\Property(property="published_at", type="string", format="date-time"),
    *                       @OA\Property(property="created_at", type="string", format="date-time"),
    *                       @OA\Property(property="updated_at", type="string", format="date-time"),
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer"),
     *                 @OA\Property(property="last_page", type="integer"),
     *                 @OA\Property(property="per_page", type="integer"),
     *                 @OA\Property(property="total", type="integer")
     *             )
     *         )
     *     )
     * )
     */


    public function personalizedNewsFeed(Request $request)
    {
        $preferences = $this->userService->getPreferences($request->user());

        // return all news if no preference
        if (empty($preferences)) {
            $preferences = [];
        }

        $articles = $this->articleService->getArticles($preferences);
        return $this->message_success($articles);
    }
}
