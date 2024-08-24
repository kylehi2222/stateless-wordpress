<?php

// Custom (Container | Header, Footer)

get_header();

?>

  <div class="x-container max width offset">
    <div class="x-main full" role="main">

      <?php while ( have_posts() ) : the_post(); ?>

        <?php x_get_view( 'global', '_content', 'the-content' ); ?>

      <?php endwhile; ?>

    </div>
  </div>

<?php get_footer(); ?>
