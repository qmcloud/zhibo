<?php
    /*
     * 获取IP
    */
    function getip() {
        if (isset ( $_SERVER )) {
            if (isset ( $_SERVER ['HTTP_X_FORWARDED_FOR'] )) {
                $aIps = explode ( ',', $_SERVER ['HTTP_X_FORWARDED_FOR'] );
                foreach ( $aIps as $sIp ) {
                    $sIp = trim ( $sIp );
                    if ($sIp != 'unknown') {
                        $sRealIp = $sIp;
                        break;
                    }
                }
            } elseif (isset ( $_SERVER ['HTTP_CLIENT_IP'] )) {
                $sRealIp = $_SERVER ['HTTP_CLIENT_IP'];
            } else {
                if (isset ( $_SERVER ['REMOTE_ADDR'] )) {
                    $sRealIp = $_SERVER ['REMOTE_ADDR'];
                } else {
                    $sRealIp = '0.0.0.0';
                }
            }
        } else {
            if (getenv ( 'HTTP_X_FORWARDED_FOR' )) {
                $sRealIp = getenv ( 'HTTP_X_FORWARDED_FOR' );
            } elseif (getenv ( 'HTTP_CLIENT_IP' )) {
                $sRealIp = getenv ( 'HTTP_CLIENT_IP' );
            } else {
                $sRealIp = getenv ( 'REMOTE_ADDR' );
            }
        }
        return $sRealIp;
    }
class session {
    /*
     *  数据库接口
     */
    private $oDB;
    function __construct($aConfig) {
        session_cache_limiter ( 'private, must-revalidate' );
        session_cache_expire ( 1800 );
        @ini_set ( 'session.cookie_lifetime', 0 );
        @ini_set ( 'session.cookie_httponly', TRUE );
        @ini_set ( 'session.use_cookies', 1 );
        @ini_set ( 'session.use_only_cookies', 1 );
        @ini_set ( 'session.use_trans_sid', 0 );
        @ini_set ( 'session.gc_probability', 1 );
        @ini_set ( 'session.gc_divisor', 1 );
        @ini_set ( 'session.gc_maxlifetime', 1800 );

        if($aConfig["session"]==1){
            $this->oDB = mysql_connect($aConfig["db"]["host"],$aConfig["db"]["user"],$aConfig["db"]["pass"]);
            mysql_select_db($aConfig["db"]["name"],$this->oDB);
            mysql_query("SET NAMES UTF8",$this->oDB);
            session_set_save_handler ( array (&$this, "open" ), array (&$this, "close" ), array (&$this, "read" ), array (&$this, "write" ), array (&$this, "destory" ), array (&$this, "gc" ) );
        }elseif($aConfig["session"]==2){
            @ini_set('session.save_handler','memcache');
            @ini_set("session.save_path","tcp://".$aConfig["mem"]["host"].":".$aConfig["mem"]["port"]);
        }
        session_start ();
    }

    function open($session_save_path, $session_name) {
        return true;
    }

    function close() {
        return true;
    }

    function write($key, $value) {
        $query = mysql_query( "select * from `sessions` where `sessionkey`='" . $key . "'",$this->oDB );
        if (mysql_num_rows($query) == 0) {
            mysql_query("insert into `sessions` set `sessionkey`='".$key."',`sessionvalue`='".$value."',`sessionip`='".getip()."', `sessionexpiry` ='". date ( "Y-m-d H:i:s", strtotime ( "+1800 seconds" ) )."'",$this->oDB);
        } else {
            mysql_query("update `sessions` set `sessionvalue`='".$value."',`sessionip`='".getIp()."',`sessionexpiry`='".date ( "Y-m-d H:i:s", strtotime ( "+1800 seconds" ) )."' where `sessionkey`='" . $key . "'", $this->oDB);
        }
    }

    function read($key) {
        $Query = mysql_query( "select `sessionvalue` from `sessions` where `sessionkey`='" . $key . "' and `sessionexpiry`>'" . date ( "Y-m-d H:i:s" ) . "' and `sessionip`='" . getIp () . "'",$this->oDB );
        $aValue = mysql_fetch_assoc($Query);
        if (empty ( $aValue )) {
            return NULL;
        }
        return $aValue ["sessionvalue"];
    }

    function gc() {
        return mysql_query ( "delete from `sessions` where `sessionexpiry`<='" . date ( "Y-m-d H:i:s" ) . "'",$this->oDB );
    }

    function destory($key) {
        return mysql_query ( "delete from `sessions` where `sessionkey`='" . $key . "'",$this->oDB );
    }
}