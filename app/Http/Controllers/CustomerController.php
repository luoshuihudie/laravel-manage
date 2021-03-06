<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Http\Controllers\Controller;
// use Dingo\Api\Routing\Helpers;
// use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;


class CustomerController extends Controller
{
  public function getCustomer()
  {
    try {
      $page = input::get('page');
      $signed = input::get('signed');
      if($page != '' && $signed == '') {
        $size = input::get('size');
        $keyword = input::get('keyword');
        $dataStart = ($page-1)*$size;
        // $user = DB::select("select name,phone,email,department,isSuperAdmin from laravel_manage_customer limit {$dataStart},{$size}");
        $customer = DB::table('laravel_manage_customer')
        ->select('*')
        ->where('customer_name', 'like', '%'.$keyword.'%')
        ->orWhere('customer_contacts', 'like', '%'.$keyword.'%')
        ->orWhere('user_name', 'like', '%'.$keyword.'%')
        // ->orWhere('bill_order_num', 'like', '%'.$keyword.'%')
        ->offset($dataStart)
        ->limit($size)
        ->get();
        $count = DB::table('laravel_manage_customer')
        ->select('*')
        ->where('customer_name', 'like', '%'.$keyword.'%')
        ->orWhere('customer_contacts', 'like', '%'.$keyword.'%')
        ->orWhere('user_name', 'like', '%'.$keyword.'%')
        // ->orWhere('bill_order_num', 'like', '%'.$keyword.'%')
        ->count();
      } else if($page != '' && $signed == 'yes') {
        $size = input::get('size');
        $keyword = input::get('keyword');
        $dataStart = ($page-1)*$size;
        $customer2 = DB::table('laravel_manage_customer')
        ->select('*')
        ->where('customer_name', 'like', '%'.$keyword.'%')
        ->orWhere('customer_contacts', 'like', '%'.$keyword.'%')
        ->orWhere('user_name', 'like', '%'.$keyword.'%')
        // ->orWhere('bill_order_num', 'like', '%'.$keyword.'%')
        // ->orWhere('bill_order_num',null)
        ->offset($dataStart)
        ->limit($size)
        ->get();
        // $customer = json_decode($customer2,true);
        // $customer = array_filter($customer, function($el) {
        //   return $el['bill_order_num'] != null;
        // });
        $count = count($customer2);
      } else if($page == '' && $signed == 'yes') {
        $customer2 = DB::table('laravel_manage_customer')
        ->get();
        // $customer = json_decode($customer2,true);
        // $customer = array_filter($customer, function($el) {
        //   return $el['bill_order_num'] != null;
        // });
        $count = count($customer2);
      } else if($page == '' && $signed == '') {
        $customer = DB::table('laravel_manage_customer')
        ->get();
        $count = DB::table('laravel_manage_customer')
        ->count();
      }
      
      $response = [
        'data' => $customer,
        'total' => $count
      ];
      return Response::json($response);
    } catch (Exception $e) {
        report($e);
        return false;
    }

  }
  public function updateCustomer (Request $request) {
    $action = $request->input('action');
    if($action=='add' || $action == 'edit') {
      $inputDate = $request->input('inputDate');
      $user_name = $request->input('user_name');
      $customer_resources = $request->input('customer_resources');
      $customer_name = $request->input('customer_name');
      $customer_contacts = $request->input('customer_contacts');
      $customer_phone = $request->input('customer_phone');
      // $customer_area = $request->input('customer_area');
      $customer_website = $request->input('customer_website');
      $customer_email = $request->input('customer_email');
      // $moveDate = $request->input('moveDate');
      // $company_address = $request->input('company_address');
    } else if($action=='bill') {
      $bill_order_num = $request->input('bill_order_num');
      $bill_sale_date = $request->input('bill_sale_date');
      $bill_sale_money = $request->input('bill_sale_money');
      $bill_sale_discount = $request->input('bill_sale_discount');
      $bill_sale_first_money = $request->input('bill_sale_first_money');
      $bill_sale_first_money_method = $request->input('bill_sale_first_money_method');
    
      $bill_info_fee = $request->input('bill_info_fee');
      $bill_info_fee_method = $request->input('bill_info_fee_method');
      $bill_deliery_date = $request->input('bill_deliery_date');
      $bill_payment_method = $request->input('bill_payment_method');
      $company_open_bank = $request->input('company_open_bank');
    } else {
      $invoice_raise = $request->input('invoice_raise');
      $invoice_num = $request->input('invoice_num');
      $invoice_money = $request->input('invoice_money');
      $invoice_type = $request->input('invoice_type');
      $invoice_desc = $request->input('invoice_desc');
    }
    if($action == 'add') {
      $addCustomer = DB::table('laravel_manage_customer')->insert(
        ['inputDate' => $inputDate, 'user_name' => $user_name, 'customer_resources' => $customer_resources, 
        'customer_name' => $customer_name,         
        'customer_contacts' => $customer_contacts, 
        'customer_phone' => $customer_phone,       
        // 'customer_area' => $customer_area, 
        'customer_email' => $customer_email, 
        // 'moveDate' => $moveDate,                   
        // 'company_address' => $company_address,     
         ]
      );
      if($addCustomer) {
        $response = [
          'message' => '新增成功',
          'status' => 200
        ];
        return Response::json($response);
      } else {
        $response = [
          'message' => '新增失败',
          'status' => 403
        ];
        return Response::json($response);
      }
    } else if($action == 'edit') {
      $customer_id = $request->input('customer_id');
      $updateCustomer = DB::update('update laravel_manage_customer set 
      inputDate = ?,            user_name = ?,     customer_resources = ?, 
      customer_name = ?,        
      customer_contacts = ?,    
      customer_phone = ?,     
      customer_email = ? where customer_id = ?',
      [$inputDate, $user_name,  $customer_resources, 
      $customer_name,         
      $customer_contacts,    
      $customer_phone,       
      $customer_email, 
      $customer_id]);
      if($updateCustomer) {
        $response = [
          'message' => '编辑成功',
          'status' => 200
        ];
        return Response::json($response);
      } else {
        $response = [
          'message' => '编辑失败',
          'status' => 403
        ];
        return Response::json($response);
      }
      
    } else if ($action == 'bill') {
      $customer_id = $request->input('customer_id');
      $updateCustomerBill = DB::update('update laravel_manage_customer set 
      bill_order_num = ?,        bill_sale_date = ?,      
      bill_sale_money = ?,       bill_sale_discount = ?, 
      bill_sale_first_money = ?, bill_sale_first_money_method = ?, 
      bill_info_fee = ?,         bill_info_fee_method = ?, 
      bill_deliery_date = ?,     bill_payment_method = ?, 
      company_open_bank = ? where customer_id = ?',
      [$bill_order_num,       $bill_sale_date, 
      $bill_sale_money,       $bill_sale_discount, 
      $bill_sale_first_money, $bill_sale_first_money_method, 
      $bill_info_fee,         $bill_info_fee_method, 
      $bill_deliery_date,     $bill_payment_method, 
      $company_open_bank,     $customer_id]);
      if($updateCustomerBill) {
        $response = [
          'message' => '开单成功',
          'status' => 200
        ];
        return Response::json($response);
      } else {
        $response = [
          'message' => '开单失败',
          'status' => 403
        ];
        return Response::json($response);
      }
    } else if ($action == 'invoice') {
      $customer_id = $request->input('customer_id');
      $updateCustomerInvoice = DB::update('update laravel_manage_customer set 
      invoice_raise = ?,   invoice_num = ?,      
      invoice_money = ?,   invoice_type = ?, 
      invoice_desc = ? where customer_id = ?',
      [$invoice_raise,     $invoice_num, 
      $invoice_money,      $invoice_type, 
      $invoice_desc,     $customer_id]);
      if($updateCustomerInvoice) {
        $response = [
          'message' => '保存成功',
          'status' => 200
        ];
        return Response::json($response);
      } else {
        $response = [
          'message' => '保存失败',
          'status' => 403
        ];
        return Response::json($response);
      }
    }
    
  }
  public function delCustomer (Request $request) {
    $customer_id = $request->input('customer_id');
    $delCustomer = DB::delete('delete from laravel_manage_customer where customer_id = ?',[$customer_id]);
    if($delCustomer) {
      $response = [
        'message' => '删除成功',
        'status' => 200
      ];
      return Response::json($response);
    } else {
      $response = [
        'message' => '删除失败',
        'status' => 403
      ];
      return Response::json($response);
    }
  }



}