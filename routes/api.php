<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    CategoryController,
    SubCategoryController,
    ProductController,
    PackController,
    PackProductController,
    ServiceController,
    QuoteController,
    SupportTicketController,
    ContactController,
    OrderController,
    UserController,
    QuoteRequestController,
    AccessoryController,
    BlogPostController,
    ProjectTypeController,
    ProjectController,
    ClientController
};




/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
| Toutes les routes de listing (index) + affichage (show) sont publiques.
| Les créations (store) publiques uniquement si nécessaire.
*/

Route::prefix('public')->group(function () {

    /* ----------- Categories ----------- */
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::get('/categories/{id}/subcategories', [SubCategoryController::class, 'fromCategory']);

    /* ----------- Subcategories ----------- */
    Route::get('/subcategories', [SubCategoryController::class, 'index']);
    Route::get('/subcategories/{id}', [SubCategoryController::class, 'show']);

    /* ----------- Products ----------- */
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'show']);



    // 📌 Filtres publics par catégorie / sous-catégorie
Route::get('/products/category/{categoryId}', [ProductController::class, 'getByCategory']);
Route::get('/products/subcategory/{subCategoryId}', [ProductController::class, 'getBySubCategory']);

    /* ----------- Packs ----------- */
    Route::get('/packs', [PackController::class, 'index']);
    Route::get('/packs/{id}', [PackController::class, 'show']);

    /* ----------- Services ----------- */
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{id}', [ServiceController::class, 'show']);

    /* ----------- Accessories ----------- */
    Route::get('/accessories', [AccessoryController::class, 'index']);
    Route::get('/accessories/{id}', [AccessoryController::class, 'show']);

    /* ----------- Contact ----------- */
    Route::post('/contact', [ContactController::class, 'store']);

    /* ----------- Quote ----------- */
    Route::post('/quote', [QuoteController::class, 'store']);

    /* ----------- Quote Requests ----------- */
    Route::post('/quote-request', [QuoteRequestController::class, 'store']);
    Route::get('/quote-requests', [QuoteRequestController::class, 'index']);
    Route::get('/quote-requests/{id}', [QuoteRequestController::class, 'show']);


Route::get('/posts', [BlogPostController::class, 'index']);
Route::get('/posts/{post}', [BlogPostController::class, 'show']);


    // Project Types
    Route::get('/project-types', [ProjectTypeController::class, 'index']);
    Route::get('/project-types/{projectType:slug}', [ProjectTypeController::class, 'show']);


        // Project Types
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/project/{project:slug}', [ProjectController::class, 'show']);


        // Clients
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/clients/featured', [ClientController::class, 'featured']);
    Route::get('/clients/logos', [ClientController::class, 'logos']);
    Route::get('/clients/statistics', [ClientController::class, 'statistics']);
    Route::get('/clients/industries', [ClientController::class, 'industries']);
    Route::get('/clients/search', [ClientController::class, 'search']);
    Route::get('/clients/by-industry/{industry}', [ClientController::class, 'byIndustry']);
    Route::get('/clients/{slug}', [ClientController::class, 'show']);

});

/*
|--------------------------------------------------------------------------
| PUBLIC Profile, Orders & Support (plus de sanctum)
|--------------------------------------------------------------------------
*/

Route::get('/profile', [UserController::class, 'profile']);
Route::put('/profile', [UserController::class, 'update']);

Route::get('/orders', [OrderController::class, 'userOrders']);
Route::get('/orders/{id}', [OrderController::class, 'show']);
Route::post('/orders', [OrderController::class, 'store']);

Route::get('/support', [SupportTicketController::class, 'userTickets']);
Route::post('/support', [SupportTicketController::class, 'store']);
Route::get('/support/{id}', [SupportTicketController::class, 'show']);

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (toujours publiques selon ta demande)
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {

    /* ----------- Accessories ----------- */
    Route::apiResource('accessories', AccessoryController::class);

    /* ----------- Users ----------- */
    Route::apiResource('users', UserController::class);

    /* ----------- Categories ----------- */
    Route::apiResource('categories', CategoryController::class);

    /* ----------- Subcategories ----------- */
    Route::apiResource('subcategories', SubCategoryController::class);

    /* ----------- Products ----------- */
    Route::apiResource('products', ProductController::class);

    /* ----------- Packs ----------- */
    Route::apiResource('packs', PackController::class);
    Route::post('packs/{id}/products', [PackProductController::class, 'attach']);
    Route::delete('packs/{id}/products/{productId}', [PackProductController::class, 'detach']);

    /* ----------- Quotes ----------- */
    Route::get('quotes', [QuoteController::class, 'index']);
    Route::put('quotes/{id}', [QuoteController::class, 'update']);
    Route::delete('quotes/{id}', [QuoteController::class, 'destroy']);

    /* ----------- Support ----------- */
    Route::get('support', [SupportTicketController::class, 'index']);
    Route::put('support/{id}', [SupportTicketController::class, 'update']);

    /* ----------- Orders ----------- */
    Route::get('orders', [OrderController::class, 'index']);
    Route::put('orders/{id}', [OrderController::class, 'update']);
});
