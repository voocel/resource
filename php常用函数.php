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
