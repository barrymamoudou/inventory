<?php

namespace App\Http\Controllers\Pos;

use Auth;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;

class SupplierController extends Controller
{
    public function SupplierAll(){
        $suppliers = Supplier::latest()->get();
        return view('backend.supplier.supplier_all',compact('suppliers'));

    } // End Method 

    public function SupplierAdd(){
        return view('backend.supplier.supplier_add');
    }

    public function SupplierStore(Request $request ){
        Supplier::insert([
            'name' => $request->name,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'address' => $request->address,
            'created_by' => Auth::user()->id,
            'created_at' => Carbon::now(), 

        ]);

         $notification = array(
            'message' => 'Fournisseur ajoute avec success', 
            'alert-type' => 'success'
        );

        return redirect()->route('supplier_all')->with($notification);

    }
    
    public function SupplierEdit($id){

        $supplier = Supplier::findOrFail($id);
        return view('backend.supplier.supplier_edit',compact('supplier'));

    } // End Method 

    public function SupplierUpdate(Request $request){

        $sullier_id = $request->id;

        Supplier::findOrFail($sullier_id)->update([
            'name' => $request->name,
            'mobile_no' => $request->mobile_no,
            'email' => $request->email,
            'address' => $request->address,
            'updated_by' => Auth::user()->id,
            'updated_at' => Carbon::now(), 

        ]);

         $notification = array(
            'message' => 'Fournisseur Modifie avec success ', 
            'alert-type' => 'success'
        );

        return redirect()->route('supplier_all')->with($notification);

    } // End Method

    
    public function SupplierDelete($id){

        Supplier::findOrFail($id)->delete();
             $notification = array(
             'message' => 'Product Deleted Successfully', 
             'alert-type' => 'success'
         );
 
         return redirect()->back()->with($notification); 
 
     } // End Method 

    
}
