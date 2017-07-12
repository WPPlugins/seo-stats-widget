<?php
/*****************************************************************************
Class Name: PHP SEO Stats
Description: Extracts various SEO data about a website from Different Sources.
Author: Sunny Verma
Website: http://99webtools.com/
Email: er.sunny.verma@gmail.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
******************************************************************************/
class SEOStats
{
private $url;
public function __construct($u) {
$this->url=$u;
}
public function get_PR() {
 $query="http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=".$this->CheckHash($this->HashURL($this->url)). "&features=Rank&q=info:".$this->url."&num=100&filter=0";
 $data=$this->getPageData($query);
 $pos = strpos($data, "Rank_");
 if($pos === false){return 0;} else{
 $pagerank = substr($data, $pos + 9);
 return $pagerank;
 }
 }
 public function get_GIP(){
 $query="http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=site:".$this->url."&filter=0&rsz=1";
 $data=$this->getPageData($query);
 $data=json_decode($data,true);
 return isset($data['responseData']['cursor']['resultCount'])?$data['responseData']['cursor']['resultCount']:0;
 }
  public function get_GBL(){
 $query="http://ajax.googleapis.com/ajax/services/search/web?v=1.0&q=link:".$this->url."&filter=0&rsz=1";
 $data=$this->getPageData($query);
 $data=json_decode($data,true);
 return isset($data['responseData']['cursor']['resultCount'])?$data['responseData']['cursor']['resultCount']:0;
 }
 public function get_SEM(){
 $query = 'http://us.backend.semrush.com/?action=report&type=domain_rank&domain='.$this->url;
 $data=$this->getPageData($query);
 $data=json_decode($data,true);
if(isset($data['rank']['data'][0]))
return array('rank'=>$data['rank']['data'][0]['Rk'],'keywords'=>$data['rank']['data'][0]['Or'],'traffic'=>$data['rank']['data'][0]['Ot'],'cost'=>$data['rank']['data'][0]['Oc']);
else
return array('rank'=>'NA','keywords'=>'NA','traffic'=>'NA','cost'=>'NA');
 }
 public function get_Alexa(){
 $query="http://data.alexa.com/data?cli=10&dat=snbamz&url=".$this->url;
 $data=$this->getPageData($query);
 $rank = preg_match("/<POPULARITY[^>]*TEXT=\"([\d]*)\"/",$data,$match)?$match[1]:0;
 $speed = preg_match("/<SPEED[^>]*TEXT=\"([\d]*)\"/",$data,$match)?$match[1]:0;
 $isdmoz=preg_match("/FLAGS=\"DMOZ\"/",$data,$match)?1:0;
 $links=preg_match("/<LINKSIN[^>]*NUM=\"([\d]*)\"/",$data,$match)?$match[1]:0;
 return array("rank"=>$rank,"dmoz"=>$isdmoz,"links"=>$links,"speed"=>$speed);
 }
 private function StrToNum($Str, $Check, $Magic)
 {
 $Int32Unit = 4294967296;
 $length = strlen($Str);
 for ($i = 0; $i < $length; $i++) {
 $Check *= $Magic;
 if ($Check >= $Int32Unit) {
 $Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
 $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
 }
 $Check += ord($Str{$i});
 }
 return $Check;
 }
 private function HashURL($String)
 {
 $Check1 = $this->StrToNum($String, 0x1505, 0x21);
 $Check2 = $this->StrToNum($String, 0, 0x1003F);
 $Check1 >>= 2;
 $Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
 $Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
 $Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);
 $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
 $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );
 return ($T1 | $T2);
 }
 private function CheckHash($Hashnum)
 {
 $CheckByte = 0;
 $Flag = 0;
 $HashStr = sprintf('%u', $Hashnum) ;
 $length = strlen($HashStr);
 for ($i = $length - 1; $i >= 0; $i --) {
 $Re = $HashStr{$i};
 if (1 === ($Flag % 2)) {
 $Re += $Re;
 $Re = (int)($Re / 10) + ($Re % 10);
 }
 $CheckByte += $Re;
 $Flag ++;
 }
 $CheckByte %= 10;
 if (0 !== $CheckByte) {
 $CheckByte = 10 - $CheckByte;
 if (1 === ($Flag % 2) ) {
 if (1 === ($CheckByte % 2)) {
 $CheckByte += 9;
 }
 $CheckByte >>= 1;
 }
 }
 return '7'.$CheckByte.$HashStr;
 }
 private function getPageData($url) {
	if(function_exists('curl_init')) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		if((ini_get('open_basedir') == '') && (ini_get('safe_mode') == 'Off')) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		}
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_FAILONERROR, 1);
		return @curl_exec($ch);
	}
	else {
		return @file_get_contents($this->url);
	}
}
}
?>