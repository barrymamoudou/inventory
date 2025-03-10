<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Category;
use Auth;
use Illuminate\Support\Carbon;

class PurchaseController extends Controller
{
      //
      public function PurchaseAll(){

        $allData = Purchase::orderBy('date','desc')->orderBy('id','desc')->get();

        return view('backend.purchase.purchase_all',compact('allData'));
    }
    public function PurchaseAdd(){

      $supplier = Supplier::all();
      $unit = Unit::all();
      $category = Category::all();
      return view('backend.purchase.purchase_add',compact('supplier','unit','category'));

  } // End Method 

  public function PurchaseStore(Request $request){
    if ($request->category_id == null) {

      $notification = array(
       'message' => 'Sorry you do not select any item', 
       'alert-type' => 'error'
   );
   return redirect()->back( )->with($notification);
   } else {

       $count_category = count($request->category_id);
       for ($i=0; $i < $count_category; $i++) { 
           $purchase = new Purchase();
           $purchase->date = date('Y-m-d', strtotime($request->date[$i]));
           $purchase->purchase_no = $request->purchase_no[$i];
           $purchase->supplier_id = $request->supplier_id[$i];
           $purchase->category_id = $request->category_id[$i];

           $purchase->product_id = $request->product_id[$i];
           $purchase->buying_qty = $request->buying_qty[$i];
           $purchase->unit_price = $request->unit_price[$i];
           $purchase->buying_price = $request->buying_price[$i];
           $purchase->description = $request->description[$i];

           $purchase->created_by = Auth::user()->id;
           $purchase->status = '0';
           $purchase->save();
       } // end foreach
   } // end else 

   $notification = array(
       'message' => 'Data Save Successfully', 
       'alert-type' => 'success'
   );
   return redirect()->route('purchase.all')->with($notification); 
  }
  public function PurchaseStore1(Request $request){
    if (empty($request->category_id)) {
      return redirect()->back()->with([
          'message' => 'Sorry, you did not select any item', 
          'alert-type' => 'error'
      ]);
    }
    collect($request->category_id)->each(function ($categoryId, $index) use ($request) {
        Purchase::create([
            'date'         => date('Y-m-d', strtotime($request->date[$index])),
            'purchase_no'  => $request->purchase_no[$index],
            'supplier_id'  => $request->supplier_id[$index],
            'category_id'  => $categoryId,
            'product_id'   => $request->product_id[$index],
            'buying_qty'   => $request->buying_qty[$index],
            'unit_price'   => $request->unit_price[$index],
            'buying_price' => $request->buying_price[$index],
            'description'  => $request->description[$index],
            'created_by'   => Auth::id(),
            'status'       => '0',
        ]);
    });

    return redirect()->route('purchase.all')->with([
        'message' => 'Data saved successfully', 
        'alert-type' => 'success'
    ]);
  }


  public function PurchaseDelete($id){

    Purchase::findOrFail($id)->delete();

      $notification = array(
      'message' => 'Article d\'achat supprimé avec succès', 
      'alert-type' => 'success'
      );
    return redirect()->back()->with($notification); 

  } 

  public function PurchasePending(){

      $allData = Purchase::orderBy('date','desc')->orderBy('id','desc')->where('status','0')->get();

      return view('backend.purchase.purchase_pending',compact('allData'));
  }

  public function PurchaseApprove($id){

    $purchase = Purchase::findOrFail($id);
    $product = Product::where('id',$purchase->product_id)->first();
    $purchase_qty = ((float)($purchase->buying_qty))+((float)($product->quantity));
    $product->quantity = $purchase_qty;

    if($product->save()){
        Purchase::findOrFail($id)->update([
            'status' => '1',
        ]);
            $notification = array(
          'message' => 'Statut approuvé avec succès', 
          'alert-type' => 'success'
          );
    return redirect()->route('purchase.all')->with($notification); 

    }

}

}
