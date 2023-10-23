<?php

$players = _capalot('players');

?>

<iframe
  style="width: 53vw;height: 80vh" src="<?php echo $players[0]['play_url'] . $args; ?>" frameborder="0"></iframe>
