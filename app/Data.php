<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Data extends Model
{
    function getHTML($url,$timeout)
{
        $userAgent = "Mozilla / 5.0 (Windows NT 6.1) AppleWebKit / 537.2 (KHTML, như Gecko) Chrome / 22.0.1216.0 Safari / 537.2";
       $ch = curl_init($url); // initialize curl with given url
       curl_setopt($ch, CURLOPT_USERAGENT, $userAgent); // set  useragent
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // write the response to a variable
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects if any
       curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // max. seconds to execute
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
       curl_setopt($ch, CURLOPT_FAILONERROR, 1); // stop when it encounters an error
       return @curl_exec($ch);
}

function getDomPath($url){
    $response= $this->getHTML($url,60);
    $thispage = new \DOMDocument();
    libxml_use_internal_errors(true);
    $thispage->loadHTML($response);
    libxml_clear_errors();
    $xpath = new \DOMXPath($thispage);
    return $xpath;
}

function strpos_array($haystack, $needles) {
        if ( is_array($needles) ) {
            foreach ($needles as $str) {
                if ( is_array($str) ) {
                    $pos = strpos_array($haystack, $str);
                } else {
                    $pos = strpos($haystack, $str);
                }
                if ($pos !== FALSE) {
                    return $newString = str_replace($str, "",$haystack);
                }
            }
        } else {
            return $newString = str_replace($needles, "",$haystack);
        }
        return $haystack;
    }
    
    // website batdongsan.com.vn
    function batdongsan($url){
        $xpath = $this->getDomPath($url);
        $baseUrl = 'https://batdongsan.com.vn';
        $price = $xpath->query("//*[contains(@class, 'product-price')]");
        $dientich = $xpath->query("//*[contains(@class, 'product-area')]");
        $link = $xpath->query("//div[@class='p-title']/h3/a/@href");
        $arrayStrPos = ['Mức giá: ','Diện tích: '];
        $last_row = DB::table('data')
                    ->where('website','=',$baseUrl)
                    ->orderBy('id', 'desc')
                    ->first();
        $db = json_decode(json_encode($last_row), true);
        $array = array();
        for($i = 0; $i < 20;$i++){

            // check trùng dữ liệu
            if(in_array(trim($baseUrl.$link->item($i)->nodeValue),$db)){
                return $array;
            }
            $array[$i]['website'] = $baseUrl;
            $array[$i]['price'] = trim($this->strpos_array($price->item($i)->nodeValue,$arrayStrPos));
            $array[$i]['dientich'] = trim($this->strpos_array($dientich->item($i)->nodeValue,$arrayStrPos));
            $array[$i]['link'] = trim($baseUrl.$link->item($i)->nodeValue);
            $arrayDetail = $this->detailPageBds($array[$i]['link']);
            $array[$i] = array_merge($array[$i],$arrayDetail);
            
           
        }
        return $array;
    }

   

    function detailPageBds($url){
        $xpath = $this->getDomPath($url);
        $baseUrl = 'http://bannhasg.com';
        $phone = $xpath->query("//div[@id='LeftMainContent__productDetail_contactMobile']/div[@class='right']");
        $tang = $xpath->query("//div[@id='LeftMainContent__productDetail_floor']/div[@class='right']");
        $address = $xpath->query("//div[@class='div-hold']/div[@class='table-detail']/div[@class='row'][2]/div[@class='right']");
        $arrayStrPos = ['Khu vực: Bán đất tại','Khu vực: Bán nhà riêng tại','Khu vực: Bán căn hộ chung cư tại','Khu vực: Bán đất nền dự án tại','Khu vực: Bán nhà biệt thự, liền kề tại'];

        if($phone->length != 0){
            $array['phone'] = trim($phone->item(0)->nodeValue);
        }else{
           $array['phone'] = trim($xpath->query("//div[@id='LeftMainContent__productDetail_contactPhone']/div[@class='right']")->item(0)->nodeValue);
        }

        if($address->length != 0){
            $address = $address->item(0)->nodeValue;
            $array['address'] = trim($this->strpos_array($address,$arrayStrPos));
        }else{
           $array['address'] = trim($xpath->query("//*[contains(@class, 'diadiem-title')]")->item(0)->nodeValue);
        }

        if($tang->length != 0){
            $tang = $tang->item(0)->nodeValue;
            $array['tang'] = trim($this->strpos_array($tang,'(tầng)'));
        }else{
            $array['tang'] = '';
        }
        
        
        return $array;

    }

    // $data_bds = batdongsan("https://batdongsan.com.vn/ban-nha-rieng-tp-hcm");
    // if(count($data_bds) == 20){
    //     $data_bds2 = batdongsan("https://batdongsan.com.vn/ban-nha-rieng-tp-hcm/p2");
    //     $data_bds = array_merge($data_bds,$data_bds2);
    // }



    // website dothi.net

    function dothi($url){
        $xpath = $this->getDomPath($url);
        $baseUrl = 'https://dothi.net';
        $price = $xpath->query("//*[contains(@class, 'price')]");
        $dientich = $xpath->query("//*[contains(@class, 'area')]");
        $link = $xpath->query("//div[@class='desc']/h3/a/@href");
        $arrayStrPos = ['Giá:','Diện tích:'];
        $last_row = DB::table('data')
                    ->where('website','=',$baseUrl)
                    ->orderBy('id', 'desc')
                    ->first();
        $db = json_decode(json_encode($last_row), true);
        $array = array();
        for($i = 0; $i < 20;$i++){

            // check trùng dữ liệu
            if($last_row){
                if(in_array(trim($baseUrl.$link->item($i)->nodeValue),$db)){
                    return $array;
                }
            }
            
            $array[$i]['website'] = $baseUrl;
            $array[$i]['price'] = trim($this->strpos_array($price->item($i)->nodeValue,$arrayStrPos));
            $array[$i]['dientich'] = trim($this->strpos_array($dientich->item($i)->nodeValue,$arrayStrPos));
            $array[$i]['link'] = trim($baseUrl.$link->item($i)->nodeValue);
            $arrayDetail = $this->detailPageDothi($array[$i]['link']);
            $array[$i] = array_merge($array[$i],$arrayDetail);
            
           
        }
        return $array;
    }

   

    function detailPageDothi($url){
        $xpath = $this->getDomPath($url);
        $baseUrl = 'http://bannhasg.com';
        $phone = $xpath->query("//table[@id='tbl2']//td[2]");
        $tang = $xpath->query("//table[@id='tbl1']//td[2]");
        $address = $xpath->query("//div[@id='ContentPlaceHolder1_ProductDetail1_divlocation']");
        $arrayStrPos = ['Khu vực: Bán đất tại','Khu vực:  Bán nhà riêng tại','Khu vực: Bán căn hộ chung cư tại','Khu vực: Bán đất nền dự án tại','Khu vực: Bán nhà biệt thự, liền kề tại'];

        if($phone->length != 0){
            $array['phone'] = trim($phone->item(3)->nodeValue);
        }else{
           $array['phone'] = '';
        }

        $address = $address->item(0)->nodeValue;
        $array['address'] = trim($this->strpos_array($address,$arrayStrPos));

        if($tang->length != 0){
            $tang = $tang->item(9)->nodeValue;
            $array['tang'] = trim($this->strpos_array($tang,'(tầng)'));
        }else{
            $array['tang'] = '';
        }
        
        
        return $array;

    }

    // $data_dothi = dothi("https://dothi.net/ban-nha-rieng-tp-hcm.htm");
    // $data = detailPageDothi('https://dothi.net/ban-nha-rieng-duong-26-3-65/ban-nha-rieng-1-tret-3-lau-dt192m2-o-263-quan-binh-tan-cach-aeon-tan-phu-1km-mien-moi-gioi-pr9470377.htm'); 
    // if(count($data_dothi) == 20){
    //     $data_dothi2 = batdongsan("https://batdongsan.com.vn/ban-nha-rieng-tp-hcm/p2");
    //     $data_dothi = array_merge($data_dothi,$data_dothi2);
    // }

    //website bannhasg.com 

    function banhangsg($url){

        $xpath = $this->getDomPath($url);
        $baseUrl = 'http://bannhasg.com';
        $price = $xpath->query("//*[contains(@class, 'price')]");
        $dientich = $xpath->query("//div[@class='area']/span[@class='info']");
        $link = $xpath->query("//h4[@class='title']/a/@href");
        $arrayStrPos = ['Mức giá:','Diện tích:'];
        $last_row = DB::table('data')
                    ->where('website','=',$baseUrl)
                    ->orderBy('id', 'desc')
                    ->first();
        $db = json_decode(json_encode($last_row), true);
        $array = array();
        for($i = 0; $i < 20;$i++){
            // check trùng dữ liệu
            if(in_array(trim($baseUrl.$link->item($i)->nodeValue),$db)){
                return $array;
            }
            $array[$i]['website'] = $baseUrl;
            $array[$i]['price'] = trim($this->strpos_array($price->item($i)->nodeValue,$arrayStrPos));
            $array[$i]['dientich'] = trim($this->strpos_array($dientich->item($i)->nodeValue,$arrayStrPos));
            $array[$i]['link'] = trim($baseUrl.$link->item($i)->nodeValue);
            $arrayDetail = $this->detailPage($array[$i]['link']);
            $array[$i] = array_merge($array[$i],$arrayDetail);
           
        }
        return $array;
    }

   

    function detailPage($url){
        $xpath = $this->getDomPath($url);
        $phone = $xpath->query("//table[@id='tbl2']//td[2]");
        $tang = $xpath->query("//table[@id='tbl3']//td[2]");
        $address = $xpath->query("//div[@class='box_detail']/h2");
        $arrayStrPos = ['Khu vực: Bán đất tại','Khu vực: Bán nhà riêng tại','Khu vực: Bán căn hộ chung cư tại','Khu vực: Bán đất nền dự án tại','Khu vực: Bán nhà biệt thự, liền kề tại'];

        if($phone->length != 0){
            $array['phone'] = trim($phone->item(3)->nodeValue);
        }else{
           $array['phone'] = trim($phone->item(2)->nodeValue);
        }

        $address = $address->item(0)->nodeValue;
        $array['address'] = trim($this->strpos_array($address,$arrayStrPos));

        if($tang->length != 0){
            $tang = $tang->item(5)->nodeValue;
            $array['tang'] = trim($this->strpos_array($tang,'(tầng)'));
        }else{
            $array['tang'] = '';
        }

        return $array;
        
    }

    // $data_bannha = banhangsg("http://bannhasg.com/ban-nha-rieng-tp-hcm.htm");
    // $data = detailPage("http://bannhasg.com/ban-nha-rieng-duong-so-79-phuong-tan-quy/can-ban-gap-1-so-can-nha-duong-so-79-tan-quyquan-7dt4x16m1-tret2-lausan-thuonggia57-ty-pr3604183.htm");
    // if(count($data_bannha) == 20){
    //     $data_bannha2 = batdongsan("https://batdongsan.com.vn/ban-nha-rieng-tp-hcm/p2");
    //     $data_bannha = array_merge($data_bannha,$data_bannha2);
    // }

    // $arrayStrPos = ['Giá:','Diện tích:'];
    // echo trim(strpos_array('Giá:3.6 Tỷ',$arrayStrPos));

}
