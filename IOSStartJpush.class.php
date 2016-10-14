<?php
/**
 *@FILENAME:IOSStartJpush;
 *@AUTHOR:dudongjiang;
 *@DATE:2016年10月13日;
 *@EFFORT:IOS原生推送信息;
 **/
class IOSStartJpush
{
    private $passphrase;
    function __construct($passsphrase = "123456"){
        $this->passphrase = $passsphrase;
    }
    /**
     *@FUNCNAME:iosJpush;
     *@AUTHOR:dudongjiang;
     *@DATE:2016年10月13日;
     *@EFFORT:ios发送消息;
     *@PARAM:
     *  $registration_id : string 手机唯一标示
     *  $message: string 发送的消息
     **/
    function iosJpush($registration_id, $message){
        $ctx = stream_context_create();
        //生成的密钥
        stream_context_set_option($ctx, 'ssl', 'local_cert', './ios/ck.pem');
        //口令
        stream_context_set_option($ctx, 'ssl', 'passphrase', $this->passphrase);
        // Open a connection to the APNS server
        //这个为正是的发布地址
        //$fp = stream_socket_client(“ssl://gateway.push.apple.com:2195“, $err, $errstr, 60, //STREAM_CLIENT_CONNECT, $ctx);
        //这个是沙盒测试地址，发布到appstore后记得修改哦
        $fp = stream_socket_client(
            'ssl://gateway.sandbox.push.apple.com:2195', $err,
            $errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
        if (!$fp){
            return "Failed to connect: $err $errstr";
        }
        $body['aps'] = array(
            //发送消息的内容
            'alert' => $message,
            //默认的铃声
            'sound' => 'default',
            //消息显示的个数
            'badge' => 1,
            //还可以加入其它的参数
        );
        //json格式化数据
        $payload = json_encode($body);
        // Build the binary notification
        $msg = chr(0) . pack('n', 32) . pack('H*', $registration_id) . pack('n', strlen($payload)) . $payload;
        // Send it to the server
        $result = fwrite($fp, $msg, strlen($msg));
        if($result){
            fclose($fp);
            return "ok";
        }
    }
}
?>