<?php

// =============================================================================
// Standard post output for Custom stacks
// =============================================================================

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <div class="entry-wrap">
    <?php x_get_view( 'global', '_content' ); ?>
  </div>
</article>
