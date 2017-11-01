
<?php
//1.发送短信
//调用 TextMagic API。

// Include the TextMagic PHP lib 
require('textmagic-sms-api-php/TextMagicAPI.php'); 

// Set the username and password information 
$username = 'myusername'; 
$password = 'mypassword'; 
// Create a new instance of TM 
$router = new TextMagicAPI(array( 
'username' => $username, 
'password' => $password 
)); 

// Send a text message to '999-123-4567' 
$result = $router->send('Wake up!', array(9991234567), true); 

// result: Result is: Array ( [messages] => Array ( [19896128] => 9991234567 ) [sent_text] => Wake up! [parts_count] => 1 ) 
//2. 根据IP查找地址

function detect_city($ip) { 
$default = 'UNKNOWN'; 
if (!is_string($ip) || strlen($ip) < 1 || $ip == '127.0.0.1' || $ip == 'localhost') 
 $ip = '8.8.8.8'; 
$curlopt_useragent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2) Gecko/20100115 Firefox/3.6 (.NET CLR 3.5.30729)'; 
$url = 'http://ipinfodb.com/ip_locator.php?ip=' . urlencode($ip); 
$ch = curl_init(); 
$curl_opt = array( 
 CURLOPT_FOLLOWLOCATION => 1, 
 CURLOPT_HEADER  => 0, 
 CURLOPT_RETURNTRANSFER => 1, 
 CURLOPT_USERAGENT => $curlopt_useragent, 
 CURLOPT_URL  => $url, 
 CURLOPT_TIMEOUT   => 1, 
 CURLOPT_REFERER   => 'http://' . $_SERVER['HTTP_HOST'], 
); 
curl_setopt_array($ch, $curl_opt); 
$content = curl_exec($ch); 
if (!is_null($curl_info)) { 
 $curl_info = curl_getinfo($ch); 
} 
curl_close($ch); 
if ( preg_match('{<li>City : ([^<]*)</li>}i', $content, $regs) ) { 
 $city = $regs[1]; 
} 
if ( preg_match('{<li>State/Province : ([^<]*)</li>}i', $content, $regs) ) { 
 $state = $regs[1]; 
} 
if( $city!='' && $state!='' ){ 
 $location = $city . ', ' . $state; 
 return $location; 
}else{ 
 return $default; 
} 
} 

//3. 显示网页的源代码

// display source code  
$lines = file('http://google.com/');  
foreach ($lines as $line_num => $line) {  
  // loop thru each line and prepend line numbers  
  echo "Line #<b>{$line_num}</b> : " . htmlspecialchars($line) . "<br>\n";  
} 


//4. 检查服务器是否使用HTTPS

if ($_SERVER['HTTPS'] != "on") { 
echo "This is not HTTPS"; 
}else{ 
echo "This is HTTPS"; 
} 


//5. 显示Facebook粉丝数量

function fb_fan_count($facebook_name){ 
// Example: https://graph.facebook.com/digimantra 
$data = json_decode(file_get_contents("https://graph.facebook.com/".$facebook_name)); 
echo $data->likes; 
} 

//6. 检测图片的主要颜色

$i = imagecreatefromjpeg("image.jpg"); 
for ($x=0;$x<imagesx($i);$x++) { 
for ($y=0;$y<imagesy($i);$y++) { 
$rgb = imagecolorat($i,$x,$y); 
$r = ($rgb >> 16) & 0xFF; 
$g = ($rgb >> '?')& 0xFF; 
$b = $rgb & 0xFF; 

$rTotal += $r; 
$gTotal += $g; 
$bTotal += $b; 
$total++; 
} 
} 
$rAverage = round($rTotal/$total); 
$gAverage = round($gTotal/$total); 
$bAverage = round($bTotal/$total); 

//7. 获取内存使用信息

echo "Initial: ".memory_get_usage()." bytes \n"; 
/* prints 
Initial: 361400 bytes 
*/ 

// let's use up some memory 
for ($i = 0; $i < 100000; $i++) { 
$array []= md5($i); 
} 

// let's remove half of the array 
for ($i = 0; $i < 100000; $i++) { 
unset($array[$i]); 
} 

echo "Final: ".memory_get_usage()." bytes \n"; 
/* prints 
Final: 885912 bytes 
*/ 

echo "Peak: ".memory_get_peak_usage()." bytes \n"; 
/* prints 
Peak: 13687072 bytes 
*/ 

//8. 使用 gzcompress() 压缩数据

