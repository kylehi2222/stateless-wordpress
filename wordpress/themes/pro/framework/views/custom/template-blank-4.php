<?php

// Custom (No Container | Header, Footer)

get_header();

?>

  <div class="x-main full" role="main">

    <?php while ( have_posts() ) : the_post(); ?>

      <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <?php x_get_view( 'global', '_content', 'the-content' ); ?>
      </article>

    <?php endwhile; ?>

  </div>

<?php get_footer(); ?>
