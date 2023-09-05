<?php
if (is_active_sidebar('home-module')) {
  dynamic_sidebar('home-module');
} else { ?>
  <h1>hello capalot!</h1>
<?php
}
