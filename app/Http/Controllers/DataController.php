<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Data;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Http\Requests;
use Datatables;

class DataController extends Controller
{
    function Curl(){
    	$data = new Data();
    	$result = $data->dothi('https://dothi.net/ban-nha-rieng-tp-hcm.htm');
	    if(count($result) == 20){
	        $data_dothi = $data->dothi("https://dothi.net/ban-nha-rieng-tp-hcm/p2.htm");
	        $result = array_merge($result,$data_dothi);
	    }

	    // $result = $data->banhangsg('http://bannhasg.com/ban-nha-rieng-tp-hcm.htm');
	    // if(count($result) == 20){
	    //     $data_bds = $data->banhangsg("http://bannhasg.com/ban-nha-rieng-tp-hcm/p2.htm");
	    //     $result = array_merge($result,$data_bds);
	    // }
    	// $data = DB::table('data')->get();
	    echo "<pre>";
	    var_dump($result);
	    // $result = array_reverse($result);
	    // $results = DB::table('data')->insert($result);
	    // if(count($result) < 1){
     //       return 'Fail';
     //    }else{
     //        return 'Success';
     //    }
    }

    function getData(){
  //   	$data = DB::table('data')
  //                   ->orderBy('id', 'desc')
  //                   ->get();
  //   	$currentPage = LengthAwarePaginator::resolveCurrentPage();
		// $itemCollection = collect($data);
		// $perPage = 20;
		// // $perPage = 20;
		// $currentPageItems = $itemCollection->slice(($currentPage * $perPage) - $perPage, $perPage)->all();
		// $data= new LengthAwarePaginator($currentPageItems , count($itemCollection), $perPage);
		// $data->setPath(request()->url());
  //   	return View('getData',compact('data'));
    	return View('getData');
    }

    public function anyData()
    {
    	return $data = DB::table('data')
                    ->orderBy('id', 'desc')
                    ->get();

        return Datatables::of($data)->addColumn('action', function ($data) {
                return '<a href="editData/'.$data->id.'" >Chỉnh sửa</a>';
            })->addColumn('trangthai', function ($data) {
                return ($data->lansua == null) ? '<span style="color:red">Chưa sửa</span>' : '<span style="color:blue">Đã sửa</span>';
            })->addColumn('trangthai', function ($data) {
                return ($data->lansua == null) ? '<span style="color:red">Chưa sửa</span>' : '<span style="color:blue">Đã sửa</span>';
            })->editColumn('lansua', function($data){
            	return ($data->lansua == null) ? '0' : $data->lansua;
            })->rawColumns(['trangthai','action'])->make(true);


    }
}
