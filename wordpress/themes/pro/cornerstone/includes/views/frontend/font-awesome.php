<?php

// =============================================================================
// Due to requirements of keeping things backward compatible, when FA v5.0 was
// released we made solid the primary font family because it was the only one
// that included all the icons for free. When FA Pro was added we are continuing
// to use that pattern but the FontAwesomePro family was added for using custom
// CSS where font weight can be adjusted.
//
// FontAwesome        - Solid face as one family.
// FontAwesomeRegular - Outline face as one family.
// FontAwesomeLight   - Light face as one family.
// FontAwesomeBrands  - All branded icons.
// FontAwesomePro     - Solid, Regular, and Light faces setup as weights of a
//                      single family.
//
// PHP side Icons are loaded with keys that have prefixes. For example:
//
// - `arrow-alt-circle-right`
// - `o-arrow-alt-circle-right`
// - `l-arrow-alt-circle-right`
//
// The `o-` prefix comes from an older FA notation where the letter "o"
// indicated an "outline" version.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Solid
//   02. Regular
//   03. Light
//   04. Brands
//   05. Pro / Shared
// =============================================================================

$fa_css_prefix  = 'cs-fa';

$fa_version = '?ver=' . CS_FA_VERSION;

$fa_output_path = function($file) use ($fa_font_path, $fa_version) {
  echo $fa_font_path . $file . $fa_version;
};

/* Pro / Shared
// ========================================================================== */
?>


<?php if ( $fa_solid_enable || $fa_regular_enable || $fa_light_enable || $fa_brands_enable ) : ?>

  @font-face {
    font-family: 'FontAwesomePro';
    font-style: normal;
    font-weight: 900;
    font-display: block;
    src: url('<?php $fa_output_path("fa-solid-900.woff2"); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-solid-900.ttf');?>') format('truetype');
  }

  [data-x-fa-pro-icon] {
    font-family: "FontAwesomePro" !important;
  }

  [data-x-fa-pro-icon]:before {
    content: attr(data-x-fa-pro-icon);
  }

  [data-x-icon],
  [data-x-icon-o],
  [data-x-icon-l],
  [data-x-icon-s],
  [data-x-icon-b],
  [data-x-icon-sr],
  [data-x-icon-ss],
  [data-x-icon-sl],
  [data-x-fa-pro-icon],
  [class*="<?php echo $fa_css_prefix; ?>-"] {
    display: inline-flex;
    font-style: normal;
    font-weight: 400;
    text-decoration: inherit;
    text-rendering: auto;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
  }

  [data-x-icon].left,
  [data-x-icon-o].left,
  [data-x-icon-l].left,
  [data-x-icon-s].left,
  [data-x-icon-b].left,
  [data-x-icon-sr].left,
  [data-x-icon-ss].left,
  [data-x-icon-sl].left,
  [data-x-fa-pro-icon].left,
  [class*="<?php echo $fa_css_prefix; ?>-"].left {
    margin-right: 0.5em;
  }

  [data-x-icon].right,
  [data-x-icon-o].right,
  [data-x-icon-l].right,
  [data-x-icon-s].right,
  [data-x-icon-b].right,
  [data-x-icon-sr].right,
  [data-x-icon-ss].right,
  [data-x-icon-sl].right,
  [data-x-fa-pro-icon].right,
  [class*="<?php echo $fa_css_prefix; ?>-"].right {
    margin-left: 0.5em;
  }

  [data-x-icon]:before,
  [data-x-icon-o]:before,
  [data-x-icon-l]:before,
  [data-x-icon-s]:before,
  [data-x-icon-b]:before,
  [data-x-icon-sr]:before,
  [data-x-icon-ss]:before,
  [data-x-icon-sl]:before,
  [data-x-fa-pro-icon]:before,
  [class*="<?php echo $fa_css_prefix; ?>-"]:before {
    line-height: 1;
  }

<?php endif; ?>


/* Solid
// ========================================================================== */

<?php if ( $fa_solid_enable ) : ?>

  @font-face {
    font-family: 'FontAwesome';
    font-style: normal;
    font-weight: 900;
    font-display: block;
    src: url('<?php $fa_output_path('fa-solid-900.woff2'); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-solid-900.ttf'); ?>') format('truetype');
  }

  [data-x-icon],
  [data-x-icon-s],
  [data-x-icon][class*="<?php echo $fa_css_prefix; ?>-"] {
    font-family: "FontAwesome" !important;
    font-weight: 900;
  }

  [data-x-icon]:before,
  [data-x-icon][class*="<?php echo $fa_css_prefix; ?>-"]:before {
    content: attr(data-x-icon);
  }

  [data-x-icon-s]:before {
    content: attr(data-x-icon-s);
  }

<?php endif; ?>



/* Regular
// ========================================================================== */

<?php if ( $fa_regular_enable ) : ?>

  @font-face {
    font-family: 'FontAwesomeRegular';
    font-style: normal;
    font-weight: 400;
    font-display: block;
    src: url('<?php $fa_output_path('fa-regular-400.woff2'); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-regular-400.ttf'); ?>') format('truetype');
  }

  @font-face {
    font-family: 'FontAwesomePro';
    font-style: normal;
    font-weight: 400;
    font-display: block;
    src: url('<?php $fa_output_path('fa-regular-400.woff2'); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-regular-400.ttf'); ?>') format('truetype');
  }

  [data-x-icon-o] {
    font-family: "FontAwesomeRegular" !important;
  }

  [data-x-icon-o]:before {
    content: attr(data-x-icon-o);
  }

