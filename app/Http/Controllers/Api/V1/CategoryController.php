<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\Category;

class CategoryController extends Controller
{
    public function getCategories(){
        $category = Category::where('parent_id', '=' , NULL)->with('subcategories')->get();
        return response($category);
    }

    public function show($category_id){
        $category = Category::with('courses')->findOrFail($category_id);
        return response($category);
    }

    public function create(Request $request){
        $this->validate($request, [
            'name' => ['required', 'string'],
            'parent_id' => ['numeric', 'exists:categories,id']
        ]);

        $category = new Category;
        $category->name = $request->name;
        isset($request->parent_id) ? ($category->parent_id = $request->parent_id) : '';
        
        if(!$category->save()){
            return response('category creation failed', 500);
        }

        return response($category);

    }

    public function update(Request $request, $category_id){
        $this->validate($request, [
            'name' => ['string'],
            'parent_id' => ['numeric', 'exists:categories,id']
        ]);

        $category = Category::findOrFail($category_id);
        foreach($request->input() as $field => $value){
            $category->{$field} = $request->{$field};
        }
        
        if(!$category->save()){
            return response('category creation failed', 500);
        }

        return response($category);
    }

    public function delete($category_id){
        $category = Category::findOrFail($category_id);
        $category->destroy();

        return response('category deleted!');
    }
}
