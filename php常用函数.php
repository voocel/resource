<?php

/* * *************************
 * 生成随机字符串，可以自己扩展   //若想唯一，只需在开头加上用户id
 * $type可以为：upper(只生成大写字母)，lower(只生成小写字母)，number(只生成数字)
 * $len为长度，定义字符串长度
 * date 2017/10/5
 * ************************** */

function random($type, $len = 0) {
    $new = '';
    $string = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';  //数据池
    if ($type == 'upper') {
        for ($i = 0; $i < $len; $i++) {
            $new .= $string[mt_rand(36, 61)];
        }
        return $new;
    }
    if ($type == 'lower') {
        for ($i = 0; $i < $len; $i++) {
            $new .= $string[mt_rand(10, 35)];
        }
        return $new;
    }
    if ($type == 'number') {
        for ($i = 0; $i < $len; $i++) {
            $new .= $string[mt_rand(0, 9)];
        }
        return $new;
    }
}

//计算该月有几天
function getdaysInmonth($month, $year) {
    $days = '';
    if ($month == 1 || $month == 3 || $month == 5 || $month == 7 || $month == 8 || $month == 10 || $month == 12)
        $days = 31;
    else if ($month == 4 || $month == 6 || $month == 9 || $month == 11)
        $days = 30;
    else if ($month == 2) {
        if (isLeapyear($year)) {
            $days = 29;
        } else {
            $days = 28;
        }
    }
    return ($days);
}

//判断是否为润年
function isLeapyear($year) {
    if ((($year % 4) == 0) && (($year % 100) != 0) || (($year % 400) == 0)) {
        return (true);
    } else {
        return (false);
    }
}

//生成订单15位
function auto_order($ord = 0) {
    //自动生成订单号  传入参数为0 或者1   0为本地  1为线上订单
    if ($ord == 0) {
        $str = '00' . time() . rand(1000, 9999); //00 本地订单
    } else {
        $str = '99' . time() . rand(1000, 9999);  //11 线上订单
    }
    return $str;
}

//生成订单15位
function auto_new_order($ord = 0) {
    srand(time());
    //自动生成订单号  传入参数为0 或者1   0为本地  1为线上订单
    if ($ord == 0) {
        $str = '00' . time() . mt_rand(100000,999999); //00 本地订单
    } else {
        $str = '11' . time() . mt_rand(100000,999999);  //11 线上订单
    }
    return $str;
}

/**
 * 检测是否为UTF8编码 
 * @param $string 检测字符串
 * @return bool
 */
