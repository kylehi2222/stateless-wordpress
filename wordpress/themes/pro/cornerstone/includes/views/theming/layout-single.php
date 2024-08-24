<?php

do_action('cs_header');
do_action('cs_layout_begin');

if (is_404()) {
  do_action('cs_layout_begin_404');
  do_action('cs_layout');
  do_action('cs_layout_end_404');
} else {
  while ( have_posts() ) : the_post();
  do_action('cs_layout_begin_single');
  do_action('cs_layout');
  do_action('cs_layout_end_single');
  endwhile;
}

do_action('cs_layout_end');
do_action('cs_footer');
