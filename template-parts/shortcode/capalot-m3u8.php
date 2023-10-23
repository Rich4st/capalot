<?php

$players = _capalot('players');

?>

<iframe class="w-full h-96 bg-center bg-no-repeat bg-cover"  src="<?php echo $players[0]['play_url'] . $args; ?>" frameborder="0"></iframe>
<select name="languages" id="lang" class="my-4 w-1/3 dark:border-gray-500 dark:bg-dark form-control p-1.5 border rounded-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500">
  <option value="javascript">JavaScript</option>
  <option value="php">PHP</option>
  <option value="java">Java</option>
</select>