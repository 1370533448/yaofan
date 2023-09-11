<?php
/*
 * 首页
 * Author：Ysnsn
 * Mail：wyang0558@gmail.com
 * Date:2020/10/10
 */

include './Core/Common.php';
$data = $_REQUEST;
if (isset($data['type']) && !empty($data['type'])) {
    $pay = [
        'wxpay',
        'qqpay',
        'alipay'
    ];
    if (!in_array($data['type'], $pay)) yyhy_json(['code' => -1, 'msg' => '支付方式不合法！']);
    $trade_no = date('YmdHis') . mt_rand(1111, 9999);
    if (!$data['qq']) yyhy_json(['code' => -1, 'msg' => 'QQ不可为空！']);
    if (!preg_match('/^[1-9][0-9]{4,9}$/', $data['qq'])) yyhy_json(['code' => -1, 'msg' => 'QQ号格式不正确！']);
    if (!$data['msg']) yyhy_json(['code' => -1, 'msg' => '留言不可为空！']);
    if (!$data['money']) yyhy_json(['code' => -1, 'msg' => '金额不可为空！']);
    if ($data['money'] < 0 || !is_numeric($data['money']) || $data['money'] > 5000) yyhy_json(['code' => -1, 'msg' => '金额不合法！']);
    $data['money'] = round($data['money'], 2);
    $arr = [
        'trade_no' => $trade_no,
        'qq' => strip_tags($data['qq']),
        'nick' => get_qq_nick($data['qq']),
        'city' => get_ip_city(real_ip()),
        'msg' => strip_tags($data['msg']),
        'ip' => real_ip(),
        'money' => strip_tags($data['money']),
        'type' => strip_tags($data['type']),
        'addtime' => date('Y-m-d H:i:s')
    ];
    $row = Db(insert('yyhy_order', $arr), 1);
    if (!$row) yyhy_json(['code' => -1, 'msg' => '订单发起失败！']);
    yyhy_json(['code' => 1, 'msg' => '订单发起成功！', 'trade_no' => $trade_no]);
}
if (isset($data['msg']) && !empty($data['msg'])) {
    $order = Db('select * from yyhy_order where trade_no="' . $data['msg'] . '"');
    if (!$order) yyhy_json(['code' => -1, 'msg' => '留言查看失败！']);
    $order = $order[0];
    yyhy_json(['code' => 1, 'msg' => $order['msg']]);
}
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10; // 每页显示的数量

$start_from = ($page-1) * $per_page;

$order = Db("SELECT * FROM yyhy_order ORDER BY trade_no DESC LIMIT $start_from, $per_page"); 



?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
      <style>
      
      .pagination {
    display: flex !important;
    justify-content: center !important; /* 或者使用 'flex-end' 将链接靠右显示 */
}
 .pagination {
  display: flex;
  justify-content: center;
  margin: 20px auto;
}

.pagination a {
  color: #333;
  padding: 5px 7px;
  margin: 0 5px;
  text-decoration: none;
  border: 1px solid #ddd;
}

.pagination a:hover {
  background-color: #f0f0f0;
}

