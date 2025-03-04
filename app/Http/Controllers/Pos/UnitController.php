<?php

namespace App\Http\Controllers\Pos;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Auth;

class UnitController extends Controller
{
    public function UnitAll(){

        $units = Unit::latest()->get();
        return view('backend.unit.unit_all',compact('units'));
    } // End Method 

    public function UnitAdd(){
        return view('backend.unit.unit_add');
    } // End Method 



     public function UnitStore(Request $request){

        if(empty($request->name)){
            $notification = array('message' => 'Le nom Unité ne peut pas être vide.', 
                'alert-type' => 'error');
                
            return redirect()->route('unit.all')->with($notification);
        }

        $message=Unit::whereRaw('LOWER(name)= ?',[strtolower($request->name)])->first();

        if($message){
            $notification = array(
                'message' => 'Unité existe déjà avec ce nom !', 
                'alert-type' => 'error'
            );
            return redirect()->route('unit.all')->with($notification);
        }

        Unit::insert([
            'name' => $request->name, 
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(), 

        ]);

         $notification = array(
            'message' => 'Unit Inserted Successfully', 
            'alert-type' => 'success'
        );

        return redirect()->route('unit.all')->with($notification);

    } // End Method 

    public function UnitEdit($id){

        $unit = Unit::findOrFail($id);
      return view('backend.unit.unit_edit',compact('unit'));

  }// End Method 


  public function UnitUpdate(Request $request){

      $unit_id = $request->id;

      Unit::findOrFail($unit_id)->update([
          'name' => $request->name, 
          'updated_by' => Auth::user()->id,
          'updated_at' => Carbon::now(), 

      ]);

       $notification = array(
          'message' => 'Modification reussi avec success', 
          'alert-type' => 'success'
      );

      return redirect()->route('unit.all')->with($notification);

  }// End Method 


  public function UnitDelete($id){

        Unit::findOrFail($id)->delete();

     $notification = array(
          'message' => 'Suppression reussion avec success', 
          'alert-type' => 'success'
      );

      return redirect()->back()->with($notification);

  } // End Method 

}
