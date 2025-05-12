<?php

namespace App\Http\Controllers;

use Api\Support\Http\Controllers\ApiController;
use App\Http\Resources\CategoryResource;
use App\Models\Category;

class CategoryController extends ApiController
{
    public function __invoke()
    {
        return $this->success(CategoryResource::collection(Category::all()));
    }
}
