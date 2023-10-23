<?php

defined('WPINC') || exit;

/**
 * m3u8采集管理
 */

function start_collect()
{
  $url = 'https://api.wujinapi.me/api.php/provide/vod/?ac=detail&ids=';
  $response = wp_remote_get($url);
  var_dump($response);
}


?>

<main>
  <input id="collect_url" type="text">

  <button id="collect_button">开始采集</button>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const button = document.querySelector('#collect_button');

    button.addEventListener('click', () => {
      const collect_url = document.querySelector('#collect_url').value;

      $.ajax({
        url: collect_url,
        type: 'get',
        success: function(res) {
          console.log(res);
        },
        complete: function(res) {
          console.log(res);
        }
      });
    });
  });
</script>
