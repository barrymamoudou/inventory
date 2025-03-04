<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Carbon;
use App\Models\Category;

class CategoryController extends Controller
{
    public function CategoryAll(){

        $categoris = Category::latest()->get();
        return view('backend.category.category_all',compact('categoris'));

    } // End Mehtod
    
    public function CategoryAdd(){
        return view('backend.category.category_add');
    } // End Mehtod 

    public function CategoryStore(Request $request){

        if(empty($request->name)){
            $notification = array('message' => 'Le nom de la catégorie ne peut pas être vide.', 
                'alert-type' => 'error');
                
            return redirect()->route('category.all')->with($notification);
        }

        //existe
        $existeDeja=Category::whereRaw('LOWER(name)=?', [strtolower($request->name)])->first();
        if($existeDeja){
            $notification = array(
                'message' => 'Category existe déjà avec ce nom ! ', 
                'alert-type' => 'error'
            );
    
            return redirect()->route('category.all')->with($notification);
        }
        Category::insert([
            'name' => $request->name, 
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(), 

        ]);

         $notification = array(
            'message' => 'Enregistrement Reussi avec Success', 
            'alert-type' => 'success'
        );

        return redirect()->route('category.all')->with($notification);

    } // End Method 
}
