<!DOCTYPE html>
<html>
  <head>
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type">
    <title>系统发生致命错误</title>
    <style type="text/css">
      *{ padding: 0; margin: 0; }
      html{ overflow-y: scroll; }
      body{ background: #fff; font-family: '微软雅黑'; color: #333; font-size: 16px; }
      img{ border: 0; }
      .error{ padding: 24px 48px; }
      h1{ font-size: 32px; line-height: 48px; }
      .error .content{ padding-top: 10px}
      .error .info{ margin-bottom: 12px; }
      .error .info .title{ margin-bottom: 3px; }
      .error .info .title h3{ color: #000; font-weight: 700; font-size: 16px; }
      .error .info .text{ line-height: 24px; }
      .copyright{ padding: 12px 48px; color: #999; }
      .copyright a{ color: #000; text-decoration: none; }
    </style>
  </head>
  <body>
    <div class="error">
      <h1>系统发生致命错误</h1><hr/><br/>
      <h2><?php echo strip_tags($errorArray['message']);?></h2><br/>
      <div class="content">
        <?php if(isset($errorArray['file'])) {?>
        <div class="info">
          <div class="title">
            <h3>错误位置</h3>
          </div>
          <div class="text">
            <p>文件: <?php echo $errorArray['file'] ;?> &#12288;行: <?php echo $errorArray['line'];?></p>
          </div>
        </div>
        <?php }?>
        <div class="info">
          <div class="title">
            <h3>PHP版本</h3>
          </div>
          <div class="text">
            <p><?php echo PHP_VERSION ?></p>
          </div>
        </div>
        <div class="info">
          <div class="title">
            <h3>操作系统</h3>
          </div>
          <div class="text">
            <p><?php echo PHP_OS ?></p>
          </div>
        </div>
        <?php if(isset($errorArray['trace'])) {?>
        <div class="info">
          <div class="title">
            <h3>流程</h3>
          </div>
          <div class="text">
            <p><?php echo nl2br($errorArray['trace']);?></p>
          </div>
        </div>
        <?php }?>
      </div>
    </div>
    <div class="copyright"><p>NookeryPHP</p></div>
  </body>
</html>
