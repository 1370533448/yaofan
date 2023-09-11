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
      <div class="panel-heading" style="background: linear-gradient(to right,#b221ff,#14b7ff,#8ae68a);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <font color="#000000"><b><i class="icon-heart" style="color:red"></i>大佬们V过的记录</b></font>
          <button onclick="location.href='index.php'" style="background-color: #14b7ff; color: white; border: none; padding: 5px 10px;">返回首页，用钱羞辱我</button>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table table-striped">
          <thead>
            <tr>
              <th>QQ/昵称</th>
              <th>V钳方式/金额</th>
              <th>ip/城市</th>
              <th>留言</th>
              <th>V钳时间/完成时间</th>
              <th>状态</th>
            </tr>
          </thead>
          <tbody>
            <?php echo ss_list($order); ?>
          </tbody>
        </table>

        <div class="pagination">
          <?php 
            $total_records = Db("SELECT COUNT(*) as count FROM yyhy_order")[0]['count'];
            $total_pages = ceil($total_records / $per_page);
            $start_page = max(1, min($page-2, $total_pages-5));
            $end_page = min($total_pages, max($page+3, 6));

            if ($page > 1) {
              echo "<a href='log.php?page=".($page-1)."'>上一页</a> ";
            }

            for ($i=$start_page; $i<=$end_page; $i++) {
              echo "<a href='log.php?page=".$i."' ".($i == $page ? "class='active'" : "").">".$i."</a> ";
            }

            if ($page < $total_pages) {
              echo "<a href='log.php?page=".($page+1)."'>下一页</a> ";
            } 
          ?>
        </div>
      </div>
    </div>
  </div>
</div>

        
        
  
    </div>
    
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
