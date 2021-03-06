<?php
/**
 * Challenges Feed Template
 */
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="' . get_option('blog_charset') . '"?' . '>';
echo '<?xml-stylesheet type="text/xsl" media="screen" href="' . get_stylesheet_directory_uri() . '/css/rss2full.xsl"?>';

$listType = get_query_var('list');
$contestType = get_query_var('contestType');
$technologies = get_query_var('technologies');
$platforms = get_query_var('platforms');
$contests = array();

if ($contestType == 'all') {
  $contestType = '';
}

$contests = get_contests_rss($listType, $contestType, $technologies, $platforms);
$contests = isset($contests->data) && is_array($contests->data) ? $contests->data : array();

// sort contests on registration start date
uasort($contests, function($a, $b) {
  return strtotime($b->registrationStartDate) - strtotime($a->registrationStartDate);
});
?>
<rss version="2.0"
     xmlns:content="http://purl.org/rss/1.0/modules/content/"
     xmlns:wfw="http://wellformedweb.org/CommentAPI/"
     xmlns:dc="http://purl.org/dc/elements/1.1/"
     xmlns:atom="http://www.w3.org/2005/Atom"
     xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
     xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
    <?php do_action('rss2_ns'); ?>>
    <channel>
      <title><?php bloginfo_rss('name'); ?> - Challenges</title>
      <atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml"/>
      <link><?php bloginfo_rss('url') ?></link>
      <description><?php bloginfo_rss('description') ?></description>
      <sy:updatePeriod><?php echo apply_filters('rss_update_period', 'hourly'); ?></sy:updatePeriod>
      <sy:updateFrequency><?php echo apply_filters('rss_update_frequency', '1'); ?></sy:updateFrequency>
      <?php
      do_action('rss2_head');

      $base_url = get_bloginfo('siteurl') . '/challenge-details';
      foreach ($contests as $contest) {
        $name = trim($contest->challengeName)
        ?>
        <item>
          <title><?php echo $name ?></title>
          <link><?php echo "{$base_url}/{$contest->challengeId}?type={$contest->challengeCommunity}" ?></link>
          <pubDate><?php echo date('d M Y H:i T', strtotime($contest->registrationStartDate)) ?></pubDate>
          <guid><?php echo $contest->challengeId ?></guid>        
          <?php rss_enclosure(); ?>
          <?php do_action('rss2_item'); ?>
        </item>
      <?php } ?>
    </channel>
</rss>