.pagination a.active {
    color: white;
    background: linear-gradient(to right, #32cd32, #20b2aa); /* 绿色到蓝绿色的渐变 */
}



    </style>
    <title><?php echo config('sitename'); ?> - <?php echo config('title'); ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=0.9">
    <link href="/Static/css/main.css" rel="stylesheet">
    <link href="//cdn.bootcss.com/simple-line-icons/2.4.1/css/simple-line-icons.min.css" rel="stylesheet">
<body background="/Static/img/bg.jpg">
<div class="container" style="padding-top:20px;">
    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
        <div class="panel panel-primary">
            <div class="panel-heading" style="background: linear-gradient(to right,#8ae68a,#5ccdde,#b221ff);">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <center><font color="#000000"><b><?php echo config('panel'); ?></b></font></center>
                    <button onclick="location.href = 'log.php'" style="background-color:#14b7ff; color:white; border:none; padding:5px 10px;">查看大佬们的打赏记录</button>
                </div>
            </div>
            <div class="panel-body">
                <center>
                    <div class="alert alert-success">
                        <a href="http://wpa.qq.com/msgrd?v=3&uin=<?php echo config('qq'); ?>&site=qq&menu=yes">
                            <img class="img-circle"
                                 style="border: 2px solid #1281FF; margin-left:3px; margin-right:3px;"
                                 src="/V50.jpg"
                                 width="60px" height="60px" alt="<?php echo config('sitename'); ?>">
                        </a><br>
                        <?php echo config('gg'); ?>
                    </div>
                </center>
                <div class="input-group">
                    <span class="input-group-addon"><i class="icon-ghost"></i>您的QQ</span>
                    <input name="qq" class="form-control" placeholder="留下QQ让我知道你是谁！">
                </div>
                <br/>
                <div class="input-group">
                    <span class="input-group-addon"><i class="icon-envelope"></i>您的留言</span>
                    <textarea class="form-control" name="msg" cols="30" rows="3" placeholder="V完之后有什么想说的话吗？"></textarea>
                </div>
                <br/>
                <div class="input-group">
                    <span class="input-group-addon"><i class="icon-cup"></i>V我多少？</span>
                    <input name="money" class="form-control" placeholder="V50V50V50V50V50V50V50">
                </div>
                <br/>
                <center>
                    <div class="alert alert-warning">选择一种方式狠狠的羞辱我...</div>
                    <div class="btn-group btn-group-justified" role="group" aria-label="...">
                        <div class="btn-group" role="group">
                            <button onclick="pay('alipay')" class="btn btn-primary">支付宝</button>
                        </div>
                        <div class="btn-group" role="group">
                            <button onclick="pay('qqpay')" class="btn btn-danger">QQ</button>
                        </div>
                        <div class="btn-group" role="group">
                            <button onclick="pay('wxpay')" class="btn btn-info">微信</button>
                        </div>
                    </div>
                </center>
            </div>
        </div>
    </div>
   
        
        
        
        
    </div>
    <?php
    if (config('music_sw') == 'on') {
        echo <<<EOF
<audio autoplay="autoplay">
    <source src="/v50.mp3" type="audio/mp3"/>
</audio>
EOF;
    }
    ?>

 
 
<script>
// 
document.addEventListener('click', function() {
    document.getElementById('audios').play()
})
 
</script>
    <script src="//cdn.bootcss.com/jquery/1.11.3/jquery.min.js"></script>
    <script src="//cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script src="/Static/js/layer.js"></script>
    <script>
    
        function pay(type) {
            layer.open({
                type: 2,
                content: '订单发起中...',
                time: false
            });
            var cont = $("input,textarea").serialize();
            $.ajax({
                url: "/index.php?type=" + type,
                data: cont,
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    layer.closeAll();
                    if (data.code == 1) {
                        layer.open({
                            content: data.msg,
                            btn: ['支付', '取消'],
                            skin: 'footer',
                            yes: function () {
                                window.location.href = "/Pay/Submit?trade_no=" + data.trade_no;
                            }
                        });
                    } else {
                        layer.open({
                            content: data.msg,
                            skin: 'msg',
                            time: 2
                        });
                    }
                },
                timeout: 10000,
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer.closeAll();
                    if (textStatus == "timeout") {
                        layer.open({
                            content: '请求超时！',
                            skin: 'msg',
                            time: 2
                        });
                    } else {
                        layer.open({
                            content: '服务器错误！',
                            skin: 'msg',
                            time: 2
                        });
                    }
                }
            });
        }
        
        

        function msg(trade_no) {
            layer.open({
                type: 2,
                content: '查询中...',
                time: false
            });
            $.ajax({
                url: "/index.php?msg=" + trade_no,
                type: 'get',
                dataType: 'json',
                success: function (data) {
                    layer.closeAll();
                    if (data.code == 1) {
                        layer.open({
                            content: data.msg,
                            btn: '关闭'
                        });
                    } else {
                        layer.open({
                            content: data.msg,
                            skin: 'msg',
                            time: 2
                        });
                    }
                },
                timeout: 10000,
                error: function (XMLHttpRequest, textStatus, errorThrown) {
                    layer.closeAll();
                    if (textStatus == "timeout") {
                        layer.open({
                            content: '请求超时！',
                            skin: 'msg',
                            time: 2
                        });
                    } else {
                        layer.open({
                            content: '服务器错误！',
                            skin: 'msg',
                            time: 2
                        });
                    }
                }
            });
        }
    </script>
</body>
</html>
