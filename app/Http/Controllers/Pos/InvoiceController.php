<?php

namespace App\Http\Controllers\Pos;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Payment;
use App\Models\PaymentDetail;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    //
    public function InvoiceAll(){
        $allData=Invoice::orderBy('date','desc')->orderBy('id', 'desc')->get();
        return view('backend.invoice.invoice_all',compact('allData'));
    }

    public function invoiceAdd(request $request){
        $category = Category::all();
        $costomer = Customer::all();
        $invoice_data = Invoice::orderBy('id','desc')->first();

        if ($invoice_data == null) {
           $firstReg = '0';
           $invoice_no = $firstReg+1;
        }else{
            $invoice_data = Invoice::orderBy('id','desc')->first()->invoice_no;
            $invoice_no = $invoice_data+1;
        }
        $date = date('Y-m-d');
        return view('backend.invoice.invoice_add',compact('invoice_no','category','costomer','date'));
    }

    public function invoiceStore(Request $request){
        //faire un controle sur la categorie et verifie si la categorie n'est pas null sinon 
        $categories=$request->category_id;
        if($categories==null){
            $notification = array(
                'message' => 'Désolé, vous ne sélectionnez aucun article', 
                'alert-type' => 'error');
            return redirect()->back()->with($notification);
        }else{
            //faire un controle sur le paiment de en partiel sur la valeur net a paye
            if($request->estimated_amount > $request->paid_amount){

                $notification = array(
                    'message' => 'Désolé, le montant payé est le maximum du prix total.', 
                    'alert-type' => 'error');
                return redirect()->back()->with($notification);
            }else{
                //Enregistrement de la facture
                $invoice=new Invoice();
                $invoice->invoice_no=$request->invoice_no;
                $invoice->date=date('Y-m-d',strtotime($request->date));
                $invoice->description=$request->description;
                $invoice->status='0';
                $invoice->created_by = Auth::user()->id;

                DB::transaction(function() use($request,$invoice){
                    //verification d'enregistrement des donnees 
                    if($invoice->save()){
                        $count_categorie=count($request->category_id);
                        for ($i=0; $i <$count_categorie; $i++) { 
                            $invoice_details=new InvoiceDetail();
                            $invoice_details->date = date('Y-m-d',strtotime($request->date));
                            $invoice_details->invoice_id=$invoice->id;
                            $invoice_details->category_id=$request->category_id[$i];
                            $invoice_details->product_id =$request->product_id [$i];
                            $invoice_details->selling_qty=$request->selling_qty[$i];
                            $invoice_details->unit_price=$request->unit_price[$i];
                            $invoice_details->selling_price=$request->selling_price[$i];
                            $invoice_details->status="1";
                            $invoice_details->created_by = Auth::user()->id;
                            $invoice_details->save(); 

                        }
                        if($request->customer_id=='0'){
                            $customer = new Customer();
                            $customer->name = $request->name;
                            $customer->mobile_no = $request->mobile_no;
                            $customer->email = $request->email;
                            $customer->save();
                            $customer_id = $customer->id;
                        }else{
                            $customer_id=$request->customer_id;
                        }
                    }
                });
            }
            $payment = new Payment();
            $payment_details = new PaymentDetail();
           
        }
    }
}
