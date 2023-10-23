<?php

defined('WPINC') || exit;

/**
 * m3u8采集管理
 */

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

    button.addEventListener('click', async () => {
      const collect_url = document.querySelector('#collect_url').value;

      const response = await $.ajax({
        type: 'POST',
        url: capalot.ajax_url,
        dataType: 'json',
        data: {
          action: 'capalot_start_collect',
          url: collect_url,
          nonce: capalot.ajax_nonce
        },
      });

      const dataList = JSON.parse(response.msg.body).list

      const promises = dataList.map(async item => {
        const {
          vod_play_from: parent_cate,
          type_name: category,
          vod_name: title,
          vod_pic: thumb,
          vod_blurb: excerpt,
          vod_actor: actors,
          vod_director: director,
          vod_area: area,
          vod_lang: lang,
          vod_year: year,
          vod_class: tags,
          vod_play_url,
          vod_time
        } = item;

        const urls_payload = vod_play_url.split('#');

        const urls = urls_payload.forEach(async (item) => {
          const [name, url] = item.split('$');

          const formData = new FormData();
          formData.append('post_title', `${title}-${name}`);
          formData.append('post_excerpt', excerpt);
          formData.append('post_tags', tags);
          formData.append('post_category', category);
          formData.append('post_parent_cate', parent_cate);
          formData.append('post_content', `
                        [capalot-m3u8]${url}[/capalot-m3u8]
                        <img src="${thumb}" alt="${title}">
                        <p>${title}</p>
                        <p>${excerpt}</p>
                        <p>导演：${director}</p>
                        <p>演员：${actors}</p>
                        <p>地区：${area}</p>
                        <p>语言：${lang}</p>
                        <p>上映年份：${year}</p>
                        <p>类型：${tags}</p>
                      `);

          return await fetch('http://106.52.244.92/auto-post.php?action=publish&key=123456', {
            method: 'POST',
            body: formData
          })
        });

        return urls;
      })

      const responses = await Promise.all(promises);

      responses.forEach((response, index) => {
        console.log("Response for item with ID " + initialData[index].id + ":", response);
      });

    });
  });
</script>