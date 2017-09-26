<?php
// 为什么不用预处理？？？？？？？？？？
// 为什么不用预处理？？？？？？？？？？
// 为什么不用预处理？？？？？？？？？？
// 重要的事情说三遍！
// SQL注入已经是上个世纪的事情了！
// 如果你不想重新编写，可以使用我简单封装的现成PHP类，请选择其中一个使用。
// （我发现代码有我的项目的某些痕迹，所以删掉了无关代码）
// MySQLi版：

class DB
{
    var $Sql;
    var $Fetch;
    var $Param = array();
    
    public function __construct($Sql, $Fetch, $Param = array())
    {
        $this->sql = $Sql;
        $this->fetch = $Fetch;
        $this->param = $Param;
        //数据库信息存放在配置文件里面，请自行修改成正确的路径和值
        require($_SERVER['DOCUMENT_ROOT'] . '/configs/config.inc.php');
        $this->dbhost = & $DBHost;
        $this->dbuser = & $DBUser;
        $this->dbpw = & $DBPassword;
        $this->dbname = & $DBName;
    }
    
    public function Query()
    {
        $Mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpw, $this->dbname);
        if ($Mysqli->connect_errno)
        {
            echo '无法连接数据库';
            return false;
        }
        $Mysqli->query('SET NAMES UTF8');
        $Mysqli->begin_transaction(MYSQLI_TRANS_START_READ_ONLY);
        $Stmt = $Mysqli->stmt_init();
        $Stmt->prepare($this->sql);
        if (count($this->param) > 0)
        {
            $Type = '';
            for ($i = 0; $i < count($this->param); $i++)
            {
                if (is_double($this->param[$i]))
                {
                    $Type .= 'd';
                }
                else if (is_int($this->param[$i]))
                {
                    $Type .= 'i';
                }
                else if (is_string($this->param[$i]))
                {
                    $Type .= 's';
                }
                else
                {
                    $Type .= 'b';
                }
            }
            $RefArg = array($Type);
            for ($I = 0; $I < count($this->param); $I++)
            {
                $RefArg[] = & $this->param[$I];
            }
            call_user_func_array(array($Stmt, 'bind_param'), $RefArg);
        }
        if (!$Stmt->execute())
        {
            echo '读取数据库时发生错误：'. $Stmt->error;
            echo $this->sql;
            print_r($this->param);
            $Mysqli->rollback();            
            return false;
        }
        $Mysqli->commit();
        if (strtolower(substr($this->sql, 0, 6)) == 'select')
        {
            $this->res = $Stmt->get_result();
            $Stmt->free_result();
            return $this->GetRes();
        }
        else
        {
            $Stmt->free_result();
            return true;
        }
    }
    
    public function GetRes()
    {
        switch(strtolower($this->fetch))
        {
            case 'all':
                $row = $this->res->fetch_all();
                break;
            case 'array':
                $row = $this->res->fetch_array();
                break;
            case 'assoc':
                $row = $this->res->fetch_assoc();
                break;
            case 'field':
                $row = $this->res->fetch_field();
                break;
            case 'row':
                $row = $this->res->fetch_row();
                break;
            default:
                echo 'Please select a row return mode.';
                exit;
        }
        return $row;
    }
    
    public function NumRow()
    {
        if (isset($this->res))
        {
            return $this->res->num_rows;
        }
        else
        {
            return false;
        }
    }
}
?>




//PDO_MYSQL版：

<?php
class DB
{
    var $SQL;
    var $Fetch;
    var $Param = array();
    
    public function __construct($SQL, $Fetch, $Param)
    {
        //数据库信息存放在配置文件里面，请自行修改成正确的路径和值
        require($_SERVER['DOCUMENT_ROOT'] . '/configs/config.inc.php');
        $this->DBHost = & $DBHost;
        $this->DBUser = & $DBUser;
        $this->DBPW = & $DBPassword;
        $this->DBName = & $DBName;
        $this->SQL = $SQL;
        $this->Fetch = $Fetch;
        $this->Param = $Param;
    }
    
    public function Query()
    {
        try
        {
            $Pdo = new PDO('mysql:host=' . $this->DBHost . ';dbname=' . $this->DBName, $this->DBUser, $this->DBPW);
            $Pdo->query('SET NAMES UTF8');
            $Pdo->beginTransaction();
            $Stmt = $Pdo->prepare($this->SQL);
            if (count($this->Param) > 0)
            {
                for ($I = 0; $I < count($this->Param); $I++)
                {
                    $Stmt->bindParam($I + 1, $this->Param[$I]);
                }
            }
            if (!$Stmt->execute())
            {
                echo '读取数据库时发生错误：' . $Stmt->errorinfo()[2];
                $Pdo->rollback();
                return false;
            }
            $Pdo->commit();
            if (strtolower(substr($this->SQL, 0, 6)) == 'select' || strtolower(substr($this->SQL, 0, 4)) == 'desc')
            {
                $this->Res = $Stmt;
                return $this->GetRes();
            }
            else
            {
                return true;
            }
        }
        catch (PDOException $e)
        {
            echo '无法连接数据库';
            $Pdo->rollback();
            return false;
        }
    }
    
    public function GetRes()
    {
        switch(strtolower($this->Fetch))
        {
            case 'all':
                $Row = $this->Res->fetchAll(PDO::FETCH_ASSOC);
                break;
            case 'array':
                $Row = $this->Res->fetch(PDO::FETCH_BOTH);
                break;
            case 'assoc':
            case 'field':
                $Row = $this->Res->fetch(PDO::FETCH_ASSOC);
                break;
            case 'row':
                $Row = $this->Res->fetch(PDO::FETCH_NUM);
                break;
            default:
                echo 'Please select a row return mode.';
                exit;
        }
        return $Row;
    }
    
    public function NumRow()
    {
        if (isset($this->Res))
        {
            return $this->Res->num_rows;
        }
        else
        {
            return false;
        }
    }
}
?>

<!-- 
使用方法：

$DB = new DB(SQL语句, 结果集方式, array(要绑定的参数);
$DB->Query();
注意！！！SQL语句用 ? 代替要查询的参数！！！SQL注入漏洞是因为SQL语句拼接了变量！！！
PS：如果不能改框架，那还是洗洗睡吧，时间只会淘汰墨守成规的人和过时的技术。
别指望正则过滤SQL语句能彻底杜绝SQL注入，PHP7放弃MySQL扩展的原因就是因为这个扩展有安全漏洞！ -->