<?php endif; ?>



/* Light
// ========================================================================== */

<?php if ( $fa_light_enable ) : ?>

  @font-face {
    font-family: 'FontAwesomeLight';
    font-style: normal;
    font-weight: 300;
    font-display: block;
    src: url('<?php $fa_output_path('fa-light-300.woff2'); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-light-300.ttf'); ?>') format('truetype');
  }

  @font-face {
    font-family: 'FontAwesomePro';
    font-style: normal;
    font-weight: 300;
    font-display: block;
    src: url('<?php $fa_output_path('fa-light-300.woff2'); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-light-300.ttf'); ?>') format('truetype');
  }

  [data-x-icon-l] {
    font-family: "FontAwesomeLight" !important;
    font-weight: 300;
  }

  [data-x-icon-l]:before {
    content: attr(data-x-icon-l);
  }

<?php endif; ?>



/* Brands
// ========================================================================== */

<?php if ( $fa_brands_enable ) : ?>

  @font-face {
    font-family: 'FontAwesomeBrands';
    font-style: normal;
    font-weight: normal;
    font-display: block;
    src: url('<?php $fa_output_path('fa-brands-400.woff2'); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-brands-400.ttf'); ?>') format('truetype');
  }

  [data-x-icon-b] {
    font-family: "FontAwesomeBrands" !important;
  }

  [data-x-icon-b]:before {
    content: attr(data-x-icon-b);
  }

<?php endif; ?>


/* WooCommerce
// ========================================================================== */
<?php if ( class_exists( 'woocommerce' ) ) : ?>

  <?php

    if ( $fa_solid_enable || $fa_regular_enable || $fa_light_enable ){
      // light
      if ( $fa_light_enable ){
        $fontawesome_family = 
        'font-family: "FontAwesomeLight" !important;
        font-weight: 300;';
      }

      // regular
      if ( $fa_regular_enable ){
        $fontawesome_family = 'font-family: "FontAwesomeRegular" !important;';
      }

      // solid
      if ( $fa_solid_enable ){
        $fontawesome_family = 
        'font-family: "FontAwesome" !important;
        font-weight: 900;';
      }
    }else{
      // default
      $fontawesome_family = 
      'font-family: "FontAwesome" !important;
      font-weight: 900;';
    }

  ?>
  .woocommerce .button.product_type_simple:before, 
  .woocommerce .button.product_type_variable:before, 
  .woocommerce .button.single_add_to_cart_button:before{
    <?php echo $fontawesome_family; ?>
  }

<?php endif; ?>

/* Sharp Solid
// ========================================================================== */

<?php if ( $data['fa_sharp-solid_enable'] ) : ?>

  @font-face {
    font-family: 'FontAwesome';
    font-style: normal;
    font-weight: 900;
    font-display: block;
    src: url('<?php $fa_output_path('fa-sharp-solid-900.woff2'); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-sharp-solid-900.ttf'); ?>') format('truetype');
  }

  [data-x-icon],
  [data-x-icon-ss],
  [data-x-icon][class*="<?php echo $fa_css_prefix; ?>-"] {
    font-family: "FontAwesome" !important;
    font-weight: 900;
  }

  [data-x-icon]:before,
  [data-x-icon][class*="<?php echo $fa_css_prefix; ?>-"]:before {
    content: attr(data-x-icon);
  }

  [data-x-icon-ss]:before {
    content: attr(data-x-icon-ss);
  }

<?php endif; ?>

/* Sharp Regular
// ========================================================================== */

<?php if ( $data['fa_sharp-regular_enable'] ) : ?>

  @font-face {
    font-family: 'FontAwesome';
    font-style: normal;
    font-weight: 400;
    font-display: block;
    src: url('<?php $fa_output_path('fa-sharp-regular-400.woff2'); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-sharp-regular-400.ttf'); ?>') format('truetype');
  }

  [data-x-icon],
  [data-x-icon-sr],
  [data-x-icon][class*="<?php echo $fa_css_prefix; ?>-"] {
    font-family: "FontAwesome" !important;
    font-weight: 400;
  }

  [data-x-icon]:before,
  [data-x-icon][class*="<?php echo $fa_css_prefix; ?>-"]:before {
    content: attr(data-x-icon);
  }

  [data-x-icon-sr]:before {
    content: attr(data-x-icon-sr);
  }

<?php endif; ?>

/* Sharp Light
// ========================================================================== */

<?php if ( $data['fa_sharp-light_enable'] ) : ?>

  @font-face {
    font-family: 'FontAwesome';
    font-style: normal;
    font-weight: 300;
    font-display: block;
    src: url('<?php $fa_output_path('fa-sharp-light-300.woff2'); ?>') format('woff2'),
         url('<?php $fa_output_path('fa-sharp-light-300.ttf'); ?>') format('truetype');
  }

  [data-x-icon],
  [data-x-icon-sl],
  [data-x-icon][class*="<?php echo $fa_css_prefix; ?>-"] {
    font-family: "FontAwesome" !important;
    font-weight: 300;
  }

  [data-x-icon]:before,
  [data-x-icon][class*="<?php echo $fa_css_prefix; ?>-"]:before {
    content: attr(data-x-icon);
  }

  [data-x-icon-sl]:before {
    content: attr(data-x-icon-sl);
  }

<?php endif; ?>

<?php if (get_theme_support("cornerstone-managed")) {
  include_once(__DIR__ . '/font-awesome-extras.php');
} ?>
