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

  <div class="wrap cj_page">
    <div>
      <div class="cj_box ">
        <h1 class="wp-heading-inline">采集管理</h1>
        <p>采集管理相关描述......</p>
        <div class="cj_form">
          <form method="post" class="form-wrap">
            <div class="form-field form-required term-name-wrap">
              <label>采集数据</label>
              <input id="collect_url" class=" " type="text">
            </div>
            <div class="submit">
              <button id="collect_button" class=" button-primary">开始采集</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <br class="clear">
  </div>

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