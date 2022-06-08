<?php
include_once ("pullNews.php");

include_once ("php/header.php");

?>

<h1>News From EWN.co.za for the test</h1>
<?php
output_rss_feed('https://ewn.co.za/RSS%20Feeds/Latest%20News', 500, true, true, 500);

?>

<?php
include_once ("php/footer.php");
?>