function is_utf8($string) {
    return preg_match('%^(?:
          [\x09\x0A\x0D\x20-\x7E]            # ASCII
        | [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |  \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        | [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}  # straight 3-byte
        |  \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |  \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        | [\xF1-\xF3][\x80-\xBF]{3}          # planes 4-15
        |  \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
    )*$%xs', $string);
}

//处理json字符中的特殊字符
function getJsonToArr($result,$return_array=true)
{
        $tempArr = NULL;
        $result = preg_replace('/([^\\\])(":)(\d{9,})(,")/i', '${1}${2}"${3}"${4}', $result); //taobao bug,number >2^32
        $tempArr = json_decode($result, $return_array);
        if ($tempArr == NULL) {
            $patterns = array('/,+\s*\}/', '/,+\s*\]/', '/"\s+|\s+"/', '/\n|\r|\t/','/\\\/','/ss+/');
            $replacements = array('}', ']', '"', ' ','',' ');
            $result = preg_replace($patterns, $replacements, $result);            
            $tempArr = json_decode($result, $return_array);
        }

        return $tempArr;
}


//非法字符过滤函数
function has_unsafeword($str) {
    $regex = "/\/|\~|\!|\@|\#|\\$|\%|\^|\&|\*|\(|\)|\_|\+|\{|\}|\:|\<|\>|\?|\[|\]|\.|\/|\;|\'|\`|\=|\\\|\|/";
    return preg_replace($regex,"",$str);   
}

//去空格，以及字符添加斜杠
function _trim(&$value) {
    Return addslashes(trim($value));
}

/**
 * 将多维数组转为一维数组
 * @param array $arr
 * @return array
 */
function ArrMd2Ud($arr) {
    #将数值第一元素作为容器，作地址赋值。
    $ar_room = &$arr[key($arr)];
    #第一容器不是数组进去转呀
    if (!is_array($ar_room)) {
        #转为成数组
        $ar_room = array($ar_room);
    }
    #指针下移
    next($arr);
    #遍历
    while (list($k, $v) = each($arr)) {
        #是数组就递归深挖，不是就转成数组
        $v = is_array($v) ? call_user_func(__FUNCTION__, $v) : array($v);
        #递归合并
        $ar_room = array_merge_recursive($ar_room, $v);
        #释放当前下标的数组元素
        unset($arr[$k]);
    }
    return $ar_room;
}

    /**
     * 判断是PC端还是wap端访问
     * @return type判断手机移动设备访问
     */
     function isMobile()
     { 
         // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
         if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
         {
             return true;
         } 
         // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
         if (isset ($_SERVER['HTTP_VIA']))
         { 
             // 找不到为flase,否则为true
             return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
         } 
         // 脑残法，判断手机发送的客户端标志,兼容性有待提高
         if (isset ($_SERVER['HTTP_USER_AGENT']))
         {
             $clientkeywords = array ('nokia',
                 'sony',
                 'ericsson',
                 'mot',
                 'samsung',
                 'htc',
                 'sgh',
                 'lg',
                 'sharp',
                 'sie-',
                 'philips',
                 'panasonic',
                 'alcatel',
                 'lenovo',
                 'iphone',
                 'ipod',
                 'blackberry',
                 'meizu',
                 'android',
                 'netfront',
                 'symbian',
                 'ucweb',
                 'windowsce',
                 'palm',
                 'operamini',
                 'operamobi',
                 'openwave',
                 'nexusone',
                 'cldc',
                 'midp',
                 'wap',
                 'mobile'
                 ); 
             // 从HTTP_USER_AGENT中查找手机浏览器的关键字
             if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT'])))
             {
                 return true;
             } 
         } 
         // 协议法，因为有可能不准确，放到最后判断
         if (isset ($_SERVER['HTTP_ACCEPT']))
         { 
             // 如果只支持wml并且不支持html那一定是移动设备
             // 如果支持wml和html但是wml在html之前则是移动设备
             if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html'))))
             {
                 return true;
             } 
         } 
         return false;
     }
 
     //判断是否为安卓手机
     function isAndroid()
     {
         if(isset($_SERVER['HTTP_USER_AGENT'])){
             $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
             if(strpos($agent,'android') !== false)
                 return true;
         }
         return false;
     }
 
     //判断是否为iphone或者ipad
     function isIos()
     {
         if(isset($_SERVER['HTTP_USER_AGENT'])){
             $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
             if(strpos($agent, 'iphone')||strpos($agent, 'ipad'))
                 return true;
         }
         return false;
     }
 
     //判断是否为微信内置浏览器打开
     function isWechet()
     {
         if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false){
             return true;
         }
         return false;
     }
 
     //整合到一起，判断当前设备，1：安卓；2：IOS；3：微信；0：未知
     function isDevice()
     {
         if($_SERVER['HTTP_USER_AGENT']){
             $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
             if(strpos($agent, 'micromessenger') !== false)
                 return 3;
             elseif(strpos($agent, 'iphone')||strpos($agent, 'ipad'))
                 return 2;
             else
                 return 1;
         }
         return 0;
     }




    /**
     * 日期转换成几分钟前
     */
    function formatTime($date) {
        $timer = strtotime($date);
        $diff = $_SERVER['REQUEST_TIME'] - $timer;
        $day = floor($diff / 86400);
        $free = $diff % 86400;
        if($day > 0) {
            if(15 < $day && $day <30){
                return "半个月前";
            }elseif(30 <= $day && $day <90){
                return "1个月前";
            }elseif(90 <= $day && $day <187){
                return "3个月前";
            }elseif(187 <= $day && $day <365){
                return "半年前";
            }elseif(365 <= $day){
                return "1年前";
            }else{
                return $day."天前";
            }
        }else{
            if($free>0){
                $hour = floor($free / 3600);
                $free = $free % 3600;
                if($hour>0){
                    return $hour."小时前";
                }else{
                    if($free>0){
                        $min = floor($free / 60);
                        $free = $free % 60;
                    if($min>0){
                        return $min."分钟前";
                    }else{
                        if($free>0){
                            return $free."秒前";
                        }else{
                            return '刚刚';
                        }
                    }
                    }else{
                        return '刚刚';
                    }
                }
            }else{
                return '刚刚';
            }
        }
    }

    /**
     * 截取长度
     */
    function getSubString($rawString,$length='100',$etc = '...',$isStripTag=true){
        $rawString = str_replace('_baidu_page_break_tag_', '', $rawString);
        $result = '';
        if($isStripTag)
            $string = html_entity_decode(trim(strip_tags($rawString)), ENT_QUOTES, 'UTF-8');
        else
            $string = trim($rawString);
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++){
            if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0'))
            {
                if ($length < 1.0){
                    break;
                }
                $result .= substr($string, $i, $number);
                $length -= 1.0;
                $i += $number - 1;
            }else{
                $result .= substr($string, $i, 1);
                $length -= 0.5;
            }
        }
        if($isStripTag)
            $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');

        if ($i < $strlen){
            $result .= $etc;
        }
        return $result;
    }

    /**
     * utf-8和gb2312自动转化
     * @param unknown $string
     * @param string $outEncoding
     * @return unknown|string
     */
    function safeEncoding($string,$outEncoding = 'UTF-8')
    {
        $encoding = "UTF-8";
        for($i = 0; $i < strlen ( $string ); $i ++) {
            if (ord ( $string {$i} ) < 128)
                continue;

            if ((ord ( $string {$i} ) & 224) == 224) {
                // 第一个字节判断通过
                $char = $string {++ $i};
                if ((ord ( $char ) & 128) == 128) {
                    // 第二个字节判断通过
                    $char = $string {++ $i};
                    if ((ord ( $char ) & 128) == 128) {
                        $encoding = "UTF-8";
                        break;
                    }
                }
            }
            if ((ord ( $string {$i} ) & 192) == 192) {
                // 第一个字节判断通过
                $char = $string {++ $i};
                if ((ord ( $char ) & 128) == 128) {
                    // 第二个字节判断通过
                    $encoding = "GB2312";
                    break;
                }
            }
        }

        if (strtoupper ( $encoding ) == strtoupper ( $outEncoding ))
            return $string;
        else
            return @iconv ( $encoding, $outEncoding, $string );
    }

    /** 
    *对内容中的关键词添加链接 
    *只处理第一次出现的关键词，对已有链接的关键不会再加链接，支持中英文 
    *$content:string 原字符串 
    *$keyword:string  关键词 
    *$link:string,链接 
    */ 
   function yang_keyword_link($content,$keyword,$link){ 
        //排除图片中的关键词 
        $content = preg_replace( '|(<img[^>]*?)('.$keyword.')([^>]*?>)|U', '$1%&&&&&%$3', $content); 
        $regEx = '/(?!((<.*?)|(<a.*?)))('.$keyword.')(?!(([^<>]*?)>)|([^>]*?<\/a>))/si'; 
        $url='<a href="'.$link.'" target="_blank" class-og="content_guanjianci">'.$keyword.'</a>'; 
        $content = preg_replace($regEx,$url,$content,1); 
        //还原图片中的关键词 
        $content=str_replace('%&&&&&%',$keyword,$content); 
        return $content; 
    }
    
    /**
     * 遍历一个文件夹下的所有文件和子文件夹
     */
    function my_scandir($dir){
        $files = array();
        if (is_dir($dir)) {
            if ($handle = opendir($dir)) {
                while (($file = readdir($handle)) !== false) {
                    if ($file != "." && $file != "..") {
                        if (is_dir($dir . "/" . $file)) {
                            $files[$file] = my_scandir($dir . "/" . $file);
                        } else {
                            $files[] = $dir . "/" . $file;
                        }
                    }
                }
                closedir($handle);
                return $files;
            }
        }
    }
    
    usleep() 函数延迟代码执行若干微秒。
    unpack() 函数从二进制字符串对数据进行解包。
    uniqid() 函数基于以微秒计的当前时间，生成一个唯一的 ID。
    time_sleep_until() 函数延迟代码执行直到指定的时间。
    time_nanosleep() 函数延迟代码执行若干秒和纳秒。
    sleep() 函数延迟代码执行若干秒。
    show_source() 函数对文件进行语法高亮显示。
    strip_whitespace() 函数返回已删除 PHP 注释以及空白字符的源代码文件。
    pack() 函数把数据装入一个二进制字符串。
    ignore_user_abort() 函数设置与客户机断开是否会终止脚本的执行。
    highlight_string() 函数对字符串进行语法高亮显示。
    highlight_file() 函数对文件进行语法高亮显示。
    get_browser() 函数返回用户浏览器的性能。
    exit() 函数输出一条消息，并退出当前脚本。
    eval() 函数把字符串按照 PHP 代码来计算。
    die() 函数输出一条消息，并退出当前脚本。
    defined() 函数检查某常量是否存在。
    define() 函数定义一个常量。
    constant() 函数返回常量的值。
    connection_status() 函数返回当前的连接状态。
    connection_aborted() 函数检查是否断开客户机。
    zip_read() 函数读取打开的 zip 档案中的下一个文件。
    zip_open() 函数打开 ZIP 文件以供读取。
    zip_entry_read() 函数从打开的 zip 档案项目中获取内容。
    zip_entry_open() 函数打开一个 ZIP 档案项目以供读取。
    zip_entry_name() 函数返回 zip 档案项目的名称。
    zip_entry_filesize() 函数返回 zip 档案项目的原始大小（在压缩之前）。
    zip_entry_compressionmethod() 函数返回 zip 档案项目的压缩方法。
    zip_entry_compressedsize() 函数返回 zip 档案项目的压缩文件尺寸。
    zip_entry_close() 函数关闭由 zip_entry_open() 函数打开的 zip 档案文件。
    zip_close() 函数关闭由 zip_open() 函数打开的 zip 档案文件。
    xml_set_unparsed_entity_decl_handler() 函数规定在遇到无法解析的实体名称（NDATA）声明时被调用的函数。
    xml_set_processing_instruction_handler() 函数规定当解析器在 xml 文档中找到处理指令时所调用的函数。
    xml_set_object() 函数允许在对象中使用 xml 解析器。
    xml_set_notation_decl_handler() 函数规定当解析器在 xml 文档中找到符号声明时被调用的函数。
    xml_set_external_entity_ref_handler() 函数规定当解析器在 xml 文档中找到外部实体时被调用的函数。
    xml_set_element_handler() 函数建立起始和终止元素处理器。
    xml_set_default_handler() 函数为 xml 解析器建立默认的数据处理器。
    xml_set_character_data_handler() 函数建立字符数据处理器。
    xml_parser_set_option() 函数为 xml 解析器进行选项设置。
    xml_parser_get_option() 函数从 xml 解析器获取选项设置信息。
    xml_parser_free() 函数释放 xml 解析器。
    xml_parser_create() 函数创建 xml 解析器。
    xml_parser_create_ns() 函数创建带有命名空间支持的 xml 解析器。
    xml_parse_into_struct() 函数把 xml 数据解析到数组中。
    xml_parse() 函数解析 xml 文档。
    xml_get_error_code() 函数获取 xml 解析器错误代码。
    xml_get_current_line_number() 函数获取 xml 解析器的当前行号。
    xml_get_current_column_number() 函数获取 xml 解析器的当前列号。
    xml_get_current_byte_index() 函数获取 xml 解析器的当前字节索引。
    xml_error_string() 函数获取 xml 解析器的错误描述。
    utf8_encode() 函数把 ISO-8859-1 字符串编码为 UTF-8。
    utf8_decode() 函数把 UTF-8 字符串解码为 ISO-8859-1。
    wordwrap() 函数按照指定长度对字符串进行折行处理。
    vsprintf() 函数把格式化字符串写入变量中。
    vprintf() 函数输出格式化的字符串。
    vfprintf() 函数把格式化的字符串写到指定的输出流。
    ucwords() 函数把字符串中每个单词的首字符转换为大写。
    ucfirst() 函数把字符串中的首字符转换为大写。
    trim() 函数从字符串的两端删除空白字符和其他预定义字符。
    substr_replace() 函数把字符串的一部分替换为另一个字符串。
    substr_count() 函数计算子串在字符串中出现的次数。
    substr_compare() 函数从指定的开始长度比较两个字符串。
    substr() 函数返回字符串的一部分。
    strtr() 函数转换字符串中特定的字符。
    strtoupper() 函数把字符串转换为大写。
    strtolower() 函数把字符串转换为小写。
    strtok() 函数把字符串分割为更小的字符串。
    strstr() 函数搜索一个字符串在另一个字符串中的第一次出现。
    strspn() 函数返回在字符串中包含的特定字符的数目。
    strrpos() 函数查找字符串在另一个字符串中最后一次出现的位置。
    strripos() 函数查找字符串在另一个字符串中最后一次出现的位置。
    strrev() 函数反转字符串。
    strrchr() 函数查找字符串在另一个字符串中最后一次出现的位置，并返回从该位置到字符串结尾的所有字符。
    strpos() 函数返回字符串在另一个字符串中第一次出现的位置。
    strpbrk() 函数在字符串中搜索指定字符中的任意一个。
    strncmp() 函数比较两个字符串。
    strncasecmp() 函数比较两个字符串。
    strnatcmp() 函数使用一种“自然”算法来比较两个字符串。
    strnatcasecmp() 函数使用一种“自然”算法来比较两个字符串。
    strlen() 函数返回字符串的长度。
    stristr() 函数查找字符串在另一个字符串中第一次出现的位置。
    stripos() 函数返回字符串在另一个字符串中第一次出现的位置。
    stripslashes() 函数删除由 addslashes() 函数添加的反斜杠。
    stripcslashes() 函数删除由 addcslashes() 函数添加的反斜杠。
    strip_tags() 函数剥去 HTML、xml 以及 PHP 的标签。
    strcspn() 函数返回在找到任何指定的字符之前，在字符串查找的字符数。
    strcoll() 函数比较两个字符串。
    strcmp() 函数比较两个字符串。
    strchr() 函数搜索一个字符串在另一个字符串中的第一次出现。
    strcasecmp() 函数比较两个字符串。
    str_word_count() 函数计算字符串中的单词数。
    str_split() 函数把字符串分割到数组中。
    str_shuffle() 函数随机地打乱字符串中的所有字符。
    str_rot13() 函数对字符串执行 ROT13 编码。
    str_replace() 函数使用一个字符串替换字符串中的另一些字符。
    str_repeat() 函数把字符串重复指定的次数。
    str_pad() 函数把字符串填充为指定的长度。
    str_ireplace() 函数使用一个字符串替换字符串中的另一些字符。
    sscanf() 函数根据指定的格式解析来自一个字符串的输入。
    sprintf() 函数把格式化的字符串写写入一个变量中。
    soundex() 函数计算字符串的 soundex 键。
    similar_text() 函数计算两个字符串的匹配字符的数目。
    sha1_file() 函数计算文件的 SHA-1 散列。
    sha1() 函数计算字符串的 SHA-1 散列。
    setlocale() 函数设置地区信息（地域信息）。
    rtrim() P rtrim() 函数 
    PHP String 函数
    quotemeta() 函数在字符串中某些预定义的字符前添加反斜杠。
    quoted_printable_decode() 函数对经过 quoted-printable 编码后的字符串进行解码，返回 8 位的字符串。
    printf() 函数输出格式化的字符串。
    print() 函数输出一个或多个字符串。
    parse_str() 函数把查询字符串解析到变量中。
    ord() 函数返回字符串第一个字符的 ASCII 值。
    number_format() 函数通过千位分组来格式化数字。
    nl2br() 函数在字符串中的每个新行 (\n) 之前插入 HTML 换行符 (<br />)。
    nl_langinfo() 函数返回指定的本地信息。
    money_format() 函数把字符串格式化为货币字符串。
    metaphone() 函数计算字符串的 metaphone 键。
    md5_file() 函数计算文件的 MD5 散列。
    md5() 函数计算字符串的 MD5 散列。
    ltrim() 函数从字符串左侧删除空格或其他预定义字符。
    localeconv() 函数返回包含本地数字及货币信息格式的数组。
    levenshtein() 函数返回两个字符串之间的 Levenshtein 距离。
    join() 函数把数组元素组合为一个字符串。
    implode() 函数把数组元素组合为一个字符串。
    htmlspecialchars() 函数把一些预定义的字符转换为 HTML 实体。
    html_entity_decode() chars_decode() 函数
    
    PHP String 函数
    htmlentities() 函数把字符转换为 HTML 实体。
    html_entity_decode() 函数把 HTML 实体转换为字符。
    hebrevc() 函数把希伯来文本从右至左的流转换为左至右的流。它也会把新行 (\n) 转换为 <br />。
    hebrev() 函数把希伯来文本从右至左的流转换为左至右的流。
    get_html_translation_table() 函数返回被 htmlentities() 和 htmlspecialchars() 函数使用的翻译表。
    fprintf() 函数把格式化的字符串写到指定的输出流（例如：文件或数据库）。
    explode() 函数把字符串分割为数组。
    echo() 函数输出一个或多个字符串。
    crypt() 函数返回使用 DES、Blowfish 或 MD5 加密的字符串。
    crc32() 函数计算一个字符串的 crc32 多项式。
    count_chars() 函数返回字符串所用字符的信息。
    convert_uuencode() 函数使用 uuencode 算法对字符串进行编码。
    convert_uudecode() 函数对 uuencode 编码的字符串进行解码。
    convert_cyr_string() 函数把字符由一种 Cyrillic 字符转换成另一种。
    chunk_split() 函数把字符串分割为一连串更小的部分。
    chr() 函数从指定的 ASCII 值返回字符。
    chop() 函数从字符串的末端开始删除空白字符或其他预定义字符。
    bin2hex() 函数把 ASCII 字符的字符串转换为十六进制值。
    addslashes() 函数在指定的预定义字符前添加反斜杠。
    addcslashes() 函数在指定的字符前添加反斜杠。
    xpath() 函数运行对 xml 文档的 XPath 查询。
    simplexml_load_string() 函数把 xml 字符串载入对象中。
    simplexml_load_file() 函数把 xml 文档载入对象中。
    simplexml_import_dom() 函数把 DOM 节点转换为 SimplexmlElement 对象。
    registerXPathNamespace() 函数为下一次 XPath 查询创建命名空间语境。
    getNamespace() 函数获取在 xml 文档中使用的命名空间。
    getName() 函数从 SimplexmlElement 对象获取 xml 元素的名称。
    getDocNamespaces() 函数从 SimplexmlElement 对象返回在 xml 文档中声明的命名空间。
    children() 函数获取指定节点的子节点。
    attributes() 函数获取 Simplexml 元素的属性。
    asxml() 函数以字符串的形式从 SimplexmlElement 对象返回 xml 文档。
    addChild() 函数向指定的 xml 节点添加一个子节点。
    addAttribute() 函数给 Simplexml 元素添加一个属性。
    __construct() 函数创建一个新的 SimplexmlElement 对象。
    mysql_unbuffered_query() 函数向 MySQL 发送一条 SQL 查询（不获取 / 缓存结果）。
    mysql_thread_id() 函数返回当前线程的 ID。
    mysql_stat() 函数返回 MySQL 服务器的当前系统状态。
    mysql_select_db() 函数设置活动的 MySQL 数据库。
    mysql_result() 函数返回结果集中一个字段的值。
    mysql_real_escape_string() 函数转义 SQL 语句中使用的字符串中的特殊字符。
    mysql_query() 函数执行一条 MySQL 查询。
    mysql_ping() 函数 Ping 一个服务器连接，如果没有连接则重新连接。
    mysql_pconnect() 函数打开一个到 MySQL 服务器的持久连接。
    mysql_num_rows() 函数返回结果集中行的数目。
    mysql_num_fields() 函数返回结果集中字段的数。
    mysql_list_processes() 函数列出 MySQL 进程。
    mysql_list_dbs() 函数列出 MySQL 服务器中所有的数据库。
    mysql_insert_id() 函数返回上一步 INSERT 操作产生的 ID。
    mysql_info() 函数返回最近一条查询的信息。
    mysql_get_server_info() 函数返回 MySQL 服务器的信息。
    mysql_get_proto_info() 函数返回 MySQL 协议的信息。
    mysql_get_host_info() 函数返回 MySQL 主机的信息。
    mysql_get_client_info() 函数返回 MySQL 客户端信息。
    mysql_free_result() 函数释放结果内存。
    mysql_field_type() 函数返回结果集中指定字段的类型。
    mysql_field_table() 函数返回指定字段所在的表名。
    mysql_field_seek() 函数将结果集中的指针设定为指定的字段偏移量。
    mysql_field_name() 函数取得结果中指定字段的字段名。
    mysql_field_len() 函数返回指定字段的长度。
    mysql_field_flags() 函数从结果中取得和指定字段关联的标志。
    mysql_fetch_row() 函数从结果集中取得一行作为数字数组。
    mysql_fetch_object() 函数从结果集（记录集）中取得一行作为对象。
    mysql_fetch_lengths() 函数取得一行中每个字段的内容的长度。
    mysql_fetch_field() 函数从结果集中取得列信息并作为对象返回。
    mysql_fetch_assoc() 函数从结果集中取得一行作为关联数组。
    mysql_fetch_array() 函数从结果集中取得一行作为关联数组，或数字数组，或二者兼有
    mysql_error() 函数返回上一个 MySQL 操作产生的文本错误信息。
    mysql_errno() 函数返回上一个 MySQL 操作中的错误信息的数字编码。
    mysql_db_name() 函数取得 mysql_list_dbs() 调用所返回的数据库名。
    mysql_data_seek() 函数移动内部结果的指针。
    mysql_connect() 函数打开非持久的 MySQL 连接。
    mysql_close() 函数关闭非持久的 MySQL 连接。
    mysql_client_encoding() 函数返回当前连接的字符集的名称。
    mysql_affected_rows() 函数返回前一次 MySQL 操作所影响的记录行数。
    tanh() 函数返回双曲正切。
    tan() 函数返回正切。
    srand() 函数播下随机数发生器种子。
    sqrt() 函数返回一个数的平方根。
    sinh() 函数返回一个数的双曲正弦。
    sin() 函数返回一个数的正弦。
    round() 函数对浮点数进行四舍五入。
    rand() 函数返回随机整数。
    rad2deg() 函数把弧度数转换为角度数。
    pow() 函数返回 x 的 y 次方。
    pi() 函数返回圆周率的值。
    octdec() 函数把八进制转换为十进制。
    mt_srand() 播种 Mersenne Twister 随机数生成器。
    mt_rand() 使用 Mersenne Twister 算法返回随机整数。
    mt_getrandmax() 显示随机数的最大可能值。
    min() 返回最小值。
    max() 返回最大值。
    log1p() 以返回 log(1 + x)，甚至当 x 的值接近零也能计算出准确结果。
    log10() 以 10 为底的对数。
    log() 返回自然对数。
    lcg_value() 组合线性同余发生器。
    is_nan() 判断是否为合法数值。
    is_infinite() 判断是否为无限值。
    is_finite() 函数判断是否为有限值。
    hypot() 函数计算一直角三角形的斜边长度。
    hexdec() 函数把十六进制转换为十进制。
    fmod() 函数显示随机数最大的可能值。
    fmod() 函数返回除法的浮点数余数。
    floor() 函数向下舍入为最接近的整数。
    expm1() 函数返回 exp(x) - 1，甚至当 number 的值接近零也能计算出准确结果。
    exp() 函数计算 e 的指数。
    deg2rad() 函数将角度转换为弧度。
    decoct() 函数把十进制转换为八进制。
    dechex() 函数把十进制转换为十六进制。
    decbin() 函数把十进制转换为二进制。
    cosh() 函数返回一个数的双曲余弦。
    cos() 函数返回一个数的余弦。
    ceil() 函数向上舍入为最接近的整数。
    bindec() 函数把二进制转换为十进制。
    base_convert() 函数在任意进制之间转换数字。
    atanh() 函数返回一个角度的反双曲正切。
    atan() 和 atan2() 和 atan2() 函数
    
    PHP Math 函数
    atan() 和 atan2() 和 atan2() 函数
    
    PHP Math 函数
    asinh() 函数返回一个数的反双曲正弦。
    asin() 函数返回不同数值的反正弦，返回的结果是介于 -PI/2 与 PI/2 之间的弧度值。
    acosh() 函数返回一个数的反双曲余弦。
    acos() 函数返回一个数的反余弦。
    abs() 函数返回一个数的绝对值。
    mail() 函数允许您从脚本中直接发送电子邮件。
    libxml_use_internal_errors() 函数禁用标准的 libxml 错误，并启用用户错误处理。
    libxml_get_last_error() 函数从 libxml 错误缓冲中获取最后一个错误。
    libxml_get_errors() 函数从 libxml 错误缓冲中获取错误。
    libxml_clear_errors() 函数清空 libxml 错误缓冲。
    setrawcookie() 函数不对 cookie 值进行 URL 编码，发送一个 HTTP cookie。
    setcookie() 函数向客户端发送一个 HTTP cookie。
    headers_sent() 函数检查 HTTP 报头是否发送/已发送到何处。
    headers_list() 函数返回已发送的（或待发送的）响应头部的一个列表。
    header() 函数向客户端发送原始的 HTTP 报头。
    ftp_systype() 函数返回远程 FTP 服务器的系统类型标识符。
    ftp_ssl_connect() 函数打开一个安全的 SSL-FTP 连接。
    ftp_size() 函数返回指定文件的大小。
    ftp_site() 函数向服务器发送 SITE 命令。
    ftp_set_option() 函数设置各种 FTP 运行时选项。
    ftp_rmdir() 函数删除一个目录。
    ftp_rename() 函数更改 FTP 服务器上的文件或目录名。
    ftp_rawlist() 函数返回指定目录中文件的详细列表。
    ftp_raw() 函数向 FTP 服务器发送一个 raw 命令。
    ftp_quit() 函数关闭 FTP 连接。
    ftp_pwd() 函数返回当前目录名。
    ftp_put() 函数把文件上传到服务器。
    ftp_pasv() 函数把被动模式设置为打开或关闭。
    ftp_nlist() 函数返回指定目录的文件列表。
    ftp_nb_put() 函数把文件上传到服务器 (non-blocking)。
    ftp_nb_get() 函数从 FTP 服务器上获取文件并写入本地文件 (non-blocking)。
    ftp_nb_fput() 函数上传一个已打开的文件，并在 FTP 服务器上把它保存为文件 (non-blocking)。
    ftp_nb_fget() 函数从 FTP 服务器上下载一个文件并保存到本地已经打开的一个文件中 (non-blocking)。
    ftp_nb_continue() 函数连续获取 / 发送文件。
    ftp_mkdir() 函数在 FTP 服务器上建立新目录。
    ftp_mdtm() 函数返回指定文件的最后修改时间。
    ftp_login() 函数登录 FTP 服务器。
    ftp_get() 函数从 FTP 服务器上下载一个文件。
    ftp_get_option() 函数返回当前 FTP 连接的各种不同的选项设置。
    ftp_fput() 函数上传一个已经打开的文件到 FTP 服务器。
    ftp_fget() 函数从 FTP 服务器上下载一个文件并保存到本地一个已经打开的文件中。
    ftp_exec() 函数请求在 FTP 服务器上执行一个程序或命令。
    ftp_delete() 函数删除 FTP 服务器上的一个文件。
    ftp_connect() 函数建立一个新的 FTP 连接。
    ftp_close() 函数关闭 FTP 连接。
    ftp_chmod() 函数设置 FTP 服务器上指定文件的权限。
    ftp_chdir() 函数改变 FTP 服务器上的当前目录。
    ftp_cdup() 函数把当前目录改变为 FTP 服务器上的父目录。
    ftp_alloc() 函数为要上传到 FTP 服务器的文件分配空间。
    filter_var() 函数通过指定的过滤器过滤变量。
    filter_var_array() 函数获取多项变量，并进行过滤。
    filter_list() 函数返回包含所有得到支持的过滤器的一个数组。
    filter_input_array() 函数从脚本外部获取多项输入，并进行过滤。
    filter_input() 函数从脚本外部获取输入，并进行过滤。
    filter_id() 函数返回指定过滤器的 ID 号。
    filter_has_var() 函数检查是否存在指定输入类型的变量。
    unlink() 函数删除文件。
    umask() 函数改变当前的 umask。
    touch() 函数设置指定文件的访问和修改时间。
    tmpfile() 函数以读写（w+）模式建立一个具有唯一文件名的临时文件。
    tempnam() 函数创建一个具有唯一文件名的临时文件。
    symlink() 函数创建符号连接。
    stat() 函数返回关于文件的信息。
    set_file_buffer() 函数设置打开文件的缓冲大小。
    rmdir() 函数删除空的目录。
    rewind() 函数将文件指针的位置倒回文件的开头。
    rename() 函数重命名文件或目录。
    realpath() 函数返回绝对路径。
    readlink() 函数返回符号连接指向的目标。
    readfile() 函数输出一个文件。
    popen() 函数打开进程文件指针。
    pclose() 函数关闭由 popen() 打开的管道。
    pathinfo() 函数以数组的形式返回文件路径的信息。
    parse_ini_file() 函数解析一个配置文件，并以数组的形式返回其中的设置。
    move_uploaded_file() 函数将上传的文件移动到新位置。
    mkdir() 函数创建目录。
    lstat() 函数返回关于文件或符号连接的信息。
    linkinfo() 函数返回连接的信息。
    link() 函数建立一个硬连接。
    is_writeable() 函数判断指定的文件是否可写。
    is_writable() 函数判断指定的文件是否可写。
    is_uploaded_file() 函数判断指定的文件是否是通过 HTTP POST 上传的。
    is_readable() 函数判断指定文件名是否可读。
    is_link() 函数判断指定文件名是否为一个符号连接。
    is_file() 函数检查指定的文件名是否是正常的文件。
    is_executable() 函数检查指定的文件是否可执行。
    is_dir() 函数检查指定的文件是否是目录。
    glob() 函数返回匹配指定模式的文件名或目录。
    fwrite() 函数写入文件（可安全用于二进制文件）。
    ftruncate() 函数把文件截断到指定的长度。
    ftell() 函数在打开文件中的当前位置。
    fstat() 函数返回关于打开文件的信息。
    fseek() 函数在打开的文件中定位。
    fscanf() 函数根据指定的格式对来自打开的文件的输入进行解析。
    fread() 函数读取文件（可安全用于二进制文件）。
    fputs() 函数写入文件（可安全用于二进制文件）。
    fputcsv() 函数将行格式化为 CSV 并写入一个打开的文件。
    fpassthru() 函数输出文件指针处的所有剩余数据。
    fopen() 函数打开文件或者 URL。
    fnmatch() 函数根据指定的模式来匹配文件名或字符串。
    flock() 函数锁定或释放文件。
    filetype() 函数返回指定文件或目录的类型。
    filesize() 函数返回指定文件的大小。
    fileperms() 函数返回文件或目录的权限。
    fileowner() 函数返回文件的所有者。
    filemtime() 函数返回文件内容上次的修改时间。
    fileinode() 函数返回文件的 inode 编号。
    filegroup() 函数返回指定文件的组 ID。
    filectime() 函数返回指定文件的上次 inode 修改时间。
    fileatime() 函数返回指定文件的上次访问时间。
    file_put_contents() 函数把一个字符串写入文件中。
    file_get_contents() 函数把整个文件读入一个字符串中。
    file_exists() 函数检查文件或目录是否存在。
    file() 函数把整个文件读入一个数组中。
    fgetss() 函数从打开的文件中读取一行并过滤掉 HTML 和 PHP 标记。
    fgets() 函数从文件指针中读取一行。
    fgetcsv() 函数从文件指针中读入一行并解析 CSV 字段。
    fgetc() 函数从文件指针中读取一个字符。
    fflush() 函数将缓冲内容输出到文件。
    feof() 函数检测是否已到达文件末尾 (eof)。
    fclose() 函数关闭一个打开文件。
    diskfreespace() 函数返回目录中的可用空间。该函数是 disk_free_space() 函数的别名。
    disk_total_space() 函数返回指定目录的磁盘总大小。
    disk_free_space() 函数返回目录中的可用空间
    dirname() 函数返回路径中的目录部分。
    clearstatcache() 函数拷贝文件。
    clearstatcache() 函数清除文件状态缓存。
    chown() 函数改变指定文件的所有者。
    chmod() 函数改变文件模式。
    chgrp() 函数改变文件所属的组。
    basename() 函数返回路径中的文件名部分。
    set_exception_handler() handler() 函数
    
    PHP Error 和 Logging 函数
    set_exception_handler() 函数设置用户自定义的异常处理函数。
    set_error_handler() 函数设置用户自定义的错误处理函数。
    restore_exception_handler() 函数恢复之前的异常处理程序，该程序是由 set_exception_handler() 函数改变的。
    restore_error_handler() 函数恢复之前的错误处理程序，该程序是由 set_error_handler() 函数改变的。
    error_reporting() 设置 PHP 的报错级别并返回当前级别。
    error_log() 函数向服务器错误记录、文件或远程目标发送一个错误。
    error_get_last() 函数获取最后发生的错误。
    debug_print_backtrace() 函数输出 backtrace。
    debug_backtrace() cktrace() 函数
    
    PHP Error 和 Logging 函数
    scandir() 函数返回一个数组，其中包含指定路径中的文件和目录。
    rewinddir() 函数重置由 opendir() 打开的目录句柄。
    readdir() 函数返回由 opendir() 打开的目录句柄中的条目。
    opendir() 函数打开一个目录句柄，可由 closedir()，readdir() 和 rewinddir() 使用。
    getcwd() 函数返回当前目录。
    closedir() 函数关闭由 opendir() 函数打开的目录句柄。
    dir() 函数打开一个目录句柄，并返回一个对象。这个对象包含三个方法：read() , rewind() 以及 close()。
    chroot() 函数把当前进程的根目录改变为指定的目录。
    chdir() 函数把当前的目录改变为指定的目录。
    time() 函数返回当前时间的 Unix 时间戳。
    strtotime() 函数将任何英文文本的日期时间描述解析为 Unix 时间戳。
    strptime() 函数解析由 strftime() 生成的日期／时间。
    strftime() 函数根据区域设置格式化本地时间／日期。
    mktime() 函数返回一个日期的 Unix 时间戳。
    microtime() 函数返回当前 Unix 时间戳和微秒数。
    localtime() 函数返回本地时间（一个数组）。
    idate() 函数将本地时间/日期格式化为整数。
    gmstrftime() 函数根据本地区域设置格式化 GMT/UTC 时间／日期。
    gmmktime() 函数取得 GMT 日期的 UNIX 时间戳。
    gmdate() 函数格式化 GMT/UTC 日期/时间。
    gettimeofday() 函数返回一个包含当前时间信息的数组。
    getdate() 函数取得日期／时间信息。
    date() 函数格式化一个本地时间／日期。
    date_sunset() 函数返回指定的日期与地点的日落时间。
    date_sunrise() 函数返回指定的日期与地点的日出时间。
    date_default_timezone_set() 函数设置用在脚本中所有日期/时间函数的默认时区。
    date_default_timezone_get() 函数返回脚本中所有日期时间函数所使用的默认时区。
    checkdate() 函数验证一个格里高里日期。
    UnixToJD() 函数把 Unix 时间戳转换为儒略日计数。
    JulianToJD() 函数把儒略历转换为儒略日计数。
    JewishToJD() 函数把犹太历法转换为儒略日计数。
    JDToUnix() 函数把儒略日计数转换为 Unix 时间戳。
    JDToGregorian() lian() 函数
    
    PHP Array 函数
    JDToGregorian() wish() 函数
    
    PHP Array 函数
    JDToGregorian() 函数把儒略日计数转换为格利高里历法。
    JDToFrench() 函数把儒略日计数转换为法国共和国历法。
    JDMonthName() 函数返回指定历法的月份字符串。
    JDDayOfWeek() 函数返回日期在周几。
    GregorianToJD() 函数将格利高里历法转换成为儒略日计数。
    FrenchToJD() 函数将法国共和历法转换成为儒略日计数。
    easter_days() 函数返回指定年份的复活节与 3 月 21 日之间的天数。
    easter_date() 函数返回指定年份的复活节午夜的 Unix 时间戳。
    cal_to_jd() 函数把指定的日期转换为儒略日计数。
    cal_info() 函数返回一个数组，其中包含了关于给定历法的信息。
    cal_from_jd() 函数把儒略日计数转换为指定历法的日期。
    cal_days_in_month() 函数针对指定的年份和日历，返回一个月中的天数。
    usort() 函数使用用户自定义的函数对数组排序。
    uksort() 函数使用用户自定义的比较函数按照键名对数组排序，并保持索引关系。
    uasort() 函数使用用户自定义的比较函数对数组排序，并保持索引关联（不为元素分配新的键）。
    sort() 函数按升序对给定数组的值排序。
    sizeof() 函数计算数组中的单元数目或对象中的属性个数。
    shuffle() 函数把数组中的元素按随机顺序重新排列。
    rsort() 函数对数组的元素按照键值进行逆向排序。与 arsort() 的功能基本相同。
    reset() 函数把数组的内部指针指向第一个元素，并返

