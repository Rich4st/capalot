<?php

if (empty($args))
  exit;

$footer_text = $args['footer_text'];
?>

<div>
  <h1>
    资源购买信息组件
  </h1>

  <?php if (!empty($footer_text)): ?>
    <p>
      <?php echo $footer_text; ?>
    </p>
  <?php endif; ?>
</div>
