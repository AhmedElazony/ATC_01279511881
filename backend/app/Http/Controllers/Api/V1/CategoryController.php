<?php

namespace App\Http\Controllers\Api\V1;

use Api\Support\Http\Controllers\ApiController;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends ApiController
{
    public function __invoke()
    {
        return $this->success(CategoryResource::collection(Category::withCount('events')->get()));
    }
}
