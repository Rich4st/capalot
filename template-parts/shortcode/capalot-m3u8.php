<?php

$players = _capalot('players');
?>

<iframe id="audio" class="w-full md:h-96 h-48 bg-center bg-no-repeat bg-cover" src="<?php echo $players[0]['play_url'] . $args; ?>" allowfullscreen frameborder="0"></iframe>
<div>
  播放源：<select name="languages" id="lang" class="my-4 w-1/3 dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
    <?php foreach ($players as $item) {
      echo "<option value='" . $item['play_url'] . "'>" . $item['name'] . '</option>';
    } ?>
  </select>
</div>
<script>
  var lang = document.getElementById('lang');
  lang.addEventListener('change', function() {
    var select = this.value;
    var audio = document.getElementById('audio');
    let old_url = audio.src.split('=');
    const new_url = `${select}${old_url[1]}`
    audio.src = new_url;
  })
</script>