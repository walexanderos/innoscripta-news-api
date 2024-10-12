<?php
namespace App\Http\Controllers;

use App\Services\ArticleService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Articles",
 *     description=""
 * )
 *
 */

class ArticleController extends Controller
{
    use ApiResponse;
    protected $articleService;

    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }

    // Fetch Articles with Pagination and Filters
    /**
     * @OA\Get(
     *     path="/api/articles",
     *     summary="",
     *     description="Retrive and Filter articles",
     *     tags={"Articles"},
     *     security={{"BearerToken":{}}},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string"),
     *         description="Search keyword"
     *     ),
     *     @OA\Parameter(
     *         name="categories",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="string")),
     *         description="Array of categories to filter articles."
     *     ),
     *     @OA\Parameter(
     *         name="authors",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="string")),
     *         description="Array of authors to filter articles."
     *     ),
     *     @OA\Parameter(
     *         name="sources",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="array", @OA\Items(type="string")),
     *         description="Array of sources to filter articles."
     *     ),
     *     @OA\Parameter(
     *         name="date",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", format="date"),
     *         description="Filter articles by published date (YYYY-MM-DD)."
     *     ),
     *      @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="integer", default="1"),
     *         description="Filter articles by page number."
     *     ),
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
    public function index(Request $request)
    {
        $filters = $request->validate([
            'keyword' => 'nullable|string',
            'sources' => 'nullable|array',
            'categories' => 'nullable|array',
            'authors' => 'nullable|array',
            'date' => 'nullable|date',
            'page' => 'nullable|integer',
        ]);

        $articles = $this->articleService->getArticles($filters);

        return $this->message_success($articles);
    }

    // Get Single Article by ID
    /**
 * @OA\Get(
 *     path="/api/articles/{id}",
 *     summary="Get article by ID",
 *     description="Retrieve a specific article by its ID.",
 *     tags={"Articles"},
 *     security={{"BearerToken":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="Article ID."
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer"),
*                     @OA\Property(property="title", type="string"),
*                     @OA\Property(property="description", type="string"),
*                     @OA\Property(property="content", type="string"),
*                     @OA\Property(property="category", type="string"),
*                      @OA\Property(property="author", type="string"),
*                     @OA\Property(property="source", type="string"),
*                     @OA\Property(property="published_at", type="string", format="date-time"),
*                       @OA\Property(property="created_at", type="string", format="date-time"),
*                       @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Article not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Article not found.")
 *         )
 *     )
 * )
 */

    public function show($id)
    {
        $article = $this->articleService->getArticleById($id);
        if(empty($article)){
            return $this->message_error("Article not found", 404);
        }

        return $this->message_success($article);
    }
}