$string = 
"Lorem ipsum dolor sit amet, consectetur 
adipiscing elit. Nunc ut elit id mi ultricies 
adipiscing. Nulla facilisi. Praesent pulvinar, 
sapien vel feugiat vestibulum, nulla dui pretium orci, 
non ultricies elit lacus quis ante. Lorem ipsum dolor 
sit amet, consectetur adipiscing elit. Aliquam 
pretium ullamcorper urna quis iaculis. Etiam ac massa 
sed turpis tempor luctus. Curabitur sed nibh eu elit 
mollis congue. Praesent ipsum diam, consectetur vitae 
ornare a, aliquam a nunc. In id magna pellentesque 
tellus posuere adipiscing. Sed non mi metus, at lacinia 
augue. Sed magna nisi, ornare in mollis in, mollis 
sed nunc. Etiam at justo in leo congue mollis. 
Nullam in neque eget metus hendrerit scelerisque 
eu non enim. Ut malesuada lacus eu nulla bibendum 
id euismod urna sodales. "; 

$compressed = gzcompress($string); 

echo "Original size: ". strlen($string)."\n"; 
/* prints 
Original size: 800 
*/ 

echo "Compressed size: ". strlen($compressed)."\n"; 
/* prints 
Compressed size: 418 
*/ 

// getting it back 
$original = gzuncompress($compressed); 

//9. 使用PHP做Whois检查

function whois_query($domain) { 
// fix the domain name: 
$domain = strtolower(trim($domain)); 
$domain = preg_replace('/^http:\/\//i', '', $domain); 
$domain = preg_replace('/^www\./i', '', $domain); 
$domain = explode('/', $domain); 
$domain = trim($domain[0]); 

// split the TLD from domain name 
$_domain = explode('.', $domain); 
$lst = count($_domain)-1; 
$ext = $_domain[$lst]; 

// You find resources and lists 
// like these on wikipedia: 
// 
// http://de.wikipedia.org/wiki/Whois 
// 
$servers = array( 
"biz" => "whois.neulevel.biz", 
"com" => "whois.internic.net", 
"us" => "whois.nic.us", 
"coop" => "whois.nic.coop", 
"info" => "whois.nic.info", 
"name" => "whois.nic.name", 
"net" => "whois.internic.net", 
"gov" => "whois.nic.gov", 
"edu" => "whois.internic.net", 
"mil" => "rs.internic.net", 
"int" => "whois.iana.org", 
"ac" => "whois.nic.ac", 
"ae" => "whois.uaenic.ae", 
"at" => "whois.ripe.net", 
"au" => "whois.aunic.net", 
"be" => "whois.dns.be", 
"bg" => "whois.ripe.net", 
"br" => "whois.registro.br", 
"bz" => "whois.belizenic.bz", 
"ca" => "whois.cira.ca", 
"cc" => "whois.nic.cc", 
"ch" => "whois.nic.ch", 
"cl" => "whois.nic.cl", 
"cn" => "whois.cnnic.net.cn", 
"cz" => "whois.nic.cz", 
"de" => "whois.nic.de", 
"fr" => "whois.nic.fr", 
"hu" => "whois.nic.hu", 
"ie" => "whois.domainregistry.ie", 
"il" => "whois.isoc.org.il", 
"in" => "whois.ncst.ernet.in", 
"ir" => "whois.nic.ir", 
"mc" => "whois.ripe.net", 
"to" => "whois.tonic.to", 
"tv" => "whois.tv", 
"ru" => "whois.ripn.net", 
"org" => "whois.pir.org", 
"aero" => "whois.information.aero", 
"nl" => "whois.domain-registry.nl" 
); 

if (!isset($servers[$ext])){ 
die('Error: No matching nic server found!'); 
} 

$nic_server = $servers[$ext]; 

$output = ''; 

// connect to whois server: 
if ($conn = fsockopen ($nic_server, 43)) { 
fputs($conn, $domain."\r\n"); 
while(!feof($conn)) { 
 $output .= fgets($conn,128); 
} 
fclose($conn); 
} 
else { die('Error: Could not connect to ' . $nic_server . '!'); } 

return $output; 
} 


//10. 通过Email发送PHP错误
 
// Our custom error handler 
function nettuts_error_handler($number, $message, $file, $line, $vars){ 
$email = " 
<p>An error ($number) occurred on line 
<strong>$line</strong> and in the <strong>file: $file.</strong> 
<p> $message </p>"; 

$email .= "<pre>" . print_r($vars, 1) . "</pre>"; 

$headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n"; 

// Email the error to someone... 
error_log($email, 1, 'you@youremail.com', $headers); 

// Make sure that you decide how to respond to errors (on the user's side) 
// Either echo an error message, or kill the entire project. Up to you... 
// The code below ensures that we only "die" if the error was more than 
// just a NOTICE. 
if ( ($number !== E_NOTICE) && ($number < 2048) ) { 
die("There was an error. Please try again later."); 
} 
} 

// We should use our custom function to handle errors. 
set_error_handler('nettuts_error_handler'); 

// Trigger an error... (var doesn't exist) 
echo $somevarthatdoesnotexist; 

//来源：https://zhuanlan.zhihu.com/p/30590357