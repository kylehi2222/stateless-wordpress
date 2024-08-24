(() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __markAsModule = (target) => __defProp(target, "__esModule", { value: true });
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __reExport = (target, module, copyDefault, desc) => {
    if (module && typeof module === "object" || typeof module === "function") {
      for (let key of __getOwnPropNames(module))
        if (!__hasOwnProp.call(target, key) && (copyDefault || key !== "default"))
          __defProp(target, key, { get: () => module[key], enumerable: !(desc = __getOwnPropDesc(module, key)) || desc.enumerable });
    }
    return target;
  };
  var __toESM = (module, isNodeMode) => {
    return __reExport(__markAsModule(__defProp(module != null ? __create(__getProtoOf(module)) : {}, "default", !isNodeMode && module && module.__esModule ? { get: () => module.default, enumerable: true } : { value: module, enumerable: true })), module);
  };

  // js/vendor/csslint/parserlib-base.js
  var require_parserlib_base = __commonJS({
    "js/vendor/csslint/parserlib-base.js"(exports, module) {
      "use strict";
      (() => {
        const GlobalKeywords = ["initial", "inherit", "revert", "unset"];
        const Properties = {
          __proto__: null,
          "accent-color": "auto | <color>",
          "align-items": "normal | stretch | <baseline-position> | [ <overflow-position>? <self-position> ]",
          "align-content": "normal | <baseline-position> | <content-distribution> | <overflow-position>? <content-position>",
          "align-self": "auto | normal | stretch | <baseline-position> | <overflow-position>? <self-position>",
          "all": GlobalKeywords.join(" | "),
          "alignment-baseline": "auto | baseline | use-script | before-edge | text-before-edge | after-edge | text-after-edge | central | middle | ideographic | alphabetic | hanging | mathematical",
          "animation": "[ <time> || <timing-function> || <time> || [ infinite | <num> ] || <animation-direction> || <animation-fill-mode> || [ running | paused ] || [ none | <custom-ident> | <string> ] ]#",
          "animation-composition": "[ replace | add | accumulate ]#",
          "animation-delay": "<time>#",
          "animation-direction": "<animation-direction>#",
          "animation-duration": "<time>#",
          "animation-fill-mode": "<animation-fill-mode>#",
          "animation-iteration-count": "[ <num> | infinite ]#",
          "animation-name": "[ none | <keyframes-name> ]#",
          "animation-play-state": "[ running | paused ]#",
          "animation-timing-function": "<timing-function>#",
          "appearance": "none | auto",
          "-moz-appearance": "none | button | button-arrow-down | button-arrow-next | button-arrow-previous | button-arrow-up | button-bevel | button-focus | caret | checkbox | checkbox-container | checkbox-label | checkmenuitem | dualbutton | groupbox | listbox | listitem | menuarrow | menubar | menucheckbox | menuimage | menuitem | menuitemtext | menulist | menulist-button | menulist-text | menulist-textfield | menupopup | menuradio | menuseparator | meterbar | meterchunk | progressbar | progressbar-vertical | progresschunk | progresschunk-vertical | radio | radio-container | radio-label | radiomenuitem | range | range-thumb | resizer | resizerpanel | scale-horizontal | scalethumbend | scalethumb-horizontal | scalethumbstart | scalethumbtick | scalethumb-vertical | scale-vertical | scrollbarbutton-down | scrollbarbutton-left | scrollbarbutton-right | scrollbarbutton-up | scrollbarthumb-horizontal | scrollbarthumb-vertical | scrollbartrack-horizontal | scrollbartrack-vertical | searchfield | separator | sheet | spinner | spinner-downbutton | spinner-textfield | spinner-upbutton | splitter | statusbar | statusbarpanel | tab | tabpanel | tabpanels | tab-scroll-arrow-back | tab-scroll-arrow-forward | textfield | textfield-multiline | toolbar | toolbarbutton | toolbarbutton-dropdown | toolbargripper | toolbox | tooltip | treeheader | treeheadercell | treeheadersortarrow | treeitem | treeline | treetwisty | treetwistyopen | treeview | -moz-mac-unified-toolbar | -moz-win-borderless-glass | -moz-win-browsertabbar-toolbox | -moz-win-communicationstext | -moz-win-communications-toolbox | -moz-win-exclude-glass | -moz-win-glass | -moz-win-mediatext | -moz-win-media-toolbox | -moz-window-button-box | -moz-window-button-box-maximized | -moz-window-button-close | -moz-window-button-maximize | -moz-window-button-minimize | -moz-window-button-restore | -moz-window-frame-bottom | -moz-window-frame-left | -moz-window-frame-right | -moz-window-titlebar | -moz-window-titlebar-maximized",
          "-ms-appearance": "none | icon | window | desktop | workspace | document | tooltip | dialog | button | push-button | hyperlink | radio | radio-button | checkbox | menu-item | tab | menu | menubar | pull-down-menu | pop-up-menu | list-menu | radio-group | checkbox-group | outline-tree | range | field | combo-box | signature | password | normal",
          "-webkit-appearance": "auto | none | button | button-bevel | caps-lock-indicator | caret | checkbox | default-button | listbox | listitem | media-fullscreen-button | media-mute-button | media-play-button | media-seek-back-button | media-seek-forward-button | media-slider | media-sliderthumb | menulist | menulist-button | menulist-text | menulist-textfield | push-button | radio | searchfield | searchfield-cancel-button | searchfield-decoration | searchfield-results-button | searchfield-results-decoration | slider-horizontal | slider-vertical | sliderthumb-horizontal | sliderthumb-vertical | square-button | textarea | textfield | scrollbarbutton-down | scrollbarbutton-left | scrollbarbutton-right | scrollbarbutton-up | scrollbargripper-horizontal | scrollbargripper-vertical | scrollbarthumb-horizontal | scrollbarthumb-vertical | scrollbartrack-horizontal | scrollbartrack-vertical",
          "-o-appearance": "none | window | desktop | workspace | document | tooltip | dialog | button | push-button | hyperlink | radio | radio-button | checkbox | menu-item | tab | menu | menubar | pull-down-menu | pop-up-menu | list-menu | radio-group | checkbox-group | outline-tree | range | field | combo-box | signature | password | normal",
          "aspect-ratio": "auto || <ratio>",
          "backdrop-filter": "<filter-function-list> | none",
          "backface-visibility": "<vis-hid>",
          "background": "[ <bg-layer> , ]* <final-bg-layer>",
          "background-attachment": "<attachment>#",
          "background-blend-mode": "<blend-mode>",
          "background-clip": "[ <box> | text ]#",
          "background-color": "<color>",
          "background-image": "<bg-image>#",
          "background-origin": "<box>#",
          "background-position": "<bg-position>#",
          "background-position-x": "[ center | [ left | right ]? <len-pct>? ]#",
          "background-position-y": "[ center | [ top | bottom ]? <len-pct>? ]#",
          "background-repeat": "<repeat-style>#",
          "background-size": "<bg-size>#",
          "baseline-shift": "baseline | sub | super | <len-pct>",
          "baseline-source": "auto | first | last",
          "block-size": "<width>",
          "border-collapse": "collapse | separate",
          "border-image": "[ none | <image> ] || <border-image-slice> [ / <border-image-width> | / <border-image-width>? / <border-image-outset> ]? || <border-image-repeat>",
          "border-image-outset": "[ <len> | <num> ]{1,4}",
          "border-image-repeat": "[ stretch | repeat | round | space ]{1,2}",
          "border-image-slice": "<border-image-slice>",
          "border-image-source": "<image> | none",
          "border-image-width": "[ <len-pct> | <num> | auto ]{1,4}",
          "border-spacing": "<len>{1,2}",
          "border-bottom-left-radius": "<len-pct>{1,2}",
          "border-bottom-right-radius": "<len-pct>{1,2}",
          "border-end-end-radius": "<len-pct>{1,2}",
          "border-end-start-radius": "<len-pct>{1,2}",
          "border-radius": "<len-pct0+>{1,4} [ / <len-pct0+>{1,4} ]?",
          "border-start-end-radius": "<len-pct>{1,2}",
          "border-start-start-radius": "<len-pct>{1,2}",
          "border-top-left-radius": "<len-pct>{1,2}",
          "border-top-right-radius": "<len-pct>{1,2}",
          "bottom": "<width>",
          "box-decoration-break": "slice | clone",
          "box-shadow": "none | <shadow>#",
          "box-sizing": "content-box | border-box",
          "break-after": "<break-inside> | always | left | right | page | column",
          "break-before": "<break-after>",
          "break-inside": "auto | avoid | avoid-page | avoid-column",
          "caret-color": "auto | <color>",
          "caption-side": "top | bottom | inline-start | inline-end",
          "clear": "none | right | left | both | inline-start | inline-end",
          "clip": "<rect> | auto",
          "clip-path": "<uri> | [ <basic-shape> || <geometry-box> ] | none",
          "clip-rule": "nonzero | evenodd",
          "color": "<color>",
          "color-interpolation": "auto | sRGB | linearRGB",
          "color-interpolation-filters": "<color-interpolation>",
          "color-profile": 1,
          "color-rendering": "auto | optimizeSpeed | optimizeQuality",
          "color-scheme": "normal | [ light | dark | <custom-ident> ]+ && only?",
          "column-count": "<int> | auto",
          "column-fill": "auto | balance",
          "column-gap": "normal | <len-pct>",
          "column-rule": "<border-shorthand>",
          "column-rule-color": "<color>",
          "column-rule-style": "<border-style>",
          "column-rule-width": "<border-width>",
          "column-span": "none | all",
          "column-width": "<len> | auto",
          "columns": 1,
          "contain": "none | strict | content | [ size || layout || style || paint ]",
          "contain-intrinsic-size": "<contain-intrinsic>{1,2}",
          "container": "<container-name> [ / <container-type> ]?",
          "container-name": "none | <custom-ident>+",
          "container-type": "normal || [ size | inline-size ]",
          "content": "normal | none | <content-list> [ / <string> ]?",
          "content-visibility": "auto | <vis-hid>",
          "counter-increment": "<counter>",
          "counter-reset": "<counter>",
          "counter-set": "<counter>",
          "cursor": "[ <uri> [ <num> <num> ]? , ]* [ auto | default | none | context-menu | help | pointer | progress | wait | cell | crosshair | text | vertical-text | alias | copy | move | no-drop | not-allowed | grab | grabbing | e-resize | n-resize | ne-resize | nw-resize | s-resize | se-resize | sw-resize | w-resize | ew-resize | ns-resize | nesw-resize | nwse-resize | col-resize | row-resize | all-scroll | zoom-in | zoom-out ]",
          "cx": "<x>",
          "cy": "<x>",
          "d": 1,
          "direction": "ltr | rtl",
          "display": "[ <display-outside> || <display-inside> ] | <display-listitem> | <display-internal> | <display-box> | <display-legacy> | -webkit-box | -webkit-inline-box | -ms-flexbox",
          "dominant-baseline": "auto | text-bottom | alphabetic | ideographic | middle | central | mathematical | hanging | text-top",
          "empty-cells": "show | hide",
          "fill": "<paint>",
          "fill-opacity": "<num0-1>",
          "fill-rule": "nonzero | evenodd",
          "filter": "<filter-function-list> | <ie-function> | none",
          "flex": "<flex-shorthand>",
          "flex-basis": "<width>",
          "flex-direction": "row | row-reverse | column | column-reverse",
          "flex-flow": "<flex-direction> || <flex-wrap>",
          "flex-grow": "<num>",
          "flex-shrink": "<num>",
          "flex-wrap": "nowrap | wrap | wrap-reverse",
          "float": "left | right | none | inline-start | inline-end",
          "flood-color": 1,
          "flood-opacity": "<num0-1>",
          "font": "<font-short-tweak-no-pct>? <font-short-core> | [ <font-short-tweak-no-pct> || <pct> ]? <font-short-core> | caption | icon | menu | message-box | small-caption | status-bar",
          "font-family": "[ <generic-family> | <family-name> ]#",
          "font-feature-settings": "[ <ascii4> [ <int0+> | on | off ]? ]# | normal",
          "font-kerning": "auto | normal | none",
          "font-language-override": "normal | <string>",
          "font-optical-sizing": "auto | none",
          "font-palette": "none | normal | light | dark | <custom-ident>",
          "font-size": "<absolute-size> | <relative-size> | <len-pct0+>",
          "font-size-adjust": "<num> | none",
          "font-stretch": "<font-stretch-named> | <pct>",
          "font-style": "normal | italic | oblique <angle>?",
          "font-synthesis": "none | [ weight || style ]",
          "font-synthesis-style": "auto | none",
          "font-synthesis-weight": "auto | none",
          "font-synthesis-small-caps": "auto | none",
          "font-variant": "normal | none | [ <font-variant-ligatures> || <font-variant-alternates> || <font-variant-caps> || <font-variant-numeric> || <font-variant-east-asian> ]",
          "font-variant-alternates": "<font-variant-alternates> | normal",
          "font-variant-caps": "<font-variant-caps> | normal",
          "font-variant-east-asian": "<font-variant-east-asian> | normal",
          "font-variant-emoji": "auto | text | emoji | unicode",
          "font-variant-ligatures": "<font-variant-ligatures> | normal | none",
          "font-variant-numeric": "<font-variant-numeric> | normal",
          "font-variant-position": "normal | sub | super",
          "font-variation-settings": "normal | [ <string> <num> ]#",
          "font-weight": "normal | bold | bolder | lighter | <num1-1000>",
          "forced-color-adjust": "auto | none | preserve-parent-color",
          "gap": "<column-gap>{1,2}",
          "grid": "<grid-template> | <grid-template-rows> / [ auto-flow && dense? ] <grid-auto-columns>? | [ auto-flow && dense? ] <grid-auto-rows>? / <grid-template-columns>",
          "grid-area": "<grid-line> [ / <grid-line> ]{0,3}",
          "grid-auto-columns": "<track-size>+",
          "grid-auto-flow": "[ row | column ] || dense",
          "grid-auto-rows": "<track-size>+",
          "grid-column": "<grid-line> [ / <grid-line> ]?",
          "grid-column-end": "<grid-line>",
          "grid-column-gap": -1,
          "grid-column-start": "<grid-line>",
          "grid-gap": -1,
          "grid-row": "<grid-line> [ / <grid-line> ]?",
          "grid-row-end": "<grid-line>",
          "grid-row-gap": -1,
          "grid-row-start": "<grid-line>",
          "grid-template": "none | [ <grid-template-rows> / <grid-template-columns> ] | [ <line-names>? <string> <track-size>? <line-names>? ]+ [ / <explicit-track-list> ]?",
          "grid-template-areas": "none | <string>+",
          "grid-template-columns": "<grid-template-rows>",
          "grid-template-rows": "none | <track-list> | <auto-track-list>",
          "hanging-punctuation": "none | [ first || [ force-end | allow-end ] || last ]",
          "height": "auto | <width-height>",
          "hyphenate-character": "<string> | auto",
          "hyphenate-limit-chars": "[ auto | <int> ]{1,3}",
          "hyphens": "none | manual | auto",
          "image-orientation": "from-image | none | [ <angle> || flip ]",
          "image-rendering": "auto | smooth | high-quality | crisp-edges | pixelated | optimizeSpeed | optimizeQuality | -webkit-optimize-contrast",
          "image-resolution": 1,
          "inline-size": "<width>",
          "inset": "<width>{1,4}",
          "inset-block": "<width>{1,2}",
          "inset-block-end": "<width>",
          "inset-block-start": "<width>",
          "inset-inline": "<width>{1,2}",
          "inset-inline-end": "<width>",
          "inset-inline-start": "<width>",
          "isolation": "auto | isolate",
          "justify-content": "normal | <content-distribution> | <overflow-position>? [ <content-position> | left | right ]",
          "justify-items": "normal | stretch | <baseline-position> | [ <overflow-position>? <self-position> ] | [ legacy || [ left | right | center ] ]",
          "justify-self": "auto | normal | stretch | <baseline-position> | <overflow-position>? [ <self-position> | left | right ]",
          "left": "<width>",
          "letter-spacing": "<len> | normal",
          "lighting-color": "<color>",
          "line-height": "<line-height>",
          "line-break": "auto | loose | normal | strict | anywhere",
          "list-style": "<list-style-position> || <list-style-image> || <list-style-type>",
          "list-style-image": "<image> | none",
          "list-style-position": "inside | outside",
          "list-style-type": "<string> | disc | circle | square | decimal | decimal-leading-zero | lower-roman | upper-roman | lower-greek | lower-latin | upper-latin | armenian | georgian | lower-alpha | upper-alpha | none | symbols()",
          "math-depth": "auto-add | add(<int>) | <int>",
          "math-shift": "<math-style>",
          "math-style": "normal | compact",
          "margin": "<width>{1,4}",
          "margin-bottom": "<width>",
          "margin-left": "<width>",
          "margin-right": "<width>",
          "margin-top": "<width>",
          "margin-block": "<width>{1,2}",
          "margin-block-end": "<width>",
          "margin-block-start": "<width>",
          "margin-inline": "<width>{1,2}",
          "margin-inline-end": "<width>",
          "margin-inline-start": "<width>",
          "marker": -1,
          "marker-end": 1,
          "marker-mid": 1,
          "marker-start": 1,
          "mask": "[ [ none | <image> ] || <position> [ / <bg-size> ]? || <repeat-style> || <geometry-box> || [ <geometry-box> | no-clip ] || [ add | subtract | intersect | exclude ] || [ alpha | luminance | match-source ] ]#",
          "mask-image": "[ none | <image> ]#",
          "mask-type": "luminance | alpha",
          "max-height": "none | <width-height>",
          "max-width": "none | <width-height>",
          "min-height": "auto | <width-height>",
          "min-width": "auto | <width-height>",
          "max-block-size": "<len-pct> | none",
          "max-inline-size": "<len-pct> | none",
          "min-block-size": "<len-pct>",
          "min-inline-size": "<len-pct>",
          "mix-blend-mode": "<blend-mode>",
          "object-fit": "fill | contain | cover | none | scale-down",
          "object-position": "<position>",
          "object-view-box": "none | <inset> | <rect> | <xywh>",
          "offset": "[ <offset-position>? <offset-path> [<len-pct> || <offset-rotate>]? | <offset-position> ] [ / <offset-anchor> ]?",
          "offset-anchor": "auto | <position>",
          "offset-distance": "<len-pct>",
          "offset-path": "none | ray() | path() | <uri> | [<basic-shape> && <coord-box>?] | <coord-box>",
          "offset-position": "auto | <position>",
          "offset-rotate": "[ auto | reverse ] || <angle>",
          "opacity": "<num0-1> | <pct>",
          "order": "<int>",
          "orphans": "<int>",
          "outline": "[ <color> | invert ] || [ auto | <border-style> ] || <border-width>",
          "outline-color": "<color> | invert",
          "outline-offset": "<len>",
          "outline-style": "<border-style> | auto",
          "outline-width": "<border-width>",
          "overflow": "<overflow>{1,2}",
          "overflow-anchor": "auto | none",
          "overflow-block": "<overflow>",
          "overflow-clip-margin": "visual-box | <len0+>",
          "overflow-inline": "<overflow>",
          "overflow-wrap": "normal | break-word | anywhere",
          "overflow-x": "<overflow>",
          "overflow-y": "<overflow>",
          "overscroll-behavior": "<overscroll>{1,2}",
          "overscroll-behavior-block": "<overscroll>",
          "overscroll-behavior-inline": "<overscroll>",
          "overscroll-behavior-x": "<overscroll>",
          "overscroll-behavior-y": "<overscroll>",
          "padding": "<len-pct0+>{1,4}",
          "padding-block": "<len-pct0+>{1,2}",
          "padding-block-end": "<len-pct0+>",
          "padding-block-start": "<len-pct0+>",
          "padding-bottom": "<len-pct0+>",
          "padding-inline": "<len-pct0+>{1,2}",
          "padding-inline-end": "<len-pct0+>",
          "padding-inline-start": "<len-pct0+>",
          "padding-left": "<len-pct0+>",
          "padding-right": "<len-pct0+>",
          "padding-top": "<len-pct0+>",
          "page": "auto | <custom-ident>",
          "page-break-after": "auto | always | avoid | left | right | recto | verso",
          "page-break-before": "<page-break-after>",
          "page-break-inside": "auto | avoid",
          "paint-order": "normal | [ fill || stroke || markers ]",
          "perspective": "none | <len0+>",
          "perspective-origin": "<position>",
          "place-content": "<align-content> <justify-content>?",
          "place-items": "[ normal | stretch | <baseline-position> | <self-position> ] [ normal | stretch | <baseline-position> | <self-position> ]?",
          "place-self": "<align-self> <justify-self>?",
          "pointer-events": "auto | none | visiblePainted | visibleFill | visibleStroke | visible | painted | fill | stroke | all",
          "position": "static | relative | absolute | fixed | sticky",
          "print-color-adjust": "economy | exact",
          "quotes": 1,
          "r": 1,
          "rx": "<x> | auto",
          "ry": "<rx>",
          "rendering-intent": 1,
          "resize": "none | both | horizontal | vertical | block | inline",
          "right": "<width>",
          "rotate": "none | [ x | y | z | <num>{3} ]? && <angle>",
          "row-gap": "<column-gap>",
          "ruby-align": 1,
          "ruby-position": 1,
          "scale": "none | <num-pct>{1,3}",
          "scroll-behavior": "auto | smooth",
          "scroll-margin": "<len>{1,4}",
          "scroll-margin-bottom": "<len>",
          "scroll-margin-left": "<len>",
          "scroll-margin-right": "<len>",
          "scroll-margin-top": "<len>",
          "scroll-margin-block": "<len>{1,2}",
          "scroll-margin-block-end": "<len>",
          "scroll-margin-block-start": "<len>",
          "scroll-margin-inline": "<len>{1,2}",
          "scroll-margin-inline-end": "<len>",
          "scroll-margin-inline-start": "<len>",
          "scroll-padding": "<width>{1,4}",
          "scroll-padding-left": "<width>",
          "scroll-padding-right": "<width>",
          "scroll-padding-top": "<width>",
          "scroll-padding-bottom": "<width>",
          "scroll-padding-block": "<width>{1,2}",
          "scroll-padding-block-end": "<width>",
          "scroll-padding-block-start": "<width>",
          "scroll-padding-inline": "<width>{1,2}",
          "scroll-padding-inline-end": "<width>",
          "scroll-padding-inline-start": "<width>",
          "scroll-snap-align": "[ none | start | end | center ]{1,2}",
          "scroll-snap-stop": "normal | always",
          "scroll-snap-type": "none | [ x | y | block | inline | both ] [ mandatory | proximity ]?",
          "scrollbar-color": "auto | dark | light | <color>{2}",
          "scrollbar-gutter": "auto | stable && both-edges?",
          "scrollbar-width": "auto | thin | none",
          "shape-image-threshold": "<num-pct>",
          "shape-margin": "<len-pct>",
          "shape-rendering": "auto | optimizeSpeed | crispEdges | geometricPrecision",
          "shape-outside": "none | [ <basic-shape> || <shape-box> ] | <image>",
          "speak": "auto | never | always",
          "stop-color": 1,
          "stop-opacity": "<num0-1>",
          "stroke": "<paint>",
          "stroke-dasharray": "none | <dasharray>",
          "stroke-dashoffset": "<len-pct> | <num>",
          "stroke-linecap": "butt | round | square",
          "stroke-linejoin": "miter | miter-clip | round | bevel | arcs",
          "stroke-miterlimit": "<num0+>",
          "stroke-opacity": "<num0-1>",
          "stroke-width": "<len-pct> | <num>",
          "table-layout": "auto | fixed",
          "tab-size": "<num> | <len>",
          "text-align": "<text-align> | justify-all",
          "text-align-last": "<text-align> | auto",
          "text-anchor": "start | middle | end",
          "text-combine-upright": "none | all | [ digits <int2-4>? ]",
          "text-decoration": "<text-decoration-line> || <text-decoration-style> || <color>",
          "text-decoration-color": "<color>",
          "text-decoration-line": "none | [ underline || overline || line-through || blink ]",
          "text-decoration-skip": "none | auto",
          "text-decoration-skip-ink": "none | auto | all",
          "text-decoration-style": "solid | double | dotted | dashed | wavy",
          "text-decoration-thickness": "auto | from-font | <len-pct>",
          "text-emphasis": "<text-emphasis-style> || <color>",
          "text-emphasis-color": "<color>",
          "text-emphasis-style": "none | <string> | [ [ filled | open ] || [ dot | circle | double-circle | triangle | sesame ] ]",
          "text-emphasis-position": "[ over | under ] && [ right | left ]?",
          "text-indent": "<len-pct> && hanging? && each-line?",
          "text-justify": "auto | none | inter-word | inter-character",
          "text-orientation": "mixed | upright | sideways",
          "text-overflow": "clip | ellipsis",
          "text-rendering": "auto | optimizeSpeed | optimizeLegibility | geometricPrecision",
          "text-shadow": "none | [ <color>? && <len>{2,3} ]#",
          "text-size-adjust": "auto | none | <pct0+>",
          "text-transform": "none | [ capitalize|uppercase|lowercase ] || full-width || full-size-kana",
          "text-underline-offset": "<len-pct> | auto",
          "text-underline-position": "auto | [ under || [ left | right ] ]",
          "text-wrap": "wrap | nowrap | balance | stable | pretty",
          "top": "<width>",
          "touch-action": "auto | none | pan-x | pan-y | pan-left | pan-right | pan-up | pan-down | manipulation",
          "transform": "none | <fn:transform>+",
          "transform-box": "border-box | fill-box | view-box",
          "transform-origin": "[ left | center | right | <len-pct> ] [ top | center | bottom | <len-pct> ] <len>? | [ left | center | right | top | bottom | <len-pct> ] | [ [ center | left | right ] && [ center | top | bottom ] ] <len>?",
          "transform-style": "flat | preserve-3d",
          "transition": "[ [ none | [ all | <custom-ident> ]# ] || <time> || <timing-function> || <time> ]#",
          "transition-delay": "<time>#",
          "transition-duration": "<time>#",
          "transition-property": "none | [ all | <custom-ident> ]#",
          "transition-timing-function": "<timing-function>#",
          "translate": "none | <len-pct> [ <len-pct> <len>? ]?",
          "unicode-range": "<unicode-range>#",
          "unicode-bidi": "normal | embed | isolate | bidi-override | isolate-override | plaintext",
          "user-select": "auto | text | none | contain | all",
          "vertical-align": "auto | use-script | baseline | sub | super | top | text-top | central | middle | bottom | text-bottom | <len-pct>",
          "visibility": "<vis-hid> | collapse",
          "white-space": "normal | pre | nowrap | pre-wrap | break-spaces | pre-line",
          "widows": "<int>",
          "width": "auto | <width-height>",
          "will-change": "auto | <animateable-feature>#",
          "word-break": "normal | keep-all | break-all | break-word",
          "word-spacing": "<len> | normal",
          "word-wrap": "normal | break-word | anywhere",
          "writing-mode": "horizontal-tb | vertical-rl | vertical-lr | lr-tb | rl-tb | tb-rl | bt-rl | tb-lr | bt-lr | lr-bt | rl-bt | lr | rl | tb",
          "x": "<len-pct> | <num>",
          "y": "<x>",
          "z-index": "<int> | auto",
          "zoom": "<num> | <pct> | normal",
          "-webkit-box-reflect": "[ above | below | right | left ]? <len>? <image>?",
          "-webkit-text-fill-color": "<color>",
          "-webkit-text-stroke": "<border-width> || <color>",
          "-webkit-text-stroke-color": "<color>",
          "-webkit-text-stroke-width": "<border-width>",
          "-webkit-user-modify": "read-only | read-write | write-only"
        };
        const isOwn = Object.call.bind({}.hasOwnProperty);
        const pick = (obj, keys, dst = {}) => keys.reduce((res, k) => (res[k] = obj[k], res), dst);
        const ScopedProperties = {
          __proto__: null,
          "counter-style": {
            "additive-symbols": "<pad>#",
            "fallback": "<ident-not-none>",
            "negative": "<prefix>{1,2}",
            "pad": "<int0+> && <prefix>",
            "prefix": "<string> | <image> | <custom-ident>",
            "range": "[ [ <int> | infinite ]{2} ]# | auto",
            "speak-as": "auto | bullets | numbers | words | spell-out | <ident-not-none>",
            "suffix": "<prefix>",
            "symbols": "<prefix>+",
            "system": "cyclic | numeric | alphabetic | symbolic | additive | [fixed <int>?] | [ extends <ident-not-none> ]"
          },
          "font-face": pick(Properties, [
            "font-family",
            "font-size",
            "font-variant",
            "font-variation-settings",
            "unicode-range"
          ], {
            "ascent-override": "[ normal | <pct0+> ]{1,2}",
            "descent-override": "[ normal | <pct0+> ]{1,2}",
            "font-display": "auto | block | swap | fallback | optional",
            "font-stretch": "auto | <font-stretch>{1,2}",
            "font-style": "auto | normal | italic | oblique <angle>{0,2}",
            "font-weight": "auto | [ normal | bold | <num1-1000> ]{1,2}",
            "line-gap-override": "[ normal | <pct0+> ]{1,2}",
            "size-adjust": "<pct0+>",
            "src": "[ url() [ format( <string># ) ]? | local( <family-name> ) ]#"
          }),
          "font-palette-values": pick(Properties, ["font-family"], {
            "base-palette": "light | dark | <int0+>",
            "override-colors": "[ <int0+> <color> ]#"
          }),
          "media": {
            "<all>": true,
            "any-hover": "none | hover",
            "any-pointer": "none | coarse | fine",
            "color": "<int>",
            "color-gamut": "srgb | p3 | rec2020",
            "color-index": "<int>",
            "grid": "<int0-1>",
            "hover": "none | hover",
            "monochrome": "<int>",
            "overflow-block": "none | scroll | paged",
            "overflow-inline": "none | scroll",
            "pointer": "none | coarse | fine",
            "resolution": "<resolution> | infinite",
            "scan": "interlace | progressive",
            "update": "none | slow | fast",
            "device-aspect-ratio": "<ratio>",
            "device-height": "<len>",
            "device-width": "<len>"
          },
          "page": {
            "<all>": true,
            "bleed": "auto | <len>",
            "marks": "none | [ crop || cross ]",
            "size": "<len>{1,2} | auto | [ [ A3 | A4 | A5 | B4 | B5 | JIS-B4 | JIS-B5 | ledger | legal | letter ] || [ portrait | landscape ] ]"
          },
          "property": {
            "inherits": "true | false",
            "initial-value": 1,
            "syntax": "<string>"
          }
        };
        for (const [k, reps] of Object.entries({
          "border": "{1,4}",
          "border-bottom": "",
          "border-left": "",
          "border-right": "",
          "border-top": "",
          "border-block": "{1,2}",
          "border-block-end": "",
          "border-block-start": "",
          "border-inline": "{1,2}",
          "border-inline-end": "",
          "border-inline-start": ""
        })) {
          Properties[k] = "<border-shorthand>";
          Properties[`${k}-color`] = "<color>" + reps;
          Properties[`${k}-style`] = "<border-style>" + reps;
          Properties[`${k}-width`] = "<border-width>" + reps;
        }
        for (const k of ["width", "height", "block-size", "inline-size"]) {
          Properties[`contain-intrinsic-${k}`] = "<contain-intrinsic>";
        }
        const Tokens = {
          __proto__: null,
          EOF: {},
          AMP: "&",
          AT: {},
          ATTR_EQ: ["|=", "~=", "^=", "*=", "$="],
          CDCO: {},
          CHAR: {},
          COLON: ":",
          COMBINATOR: ["~", "||"],
          COMMA: ",",
          COMMENT: {},
          DELIM: "!",
          DOT: ".",
          EQUALS: "=",
          EQ_CMP: [">=", "<="],
          FUNCTION: {},
          GT: ">",
          HASH: "#",
          IDENT: {},
          INVALID: {},
          LBRACE: "{",
          LBRACKET: "[",
          LPAREN: "(",
          MINUS: "-",
          PIPE: "|",
          PLUS: "+",
          RBRACE: "}",
          RBRACKET: "]",
          RPAREN: ")",
          SEMICOLON: ";",
          STAR: "*",
          STRING: {},
          URANGE: {},
          URI: {},
          UVAR: {},
          WS: {},
          ANGLE: {},
          DIMENSION: {},
          FLEX: {},
          FREQUENCY: {},
          LENGTH: {},
          NUMBER: {},
          PCT: {},
          RESOLUTION: {},
          TIME: {}
        };
        const TokenIdByCode = [];
        for (let id = 0, arr = Object.keys(Tokens), key, val, text; key = arr[id]; id++) {
          text = ((val = Tokens[key]).slice ? val = { text: val } : val).text;
          Tokens[val.name = key] = id;
          Tokens[id] = val;
          if (text) {
            for (const str of typeof text === "string" ? [text] : text) {
              if (str.length === 1)
                TokenIdByCode[str.charCodeAt(0)] = id;
            }
          }
        }
        const { ANGLE, IDENT, LENGTH, NUMBER, PCT, STRING, TIME } = Tokens;
        const Units = { __proto__: null };
        const UnitTypeIds = { __proto__: null };
        for (const [id, units] of [
          [ANGLE, "deg,grad,rad,turn"],
          [Tokens.FLEX, "fr"],
          [Tokens.FREQUENCY, "hz,khz"],
          [LENGTH, "cap,ch,em,ex,ic,lh,rlh,rem,cm,mm,in,pc,pt,px,q,cqw,cqh,cqi,cqb,cqmin,cqmax,vb,vi,vh,vw,vmin,vmaxdvb,dvi,dvh,dvw,dvmin,dvmaxlvb,lvi,lvh,lvw,lvmin,lvmaxsvb,svi,svh,svw,svmin,svmax"],
          [Tokens.RESOLUTION, "dpcm,dpi,dppx,x"],
          [TIME, "ms,s"]
        ]) {
          const type = Tokens[id].name.toLowerCase();
          for (const u of units.split(","))
            Units[u] = type;
          UnitTypeIds[type] = id;
        }
        const Combinators = [];
        Combinators[9] = Combinators[10] = Combinators[12] = Combinators[13] = Combinators[32] = "descendant";
        Combinators[62] = "child";
        Combinators[43] = "adjacent-sibling";
        Combinators[126] = "sibling";
        Combinators[124] = "column";
        class Bucket {
          constructor(src) {
            this.addFrom(src);
          }
          addFrom(src) {
            for (let str of typeof src === "string" ? [src] : src) {
              let c = (str = str.toLowerCase()).charCodeAt(0);
              if (c === 34)
                c = (str = str.slice(1, -1)).charCodeAt(0);
              src = this[c = c * 100 + str.length];
              if (src == null)
                this[c] = str;
              else if (typeof src === "string")
                this[c] = [src, str];
              else
                src.push(str);
            }
            return this;
          }
          join(sep) {
            let res = "";
            for (const v of Object.values(this)) {
              res += `${res ? sep : ""}${typeof v === "string" ? v : v.join(sep)}`;
            }
            return res;
          }
          has(tok, c = tok.code, lowText) {
            const len = (lowText || tok).length;
            if (!isOwn(this, c = c * 100 + len))
              return false;
            if (len === 1)
              return true;
            const val = this[c];
            const low = lowText || tok.lowText || (tok.lowText = tok.text.toLowerCase());
            return typeof val === "string" ? val === low : val.includes(low);
          }
        }
        const NamedColors = "currentColor,transparent,aliceblue,antiquewhite,aqua,aquamarine,azure,beige,bisque,black,blanchedalmond,blue,blueviolet,brown,burlywood,cadetblue,chartreuse,chocolate,coral,cornflowerblue,cornsilk,crimson,cyan,darkblue,darkcyan,darkgoldenrod,darkgray,darkgrey,darkgreen,darkkhaki,darkmagenta,darkolivegreen,darkorange,darkorchid,darkred,darksalmon,darkseagreen,darkslateblue,darkslategray,darkslategrey,darkturquoise,darkviolet,deeppink,deepskyblue,dimgray,dimgrey,dodgerblue,firebrick,floralwhite,forestgreen,fuchsia,gainsboro,ghostwhite,gold,goldenrod,gray,grey,green,greenyellow,honeydew,hotpink,indianred,indigo,ivory,khaki,lavender,lavenderblush,lawngreen,lemonchiffon,lightblue,lightcoral,lightcyan,lightgoldenrodyellow,lightgray,lightgrey,lightgreen,lightpink,lightsalmon,lightseagreen,lightskyblue,lightslategray,lightslategrey,lightsteelblue,lightyellow,lime,limegreen,linen,magenta,maroon,mediumaquamarine,mediumblue,mediumorchid,mediumpurple,mediumseagreen,mediumslateblue,mediumspringgreen,mediumturquoise,mediumvioletred,midnightblue,mintcream,mistyrose,moccasin,navajowhite,navy,oldlace,olive,olivedrab,orange,orangered,orchid,palegoldenrod,palegreen,paleturquoise,palevioletred,papayawhip,peachpuff,peru,pink,plum,powderblue,purple,rebeccapurple,red,rosybrown,royalblue,saddlebrown,salmon,sandybrown,seagreen,seashell,sienna,silver,skyblue,slateblue,slategray,slategrey,snow,springgreen,steelblue,tan,teal,thistle,tomato,turquoise,violet,wheat,white,whitesmoke,yellow,yellowgreen,ActiveBorder,ActiveCaption,ActiveText,AppWorkspace,Background,ButtonBorder,ButtonFace,ButtonHighlight,ButtonShadow,ButtonText,Canvas,CanvasText,CaptionText,Field,FieldText,GrayText,Highlight,HighlightText,InactiveBorder,InactiveCaption,InactiveCaptionText,InfoBackground,InfoText,LinkText,Mark,MarkText,Menu,MenuText,Scrollbar,ThreeDDarkShadow,ThreeDFace,ThreeDHighlight,ThreeDLightShadow,ThreeDShadow,VisitedText,Window,WindowFrame,WindowText".split(",");
        const buAlpha = new Bucket("alpha");
        const buGlobalKeywords = new Bucket(GlobalKeywords);
        const rxAltSep = /\s*\|\s*/;
        const VTComplex = {
          __proto__: null,
          "<absolute-size>": "xx-small | x-small | small | medium | large | x-large | xx-large",
          "<alpha>": "/ <num-pct-none>",
          "<animateable-feature>": "scroll-position | contents | <animateable-feature-name>",
          "<animation-direction>": "normal | reverse | alternate | alternate-reverse",
          "<animation-fill-mode>": "none | forwards | backwards | both",
          "<attachment>": "scroll | fixed | local",
          "<auto-repeat>": "repeat( [ auto-fill | auto-fit ] , [ <line-names>? <fixed-size> ]+ <line-names>? )",
          "<auto-track-list>": "[ <line-names>? [ <fixed-size> | <fixed-repeat> ] ]* <line-names>? <auto-repeat> [ <line-names>? [ <fixed-size> | <fixed-repeat> ] ]* <line-names>?",
          "<baseline-position>": "[ first | last ]? baseline",
          "<basic-shape>": "<inset> | circle( <len-pct-side>? [ at <position> ]? ) | ellipse( [ <len-pct-side>{2} ]? [ at <position> ]? ) | path( [ [ nonzero | evenodd ] , ]? <string> ) | polygon( [ [ nonzero | evenodd | inherit ] , ]? [ <len-pct> <len-pct> ]# )",
          "<bg-image>": "<image> | none",
          "<bg-layer>": "<bg-image> || <bg-position> [ / <bg-size> ]? || <repeat-style> || <attachment> || <box>{1,2}",
          "<bg-position>": "[ center | [ left | right ] <len-pct>? ] && [ center | [ top | bottom ] <len-pct>? ] | [ left | center | right | <len-pct> ] [ top | center | bottom | <len-pct> ] | [ left | center | right | top | bottom | <len-pct> ]",
          "<bg-size>": "[ <len-pct> | auto ]{1,2} | cover | contain",
          "<blend-mode>": "normal | multiply | screen | overlay | darken | lighten | color-dodge | color-burn | hard-light | soft-light | difference | exclusion | hue | saturation | color | luminosity | plus-darker | plus-lighter",
          "<border-image-slice>": (M) => M.many([true], ["<num-pct0+>", "<num-pct0+>", "<num-pct0+>", "<num-pct0+>", "fill"].map(M.term)),
          "<border-radius-round>": "round <border-radius>",
          "<border-shorthand>": "<border-width> || <border-style> || <color>",
          "<border-style>": "none | hidden | dotted | dashed | solid | double | groove | ridge | inset | outset",
          "<border-width>": "<len> | thin | medium | thick",
          "<box>": "padding-box | border-box | content-box",
          "<box-fsv>": "fill-box | stroke-box | view-box",
          "<color>": "<named-or-hex-color> | <fn:color>",
          "<coord-box>": "<box> | <box-fsv>",
          "<contain-intrinsic>": "none | <len> | auto <len>",
          "<content-distribution>": "space-between | space-around | space-evenly | stretch",
          "<content-list>": "[ <string> | <image> | <attr> | content( text | before | after | first-letter | marker ) | counter() | counters() | leader() | open-quote | close-quote | no-open-quote | no-close-quote | target-counter() | target-counters() | target-text() ]+",
          "<content-position>": "center | start | end | flex-start | flex-end",
          "<counter>": "[ <ident-not-none> <int>? ]+ | none",
          "<dasharray>": (M) => M.alt([M.term("<len-pct0+>"), M.term("<num0+>")]).braces(1, Infinity, "#", M.term(",").braces(0, 1, "?")),
          "<display-box>": "contents | none",
          "<display-inside>": "flow | flow-root | table | flex | grid | ruby",
          "<display-internal>": "table-row-group | table-header-group | table-footer-group | table-row | table-cell | table-column-group | table-column | table-caption | ruby-base | ruby-text | ruby-base-container | ruby-text-container",
          "<display-legacy>": "inline-block | inline-table | inline-flex | inline-grid",
          "<display-listitem>": "<display-outside>? && [ flow | flow-root ]? && list-item",
          "<display-outside>": "block | inline | run-in",
          "<explicit-track-list>": "[ <line-names>? <track-size> ]+ <line-names>?",
          "<family-name>": "<string> | <custom-ident>+",
          "<filter-function-list>": "[ <fn:filter> | <uri> ]+",
          "<final-bg-layer>": "<color> || <bg-image> || <bg-position> [ / <bg-size> ]? || <repeat-style> || <attachment> || <box>{1,2}",
          "<fixed-repeat>": "repeat( [ <int1+> ] , [ <line-names>? <fixed-size> ]+ <line-names>? )",
          "<fixed-size>": "<len-pct> | minmax( <len-pct> , <track-breadth> | <inflexible-breadth> , <len-pct> )",
          "<flex-direction>": "row | row-reverse | column | column-reverse",
          "<flex-shorthand>": "none | [ <num>{1,2} || <width> ]",
          "<flex-wrap>": "nowrap | wrap | wrap-reverse",
          "<font-short-core>": "<font-size> [ / <line-height> ]? <font-family>",
          "<font-short-tweak-no-pct>": "<font-style> || [ normal | small-caps ] || <font-weight> || <font-stretch-named>",
          "<font-stretch-named>": "normal | ultra-condensed | extra-condensed | condensed | semi-condensed | semi-expanded | expanded | extra-expanded | ultra-expanded",
          "<font-variant-alternates>": "stylistic() || historical-forms || styleset() || character-variant() || swash() || ornaments() || annotation()",
          "<font-variant-caps>": "small-caps | all-small-caps | petite-caps | all-petite-caps | unicase | titling-caps",
          "<font-variant-east-asian>": "[ jis78|jis83|jis90|jis04|simplified|traditional ] || [ full-width | proportional-width ] || ruby",
          "<font-variant-ligatures>": "[ common-ligatures | no-common-ligatures ] || [ discretionary-ligatures | no-discretionary-ligatures ] || [ historical-ligatures | no-historical-ligatures ] || [ contextual | no-contextual ]",
          "<font-variant-numeric>": "[ lining-nums | oldstyle-nums ] || [ proportional-nums | tabular-nums ] || [ diagonal-fractions | stacked-fractions ] || ordinal || slashed-zero",
          "<generic-family>": "serif | sans-serif | cursive | fantasy | monospace | system-ui | emoji | math | fangsong | ui-serif | ui-sans-serif | ui-monospace | ui-rounded",
          "<geometry-box>": "<shape-box> | <box-fsv>",
          "<gradient>": "radial-gradient() | linear-gradient() | conic-gradient() | gradient() | repeating-radial-gradient() | repeating-linear-gradient() | repeating-conic-gradient() | repeating-gradient()",
          "<grid-line>": "auto | [ <int> && <ident-for-grid>? ] | <ident-for-grid> | [ span && [ <int> || <ident-for-grid> ] ]",
          "<image>": "<uri> | <gradient> | -webkit-cross-fade()",
          "<inflexible-breadth>": "<len-pct> | min-content | max-content | auto",
          "<inset>": "inset( <len-pct>{1,4} <border-radius-round>? )",
          "<len-pct-side>": "<len-pct> | closest-side | farthest-side",
          "<line-height>": "<num> | <len-pct> | normal",
          "<line-names>": '"[" <ident-for-grid> "]"',
          "<overflow-position>": "unsafe | safe",
          "<overflow>": "<vis-hid> | clip | scroll | auto | overlay",
          "<overscroll>": "contain | none | auto",
          "<paint>": "none | <color> | <uri> [ none | <color> ]? | context-fill | context-stroke",
          "<position>": "[ [ left | right ] <len-pct> ] && [ [ top | bottom ] <len-pct> ] | [ left | center | right | <len-pct> ] [ top | center | bottom | <len-pct> ]? | [ left | center | right ] || [ top | center | bottom ]",
          "<ratio>": "<num0+> [ / <num0+> ]?",
          "<rect>": "rect( [ <len> | auto ]#{4} <border-radius-round>? )",
          "<relative-size>": "smaller | larger",
          "<repeat-style>": "repeat-x | repeat-y | [ repeat | space | round | no-repeat ]{1,2}",
          "<rgb-xyz>": "srgb|srgb-linear|display-p3|a98-rgb|prophoto-rgb|rec2020|xyz|xyz-d50|xyz-d65",
          "<self-position>": "center | start | end | self-start | self-end | flex-start | flex-end",
          "<shadow>": "inset? && [ <len>{2,4} && <color>? ]",
          "<shape-box>": "<box> | margin-box",
          "<timing-function>": "linear|ease|ease-in|ease-out|ease-in-out|step-start|step-end | cubic-bezier( <num0-1> , <num> , <num0-1> , <num> ) | steps( <int> [ , [ jump-start | jump-end | jump-none | jump-both | start | end ] ]? )",
          "<text-align>": "start | end | left | right | center | justify | match-parent",
          "<track-breadth>": "<len-pct> | <flex> | min-content | max-content | auto",
          "<track-list>": "[ <line-names>? [ <track-size> | <track-repeat> ] ]+ <line-names>?",
          "<track-repeat>": "repeat( [ <int1+> ] , [ <line-names>? <track-size> ]+ <line-names>? )",
          "<track-size>": "<track-breadth> | minmax( <inflexible-breadth> , <track-breadth> ) | fit-content( <len-pct> )",
          "<vis-hid>": "visible | hidden",
          "<width-height>": "<len-pct> | min-content | max-content | fit-content | -moz-available | -webkit-fill-available | fit-content( <len-pct> )",
          "<xywh>": "xywh( <len-pct>{2} <len-pct0+>{2} <border-radius-round>? )"
        };
        const VTFunctions = {
          color: {
            __proto__: null,
            "color-mix": "in [ srgb | srgb-linear | lab | oklab | xyz | xyz-d50 | xyz-d65 | [ hsl | hwb | lch | oklch ] [ [ shorter | longer | increasing | decreasing ] hue ]? ] , [ <color> && <pct0-100>? ]#{2}",
            "color": "from <color> [ <custom-prop> [ <num-pct-none> <custom-ident> ]# | <rgb-xyz> [ <num-pct-none> | r | g | b | x | y | z ]{3} ] [ / <num-pct-none> | r | g | b | x | y | z ]? | [ <rgb-xyz> <num-pct-none>{3} | <custom-prop> <num-pct-none># ] <alpha>?",
            "hsl": "<hue> , <pct>#{2} [ , <num-pct0+> ]? | [ <hue> | none ] <num-pct-none>{2} <alpha>? | from <color> [ <hue> | <rel-hsl> ] <rel-hsl-num-pct>{2} [ / <rel-hsl-num-pct> ]?",
            "hwb": "[ <hue> | none ] <num-pct-none>{2} <alpha>? | from <color> [ <hue> | <rel-hwb> ] <rel-hwb-num-pct>{2} [ / <rel-hwb-num-pct> ]?",
            "lab": "<num-pct-none>{3} <alpha>? | from <color> <rel-lab-num-pct>{3} [ / <rel-lab-num-pct> ]?",
            "lch": "<num-pct-none>{2} [ <hue> | none ] <alpha>? | from <color> <rel-lch-num-pct>{2} [ <hue> | <rel-lch> ] [ / <rel-lch-num-pct> ]?",
            "rgb": "[ <num>#{3} | <pct>#{3} ] [ , <num-pct0+> ]? | <num-pct-none>{3} <alpha>? | from <color> <rel-rgb-num-pct>{3} [ / <rel-rgb-num-pct> ]?"
          },
          filter: {
            __proto__: null,
            "blur": "<len>?",
            "brightness": "<num-pct>?",
            "contrast": "<num-pct>?",
            "drop-shadow": "[ <len>{2,3} && <color>? ]?",
            "grayscale": "<num-pct>?",
            "hue-rotate": "<angle-or-0>?",
            "invert": "<num-pct>?",
            "opacity": "<num-pct>?",
            "saturate": "<num-pct>?",
            "sepia": "<num-pct>?"
          },
          transform: {
            __proto__: null,
            matrix: "<num>#{6}",
            matrix3d: "<num>#{16}",
            perspective: "<len0+> | none",
            rotate: "<angle-or-0> | none",
            rotate3d: "<num>#{3} , <angle-or-0>",
            rotateX: "<angle-or-0>",
            rotateY: "<angle-or-0>",
            rotateZ: "<angle-or-0>",
            scale: "[ <num-pct> ]#{1,2} | none",
            scale3d: "<num-pct>#{3}",
            scaleX: "<num-pct>",
            scaleY: "<num-pct>",
            scaleZ: "<num-pct>",
            skew: "<angle-or-0> [ , <angle-or-0> ]?",
            skewX: "<angle-or-0>",
            skewY: "<angle-or-0>",
            translate: "<len-pct>#{1,2} | none",
            translate3d: "<len-pct>#{2} , <len>",
            translateX: "<len-pct>",
            translateY: "<len-pct>",
            translateZ: "<len>"
          }
        };
        {
          let obj = VTFunctions.color;
          for (const k of ["hsl", "rgb"])
            obj[k + "a"] = obj[k];
          for (const k of ["lab", "lch"])
            obj["ok" + k] = obj[k];
          obj = VTFunctions.transform;
          for (const key in obj) {
            const low = key.toLowerCase();
            if (low !== key)
              Object.defineProperty(obj, low, { value: obj[key], writable: true });
          }
        }
        const VTSimple = {
          __proto__: null,
          "<animateable-feature-name>": customIdentChecker("will-change,auto,scroll-position,contents"),
          "<angle>": (p) => p.isCalc || p.id === ANGLE,
          "<angle-or-0>": (p) => p.isCalc || p.is0 || p.id === ANGLE,
          "<ascii4>": (p) => p.id === STRING && p.length === 4 && !/[^\x20-\x7E]/.test(p.text),
          "<attr>": (p) => p.isAttr,
          "<custom-ident>": (p) => p.id === IDENT && !buGlobalKeywords.has(p),
          "<custom-prop>": (p) => p.type === "custom-prop",
          "<flex>": (p) => p.isCalc || p.units === "fr" && p.number >= 0,
          "<hue>": (p) => p.isCalc || p.id === NUMBER || p.id === ANGLE,
          "<ident-for-grid>": customIdentChecker("span,auto"),
          "<ident-not-none>": (p) => p.id === IDENT && !p.isNone,
          "<ie-function>": (p) => p.ie,
          "<int>": (p) => p.isCalc || p.isInt,
          "<int0-1>": (p) => p.isCalc || p.is0 || p.isInt && p.number === 1,
          "<int0+>": (p) => p.isCalc || p.isInt && p.number >= 0,
          "<int1+>": (p) => p.isCalc || p.isInt && p.number > 0,
          "<int2-4>": (p) => p.isCalc || p.isInt && (p = p.number) >= 2 && p <= 4,
          "<len>": (p) => p.isCalc || p.is0 || p.id === LENGTH,
          "<len0+>": (p) => p.isCalc || p.is0 || p.id === LENGTH && p.number >= 0,
          "<len-pct>": (p) => p.isCalc || p.is0 || p.id === LENGTH || p.id === PCT,
          "<len-pct0+>": (p) => p.isCalc || p.is0 || p.number >= 0 && (p.id === PCT || p.id === LENGTH),
          "<named-or-hex-color>": (p) => p.type === "color",
          "<num>": (p) => p.isCalc || p.id === NUMBER,
          "<num0+>": (p) => p.isCalc || p.id === NUMBER && p.number >= 0,
          "<num0-1>": (p) => p.isCalc || p.id === NUMBER && (p = p.number) >= 0 && p <= 1,
          "<num1-1000>": (p) => p.isCalc || p.id === NUMBER && (p = p.number) >= 1 && p <= 1e3,
          "<num-pct>": (p) => p.isCalc || p.id === NUMBER || p.id === PCT,
          "<num-pct0+>": (p) => p.isCalc || p.number >= 0 && (p.id === NUMBER || p.id === PCT),
          "<num-pct-none>": (p) => p.isCalc || p.isNone || p.id === NUMBER || p.id === PCT,
          "<pct>": (p) => p.isCalc || p.is0 || p.id === PCT,
          "<pct0+>": (p) => p.isCalc || p.is0 || p.number >= 0 && p.id === PCT,
          "<pct0-100>": (p) => p.isCalc || p.is0 || p.id === PCT && (p = p.number) >= 0 && p <= 100,
          "<keyframes-name>": customIdentChecker("", (p) => p.id === STRING),
          "<resolution>": (p) => p.id === Tokens.RESOLUTION,
          "<string>": (p) => p.id === STRING,
          "<time>": (p) => p.isCalc || p.id === TIME,
          "<unicode-range>": (p) => p.id === Tokens.URANGE,
          "<uri>": (p) => p.uri != null,
          "<width>": (p) => p.isAuto || p.isCalc || p.is0 || p.id === LENGTH || p.id === PCT
        };
        for (const type of ["hsl", "hwb", "lab", "lch", "rgb"]) {
          const letters = {};
          for (let i = 0; i < type.length; )
            letters[type.charCodeAt(i++)] = 1;
          VTSimple[`<rel-${type}>`] = (p) => p.isNone || (p.length === 1 ? isOwn(letters, p.code) : p.length === 5 && buAlpha.has(p));
          VTSimple[`<rel-${type}-num-pct>`] = (p) => p.isNone || p.isCalc || p.id === NUMBER || p.id === PCT || (p.length === 1 ? isOwn(letters, p.code) : p.length === 5 && buAlpha.has(p));
        }
        class StringSource {
          constructor(text) {
            this._break = (this.string = text.replace(/\r\n?|\f/g, "\n")).indexOf("\n");
            this.line = 1;
            this.col = 1;
            this.offset = 0;
          }
          eof() {
            return this.offset >= this.string.length;
          }
          peek(distance = 1) {
            return this.string.charCodeAt(this.offset + distance - 1);
          }
          mark() {
            this._bookmark = { o: this.offset, l: this.line, c: this.col, b: this._break };
          }
          reset() {
            const b = this._bookmark;
            if (b) {
              ({ o: this.offset, l: this.line, c: this.col, b: this._break } = b);
              this._bookmark = null;
            }
          }
          readMatch(m, asRe) {
            const res = (m.lastIndex = this.offset, m.exec(this.string));
            if (res)
              return (m = res[0]) && this.read(m.length, m) && (asRe ? res : m);
          }
          readMatchCode(code) {
            if (code === this.string.charCodeAt(this.offset)) {
              return this.read();
            }
          }
          readMatchStr(m) {
            const len = m.length;
            const { offset: i, string: str } = this;
            if (!len || str.charCodeAt(i) === m.charCodeAt(0) && (len === 1 || str.charCodeAt(i + len - 1) === m.charCodeAt(len - 1) && str.substr(i, len) === m)) {
              return m && this.read(len, m);
            }
          }
          read(count = 1, text) {
            let { offset: i, _break: br, string } = this;
            if (count <= 0 || text == null && !(text = string.substr(i, count)))
              return "";
            this.offset = i += count = text.length;
            if (i <= br || br < 0) {
              this.col += count;
            } else {
              let brPrev;
              let { line } = this;
              do
                ++line;
              while ((br = string.indexOf("\n", (brPrev = br) + 1)) >= 0 && br < i);
              this._break = br;
              this.line = line;
              this.col = i - brPrev;
            }
            return text;
          }
          readCode() {
            const c = this.string.charCodeAt(this.offset++);
            if (c === 10) {
              this.col = 1;
              this.line++;
              this._break = this.string.indexOf("\n", this.offset);
            } else if (c >= 0) {
              this.col++;
            } else {
              this.offset--;
              return;
            }
            return c;
          }
        }
        class EventTarget {
          constructor() {
            this._listeners = { __proto__: null };
          }
          addListener(type, fn) {
            (this._listeners[type] || (this._listeners[type] = /* @__PURE__ */ new Set())).add(fn);
          }
          fire(event) {
            const type = typeof event === "object" && event.type;
            const list = this._listeners[type || event];
            if (!list)
              return;
            if (!type)
              event = { type };
            list.forEach((fn) => fn(event));
          }
          removeListener(type, fn) {
            const list = this._listeners[type];
            if (list)
              list.delete(fn);
          }
        }
        const rxAndAndSep = /\s*&&\s*/y;
        const rxBraces = /{\s*(\d+)\s*(?:(,)\s*(?:(\d+)\s*)?)?}/y;
        const rxFuncBegin = /([-\w]+)\(\s*(\))?/y;
        const rxFuncEnd = /\s*\)/y;
        const rxGroupBegin = /\[\s*/y;
        const rxGroupEnd = /\s*]/y;
        const rxOrOrSep = /\s*\|\|\s*/y;
        const rxOrSep = /\s*\|(?!\|)\s*/y;
        const rxPlainTextAlt = /[-\w]+(?:\s*\|\s*[-\w]+)*(?=\s*\|(?!\|)\s*|\s*]|\s+\)|\s*$)/y;
        const rxSeqSep = /\s+(?![&|)\]])/y;
        const rxTerm = /<[^>\s]+>|"[^"]*"|'[^']*'|[^\s?*+#{}()[\]|&]+/y;
        class Matcher {
          constructor(matchFunc, toString, arg, isMeta) {
            this.matchFunc = matchFunc;
            if (arg != null)
              this.arg = arg;
            if (isMeta)
              this.isMeta = isMeta;
            if (toString.call)
              this.toString = toString;
            else
              this._string = toString;
          }
          match(expr, p) {
            const { i } = expr;
            if (!p && !(p = expr.parts[i]))
              return this.arg.min === 0;
            const isMeta = this.isMeta;
            const res = !isMeta && p.isVar || this.matchFunc(expr, p) || !isMeta && expr.tryAttr && p.isAttr;
            if (!res)
              expr.i = i;
            else if (!isMeta && expr.i < expr.parts.length)
              ++expr.i;
            return res;
          }
          toString() {
            return this._string;
          }
          static alt(ms) {
            let str;
            for (let SAT = Matcher.stringArrTest, m, i = 0; m = ms[i]; ) {
              if (m.matchFunc === SAT) {
                str = (str ? str + " | " : "") + m._string;
                ms.splice(i, 1);
              } else
                i++;
            }
            if (str)
              ms.unshift(Matcher.term(str));
            return !ms[1] ? ms[0] : new Matcher(Matcher.altTest, Matcher.altToStr, ms, true);
          }
          static altTest(expr, p) {
            for (let i = 0, m; m = this.arg[i++]; ) {
              if (m.match(expr, p))
                return true;
            }
          }
          static altToStr(prec) {
            return (prec = prec > Matcher.ALT ? "[ " : "") + this.arg.map((m) => m.toString(Matcher.ALT)).join(" | ") + (prec ? " ]" : "");
          }
          braces(min, max, marker, sep) {
            return new Matcher(Matcher.bracesTest, Matcher.bracesToStr, {
              m: this,
              min,
              max,
              marker,
              sep: sep && Matcher.seq([sep.matchFunc ? sep : Matcher.term(sep), this])
            }, true);
          }
          static bracesTest(expr, p) {
            let i = 0;
            const { min, max, sep, m } = this.arg;
            while (i < max && (i && sep || m).match(expr, p)) {
              p = void 0;
              i++;
            }
            return i >= min && (i || true);
          }
          static bracesToStr() {
            const { marker, min, max, m } = this.arg;
            return m.toString(Matcher.MOD) + (marker || "") + (!marker || marker === "#" && !(min === 1 || max === Infinity) ? `{${min}${min === max ? "" : `,${max === Infinity ? "" : max}`}}` : "");
          }
          static funcTest(expr, p) {
            const pn = p.name;
            if (!pn)
              return;
            const pnv = (p.prefix || "") + pn;
            const { name, body, list } = this.arg;
            const m = list ? list[pn] || list[pnv] : name === pn || name === pnv ? body || "" : null;
            if (m == null)
              return;
            const e = p.expr;
            if (!e && m)
              return;
            const vi = m && !e.isVar && new PropValueIterator(e);
            const mm = !vi || m.matchFunc ? m : list[pn] = m.call ? m(Matcher) : Matcher.cache[m] || Matcher.parse(m);
            return !vi || mm.match(vi) && vi.i >= vi.parts.length || !(expr.badFunc = [e, mm]);
          }
          static funcToStr(prec) {
            const { name, body, list } = this.arg;
            return name ? `${name}(${body || ""})` : (prec = prec > Matcher.ALT ? "[ " : "") + Object.keys(list).join("() | ") + (prec ? "() ]" : "()");
          }
          static many(req, ms) {
            if (!ms[1])
              return ms[0];
            const m = new Matcher(Matcher.manyTest, Matcher.manyToStr, ms, true);
            m.req = req === true ? Array(ms.length).fill(true) : req == null ? ms.map((m2) => !m2.arg || m2.arg.marker !== "?") : req;
            return m;
          }
          static manyTest(expr) {
            const state = [];
            state.expr = expr;
            state.max = 0;
            if (!this.manyTestRun(state, 0))
              this.manyTestRun(state, 0, true);
            if (!this.req)
              return state.max > 0;
            for (let i = 0; i < this.req.length; i++) {
              if (this.req[i] && !state[i])
                return false;
            }
            return true;
          }
          manyTestRun(state, count, retry) {
            for (let i = 0, { expr } = state, ms = this.arg, m, ei, x; m = ms[i]; i++) {
              if (!state[i] && ((ei = expr.i) + 1 > expr.parts.length || (x = m.match(expr)) && (x > 1 || x === 1 || m.arg.min !== 0))) {
                state[i] = true;
                if (this.manyTestRun(state, count + (!this.req || this.req[i] ? 1 : 0), retry)) {
                  return true;
                }
                state[i] = false;
                expr.i = ei;
              }
            }
            if (retry)
              return count === state.max;
            state.max = Math.max(state.max, count);
            return count === this.arg.length;
          }
          static manyToStr(prec) {
            const { req } = this;
            const p = Matcher[req ? "ANDAND" : "OROR"];
            const s = this.arg.map((m, i) => !req || req[i] ? m.toString(p) : m.toString(Matcher.MOD).replace(/[^?]$/, "$&?")).join(req ? " && " : " || ");
            return prec > p ? `[ ${s} ]` : s;
          }
          static parse(str) {
            const source = new StringSource(str);
            const res = Matcher.parseAlt(source);
            if (!source.eof()) {
              const { offset: i, string } = source;
              throw new Error(`Internal grammar error. Unexpected "${clipString(string.slice(i, 31), 30)}" at position ${i} in "${string}".`);
            }
            Matcher.cache[str] = res;
            return res;
          }
          static parseAlt(src) {
            const alts = [];
            do {
              const pt = src.readMatch(rxPlainTextAlt);
              if (pt) {
                alts.push(Matcher.term(pt));
              } else {
                const ors = [];
                do {
                  const ands = [];
                  do {
                    const seq = [];
                    do
                      seq.push(Matcher.parseTerm(src));
                    while (src.readMatch(rxSeqSep));
                    ands.push(Matcher.seq(seq));
                  } while (src.readMatch(rxAndAndSep));
                  ors.push(Matcher.many(null, ands));
                } while (src.readMatch(rxOrOrSep));
                alts.push(Matcher.many(false, ors));
              }
            } while (src.readMatch(rxOrSep));
            return Matcher.alt(alts);
          }
          static parseTerm(src) {
            let m, fn;
            if (src.readMatch(rxGroupBegin)) {
              m = Matcher.parseAlt(src);
              if (!src.readMatch(rxGroupEnd))
                Matcher.parsingFailed(src, rxGroupEnd);
            } else if (fn = src.readMatch(rxFuncBegin, true)) {
              m = new Matcher(Matcher.funcTest, Matcher.funcToStr, {
                name: fn[1].toLowerCase(),
                body: !fn[2] && Matcher.parseAlt(src)
              });
              if (!fn[2] && !src.readMatch(rxFuncEnd))
                Matcher.parsingFailed(src, rxFuncEnd);
            } else {
              m = Matcher.term(src.readMatch(rxTerm) || Matcher.parsingFailed(src, rxTerm));
            }
            fn = src.peek();
            if (fn === 123 || fn === 35 && src.peek(2) === 123) {
              const hash = fn === 35 ? src.read() : "";
              const [, a, comma, b = comma ? Infinity : a] = src.readMatch(rxBraces, true) || Matcher.parsingFailed(src, rxBraces);
              return m.braces(+a, +b, hash, hash && ",");
            }
            switch (fn) {
              case 63:
                return m.braces(0, 1, src.read());
              case 42:
                return m.braces(0, Infinity, src.read());
              case 43:
                return m.braces(1, Infinity, src.read());
              case 35:
                return m.braces(1, Infinity, src.read(), ",");
            }
            return m;
          }
          static parsingFailed(src, m) {
            throw new Error(`Internal grammar error. Expected ${m} at ${src.offset} in ${src.string}`);
          }
          static seq(ms) {
            return !ms[1] ? ms[0] : new Matcher(Matcher.seqTest, Matcher.seqToStr, ms, true);
          }
          static seqTest(expr, p) {
            let min1, i, m, res;
            for (i = 0; m = this.arg[i++]; p = void 0) {
              if (!(res = m.match(expr, p)))
                return;
              if (!min1 && (m.arg.min !== 0 || res === 1 || res > 1))
                min1 = true;
            }
            return true;
          }
          static seqToStr(prec) {
            return (prec = prec > Matcher.SEQ ? "[ " : "") + this.arg.map((m) => m.toString(Matcher.SEQ)).join(" ") + (prec ? " ]" : "");
          }
          static simpleTest(expr, p) {
            return !!this.arg(p);
          }
          static stringArrTest(expr, p) {
            return this.arg.has(p) || p.vendorCode && (expr = this.arg[p.vendorCode * 100 + p.length - p.vendorPos]) && (p = p.text.slice(p.vendorPos).toLowerCase()) && (typeof expr === "string" ? expr === p : expr.includes(p));
          }
          static stringArrToStr(prec) {
            return (prec = prec > Matcher.ALT && this._string.includes(" ") ? "[ " : "") + this._string + (prec ? " ]" : "");
          }
          static term(str) {
            let m = Matcher.cache[str = str.toLowerCase()];
            if (m)
              return m;
            if (str[0] !== "<") {
              m = new Matcher(Matcher.stringArrTest, Matcher.stringArrToStr, new Bucket(str.split(rxAltSep)));
              m._string = str;
            } else if (str.startsWith("<fn:")) {
              m = new Matcher(Matcher.funcTest, Matcher.funcToStr, { list: VTFunctions[str.slice(4, -1)] });
            } else if (m = VTSimple[str]) {
              m = new Matcher(Matcher.simpleTest, str, m);
            } else {
              m = VTComplex[str] || Properties[str.slice(1, -1)];
              m = m.matchFunc ? m : m.call ? m(Matcher) : Matcher.cache[m] || Matcher.parse(m);
            }
            Matcher.cache[str] = m;
            return m;
          }
        }
        Matcher.cache = { __proto__: null };
        Matcher.MOD = 5;
        Matcher.SEQ = 4;
        Matcher.ANDAND = 3;
        Matcher.OROR = 2;
        Matcher.ALT = 1;
        const validationCache = /* @__PURE__ */ new Map();
        class PropValueIterator {
          constructor(value) {
            this.i = 0;
            this.parts = value.parts;
            this.value = value;
          }
          get hasNext() {
            return this.i + 1 < this.parts.length;
          }
          next() {
            if (this.i < this.parts.length)
              return this.parts[++this.i];
          }
        }
        class ValidationError extends Error {
          constructor(message, pos) {
            super();
            this.col = pos.col;
            this.line = pos.line;
            this.offset = pos.offset;
            this.message = message;
          }
        }
        function validateProperty(tok, value, stream, Props) {
          const pp = value.parts;
          const p0 = pp[0];
          if (p0.type === "ident" && buGlobalKeywords.has(p0)) {
            return pp[1] && vtFailure(pp[1], true);
          }
          Props = typeof Props === "string" ? ScopedProperties[Props] : Props || Properties;
          let spec, res, vp;
          let prop = tok.lowText || tok.text.toLowerCase();
          do
            spec = Props[prop] || Props["<all>"] && (Props = Properties)[prop];
          while (!spec && !res && (vp = tok.vendorPos) && (res = prop = prop.slice(vp)));
          if (typeof spec === "number" || !spec && vp) {
            return;
          }
          if (!spec) {
            prop = Props === Properties || !Properties[prop] ? "Unknown" : "Misplaced";
            return new ValidationError(`${prop} property "${tok}".`, tok);
          }
          if (value.isVar) {
            return;
          }
          const valueSrc = value.text.trim();
          let known = validationCache.get(prop);
          if (known && known.has(valueSrc)) {
            return;
          }
          const expr = new PropValueIterator(value);
          let m = Matcher.cache[spec] || Matcher.parse(spec);
          res = m.match(expr, p0);
          if ((!res || expr.hasNext) && /\battr\(/i.test(valueSrc)) {
            if (!res) {
              expr.i = 0;
              expr.tryAttr = true;
              res = m.match(expr);
            }
            for (let p; (p = expr.parts[expr.i]) && p.isAttr; ) {
              expr.next();
            }
          }
          if (expr.hasNext && (res || expr.i))
            return vtFailure(expr.parts[expr.i]);
          if (!res && (m = expr.badFunc))
            return vtFailure(m[0], vtDescribe(spec, m[1]));
          if (!res)
            return vtFailure(expr.value, vtDescribe(spec));
          if (!known)
            validationCache.set(prop, known = /* @__PURE__ */ new Set());
          known.add(valueSrc);
        }
        function vtDescribe(type, m) {
          if (!m)
            m = VTComplex[type] || type[0] === "<" && Properties[type.slice(1, -1)];
          return m instanceof Matcher ? m.toString(0) : vtExplode(m || type);
        }
        function vtExplode(text) {
          return !text.includes("<") ? text : (Matcher.cache[text] || Matcher.parse(text)).toString(0);
        }
        function vtFailure(unit, what) {
          if (!what || what === true ? what = "end of value" : !unit.isVar) {
            return new ValidationError(`Expected ${what} but found "${clipString(unit)}".`, unit);
          }
        }
        function clipString(s, len = 30) {
          return (s = `${s}`).length > len ? s.slice(0, len) + "..." : s;
        }
        function customIdentChecker(str = "", alt) {
          const b = new Bucket(GlobalKeywords);
          if (str)
            b.addFrom(str.split(","));
          return (p) => p.id === IDENT && !b.has(p) || alt && alt(p);
        }
        const parserlib = {
          css: {
            Combinators,
            GlobalKeywords,
            NamedColors,
            Properties,
            ScopedProperties,
            Tokens,
            Units
          },
          util: {
            Bucket,
            EventTarget,
            Matcher,
            StringSource,
            TokenIdByCode,
            VTComplex,
            VTFunctions,
            VTSimple,
            UnitTypeIds,
            clipString,
            describeProp: vtExplode,
            isOwn,
            pick,
            validateProperty
          }
        };
        if (typeof self !== "undefined")
          self.parserlib = parserlib;
        else
          module.exports = parserlib;
      })();
    }
  });

  // js/vendor/csslint/parserlib.js
  var require_parserlib = __commonJS({
    "js/vendor/csslint/parserlib.js"(exports, module) {
      "use strict";
      (() => {
        const parserlib = typeof self !== "undefined" ? (require_parserlib_base(), self.parserlib) : require_parserlib_base();
        const { assign, defineProperty: define } = Object;
        const {
          css: {
            Combinators,
            NamedColors,
            Tokens,
            Units
          },
          util: {
            Bucket,
            EventTarget,
            StringSource,
            TokenIdByCode,
            UnitTypeIds,
            clipString,
            isOwn,
            pick,
            validateProperty
          }
        } = parserlib;
        const {
          AMP,
          AT,
          CHAR,
          COLON,
          COMMA,
          COMMENT,
          DELIM,
          DOT,
          HASH,
          FUNCTION,
          IDENT,
          LBRACE,
          LBRACKET,
          LPAREN,
          MINUS,
          NUMBER,
          PCT,
          PIPE,
          PLUS,
          RBRACE,
          RBRACKET,
          RPAREN,
          SEMICOLON,
          STAR,
          UVAR,
          WS
        } = Tokens;
        const TT = {
          attrEq: [Tokens.ATTR_EQ, Tokens.EQUALS],
          attrEqEnd: [Tokens.ATTR_EQ, Tokens.EQUALS, RBRACKET],
          attrStart: [PIPE, IDENT, STAR],
          attrNameEnd: [RBRACKET, UVAR, WS],
          colonLParen: [COLON, LPAREN],
          combinator: [PLUS, Tokens.GT, Tokens.COMBINATOR],
          condition: [FUNCTION, IDENT, LPAREN],
          declEnd: [SEMICOLON, RBRACE],
          docFunc: [FUNCTION, IDENT, Tokens.URI],
          identStar: [IDENT, STAR],
          identString: [IDENT, Tokens.STRING],
          mediaList: [IDENT, LPAREN],
          mediaValue: [IDENT, NUMBER, Tokens.DIMENSION, Tokens.LENGTH],
          propCustomEnd: [DELIM, SEMICOLON, RBRACE, RBRACKET, RPAREN, Tokens.INVALID],
          propValEnd: [DELIM, SEMICOLON, RBRACE],
          propValEndParen: [DELIM, SEMICOLON, RBRACE, RPAREN],
          pseudo: [FUNCTION, IDENT],
          selectorStart: [AMP, PIPE, IDENT, STAR, HASH, DOT, LBRACKET, COLON],
          stringUri: [Tokens.STRING, Tokens.URI]
        };
        const B = {
          attrIS: ["i", "s", "]"],
          colors: NamedColors,
          marginSyms: ((map) => "B-X,B-L-C,B-L,B-R-C,B-R,L-B,L-M,L-T,R-B,R-M,R-T,T-X,T-L-C,T-L,T-R-C,T-R".replace(/[A-Z]/g, (s) => map[s]).split(","))({ B: "bottom", C: "corner", L: "left", M: "middle", R: "right", T: "top", X: "center" })
        };
        for (const k in B)
          B[k] = new Bucket(B[k]);
        for (const k of "and,andOr,auto,autoNone,evenOdd,fromTo,important,layer,n,none,not,notOnly,of,or".split(","))
          B[k] = new Bucket(k.split(/(?=[A-Z])/));
        const OrDie = { must: true };
        const OrDieReusing = { must: true, reuse: true };
        const Parens = [];
        Parens[LBRACE] = RBRACE;
        Parens[LBRACKET] = RBRACKET;
        Parens[LPAREN] = RPAREN;
        const PDESC = { configurable: true, enumerable: true, writable: true, value: null };
        const UVAR_PROXY = [PCT, ...TT.mediaValue, ...TT.identString].reduce((res, id) => (res[id] = true) && res, []);
        const rxCommentUso = /(\*)\[\[[-\w]+]]\*\/|\*(?:[^*]+|\*(?!\/))*(?:\*\/|$)/y;
        const rxDigits = /\d+/y;
        const rxMaybeQuote = /\s*['"]?/y;
        const rxName = /(?:[-_\da-zA-Z\u00A0-\uFFFF]+|\\(?:(?:[0-9a-fA-F]{1,6}|.)[\t ]?|$))+/y;
        const rxNth = /(even|odd)|(?:([-+]?\d*n)(?:\s*([-+])(\s*\d+)?)?|[-+]?\d+)((?=\s+of\s+|\s*\)))?/yi;
        const rxNumberDigit = /\d*(?:(\.)\d*)?(?:(e)[+-]?\d+)?/iy;
        const rxNumberDot = /\d+(?:(e)[+-]?\d+)?/iy;
        const rxNumberSign = /(?:(\.)\d+|\d+(?:(\.)\d*)?)(?:(e)[+-]?\d+)?/iy;
        const rxSign = /[-+]/y;
        const rxSpace = /\s+/y;
        const rxSpaceCmtRParen = /(?=\s|\/\*|\))/y;
        const rxSpaceComments = /(?:\s+|\/\*(?:[^*]+|\*(?!\/))*(?:\*\/|$))+/y;
        const rxSpaceRParen = /\s*\)/y;
        const rxStringDoubleQ = /(?:[^\n\\"]+|\\(?:([0-9a-fA-F]{1,6}|.)[\t ]?|\n|$))*/y;
        const rxStringSingleQ = /(?:[^\n\\']+|\\(?:([0-9a-fA-F]{1,6}|.)[\t ]?|\n|$))*/y;
        const rxUnescapeLF = /\\(?:(?:([0-9a-fA-F]{1,6})|(.))[\t ]?|(\n))/g;
        const rxUnescapeNoLF = /\\(?:([0-9a-fA-F]{1,6})|(.))[\t ]?/g;
        const rxUnicodeRange = /\+([\da-f]{1,6})(\?{1,6}|-([\da-f]{1,6}))?/iy;
        const rxUnquotedUrl = /(?:[-!#$%&*-[\]-~\u00A0-\uFFFF]+|\\(?:(?:[0-9a-fA-F]{1,6}|.)[\t ]?|$))+/y;
        const [rxDeclBlock, rxDeclValue] = ((exclude = String.raw`'"{}()[\]\\/`, orSlash = "]|/(?!\\*))", blk = String.raw`(?:"[^"\n\\]*"|[^${exclude}${orSlash}*`, common = `(?:${[
          rxUnescapeLF.source,
          `"${rxStringDoubleQ.source}"`,
          `'${rxStringSingleQ.source}'`,
          String.raw`\(${blk}\)|\[${blk}]`,
          String.raw`/\*(?:[^*]+|\*(?!\/))*(?:\*\/|$)`
        ].join("|")}|`) => [`{${blk}}|[^`, "[^;"].map((str) => RegExp(common + str + exclude + orSlash + "+", "y")))();
        const isRelativeSelector = (sel) => isOwn(TT.combinator, sel.parts[0].id);
        const isIdentChar = (c, prev) => c >= 97 && c <= 122 || c >= 65 && c <= 90 || c === 45 || c === 92 || c === 95 || c >= 160 || c >= 48 && c <= 57 || prev === 92 && c !== 10 && c != null;
        const isIdentStart = (a, b) => a >= 97 && a <= 122 || a >= 65 && a <= 90 || a === 95 || a >= 160 || (a === 45 ? b !== 45 && isIdentStart(b) : a === 92 && isIdentChar(b, a));
        const isSpace = (c) => c === 9 && c === 10 || c === 32;
        const textToTokenMap = (obj) => Object.keys(obj).reduce((res, k) => (res[TokenIdByCode[k.charCodeAt(0)]] = obj[k], res), []);
        const toLowAscii = (c) => c >= 65 && c <= 90 ? c + 32 : c;
        const toStringPropHack = function() {
          return this.hack + this.text;
        };
        const unescapeNoLF = (m, code, char) => char || String.fromCodePoint(parseInt(code, 16));
        const unescapeLF = (m, code, char, LF) => LF ? "" : char || String.fromCodePoint(parseInt(code, 16));
        const parseString = (str) => str.slice(1, -1).replace(rxUnescapeLF, unescapeLF);
        TT.nestSel = [...TT.selectorStart, ...TT.combinator];
        for (const k in TT) {
          TT[k] = TT[k].reduce((res, id) => {
            if (UVAR_PROXY[id])
              res.isUvp = 1;
            res[id] = true;
            return res;
          }, []);
        }
        delete TT.nestSel[IDENT];
        class Token {
          constructor(id, col, line, offset, input, code) {
            this.id = id;
            this.col = col;
            this.line = line;
            this.offset = offset;
            this.offset2 = offset + 1;
            this.type = "";
            this.code = toLowAscii(code);
            this._input = input;
          }
          static from(tok) {
            return assign(Object.create(this.prototype), tok);
          }
          get length() {
            return isOwn(this, "text") ? this.text.length : this.offset2 - this.offset;
          }
          get string() {
            const str = PDESC.value = parseString(this.text);
            define(this, "string", PDESC);
            return str;
          }
          set string(val) {
            PDESC.value = val;
            define(this, "string", PDESC);
          }
          get text() {
            return this._input.slice(this.offset, this.offset2);
          }
          set text(val) {
            PDESC.value = val;
            define(this, "text", PDESC);
          }
          valueOf() {
            return this.text;
          }
          toString() {
            return this.text;
          }
        }
        class TokenFunc extends Token {
          static from(tok, expr, end) {
            tok = super.from(tok);
            tok.type = "fn";
            if (isOwn(tok, "text"))
              tok.offsetBody = tok.offset2;
            if (end)
              tok.offset2 = end.offset2;
            if (expr) {
              tok.expr = expr;
              let n = tok.name;
              if (n === "calc" || n === "clamp" || n === "min" || n === "max" || n === "sin" || n === "cos" || n === "tan" || n === "asin" || n === "acos" || n === "atan" || n === "atan2") {
                tok.isCalc = true;
              } else if (n === "var" || n === "env") {
                tok.isVar = true;
              } else if (n === "attr" && (n = expr.parts[0]) && (n.id === IDENT || n.id === UVAR)) {
                tok.isAttr = true;
              }
            }
            return tok;
          }
          toString() {
            const s = this._input;
            return isOwn(this, "text") ? this.text + s.slice(this.offsetBody + 1, this.offset2) : s.slice(this.offset, this.offset2);
          }
        }
        class TokenValue extends Token {
          static from(parts, tok = parts[0]) {
            tok = super.from(tok);
            tok.parts = parts;
            return tok;
          }
          static empty(tok) {
            tok = super.from(tok);
            tok.parts = [];
            tok.id = WS;
            tok.offset2 = tok.offset;
            delete tok.text;
            return tok;
          }
          get text() {
            return this._input.slice(this.offset, (this.parts[this.parts.length - 1] || this).offset2);
          }
          set text(val) {
            PDESC.value = val;
            define(this, "text", PDESC);
          }
        }
        class SyntaxError extends Error {
          constructor(message, pos) {
            super();
            this.name = this.constructor.name;
            this.col = pos.col;
            this.line = pos.line;
            this.offset = pos.offset;
            this.message = message;
          }
        }
        class TokenStream {
          constructor(input) {
            this.source = new StringSource(input ? `${input}` : "");
            this._amp = 0;
            this._max = 4;
            this._resetBuf();
            define(this, "grab", { writable: true, value: this.get.bind(this, true) });
          }
          _resetBuf() {
            this.token = null;
            this._buf = [];
            this._cur = 0;
            this._cycle = 0;
          }
          get(mode) {
            let { _buf: buf, _cur: i, _max: MAX } = this;
            let tok, ti, slot;
            do {
              slot = (i + this._cycle) % MAX;
              if (i >= buf.length) {
                if (buf.length < MAX)
                  i++;
                else
                  this._cycle = (this._cycle + 1) % MAX;
                ti = (tok = buf[slot] = this._getToken(mode)).id;
                break;
              }
              ++i;
              ti = (tok = buf[slot]).id;
            } while (ti === COMMENT || mode && (ti === WS || ti === UVAR && mode !== UVAR));
            if (ti === AMP)
              this._amp++;
            this._cur = i;
            this.token = tok;
            return tok;
          }
          match(what, text, tok = this.get(), opts) {
            if ((typeof what === "object" ? isOwn(what, tok.id) : !what || tok.id === what) && (!text || text.has(tok))) {
              return tok;
            }
            if (opts !== UVAR) {
              this.unget();
              if (opts && opts.must)
                this._failure(text || what, tok);
              return false;
            }
          }
          matchOrDie(what, text, tok) {
            return this.match(what, text, tok, OrDie);
          }
          matchSmart(what, opts = {}) {
            let tok;
            const text = opts.has ? opts : (tok = opts.reuse, opts.text);
            const ws = typeof what === "object" ? what[WS] : what === WS;
            let uvp = !ws && !text && (typeof what === "object" ? what.isUvp : isOwn(UVAR_PROXY, what));
            tok = tok && (tok.id != null ? tok : this.token) || this.get(uvp ? UVAR : !ws);
            uvp = uvp && tok.isVar;
            return this.match(what, text, tok, uvp ? UVAR : opts) || uvp && (this.match(what, text, this.grab()) || tok) || false;
          }
          peekCached() {
            return this._cur < this._buf.length && this._buf[(this._cur + this._cycle) % this._max];
          }
          unget() {
            if (this._cur) {
              if ((this.token || {}).id === AMP)
                this._amp--;
              this.token = this._buf[(--this._cur - 1 + this._cycle + this._max) % this._max];
            } else {
              throw new Error("Too much lookahead.");
            }
          }
          _failure(goal = "", tok = this.token, throwIt = true) {
            goal = typeof goal === "string" ? goal : goal instanceof Bucket ? `"${goal.join('", "')}"` : (+goal ? [goal] : goal).reduce((res, v, id) => res + (res ? ", " : "") + ((v = Tokens[v === true ? id : v]).text ? `"${v.text}"` : v.name), "");
            goal = goal ? `Expected ${goal} but found` : "Unexpected";
            goal = new SyntaxError(`${goal} "${clipString(tok)}".`, tok);
            if (throwIt)
              throw goal;
            return goal;
          }
          _getToken(mode) {
            const src = this.source;
            let a, b, c, text, col, line, offset;
            while (true) {
              ({ col, line, offset } = src);
              a = src.readCode();
              if (a == null)
                break;
              b = src.peek();
              if (a === 9 || a === 10 || a === 32) {
                if (isSpace(b))
                  src.readMatch(rxSpace);
                if (!mode) {
                  c = WS;
                  break;
                }
              } else if (a === 47 && b === 42) {
                a = src.readMatch(rxCommentUso, true);
                if (a[1] && mode === UVAR) {
                  c = UVAR;
                  break;
                }
              } else
                break;
            }
            const tok = new Token(c || CHAR, col, line, offset, src.string, a);
            if (c) {
              if (c === UVAR)
                tok.isVar = true;
            } else if (a >= 48 && a <= 57) {
              c = b >= 48 && b <= 57 || b === 46 || (b === 69 || b === 101) && (c = src.peek(2)) === 43 || c === 45 || c >= 48 && c <= 57;
              text = this._number(src, tok, a, b, c, rxNumberDigit);
            } else if ((a === 45 || a === 43 && (tok.id = PLUS) || a === 46 && (tok.id = DOT)) && (b >= 48 && b <= 57 || b === 46 && a !== 46 && (c = src.peek(2)) >= 48 && c <= 57)) {
              text = this._number(src, tok, a, b, 1, a === 46 ? rxNumberDot : rxNumberSign);
            } else if (a === 45) {
              if (b === 45) {
                if (isIdentChar(c || (c = src.peek(2)), b)) {
                  text = this._ident(src, tok, a, b, 1, c, 1);
                  tok.type = "custom-prop";
                } else if (c === 62) {
                  src.read(2, "->");
                  tok.id = Tokens.CDCO;
                } else {
                  tok.id = MINUS;
                }
              } else if (isIdentStart(b, b === 92 && (c || (c = src.peek(2))))) {
                text = this._ident(src, tok, a, b, 1, c);
              } else {
                tok.id = MINUS;
              }
            } else if ((a === 85 || a === 117) && b === 43) {
              c = src.readMatch(rxUnicodeRange, true);
              if (c && parseInt(c[1], 16) <= 1114111 && (c[3] ? parseInt(c[3], 16) <= 1114111 : !c[2] || (c[1] + c[2]).length <= 6)) {
                tok.id = Tokens.URANGE;
              } else {
                if (c) {
                  src.col -= c = c[0].length;
                  src.offset -= c;
                }
                tok.id = IDENT;
                tok.type = "ident";
              }
            } else if (isIdentStart(a, b)) {
              text = this._ident(src, tok, a, b);
            } else if (c = b === 61 ? (a === 36 || a === 42 || a === 94 || a === 124 || a === 126) && Tokens.ATTR_EQ || (a === 60 || a === 62) && Tokens.EQ_CMP : a === 124 && b === 124 && Tokens.COMBINATOR) {
              tok.id = c;
              src.readCode();
            } else if (a === 35) {
              if (isIdentChar(b, a)) {
                text = this._ident(src, tok, a, b, 1);
                tok.id = HASH;
              }
            } else if (a === 42) {
              tok.id = STAR;
              if (isIdentStart(b))
                tok.hack = "*";
            } else if (c = TokenIdByCode[a]) {
              tok.id = c;
            } else if (a === 34 || a === 39) {
              src.readMatch(a === 34 ? rxStringDoubleQ : rxStringSingleQ);
              if (src.readMatchCode(a)) {
                tok.id = Tokens.STRING;
                tok.type = "string";
              } else {
                tok.id = Tokens.INVALID;
              }
            } else if (a === 92) {
              if (b == null)
                text = "\uFFFD";
              else if (b === 10) {
                tok.id = WS;
                text = src.readMatch(rxSpace);
              }
            } else if (a === 64) {
              if (isIdentStart(b, c = (b === 45 || b === 92) && src.peek(2))) {
                c = this._ident(src, null, src.readCode(), c || src.peek());
                a = c.name;
                text = c.esc && `@${a}`;
                a = a.charCodeAt(0) === 45 && (c = a.indexOf("-", 1)) > 1 ? a.slice(c + 1) : a;
                tok.atName = a.toLowerCase();
                tok.id = AT;
              }
            } else if (a === 60) {
              if (b === 33 && src.readMatchStr("!--")) {
                tok.id = Tokens.CDCO;
              }
            } else if (a == null) {
              tok.id = Tokens.EOF;
            }
            if ((c = src.offset) !== offset + 1)
              tok.offset2 = c;
            if (text) {
              PDESC.value = text;
              define(tok, "text", PDESC);
            }
            return tok;
          }
          _ident(src, tok, a, b, bYes = isIdentChar(b, a), c = bYes && this.source.peek(2), cYes = c && (isIdentChar(c, b) || a === 92 && isSpace(c))) {
            const first = a === 92 ? (cYes = src.offset--, src.col--, "") : String.fromCharCode(a);
            const str = cYes ? src.readMatch(rxName) : bYes ? src.read() : "";
            const esc = a === 92 || b === 92 || bYes && c === 92 || str.length > 2 && str.includes("\\");
            const name = esc ? (first + str).replace(rxUnescapeNoLF, unescapeNoLF) : first + str;
            if (!tok)
              return { esc, name };
            if (a === 92)
              tok.code = toLowAscii(name.charCodeAt(0));
            const vp = a === 45 && b !== 45 && name.indexOf("-", 2) + 1;
            const next = cYes || esc && isSpace(c) ? src.peek() : bYes ? c : b;
            let ovrValue = esc ? name : null;
            if (next === 40) {
              src.read();
              c = name.toLowerCase();
              b = (c === "url" || c === "url-prefix" || c === "domain") && this._uriValue(src);
              tok.id = b ? Tokens.URI : FUNCTION;
              tok.type = b ? "uri" : "fn";
              tok.name = vp ? c.slice(vp) : c;
              if (vp)
                tok.prefix = c.slice(0, vp);
              if (b)
                tok.uri = b;
            } else if (next === 58 && name === "progid") {
              ovrValue = name + src.readMatch(/.*?\(/y);
              tok.id = FUNCTION;
              tok.name = ovrValue.slice(0, -1).toLowerCase();
              tok.type = "fn";
              tok.ie = true;
            } else {
              tok.id = IDENT;
              if (a === 45 || (b = name.length) < 3 || b > 20) {
                tok.type = "ident";
              }
            }
            if (vp) {
              tok.vendorCode = toLowAscii(name.charCodeAt(vp));
              tok.vendorPos = vp;
            }
            return ovrValue;
          }
          _number(src, tok, a, b, bYes, rx) {
            const numStr = String.fromCharCode(a) + (bYes ? (b = src.readMatch(rx, true))[0] : "");
            const isFloat = a === 46 || bYes && (b[1] || b[2] || b[3]);
            let ovrText, units;
            if ((a = bYes ? src.peek() : b) === 37) {
              tok.id = PCT;
              tok.type = units = src.read(1, "%");
            } else if (isIdentStart(a, b = (a === 45 || a === 92) && src.peek(2))) {
              a = this._ident(src, null, src.readCode(), b || src.peek());
              units = a.name;
              ovrText = a.esc && numStr + units;
              a = Units[units = units.toLowerCase()] || "";
              tok.id = a && UnitTypeIds[a] || Tokens.DIMENSION;
              tok.type = a;
            } else {
              tok.id = NUMBER;
              tok.type = "number";
            }
            tok.units = units || "";
            tok.number = a = +numStr;
            tok.is0 = b = !units && !a;
            tok.isInt = b || !units && !isFloat;
            return ovrText;
          }
          _spaceCmt(src) {
            const c = src.peek();
            return (c === 47 || isSpace(c)) && src.readMatch(rxSpaceComments) || "";
          }
          _uriValue(src) {
            let v = src.peek();
            src.mark();
            v = v === 34 || v === 39 ? src.read() : isSpace(v) && src.readMatch(rxMaybeQuote).trim();
            if (v) {
              v += src.readMatch(v === '"' ? rxStringDoubleQ : rxStringSingleQ);
              v = src.readMatchStr(v[0]) && parseString(v + v[0]);
            } else if ((v = src.readMatch(rxUnquotedUrl)) && v.includes("\\")) {
              v = v.replace(rxUnescapeNoLF, unescapeNoLF);
            }
            if (v != null && (src.readMatchCode(41) || src.readMatch(rxSpaceRParen))) {
              return v;
            }
            src.reset();
          }
          readNthChild() {
            const src = this.source;
            const m = (this._spaceCmt(src), src.readMatch(rxNth, true));
            if (!m)
              return;
            let [, evenOdd, nth, sign, int, next] = m;
            let a, b, ws;
            if (evenOdd)
              a = evenOdd;
            else if (!(a = nth))
              b = m[0];
            else if (sign || !next && (ws = this._spaceCmt(src), sign = src.readMatch(rxSign))) {
              if (int || (src.mark(), this._spaceCmt(src), int = src.readMatch(rxDigits))) {
                b = sign + int.trim();
              } else
                return src.reset();
            }
            if ((a || b) && (ws || src.readMatch(rxSpaceCmtRParen) != null)) {
              return [a || "", b || ""];
            }
          }
          skipDeclBlock(inBlock) {
            for (let src = this.source, stack = [], end = inBlock ? 125 : -1, c; c = src.peek(); ) {
              if (c === end || end < 0 && (c === 59 || c === 125)) {
                end = stack.pop();
                if (!end || end < 0 && c === 125) {
                  if (end || c === 59)
                    src.readCode();
                  break;
                }
              } else if (c = c === 123 ? 125 : c === 40 ? 41 : c === 91 && 93) {
                stack.push(end);
                end = c;
              }
              src.readCode();
              src.readMatch(end > 0 ? rxDeclBlock : rxDeclValue);
            }
            this._resetBuf();
          }
        }
        const parserCache = (() => {
          const MAX_DURATION = 10 * 6e4;
          const TRIM_DELAY = 1e4;
          const data = /* @__PURE__ */ new Map();
          const stack = [];
          let generation = null;
          let generationBase = null;
          let parser = null;
          let stream = null;
          return {
            start(newParser) {
              parser = newParser;
              if (!parser) {
                data.clear();
                stack.length = 0;
                generationBase = performance.now();
                return;
              }
              stream = parser.stream;
              generation = performance.now();
              trim();
            },
            addEvent(event) {
              if (!parser)
                return;
              for (let i = stack.length; --i >= 0; ) {
                const { offset, offset2, events } = stack[i];
                if (event.offset >= offset && (!offset2 || event.offset <= offset2)) {
                  events.push(event);
                  return;
                }
              }
            },
            findBlock(token = getToken()) {
              if (!token || !stream)
                return;
              const src = stream.source;
              const { string } = src;
              const start = token.offset;
              const key = string.slice(start, string.indexOf("{", start) + 1);
              let block = data.get(key);
              if (!block || !(block = getBlock(block, string, start, key)))
                return;
              shiftBlock(block, start, token.line, token.col, string);
              src.offset = block.offset2;
              src.line = block.line2;
              src.col = block.col2;
              stream._resetBuf();
              return true;
            },
            startBlock(start = getToken()) {
              if (!start || !stream)
                return;
              stack.push({
                text: "",
                events: [],
                generation,
                line: start.line,
                col: start.col,
                offset: start.offset,
                line2: void 0,
                col2: void 0,
                offset2: void 0
              });
              return stack.length;
            },
            endBlock(end = getToken()) {
              if (!parser || !stream)
                return;
              const block = stack.pop();
              block.line2 = end.line;
              block.col2 = end.col + end.offset2 - end.offset;
              block.offset2 = end.offset2;
              const { string } = stream.source;
              const start = block.offset;
              const key = string.slice(start, string.indexOf("{", start) + 1);
              block.text = string.slice(start, block.offset2);
              let blocks = data.get(key);
              if (!blocks)
                data.set(key, blocks = []);
              blocks.push(block);
            },
            cancelBlock: (pos) => pos === stack.length && stack.length--,
            feedback({ messages }) {
              messages = new Set(messages);
              for (const blocks of data.values()) {
                for (const block of blocks) {
                  if (!block.events.length)
                    continue;
                  if (block.generation !== generation)
                    continue;
                  const { line: L1, col: C1, line2: L2, col2: C2 } = block;
                  let isClean = true;
                  for (const msg of messages) {
                    const { line, col } = msg;
                    if (L1 === L2 && line === L1 && C1 <= col && col <= C2 || line === L1 && col >= C1 || line === L2 && col <= C2 || line > L1 && line < L2) {
                      messages.delete(msg);
                      isClean = false;
                    }
                  }
                  if (isClean)
                    block.events.length = 0;
                }
              }
            }
          };
          function trim(immediately) {
            if (!immediately) {
              clearTimeout(trim.timer);
              trim.timer = setTimeout(trim, TRIM_DELAY, true);
              return;
            }
            const cutoff = performance.now() - MAX_DURATION;
            for (const [key, blocks] of data.entries()) {
              const halfLen = blocks.length >> 1;
              const newBlocks = blocks.sort((a, b) => a.time - b.time).filter((b, i) => (b = b.generation) > cutoff || b !== generation && i < halfLen);
              if (!newBlocks.length) {
                data.delete(key);
              } else if (newBlocks.length !== blocks.length) {
                data.set(key, newBlocks);
              }
            }
          }
          function getBlock(blocks, input, start, key) {
            const keyLast = Math.max(key.length - 1);
            const check1 = input[start];
            const check2 = input[start + keyLast];
            const generationSpan = performance.now() - generationBase;
            blocks = blocks.filter(({ text, offset, offset2 }) => text[0] === check1 && text[keyLast] === check2 && text[text.length - 1] === input[start + text.length - 1] && text.startsWith(key) && text === input.substr(start, offset2 - offset)).sort((a, b) => (a.generation - b.generation) / generationSpan + (Math.abs(a.offset - start) - Math.abs(b.offset - start)) / input.length);
            const block = blocks.find((b) => b.generation !== generation);
            return block || deepCopy(blocks[0]);
          }
          function shiftBlock(block, cursor, line, col, input) {
            const deltaLines = line - block.line;
            const deltaCols = block.col === 1 && col === 1 ? 0 : col - block.col;
            const deltaOffs = cursor - block.offset;
            const hasDelta = deltaLines || deltaCols || deltaOffs;
            const shifted = /* @__PURE__ */ new Set();
            for (const e of block.events) {
              if (hasDelta) {
                applyDelta(e, shifted, block.line, deltaLines, deltaCols, deltaOffs, input);
              }
              parser.fire(e, false);
            }
            block.generation = generation;
            block.col2 += block.line2 === block.line ? deltaCols : 0;
            block.line2 += deltaLines;
            block.offset2 = cursor + block.text.length;
            block.line += deltaLines;
            block.col += deltaCols;
            block.offset = cursor;
          }
          function applyDelta(obj, seen, line, lines, cols, offs, input) {
            if (seen.has(obj))
              return;
            seen.add(obj);
            if (Array.isArray(obj)) {
              for (let i = 0, v; i < obj.length; i++) {
                if ((v = obj[i]) && typeof v === "object") {
                  applyDelta(v, seen, line, lines, cols, offs, input);
                }
              }
              return;
            }
            for (let i = 0, keys = Object.keys(obj), k, v; i < keys.length; i++) {
              k = keys[i];
              if (k === "col" ? (cols && obj.line === line && (obj.col += cols), 0) : k === "col2" ? (cols && obj.line2 === line && (obj.col2 += cols), 0) : k === "line" ? (lines && (obj.line += lines), 0) : k === "line2" ? (lines && (obj.line2 += lines), 0) : k === "offset" ? (offs && (obj.offset += offs), 0) : k === "offset2" ? (offs && (obj.offset2 += offs), 0) : k === "_input" ? (obj._input = input, 0) : k !== "target" && (v = obj[k]) && typeof v === "object") {
                applyDelta(v, seen, line, lines, cols, offs, input);
              }
            }
          }
          function getToken() {
            return parser && (stream.peekCached() || stream.token);
          }
          function deepCopy(obj) {
            if (!obj || typeof obj !== "object") {
              return obj;
            }
            if (Array.isArray(obj)) {
              return obj.map(deepCopy);
            }
            const copy = Object.create(Object.getPrototypeOf(obj));
            for (let arr = Object.keys(obj), k, v, i = 0; i < arr.length; i++) {
              v = obj[k = arr[i]];
              copy[k] = !v || typeof v !== "object" ? v : deepCopy(v);
            }
            return copy;
          }
        })();
        class Parser extends EventTarget {
          constructor(options) {
            super();
            this.options = options || {};
            this.stream = null;
            this._inStyle = 0;
            this._stack = [];
          }
          alarm(level, msg, token) {
            this.fire({
              type: level >= 2 ? "error" : level === 1 ? "warning" : "info",
              message: msg,
              recoverable: level <= 2
            }, token);
          }
          fire(e, tok = e.offset != null ? e : this.stream.token) {
            if (typeof e === "string")
              e = { type: e };
            if (tok && e.offset == null) {
              e.offset = tok.offset;
              e.line = tok.line;
              e.col = tok.col;
            }
            if (tok !== false)
              parserCache.addEvent(e);
            super.fire(e);
          }
          parse(input, { reuseCache } = {}) {
            const stream = this.stream = new TokenStream(input);
            const opts = this.options;
            const atAny = !opts.globalsOnly && this._unknownAtRule;
            const atFuncs = !atAny ? Parser.GLOBALS : opts.topDocOnly ? Parser.AT_TDO : Parser.AT;
            parserCache.start(reuseCache && this);
            this.fire("startstylesheet");
            for (let ti, fn, tok; ti = (tok = stream.grab()).id; ) {
              try {
                if (ti === AT && (fn = atFuncs[tok.atName] || atAny)) {
                  fn.call(this, stream, tok);
                } else if (ti === Tokens.CDCO) {
                } else if (!atAny) {
                  stream.unget();
                  break;
                } else if (!this._styleRule(stream, tok) && stream.grab().id) {
                  stream._failure();
                }
              } catch (ex) {
                if (ex === Parser.GLOBALS) {
                  break;
                }
                if (ex instanceof SyntaxError && !opts.strict) {
                  this.fire(assign({}, ex, { type: "error", error: ex }));
                } else {
                  ex.message = (ti = ex.stack).includes(fn = ex.message) ? ti : `${fn}
${ti}`;
                  ex.line = tok.line;
                  ex.col = tok.col;
                  throw ex;
                }
              }
            }
            this.fire("endstylesheet");
          }
          _condition(stream, tok = stream.grab(), fn) {
            if (B.not.has(tok)) {
              this._conditionInParens(stream, void 0, fn);
            } else {
              let more;
              do {
                this._conditionInParens(stream, tok, fn);
                tok = void 0;
              } while (more = stream.matchSmart(IDENT, !more ? B.andOr : B.or.has(more) ? B.or : B.and));
            }
          }
          _conditionInParens(stream, tok = stream.matchSmart(TT.condition), fn) {
            let x, reuse, paren;
            if (fn && fn.call(this, stream, tok)) {
            } else if (tok.name) {
              this._function(stream, tok);
              reuse = 0;
            } else if (tok.id === LPAREN && (paren = tok, tok = stream.matchSmart(TT.condition))) {
              if (fn && fn.call(this, stream, tok, paren)) {
              } else if (tok.id !== IDENT) {
                this._condition(stream, tok);
              } else if (B.not.has(tok)) {
                this._conditionInParens(stream);
              } else if ((x = stream.matchSmart(TT.colonLParen)).id === COLON) {
                this._declaration(stream, tok, { colon: x, inParens: true });
                return;
              } else if (x) {
                this._expr(stream, RPAREN, true);
                reuse = true;
              }
            }
            if (reuse !== 0)
              stream.matchSmart(RPAREN, { must: 1, reuse });
          }
          _containerCondition(stream, tok, paren) {
            if (paren && tok.id === IDENT) {
              stream.unget();
              this._mediaExpression(stream, paren);
            } else if (!paren && tok.name === "style") {
              this._condition(stream, { id: LPAREN });
            } else {
              return;
            }
            stream.unget();
            return true;
          }
          _layerName(stream, start) {
            let res = "";
            let tok;
            while (tok = !res && start || (res ? stream.match(IDENT) : stream.matchSmart(IDENT))) {
              res += tok.text;
              if (stream.match(DOT))
                res += ".";
              else
                break;
            }
            return res;
          }
          _margin(stream, start) {
            this._block(stream, start, {
              decl: true,
              event: ["pagemargin", { margin: start }]
            });
          }
          _mediaExpression(stream, start = stream.grab()) {
            if (start.id !== LPAREN)
              stream._failure(LPAREN);
            const feature = stream.matchSmart(TT.mediaValue, OrDie);
            feature.expr = this._expr(stream, RPAREN, true);
            feature.offset2 = stream.token.offset2;
            stream.matchSmart(RPAREN, OrDieReusing);
            return feature;
          }
          _mediaQueryList(stream, tok) {
            const list = [];
            while (tok = stream.matchSmart(TT.mediaList, { reuse: tok })) {
              const expr = [];
              const mod = B.notOnly.has(tok) && tok;
              const next = mod ? stream.matchSmart(TT.mediaList, OrDie) : tok;
              const type = next.id === IDENT && next;
              if (!type)
                expr.push(this._mediaExpression(stream, next));
              for (let more; stream.matchSmart(IDENT, more || (type ? B.and : B.andOr)); ) {
                if (!more)
                  more = B.and.has(stream.token) ? B.and : B.or;
                expr.push(this._mediaExpression(stream));
              }
              tok = TokenValue.from(expr, mod || next);
              tok.type = type;
              list.push(tok);
              if (!stream.matchSmart(COMMA))
                break;
              tok = null;
            }
            return list;
          }
          _supportsCondition(stream, tok, paren) {
            if (!paren && tok.name === "selector") {
              tok = this._selector(stream);
              stream.unget();
              this.fire({ type: "supportsSelector", selector: tok }, tok);
              return true;
            }
          }
          _unknownAtRule(stream, start) {
            if (this.options.strict)
              throw new SyntaxError("Unknown rule: " + start, start);
            stream.skipDeclBlock();
          }
          _selectorsGroup(stream, tok, relative) {
            const selectors = [];
            let comma;
            while (tok = this._selector(stream, tok, relative)) {
              selectors.push(tok);
              if ((tok = stream.token).isVar)
                tok = stream.grab();
              if (!(comma = tok.id === COMMA))
                break;
              tok = relative = void 0;
            }
            if (comma)
              stream._failure();
            if (selectors[0])
              return selectors;
          }
          _selector(stream, tok, relative) {
            const sel = [];
            if (!tok || tok.isVar) {
              tok = stream.grab();
            }
            if (!relative || !isOwn(TT.combinator, tok.id)) {
              tok = this._simpleSelectorSequence(stream, tok);
              if (!tok)
                return;
              sel.push(tok);
              tok = null;
            }
            for (let combinator, ws; ; tok = null) {
              if (!tok)
                tok = stream.token;
              if (isOwn(TT.combinator, tok.id)) {
                sel.push(this._combinator(stream, tok));
                sel.push(this._simpleSelectorSequence(stream) || stream._failure());
                continue;
              }
              while (tok.isVar)
                tok = stream.get();
              ws = tok.id === WS && tok;
              if (!ws)
                break;
              tok = stream.grab();
              if (tok.id === LBRACE)
                break;
              combinator = isOwn(TT.combinator, tok.id) && this._combinator(stream, tok);
              tok = this._simpleSelectorSequence(stream, combinator ? void 0 : tok);
              if (tok) {
                sel.push(combinator || this._combinator(stream, ws));
                sel.push(tok);
              } else if (combinator) {
                stream._failure();
              }
            }
            return TokenValue.from(sel);
          }
          _simpleSelectorSequence(stream, start = stream.grab()) {
            let si = start.id;
            if (!isOwn(TT.selectorStart, si))
              return;
            let ns, tag, t2;
            let tok = start;
            const mods = [];
            while (si === AMP) {
              mods.push(Parser.SELECTOR[AMP](stream, tok));
              si = (tok = stream.get()).id;
            }
            if (si === PIPE || (si === STAR || si === IDENT) && (t2 = stream.get()).id === PIPE) {
              ns = t2 ? tok : "";
              tok = null;
            } else if (t2) {
              tag = tok;
              tok = t2;
            }
            if (ns && !(tag = stream.match(TT.identStar))) {
              if (si !== PIPE)
                stream.unget();
              return;
            }
            while (true) {
              if (!tok)
                tok = stream.get();
              const fn = Parser.SELECTOR[tok.id];
              if (!(tok = fn && fn.call(this, stream, tok)))
                break;
              mods.push(tok);
              tok = false;
            }
            tok = Token.from(start);
            tok.ns = ns;
            tok.elementName = tag || "";
            tok.modifiers = mods;
            tok.offset2 = (mods[mods.length - 1] || tok).offset2;
            return tok;
          }
          _combinator(stream, tok = stream.matchSmart(TT.combinator)) {
            if (tok)
              tok.type = Combinators[tok.code] || "unknown";
            return tok;
          }
          _declaration(stream, tok, { colon, inParens, scope } = {}) {
            if (inParens && tok.id !== IDENT)
              return;
            if (tok.isVar)
              return true;
            const opts = this.options;
            const hack = tok.hack ? (tok = stream.match(IDENT), tok.col--, tok.offset--, "*") : tok.code === 95 && opts.underscoreHack && "_";
            if (hack) {
              tok.hack = hack;
              PDESC.value = tok.text.slice(1);
              define(tok, "text", PDESC);
              PDESC.value = toStringPropHack;
              define(tok, "toString", PDESC);
            }
            if (!colon && !stream.match(COLON)) {
              if (inParens || !this._inStyle) {
                stream._failure('":"', stream.get());
              } else {
                stream._failure("prop:value or a selector that is not a tag");
              }
            }
            const isCust = tok.type === "custom-prop";
            const end = isCust ? TT.propCustomEnd : inParens ? TT.propValEndParen : TT.propValEnd;
            const value = this._expr(stream, end, isCust) || isCust && TokenValue.empty(stream.token) || stream._failure("");
            const invalid = !isCust && !opts.noValidation && validateProperty(tok, value, stream, scope);
            const important = stream.token.id === DELIM && stream.matchSmart(IDENT, { must: 1, text: B.important });
            const ti = stream.matchSmart(inParens ? RPAREN : TT.declEnd, { must: 1, reuse: !important }).id;
            this.fire({
              type: "property",
              property: tok,
              message: invalid && invalid.message,
              important,
              inParens,
              invalid,
              scope,
              value
            }, tok);
            if (ti === RBRACE)
              stream.unget();
            return ti;
          }
          _declarationFailed(stream, err, inBlock) {
            stream.skipDeclBlock(inBlock);
            this.fire(assign({}, err, {
              type: err.type || "error",
              recoverable: !stream.source.eof(),
              error: err
            }));
          }
          _expr(stream, end, dumb) {
            const parts = [];
            const isEndMap = typeof end === "object";
            let tok, ti, isVar, endParen;
            while ((ti = (tok = stream.get(UVAR)).id) && !(isEndMap ? end[ti] : end === ti)) {
              if (endParen = Parens[ti]) {
                tok.expr = this._expr(stream, endParen, dumb || ti === LBRACE);
                if (stream.token.id !== endParen)
                  stream._failure(endParen);
                tok.offset2 = stream.token.offset2;
                tok.type = "block";
              } else if (ti === FUNCTION) {
                if (!tok.ie || this.options.ieFilters) {
                  tok = this._function(stream, tok, dumb);
                  isVar = isVar || tok.isVar;
                }
              } else if (ti === UVAR) {
                isVar = true;
              } else if (dumb) {
              } else if (ti === HASH) {
                this._hexcolor(stream, tok);
              } else if (ti === IDENT && !tok.type) {
                if (B.autoNone.has(tok)) {
                  tok[tok.code === 97 ? "isAuto" : "isNone"] = true;
                  tok.type = "ident";
                } else {
                  tok.type = B.colors.has(tok) ? "color" : "ident";
                }
              }
              parts.push(tok);
            }
            if (parts[0]) {
              const res = TokenValue.from(parts);
              if (isVar)
                res.isVar = true;
              return res;
            }
          }
          _function(stream, tok, dumb) {
            return TokenFunc.from(tok, this._expr(stream, RPAREN, dumb), stream.token);
          }
          _hexcolor(stream, tok) {
            let text, len, offset, i, c;
            if ((len = tok.length) === 4 || len === 5 || len === 7 || len === 9) {
              if (isOwn(tok, "text"))
                text = (offset = 0, tok.text);
              else
                ({ _input: text, offset } = tok);
              for (i = 1; i < len; i++) {
                c = text.charCodeAt(offset + i);
                if ((c < 48 || c > 57) && (c < 65 || c > 70) && (c < 97 || c > 102))
                  break;
              }
            }
            if (i === len)
              tok.type = "color";
            else
              this.alarm(1, `Expected a hex color but found "${clipString(tok)}".`, tok);
          }
          _styleRule(stream, tok, opts) {
            if (!this._inStyle && parserCache.findBlock(tok)) {
              return true;
            }
            let blk, brace;
            try {
              const amps = tok.id === AMP ? -1 : stream._amp;
              const sels = this._selectorsGroup(stream, tok, true);
              if (!sels) {
                stream.unget();
                return;
              }
              if (!this._inStyle && (stream._amp > amps || sels.some(isRelativeSelector))) {
                this.alarm(2, "Nested selector must be inside a style rule.", tok);
              }
              brace = stream.matchSmart(LBRACE, OrDieReusing);
              blk = parserCache.startBlock(sels[0]);
              const msg = { selectors: sels };
              const opts2 = { brace, decl: true, event: ["rule", msg] };
              this._block(stream, sels[0], opts ? assign({}, opts, opts2) : opts2);
              if (!msg.empty) {
                parserCache.endBlock();
                blk = 0;
              }
            } catch (ex) {
              if (this.options.strict || !(ex instanceof SyntaxError))
                throw ex;
              this._declarationFailed(stream, ex, !!brace);
            } finally {
              if (blk)
                parserCache.cancelBlock(blk);
            }
            return true;
          }
          _block(stream, start, opts = {}) {
            const { margins, scoped, decl, event = [] } = opts;
            const { brace = stream.matchSmart(LBRACE, OrDie) } = opts;
            const [type, msg = event[1] = {}] = event || [];
            const declOpts = scoped ? { scope: start.atName } : {};
            const inStyle = this._inStyle += decl ? 1 : 0;
            const star = inStyle && this.options.starHack && STAR;
            this._stack.push(start);
            let ex, child;
            if (type)
              this.fire(assign({ type: "start" + type, brace }, msg), start);
            for (let tok, ti, fn; (ti = (tok = stream.get(UVAR)).id) !== RBRACE; ex = null) {
              if (ti === SEMICOLON || ti === UVAR && (child = 1)) {
                continue;
              }
              try {
                if (ti === AT) {
                  fn = tok.atName;
                  fn = margins && B.marginSyms.has(fn) && this._margin || Parser.AT[fn] || this._unknownAtRule;
                  fn.call(this, stream, tok);
                  child = 1;
                } else if (inStyle && (ti === IDENT || ti === star && tok.hack) ? this._declaration(stream, tok, declOpts) : !scoped && (!inStyle || isOwn(TT.nestSel, ti)) && this._styleRule(stream, tok, opts)) {
                  child = 1;
                } else {
                  ex = stream._failure("", tok, false);
                }
              } catch (e) {
                ex = e;
              }
              if (ex) {
                if (this.options.strict || !(ex instanceof SyntaxError))
                  break;
                if (inStyle) {
                  this._declarationFailed(stream, ex);
                  ex = null;
                }
                if (!ti)
                  break;
              }
            }
            this._stack.pop();
            if (decl)
              this._inStyle--;
            if (ex)
              throw ex;
            if (type) {
              msg.empty = !child;
              this.fire(assign({ type: "end" + type }, msg));
            }
          }
        }
        Parser.AT = {
          __proto__: null,
          charset(stream, start) {
            const charset = stream.matchSmart(Tokens.STRING, OrDie);
            stream.matchSmart(SEMICOLON, OrDie);
            this.fire({ type: "charset", charset }, start);
          },
          container(stream, start) {
            const tok = stream.matchSmart(IDENT);
            const name = B.not.has(tok) ? stream.unget() : tok;
            this._condition(stream, void 0, this._containerCondition);
            this._block(stream, start, { event: ["container", { name }] });
          },
          document(stream, start) {
            if (this._stack[0])
              this.alarm(2, "Nested @document produces broken code", start);
            const functions = [];
            do {
              const tok = stream.matchSmart(TT.docFunc);
              const fn = tok.uri ? TokenFunc.from(tok) : tok.name && this._function(stream, tok);
              if (fn && (fn.uri || fn.name === "regexp"))
                functions.push(fn);
              else
                this.alarm(1, "Unknown document function", fn);
            } while (stream.matchSmart(COMMA));
            const brace = stream.matchSmart(LBRACE, OrDie);
            this.fire({ type: "startdocument", brace, functions, start }, start);
            if (this.options.topDocOnly) {
              stream.skipDeclBlock(true);
              stream.matchSmart(RBRACE, OrDie);
            } else {
              this._block(stream, start, { brace: null });
            }
            this.fire({ type: "enddocument", start, functions });
          },
          "font-face"(stream, start) {
            this._block(stream, start, {
              decl: true,
              event: ["fontface", {}],
              scoped: true
            });
          },
          "font-palette-values"(stream, start) {
            this._block(stream, start, {
              decl: true,
              event: ["fontpalettevalues", { id: stream.matchSmart(IDENT, OrDie) }],
              scoped: true
            });
          },
          import(stream, start) {
            let layer, name, tok;
            const uri = (tok = stream.matchSmart(TT.stringUri, OrDie)).uri || tok.string;
            if ((name = (tok = stream.grab()).name) === "layer" || !name && B.layer.has(tok)) {
              layer = name ? this._layerName(stream) : "";
              if (name)
                stream.matchSmart(RPAREN, OrDie);
              name = (tok = stream.grab()).name;
            }
            if (name === "supports") {
              this._conditionInParens(stream, { id: LPAREN });
              tok = null;
            }
            const media = this._mediaQueryList(stream, tok);
            stream.matchSmart(SEMICOLON, OrDie);
            this.fire({ type: "import", layer, media, uri }, start);
          },
          keyframes(stream, start) {
            const prefix = start.vendorPos ? start.text.slice(0, start.vendorPos) : "";
            const name = stream.matchSmart(TT.identString, OrDie);
            stream.matchSmart(LBRACE, OrDie);
            this.fire({ type: "startkeyframes", name, prefix }, start);
            let tok, ti;
            while (true) {
              const keys = [];
              do {
                ti = (tok = stream.grab()).id;
                if (ti === PCT || ti === IDENT && B.fromTo.has(tok))
                  keys.push(tok);
                else if (!keys[0])
                  break;
                else
                  stream._failure('percentage%, "from", "to"', tok);
              } while ((ti = (tok = stream.grab()).id) === COMMA);
              if (!keys[0])
                break;
              this._block(stream, keys[0], {
                decl: true,
                brace: ti === LBRACE ? tok : stream.unget(),
                event: ["keyframerule", { keys }]
              });
            }
            if (ti !== RBRACE)
              stream.matchSmart(RBRACE, OrDie);
            this.fire({ type: "endkeyframes", name, prefix });
          },
          layer(stream, start) {
            const ids = [];
            let tok;
            do {
              if ((tok = stream.grab()).id === IDENT) {
                ids.push(this._layerName(stream, tok));
                tok = stream.grab();
              }
              if (tok.id === LBRACE) {
                if (this.options.globalsOnly) {
                  this.stream.token = start;
                  throw Parser.GLOBALS;
                }
                if (ids[1])
                  this.alarm(1, "@layer block cannot have multiple ids", start);
                this._block(stream, start, { brace: tok, event: ["layer", { id: ids[0] }] });
                return;
              }
            } while (tok.id === COMMA);
            stream.matchSmart(SEMICOLON, { must: 1, reuse: tok });
            this.fire({ type: "layer", ids }, start);
          },
          media(stream, start) {
            const media = this._mediaQueryList(stream);
            this._block(stream, start, { event: ["media", { media }] });
          },
          namespace(stream, start) {
            const prefix = stream.matchSmart(IDENT).text;
            const tok = stream.matchSmart(TT.stringUri, OrDie);
            const uri = tok.uri || tok.string;
            stream.matchSmart(SEMICOLON, OrDie);
            this.fire({ type: "namespace", prefix, uri }, start);
          },
          page(stream, start) {
            const tok = stream.matchSmart(IDENT);
            if (B.auto.has(tok))
              stream._failure();
            const id = tok.text;
            const pseudo = stream.match(COLON) && stream.matchOrDie(IDENT).text;
            this._block(stream, start, {
              decl: true,
              event: ["page", { id, pseudo }],
              margins: true,
              scoped: true
            });
          },
          property(stream, start) {
            const name = stream.matchSmart(IDENT, OrDie);
            this._block(stream, start, {
              decl: true,
              event: ["property", { name }],
              scoped: true
            });
          },
          supports(stream, start) {
            this._condition(stream, void 0, this._supportsCondition);
            this._block(stream, start, { event: ["supports"] });
          }
        };
        Parser.AT_TDO = pick(Parser.AT, ["document"]);
        Parser.GLOBALS = pick(Parser.AT, ["charset", "import", "layer", "namespace"]);
        Parser.SELECTOR = textToTokenMap({
          "&": (stream, tok) => assign(tok, { type: "amp", args: [] }),
          "#": (stream, tok) => assign(tok, { type: "id", args: [] }),
          "."(stream, tok) {
            const t2 = stream.matchOrDie(IDENT);
            if (isOwn(t2, "text"))
              tok.text = "." + t2.text;
            tok.offset2 = t2.offset2;
            tok.type = "class";
            return tok;
          },
          "["(stream, start) {
            const t1 = stream.matchSmart(TT.attrStart, OrDie);
            let t2, ns, name, eq, val, mod, end;
            if (t1.id === PIPE) {
              ns = t1;
            } else if (t1.id === STAR) {
              ns = t1;
              ns.offset2 = stream.matchOrDie(PIPE).offset2;
              if (ns.length > 2)
                ns.text = "*|";
            } else if ((t2 = stream.get()).id === PIPE) {
              ns = t1;
              ns.offset2++;
            } else if (isOwn(TT.attrEq, t2.id)) {
              name = t1;
              eq = t2;
            } else if (isOwn(TT.attrNameEnd, t2.id)) {
              name = t1;
              end = t2.id === RBRACKET && t2;
            } else {
              stream._failure('"]"', t2);
            }
            name = name || stream.matchOrDie(IDENT);
            if (!eq && !end) {
              if ((t2 = stream.matchSmart(TT.attrEqEnd, OrDie)).id === RBRACKET)
                end = t2;
              else
                eq = t2;
            }
            if (eq) {
              val = stream.matchSmart(TT.identString, OrDie);
              if ((t2 = stream.grab()).id === RBRACKET)
                end = t2;
              else if (B.attrIS.has(t2))
                mod = t2;
              else
                stream._failure(B.attrIS, t2);
            }
            start.args = [
              ns || "",
              name,
              eq || "",
              val || "",
              mod || ""
            ];
            start.type = "attribute";
            start.offset2 = (end || stream.matchSmart(RBRACKET, OrDie)).offset2;
            return start;
          },
          ":"(stream, tok) {
            const colons = stream.match(COLON) ? "::" : ":";
            tok = stream.matchOrDie(TT.pseudo);
            tok.col -= colons.length;
            tok.offset -= colons.length;
            tok.type = "pseudo";
            let expr, n, x;
            if (n = tok.name) {
              if (n === "nth-child" || n === "nth-last-child") {
                expr = stream.readNthChild();
                const t1 = stream.get();
                const t2 = t1.id === WS ? stream.grab() : t1;
                if (expr && B.of.has(t2))
                  n = "not";
                else if (t2.id === RPAREN)
                  x = true;
                else
                  stream._failure("", t1);
              }
              if (n === "not" || n === "is" || n === "where" || n === "any" || n === "has") {
                x = this._selectorsGroup(stream, void 0, n === "has");
                if (!x)
                  stream._failure("a selector");
                if (expr)
                  expr.push(...x);
                else
                  expr = x;
                stream.matchSmart(RPAREN, OrDieReusing);
              } else if (!x) {
                expr = this._expr(stream, RPAREN);
              }
              tok = TokenFunc.from(tok, expr, stream.token);
            }
            tok.args = expr && expr.parts || [];
            return tok;
          }
        });
        parserlib.css.Parser = Parser;
        parserlib.css.TokenStream = TokenStream;
        parserlib.util.cache = parserCache;
        if (typeof self !== "undefined")
          self.parserlib = parserlib;
        else
          module.exports = parserlib;
      })();
    }
  });

  // js/vendor/csslint/csslint.js
  var require_csslint = __commonJS({
    "js/vendor/csslint/csslint.js"(exports, module) {
      "use strict";
      var parserlib = typeof self !== "undefined" ? (require_parserlib_base(), self.parserlib) : require_parserlib();
      var Reporter = class {
        constructor(lines, ruleset, allow, ignore) {
          this.messages = [];
          this.stats = [];
          this.lines = lines;
          this.ruleset = ruleset;
          this.allow = allow || {};
          this.ignore = ignore || [];
        }
        error(message, { line = 1, col = 1 }, rule = {}) {
          this.messages.push({
            type: "error",
            evidence: this.lines[line - 1],
            line,
            col,
            message,
            rule
          });
        }
        report(message, { line = 1, col = 1 }, rule) {
          if (line in this.allow && rule.id in this.allow[line] || this.ignore.some((range) => range[0] <= line && line <= range[1])) {
            return;
          }
          this.messages.push({
            type: this.ruleset[rule.id] === 2 ? "error" : "warning",
            evidence: this.lines[line - 1],
            line,
            col,
            message,
            rule
          });
        }
        info(message, { line = 1, col = 1 }, rule) {
          this.messages.push({
            type: "info",
            evidence: this.lines[line - 1],
            line,
            col,
            message,
            rule
          });
        }
        rollupError(message, rule) {
          this.messages.push({
            type: "error",
            rollup: true,
            message,
            rule
          });
        }
        rollupWarn(message, rule) {
          this.messages.push({
            type: "warning",
            rollup: true,
            message,
            rule
          });
        }
        stat(name, value) {
          this.stats[name] = value;
        }
      };
      var CSSLint = (() => {
        const rxEmbedded = /\/\*\s*csslint\s+((?:[^*]+|\*(?!\/))+?)\*\//ig;
        const rxGrammarAbbr = /([-<])(int|len|num|pct|rel-(\w{3}))(?=\W)/g;
        const ABBR_MAP = {
          int: "integer",
          len: "length",
          num: "number",
          pct: "percentage",
          "rel-hsl": "h-s-l-alpha-none",
          "rel-hwb": "h-w-b-alpha-none",
          "rel-lab": "l-a-b-alpha-none",
          "rel-lch": "l-c-h-alpha-none",
          "rel-rgb": "r-g-b-alpha-none"
        };
        const unabbreviate = (_, c, str) => c + ABBR_MAP[str] || str;
        const EBMEDDED_RULE_VALUE_MAP = {
          "true": 2,
          "2": 2,
          "": 1,
          "1": 1,
          "false": 0,
          "0": 0
        };
        const rules = Object.create(null);
        let prevOverrides;
        return Object.assign(new parserlib.util.EventTarget(), {
          addRule: new Proxy(rules, {
            set(_, id, [rule, init]) {
              rules[id] = rule;
              rule.id = id;
              rule.init = init;
              return true;
            }
          }),
          rules,
          getRuleList() {
            return Object.values(rules).sort((a, b) => a.id < b.id ? -1 : a.id > b.id);
          },
          getRuleSet() {
            const ruleset = {};
            for (const id in rules)
              ruleset[id] = 1;
            return ruleset;
          },
          verify(text, ruleset = this.getRuleSet()) {
            const allow = {};
            const ignore = [];
            const emi = rxEmbedded.lastIndex = text.lastIndexOf("/*", text.indexOf("csslint", text.indexOf("/*") + 1 || text.length) + 1);
            if (emi >= 0) {
              ruleset = Object.assign({}, ruleset);
              applyEmbeddedOverrides(text, ruleset, allow, ignore);
            }
            const parser = new parserlib.css.Parser({
              starHack: true,
              ieFilters: true,
              underscoreHack: true,
              strict: false
            });
            const reporter = new Reporter([], ruleset, allow, ignore);
            const { messages } = reporter;
            const report = { messages };
            const newOvr = [ruleset, allow, ignore];
            const reuseCache = !prevOverrides || JSON.stringify(prevOverrides) === JSON.stringify(newOvr);
            prevOverrides = newOvr;
            ruleset.errors = 2;
            for (const [id, mode] of Object.entries(ruleset)) {
              const rule = mode && rules[id];
              if (rule)
                rule.init(rule, parser, reporter);
            }
            try {
              parser.parse(text, { reuseCache });
            } catch (ex) {
              reporter.error("Fatal error, cannot continue!\n" + ex.stack, ex, {});
            }
            messages.sort((a, b) => !!a.rollup - !!b.rollup || a.line - b.line || a.col - b.col);
            for (const msg of messages) {
              if ((rxGrammarAbbr.lastIndex = msg.message.indexOf("<")) >= 0) {
                msg.message = msg.message.replace(rxGrammarAbbr, unabbreviate);
              }
            }
            parserlib.util.cache.feedback(report);
            return report;
          }
        });
        function applyEmbeddedOverrides(text, ruleset, allow, ignore) {
          let ignoreStart = null;
          let ignoreEnd = null;
          let lineno = 0;
          let eol = -1;
          let m;
          while (m = rxEmbedded.exec(text)) {
            while (eol <= m.index) {
              eol = text.indexOf("\n", eol + 1);
              if (eol < 0)
                eol = text.length;
              lineno++;
            }
            const ovr = m[1].toLowerCase();
            const cmd = ovr.split(":", 1)[0];
            const i = cmd.length + 1;
            switch (cmd.trim()) {
              case "allow": {
                const allowRuleset = {};
                let num = 0;
                ovr.slice(i).split(",").forEach((allowRule) => {
                  allowRuleset[allowRule.trim()] = true;
                  num++;
                });
                if (num)
                  allow[lineno] = allowRuleset;
                break;
              }
              case "ignore":
                if (ovr.includes("start")) {
                  ignoreStart = ignoreStart || lineno;
                  break;
                }
                if (ovr.includes("end")) {
                  ignoreEnd = lineno;
                  if (ignoreStart && ignoreEnd) {
                    ignore.push([ignoreStart, ignoreEnd]);
                    ignoreStart = ignoreEnd = null;
                  }
                }
                break;
              default:
                ovr.slice(i).split(",").forEach((rule) => {
                  const pair = rule.split(":");
                  const property = pair[0] || "";
                  const value = pair[1] || "";
                  const mapped = EBMEDDED_RULE_VALUE_MAP[value.trim()];
                  ruleset[property.trim()] = mapped === void 0 ? 1 : mapped;
                });
            }
          }
          if (ignoreStart) {
            ignore.push([ignoreStart, lineno]);
          }
        }
      })();
      CSSLint.Util = {
        getPropName(prop) {
          const low = prop.lowText || (prop.lowText = prop.text.toLowerCase());
          const vp = prop.vendorPos;
          return vp ? low.slice(vp) : low;
        },
        registerRuleEvents(parser, { start, property, end }) {
          for (const e of [
            "fontface",
            "keyframerule",
            "page",
            "pagemargin",
            "rule",
            "viewport"
          ]) {
            if (start)
              parser.addListener("start" + e, start);
            if (end)
              parser.addListener("end" + e, end);
          }
          if (property)
            parser.addListener("property", property);
        },
        registerShorthandEvents(parser, { property, end }) {
          const { shorthands, shorthandsFor } = CSSLint.Util;
          let props, inRule;
          CSSLint.Util.registerRuleEvents(parser, {
            start() {
              inRule = true;
              props = null;
            },
            property(event) {
              if (!inRule || event.inParens)
                return;
              const name = CSSLint.Util.getPropName(event.property);
              const sh = shorthandsFor[name];
              if (sh) {
                if (!props)
                  props = {};
                (props[sh] || (props[sh] = {}))[name] = event;
              } else if (property && props && name in shorthands) {
                property(event, props, name);
              }
            },
            end(event) {
              inRule = false;
              if (end && props) {
                end(event, props);
              }
            }
          });
        },
        get shorthands() {
          const WSC = "width|style|color";
          const TBLR = "top|bottom|left|right";
          const shorthands = Object.create(null);
          const shorthandsFor = Object.create(null);
          for (const [sh, pattern, ...args] of [
            [
              "animation",
              "%-1",
              "name|duration|timing-function|delay|iteration-count|direction|fill-mode|play-state"
            ],
            ["background", "%-1", "image|size|position|repeat|origin|clip|attachment|color"],
            ["border", "%-1-2", TBLR, WSC],
            ["border-top", "%-1", WSC],
            ["border-left", "%-1", WSC],
            ["border-right", "%-1", WSC],
            ["border-bottom", "%-1", WSC],
            ["border-block-end", "%-1", WSC],
            ["border-block-start", "%-1", WSC],
            ["border-image", "%-1", "source|slice|width|outset|repeat"],
            ["border-inline-end", "%-1", WSC],
            ["border-inline-start", "%-1", WSC],
            ["border-radius", "border-1-2-radius", "top|bottom", "left|right"],
            ["border-color", "border-1-color", TBLR],
            ["border-style", "border-1-style", TBLR],
            ["border-width", "border-1-width", TBLR],
            ["column-rule", "%-1", WSC],
            ["columns", "column-1", "width|count"],
            ["flex", "%-1", "grow|shrink|basis"],
            ["flex-flow", "flex-1", "direction|wrap"],
            ["font", "%-style|%-variant|%-weight|%-stretch|%-size|%-family|line-height"],
            [
              "grid",
              "%-1",
              "template-rows|template-columns|template-areas|auto-rows|auto-columns|auto-flow|column-gap|row-gap"
            ],
            ["grid-area", "grid-1-2", "row|column", "start|end"],
            ["grid-column", "%-1", "start|end"],
            ["grid-gap", "grid-1-gap", "row|column"],
            ["grid-row", "%-1", "start|end"],
            ["grid-template", "%-1", "columns|rows|areas"],
            ["list-style", "list-1", "type|position|image"],
            ["margin", "%-1", TBLR],
            ["mask", "%-1", "image|mode|position|size|repeat|origin|clip|composite"],
            ["outline", "%-1", WSC],
            ["padding", "%-1", TBLR],
            ["text-decoration", "%-1", "color|style|line"],
            ["text-emphasis", "%-1", "style|color"],
            ["transition", "%-1", "delay|duration|property|timing-function"]
          ]) {
            let res = pattern.replace(/%/g, sh);
            args.forEach((arg, i) => {
              res = arg.replace(/[^|]+/g, res.replace(new RegExp(`${i + 1}`, "g"), "$$&"));
            });
            (shorthands[sh] = res.split("|")).forEach((r) => {
              shorthandsFor[r] = sh;
            });
          }
          Object.defineProperties(CSSLint.Util, {
            shorthands: { value: shorthands },
            shorthandsFor: { value: shorthandsFor }
          });
          return shorthands;
        },
        get shorthandsFor() {
          return CSSLint.Util.shorthandsFor || CSSLint.Util.shorthands && CSSLint.Util.shorthandsFor;
        }
      };
      CSSLint.addRule["box-model"] = [{
        name: "Beware of broken box size",
        desc: "Don't use width or height when using padding or border.",
        url: "https://github.com/CSSLint/csslint/wiki/Beware-of-box-model-size",
        browsers: "All"
      }, (rule, parser, reporter) => {
        const sizeProps = {
          width: ["border", "border-left", "border-right", "padding", "padding-left", "padding-right"],
          height: ["border", "border-bottom", "border-top", "padding", "padding-bottom", "padding-top"]
        };
        let properties = {};
        let boxSizing = false;
        let inRule;
        CSSLint.Util.registerRuleEvents(parser, {
          start() {
            inRule = true;
            properties = {};
            boxSizing = false;
          },
          property(event) {
            if (!inRule || event.inParens)
              return;
            const name = CSSLint.Util.getPropName(event.property);
            if (sizeProps.width.includes(name) || sizeProps.height.includes(name)) {
              if (!/^0+\D*$/.test(event.value) && (name !== "border" || !/^none$/i.test(event.value))) {
                properties[name] = {
                  line: event.property.line,
                  col: event.property.col,
                  value: event.value
                };
              }
            } else if (/^(width|height)/i.test(name) && /^(length|%)/.test(event.value.parts[0].type)) {
              properties[name] = 1;
            } else if (name === "box-sizing") {
              boxSizing = true;
            }
          },
          end() {
            inRule = false;
            if (boxSizing)
              return;
            for (const size in sizeProps) {
              if (!properties[size])
                continue;
              for (const prop of sizeProps[size]) {
                if (prop !== "padding" || !properties[prop])
                  continue;
                const { value: { parts }, line, col } = properties[prop];
                if (parts.length !== 2 || parts[0].number) {
                  reporter.report(`No box-sizing and ${size} in ${prop}`, { line, col }, rule);
                }
              }
            }
          }
        });
      }];
      CSSLint.addRule["compatible-vendor-prefixes"] = [{
        name: "Require compatible vendor prefixes",
        desc: "Include all compatible vendor prefixes to reach a wider range of users.",
        url: "https://github.com/CSSLint/csslint/wiki/Require-compatible-vendor-prefixes",
        browsers: "All"
      }, (rule, parser, reporter) => {
        const compatiblePrefixes = {
          "animation": "webkit",
          "animation-delay": "webkit",
          "animation-direction": "webkit",
          "animation-duration": "webkit",
          "animation-fill-mode": "webkit",
          "animation-iteration-count": "webkit",
          "animation-name": "webkit",
          "animation-play-state": "webkit",
          "animation-timing-function": "webkit",
          "appearance": "webkit moz",
          "border-end": "webkit moz",
          "border-end-color": "webkit moz",
          "border-end-style": "webkit moz",
          "border-end-width": "webkit moz",
          "border-image": "webkit moz o",
          "border-radius": "webkit",
          "border-start": "webkit moz",
          "border-start-color": "webkit moz",
          "border-start-style": "webkit moz",
          "border-start-width": "webkit moz",
          "box-align": "webkit moz",
          "box-direction": "webkit moz",
          "box-flex": "webkit moz",
          "box-lines": "webkit",
          "box-ordinal-group": "webkit moz",
          "box-orient": "webkit moz",
          "box-pack": "webkit moz",
          "box-sizing": "",
          "box-shadow": "",
          "column-count": "webkit moz ms",
          "column-gap": "webkit moz ms",
          "column-rule": "webkit moz ms",
          "column-rule-color": "webkit moz ms",
          "column-rule-style": "webkit moz ms",
          "column-rule-width": "webkit moz ms",
          "column-width": "webkit moz ms",
          "flex": "webkit ms",
          "flex-basis": "webkit",
          "flex-direction": "webkit ms",
          "flex-flow": "webkit",
          "flex-grow": "webkit",
          "flex-shrink": "webkit",
          "hyphens": "epub moz",
          "line-break": "webkit ms",
          "margin-end": "webkit moz",
          "margin-start": "webkit moz",
          "marquee-speed": "webkit wap",
          "marquee-style": "webkit wap",
          "padding-end": "webkit moz",
          "padding-start": "webkit moz",
          "tab-size": "moz o",
          "text-size-adjust": "webkit ms",
          "transform": "webkit ms",
          "transform-origin": "webkit ms",
          "transition": "",
          "transition-delay": "",
          "transition-duration": "",
          "transition-property": "",
          "transition-timing-function": "",
          "user-modify": "webkit moz",
          "user-select": "webkit moz ms",
          "word-break": "epub ms",
          "writing-mode": "epub ms"
        };
        const applyTo = [];
        let properties = [];
        let inKeyFrame = false;
        let started = 0;
        for (const prop in compatiblePrefixes) {
          const variations = compatiblePrefixes[prop].split(" ").map((s) => `-${s}-${prop}`);
          compatiblePrefixes[prop] = variations;
          applyTo.push(...variations);
        }
        parser.addListener("startrule", () => {
          started++;
          properties = [];
        });
        parser.addListener("startkeyframes", (event) => {
          started++;
          inKeyFrame = event.prefix || true;
          if (inKeyFrame && typeof inKeyFrame === "string") {
            inKeyFrame = "-" + inKeyFrame + "-";
          }
        });
        parser.addListener("endkeyframes", () => {
          started--;
          inKeyFrame = false;
        });
        parser.addListener("property", (event) => {
          if (!started)
            return;
          const name = event.property.text;
          if (inKeyFrame && typeof inKeyFrame === "string" && name.startsWith(inKeyFrame) || !applyTo.includes(name)) {
            return;
          }
          properties.push(event.property);
        });
        parser.addListener("endrule", () => {
          started = false;
          if (!properties.length)
            return;
          const groups = {};
          for (const name of properties) {
            for (const prop in compatiblePrefixes) {
              const variations = compatiblePrefixes[prop];
              if (!variations.includes(name.text)) {
                continue;
              }
              if (!groups[prop]) {
                groups[prop] = {
                  full: variations.slice(0),
                  actual: [],
                  actualNodes: []
                };
              }
              if (!groups[prop].actual.includes(name.text)) {
                groups[prop].actual.push(name.text);
                groups[prop].actualNodes.push(name);
              }
            }
          }
          for (const prop in groups) {
            const value = groups[prop];
            const actual = value.actual;
            const len = actual.length;
            if (value.full.length <= len)
              continue;
            for (const item of value.full) {
              if (!actual.includes(item)) {
                const spec = len === 1 ? actual[0] : len === 2 ? actual.join(" and ") : actual.join(", ");
                reporter.report(`"${item}" is compatible with ${spec} and should be included as well.`, value.actualNodes[0], rule);
              }
            }
          }
        });
      }];
      CSSLint.addRule["display-property-grouping"] = [{
        name: "Require properties appropriate for display",
        desc: "Certain properties shouldn't be used with certain display property values.",
        url: "https://github.com/CSSLint/csslint/wiki/Require-properties-appropriate-for-display",
        browsers: "All"
      }, (rule, parser, reporter) => {
        const propertiesToCheck = {
          "display": 1,
          "float": "none",
          "height": 1,
          "width": 1,
          "margin": 1,
          "margin-left": 1,
          "margin-right": 1,
          "margin-bottom": 1,
          "margin-top": 1,
          "padding": 1,
          "padding-left": 1,
          "padding-right": 1,
          "padding-bottom": 1,
          "padding-top": 1,
          "vertical-align": 1
        };
        let properties;
        let inRule;
        const reportProperty = (name, display, msg) => {
          const prop = properties[name];
          if (prop && propertiesToCheck[name] !== prop.value.toLowerCase()) {
            reporter.report(msg || `"${name}" can't be used with display: ${display}.`, prop, rule);
          }
        };
        CSSLint.Util.registerRuleEvents(parser, {
          start() {
            inRule = true;
            properties = {};
          },
          property(event) {
            if (!inRule || event.inParens)
              return;
            const name = CSSLint.Util.getPropName(event.property);
            if (name in propertiesToCheck) {
              properties[name] = {
                value: event.value.text,
                line: event.property.line,
                col: event.property.col
              };
            }
          },
          end() {
            inRule = false;
            const display = properties.display && properties.display.value;
            if (!display)
              return;
            switch (display.toLowerCase()) {
              case "inline":
                ["height", "width", "margin", "margin-top", "margin-bottom"].forEach((p) => reportProperty(p, display));
                reportProperty("float", display, '"display:inline" has no effect on floated elements (but may be used to fix the IE6 double-margin bug).');
                break;
              case "block":
                reportProperty("vertical-align", display);
                break;
              case "inline-block":
                reportProperty("float", display);
                break;
              default:
                if (/^table-/i.test(display)) {
                  ["margin", "margin-left", "margin-right", "margin-top", "margin-bottom", "float"].forEach((p) => reportProperty(p, display));
                }
            }
          }
        });
      }];
      CSSLint.addRule["duplicate-background-images"] = [{
        name: "Disallow duplicate background images",
        desc: "Every background-image should be unique. Use a common class for e.g. sprites.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-duplicate-background-images",
        browsers: "All"
      }, (rule, parser, reporter) => {
        const stack = {};
        parser.addListener("property", (event) => {
          if (!/^-(webkit|moz|ms|o)-background(-image)$/i.test(event.property.text)) {
            return;
          }
          for (const part of event.value.parts) {
            if (part.type !== "uri")
              continue;
            const uri = stack[part.uri];
            if (!uri) {
              stack[part.uri] = event;
            } else {
              reporter.report(`Background image "${part.uri}" was used multiple times, first declared at line ${uri.line}, col ${uri.col}.`, event, rule);
            }
          }
        });
      }];
      CSSLint.addRule["duplicate-properties"] = [{
        name: "Disallow duplicate properties",
        desc: "Duplicate properties must appear one after the other. Exact duplicates are always reported.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-duplicate-properties",
        browsers: "All"
      }, (rule, parser, reporter) => {
        let props, lastName, inRule;
        CSSLint.Util.registerRuleEvents(parser, {
          start() {
            inRule = true;
            props = {};
          },
          property(event) {
            if (!inRule || event.inParens)
              return;
            const property = event.property;
            const name = property.text.toLowerCase();
            const last = props[name];
            const dupValue = last === event.value.text;
            if (last && (lastName !== name || dupValue)) {
              reporter.report(`${dupValue ? "Duplicate" : "Ungrouped duplicate"} "${property}".`, event, rule);
            }
            props[name] = event.value.text;
            lastName = name;
          },
          end() {
            inRule = false;
          }
        });
      }];
      CSSLint.addRule["empty-rules"] = [{
        name: "Disallow empty rules",
        desc: "Rules without any properties specified should be removed.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-empty-rules",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("endrule", (event) => {
          if (event.empty)
            reporter.report("Empty rule.", event.selectors[0], rule);
        });
      }];
      CSSLint.addRule["errors"] = [{
        name: "Parsing Errors",
        desc: "This rule looks for recoverable syntax errors.",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("error", (e) => reporter.error(e.message, e, rule));
      }];
      CSSLint.addRule["floats"] = [{
        name: "Disallow too many floats",
        desc: "This rule tests if the float property too many times",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-too-many-floats",
        browsers: "All"
      }, (rule, parser, reporter) => {
        let count = 0;
        parser.addListener("property", ({ property, value }) => {
          count += CSSLint.Util.getPropName(property) === "float" && value.text.toLowerCase() !== "none";
        });
        parser.addListener("endstylesheet", () => {
          reporter.stat("floats", count);
          if (count >= 10) {
            reporter.rollupWarn(`Too many floats (${count}), you're probably using them for layout. Consider using a grid system instead.`, rule);
          }
        });
      }];
      CSSLint.addRule["font-faces"] = [{
        name: "Don't use too many web fonts",
        desc: "Too many different web fonts in the same stylesheet.",
        url: "https://github.com/CSSLint/csslint/wiki/Don%27t-use-too-many-web-fonts",
        browsers: "All"
      }, (rule, parser, reporter) => {
        let count = 0;
        parser.addListener("startfontface", () => count++);
        parser.addListener("endstylesheet", () => {
          if (count > 5) {
            reporter.rollupWarn(`Too many @font-face declarations (${count}).`, rule);
          }
        });
      }];
      CSSLint.addRule["font-sizes"] = [{
        name: "Disallow too many font sizes",
        desc: "Checks the number of font-size declarations.",
        url: "https://github.com/CSSLint/csslint/wiki/Don%27t-use-too-many-font-size-declarations",
        browsers: "All"
      }, (rule, parser, reporter) => {
        let count = 0;
        parser.addListener("property", (event) => {
          count += CSSLint.Util.getPropName(event.property) === "font-size";
        });
        parser.addListener("endstylesheet", () => {
          reporter.stat("font-sizes", count);
          if (count >= 10) {
            reporter.rollupWarn(`Too many font-size declarations (${count}), abstraction needed.`, rule);
          }
        });
      }];
      CSSLint.addRule["globals-in-document"] = [{
        name: "Warn about global @ rules inside @-moz-document",
        desc: "Warn about @import, @charset, @namespace inside @-moz-document",
        browsers: "All"
      }, (rule, parser, reporter) => {
        let level = 0;
        let index = 0;
        parser.addListener("startdocument", () => level++);
        parser.addListener("enddocument", () => level-- * index++);
        const check = (event) => {
          if (level && index) {
            reporter.report(`A nested @${event.type} is valid only if this @-moz-document section is the first one matched for any given URL.`, event, rule);
          }
        };
        parser.addListener("import", check);
        parser.addListener("charset", check);
        parser.addListener("namespace", check);
      }];
      CSSLint.addRule["gradients"] = [{
        name: "Require all gradient definitions",
        desc: "When using a vendor-prefixed gradient, make sure to use them all.",
        url: "https://github.com/CSSLint/csslint/wiki/Require-all-gradient-definitions",
        browsers: "All"
      }, (rule, parser, reporter) => {
        let miss;
        CSSLint.Util.registerRuleEvents(parser, {
          start() {
            miss = null;
          },
          property({ inParens, value: { parts: [p] } }) {
            if (inParens)
              return;
            if (p && p.prefix && /(-|^)gradient$/.test(p.name)) {
              if (!miss)
                miss = { "-moz-": 1, "-webkit-": 1 };
              delete miss[p.prefix];
            }
          },
          end(event) {
            if (miss && (miss = Object.keys(miss))[0]) {
              reporter.report(`Missing ${miss.join(",")} prefix${miss[1] ? "es" : ""} for gradient.`, event.selectors[0], rule);
            }
          }
        });
      }];
      CSSLint.addRule["ids"] = [{
        name: "Disallow IDs in selectors",
        desc: "Selectors should not contain IDs.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-IDs-in-selectors",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("startrule", (event) => {
          for (const sel of event.selectors) {
            const cnt = sel.parts.reduce((sum = 0, { modifiers }) => modifiers ? modifiers.reduce((sum2, mod) => sum2 + (mod.type === "id"), 0) : sum, 0);
            if (cnt) {
              reporter.report(`Id in selector${cnt > 1 ? "!".repeat(cnt) : "."}`, sel, rule);
            }
          }
        });
      }];
      CSSLint.addRule["import"] = [{
        name: "Disallow @import",
        desc: "Don't use @import, use <link> instead.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-%40import",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("import", (e) => {
          reporter.report("@import prevents parallel downloads, use <link> instead.", e, rule);
        });
      }];
      CSSLint.addRule["important"] = [{
        name: "Disallow !important",
        desc: "Be careful when using !important declaration",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-%21important",
        browsers: "All"
      }, (rule, parser, reporter) => {
        let count = 0;
        parser.addListener("property", (event) => {
          if (event.important) {
            count++;
            reporter.report("!important.", event, rule);
          }
        });
        parser.addListener("endstylesheet", () => {
          reporter.stat("important", count);
          if (count >= 10) {
            reporter.rollupWarn(`Too many !important declarations (${count}), try to use less than 10 to avoid specificity issues.`, rule);
          }
        });
      }];
      CSSLint.addRule["known-properties"] = [{
        name: "Require use of known properties",
        desc: "Properties should be known (per CSS specification) or be a vendor-prefixed property.",
        url: "https://github.com/CSSLint/csslint/wiki/Require-use-of-known-properties",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("property", (event) => {
          const inv = event.invalid;
          if (inv)
            reporter.report(inv.message, inv, rule);
        });
      }];
      CSSLint.addRule["known-pseudos"] = [{
        name: "Require use of known pseudo selectors",
        url: "https://developer.mozilla.org/docs/Learn/CSS/Building_blocks/Selectors/Pseudo-classes_and_pseudo-elements",
        browsers: "All"
      }, (rule, parser, reporter) => {
        const Func = 4;
        const FuncToo = 8;
        const WK = 16;
        const Moz = 32;
        const DEAD = 3735879680;
        const definitions = {
          "after": 1 + 2,
          "backdrop": 2,
          "before": 1 + 2,
          "cue": 2,
          "cue-region": 2,
          "file-selector-button": 2,
          "first-letter": 1 + 2,
          "first-line": 1 + 2,
          "grammar-error": 2,
          "highlight": 2 + Func,
          "marker": 2,
          "part": 2 + Func,
          "placeholder": 2 + Moz,
          "selection": 2 + Moz,
          "slotted": 2 + Func,
          "spelling-error": 2,
          "target-text": 2,
          "active": 1,
          "any-link": 1 + Moz + WK,
          "autofill": 1 + WK,
          "blank": 1,
          "checked": 1,
          "current": 1 + FuncToo,
          "default": 1,
          "defined": 1,
          "dir": 1 + Func,
          "disabled": 1,
          "drop": 1,
          "empty": 1,
          "enabled": 1,
          "first": 1,
          "first-child": 1,
          "first-of-type": 1,
          "focus": 1,
          "focus-visible": 1,
          "focus-within": 1,
          "fullscreen": 1,
          "future": 1,
          "has": 1 + Func,
          "host": 1 + FuncToo,
          "host-context": 1 + Func,
          "hover": 1,
          "in-range": 1,
          "indeterminate": 1,
          "invalid": 1,
          "is": 1 + Func,
          "lang": 1 + Func,
          "last-child": 1,
          "last-of-type": 1,
          "left": 1,
          "link": 1,
          "local-link": 1,
          "not": 1 + Func,
          "nth-child": 1 + Func,
          "nth-col": 1 + Func,
          "nth-last-child": 1 + Func,
          "nth-last-col": 1 + Func,
          "nth-last-of-type": 1 + Func,
          "nth-of-type": 1 + Func,
          "only-child": 1,
          "only-of-type": 1,
          "optional": 1,
          "out-of-range": 1,
          "past": 1,
          "paused": 1,
          "picture-in-picture": 1,
          "placeholder-shown": 1,
          "playing": 1,
          "read-only": 1,
          "read-write": 1,
          "required": 1,
          "right": 1,
          "root": 1,
          "scope": 1,
          "state": 1 + Func,
          "target": 1,
          "target-within": 1,
          "user-invalid": 1,
          "valid": 1,
          "visited": 1,
          "where": 1 + Func,
          "xr-overlay": 1,
          "corner-present": 1,
          "decrement": 1,
          "double-button": 1,
          "end": 1,
          "horizontal": 1,
          "increment": 1,
          "no-button": 1,
          "single-button": 1,
          "start": 1,
          "vertical": 1,
          "window-inactive": 1 + Moz
        };
        const definitionsPrefixed = {
          "any": 1 + Func + Moz + WK,
          "calendar-picker-indicator": 2 + WK,
          "clear-button": 2 + WK,
          "color-swatch": 2 + WK,
          "color-swatch-wrapper": 2 + WK,
          "date-and-time-value": 2 + WK,
          "datetime-edit": 2 + WK,
          "datetime-edit-ampm-field": 2 + WK,
          "datetime-edit-day-field": 2 + WK,
          "datetime-edit-fields-wrapper": 2 + WK,
          "datetime-edit-hour-field": 2 + WK,
          "datetime-edit-millisecond-field": 2 + WK,
          "datetime-edit-minute-field": 2 + WK,
          "datetime-edit-month-field": 2 + WK,
          "datetime-edit-second-field": 2 + WK,
          "datetime-edit-text": 2 + WK,
          "datetime-edit-week-field": 2 + WK,
          "datetime-edit-year-field": 2 + WK,
          "details-marker": 2 + WK + DEAD,
          "drag": 1 + WK,
          "drag-over": 1 + Moz,
          "file-upload-button": 2 + WK,
          "focus-inner": 2 + Moz,
          "focusring": 1 + Moz,
          "full-page-media": 1 + WK,
          "full-screen": 1 + Moz + WK,
          "full-screen-ancestor": 1 + Moz + WK,
          "inner-spin-button": 2 + WK,
          "input-placeholder": 1 + 2 + WK + Moz,
          "loading": 1 + Moz,
          "media-controls": 2 + WK,
          "media-controls-current-time-display": 2 + WK,
          "media-controls-enclosure": 2 + WK,
          "media-controls-fullscreen-button": 2 + WK,
          "media-controls-mute-button": 2 + WK,
          "media-controls-overlay-enclosure": 2 + WK,
          "media-controls-overlay-play-button": 2 + WK,
          "media-controls-panel": 2 + WK,
          "media-controls-play-button": 2 + WK,
          "media-controls-time-remaining-display": 2 + WK,
          "media-controls-timeline": 2 + WK,
          "media-controls-timeline-container": 2 + WK,
          "media-controls-toggle-closed-captions-button": 2 + WK,
          "media-controls-volume-slider": 2 + WK,
          "media-slider-container": 2 + WK,
          "media-slider-thumb": 2 + WK,
          "media-text-track-container": 2 + WK,
          "media-text-track-display": 2 + WK,
          "media-text-track-region": 2 + WK,
          "media-text-track-region-container": 2 + WK,
          "meter-bar": 2 + WK,
          "meter-even-less-good-value": 2 + WK,
          "meter-inner-element": 2 + WK,
          "meter-optimum-value": 2 + WK,
          "meter-suboptimum-value": 2 + WK,
          "outer-spin-button": 2 + WK,
          "progress-bar": 2 + WK,
          "progress-inner-element": 2 + WK,
          "progress-value": 2 + WK,
          "resizer": 2 + WK,
          "scrollbar": 2 + WK,
          "scrollbar-button": 2 + WK,
          "scrollbar-corner": 2 + WK,
          "scrollbar-thumb": 2 + WK,
          "scrollbar-track": 2 + WK,
          "scrollbar-track-piece": 2 + WK,
          "search-cancel-button": 2 + WK,
          "search-decoration": 2 + WK,
          "slider-container": 2 + WK,
          "slider-runnable-track": 2 + WK,
          "slider-thumb": 2 + WK,
          "textfield-decoration-container": 2 + WK
        };
        const rx = /^(:+)(?:-(\w+)-)?([^(]+)(\()?/i;
        const allowsFunc = Func + FuncToo;
        const allowsPrefix = WK + Moz;
        const checkSelector = ({ parts }) => {
          for (const { modifiers } of parts || []) {
            if (!modifiers)
              continue;
            for (const mod of modifiers) {
              if (mod.type === "pseudo") {
                const { text } = mod;
                const [all, colons, prefix, name, paren] = rx.exec(text.toLowerCase()) || 0;
                const defPrefixed = definitionsPrefixed[name];
                const def = definitions[name] || defPrefixed;
                for (const err of !def ? ["Unknown pseudo"] : [
                  colons.length > 1 ? !(def & 2) && "Must use : in" : !(def & 1) && all !== ":-moz-placeholder" && "Must use :: in",
                  paren ? !(def & allowsFunc) && "Unexpected ( in" : def & Func && "Must use ( after",
                  prefix ? (!(def & allowsPrefix) || prefix === "webkit" && !(def & WK) || prefix === "moz" && !(def & Moz)) && "Unexpected prefix in" : defPrefixed && `Must use ${def & WK && def & Moz && "-webkit- or -moz-" || def & WK && "-webkit-" || "-moz-"} prefix in`,
                  def & DEAD && "Deprecated"
                ]) {
                  if (err)
                    reporter.report(`${err} ${text.slice(0, all.length)}`, mod, rule);
                }
              } else if (mod.args) {
                mod.args.forEach(checkSelector);
              }
            }
          }
        };
        parser.addListener("startrule", (e) => e.selectors.forEach(checkSelector));
        parser.addListener("supportsSelector", (e) => checkSelector(e.selector));
      }];
      CSSLint.addRule["order-alphabetical"] = [{
        name: "Alphabetical order",
        desc: "Assure properties are in alphabetical order",
        browsers: "All"
      }, (rule, parser, reporter) => {
        let last, failed;
        CSSLint.Util.registerRuleEvents(parser, {
          start() {
            last = "";
            failed = false;
          },
          property(event) {
            if (event.inParens)
              return;
            if (!failed) {
              const name = CSSLint.Util.getPropName(event.property);
              if (name < last) {
                reporter.report(`Non-alphabetical order: "${name}".`, event, rule);
                failed = true;
              }
              last = name;
            }
          }
        });
      }];
      CSSLint.addRule["outline-none"] = [{
        name: "Disallow outline: none",
        desc: "Use of outline: none or outline: 0 should be limited to :focus rules.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-outline%3Anone",
        browsers: "All",
        tags: ["Accessibility"]
      }, (rule, parser, reporter) => {
        let lastRule;
        CSSLint.Util.registerRuleEvents(parser, {
          start(event) {
            lastRule = !event.selectors ? null : {
              line: event.line,
              col: event.col,
              selectors: event.selectors,
              propCount: 0,
              outline: false
            };
          },
          property(event) {
            if (!lastRule || event.inParens)
              return;
            lastRule.propCount++;
            if (CSSLint.Util.getPropName(event.property) === "outline" && /^(none|0)$/i.test(event.value)) {
              lastRule.outline = true;
            }
          },
          end() {
            const { outline, selectors, propCount } = lastRule || {};
            lastRule = null;
            if (!outline)
              return;
            if (!/:focus/i.test(selectors)) {
              reporter.report("Outlines should only be modified using :focus.", lastRule, rule);
            } else if (propCount === 1) {
              reporter.report("Outlines shouldn't be hidden unless other visual changes are made.", lastRule, rule);
            }
          }
        });
      }];
      CSSLint.addRule["overqualified-elements"] = [{
        name: "Disallow overqualified elements",
        desc: "Don't use classes or IDs with elements (a.foo or a#foo).",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-overqualified-elements",
        browsers: "All"
      }, (rule, parser, reporter) => {
        const classes = {};
        const report = (part, mod) => {
          reporter.report(`"${part}" is overqualified, just use "${mod}" without element name.`, part, rule);
        };
        parser.addListener("startrule", (event) => {
          for (const selector of event.selectors) {
            for (const part of selector.parts) {
              if (!part.modifiers)
                continue;
              for (const mod of part.modifiers) {
                if (part.elementName && mod.type === "id") {
                  report(part, mod);
                } else if (mod.type === "class") {
                  (classes[mod] || (classes[mod] = [])).push({ modifier: mod, part });
                }
              }
            }
          }
        });
        parser.addListener("endstylesheet", () => {
          for (const prop of Object.values(classes)) {
            const { part, modifier } = prop[0];
            if (part.elementName && prop.length === 1) {
              report(part, modifier);
            }
          }
        });
      }];
      CSSLint.addRule["qualified-headings"] = [{
        name: "Disallow qualified headings",
        desc: "Headings should not be qualified (namespaced).",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-qualified-headings",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("startrule", (event) => {
          for (const selector of event.selectors) {
            let first = true;
            for (const part of selector.parts) {
              const name = part.elementName;
              if (!first && name && /h[1-6]/.test(name)) {
                reporter.report(`Heading "${name}" should not be qualified.`, part, rule);
              }
              first = false;
            }
          }
        });
      }];
      CSSLint.addRule["regex-selectors"] = [{
        name: "Disallow selectors that look like regexs",
        desc: "Selectors that look like regular expressions are slow and should be avoided.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-selectors-that-look-like-regular-expressions",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("startrule", (event) => {
          for (const { parts } of event.selectors) {
            for (const { modifiers } of parts) {
              if (modifiers) {
                for (const mod of modifiers) {
                  const eq = mod.type === "attribute" && mod.args[2];
                  if (eq && eq.length === 2) {
                    reporter.report(`Slow attribute selector ${eq}.`, eq, rule);
                  }
                }
              }
            }
          }
        });
      }];
      CSSLint.addRule["selector-newline"] = [{
        name: "Disallow new-line characters in selectors",
        desc: "New line in selectors is likely a forgotten comma and not a descendant combinator.",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("startrule", (event) => {
          for (const { parts } of event.selectors) {
            for (let i = 0, p, pn; i < parts.length - 1 && (p = parts[i]); i++) {
              if (p.type === "descendant" && (pn = parts[i + 1]).line > p.line) {
                reporter.report("Line break in selector (forgot a comma?)", pn, rule);
              }
            }
          }
        });
      }];
      CSSLint.addRule["shorthand"] = [{
        name: "Require shorthand properties",
        desc: "Use shorthand properties where possible.",
        url: "https://github.com/CSSLint/csslint/wiki/Require-shorthand-properties",
        browsers: "All"
      }, (rule, parser, reporter) => {
        const { shorthands } = CSSLint.Util;
        CSSLint.Util.registerShorthandEvents(parser, {
          end(event, props) {
            for (const [sh, events] of Object.entries(props)) {
              const names = Object.keys(events);
              if (names.length === shorthands[sh].length) {
                const msg = `"${sh}" shorthand can replace "${names.join('" + "')}"`;
                names.forEach((n) => reporter.report(msg, events[n], rule));
              }
            }
          }
        });
      }];
      CSSLint.addRule["shorthand-overrides"] = [{
        name: "Avoid shorthands that override individual properties",
        desc: "Avoid shorthands like `background: foo` that follow individual properties like `background-image: bar` thus overriding them",
        browsers: "All"
      }, (rule, parser, reporter) => {
        CSSLint.Util.registerShorthandEvents(parser, {
          property(event, props, name) {
            const ovr = props[name];
            if (ovr) {
              delete props[name];
              reporter.report(`"${event.property}" overrides "${Object.keys(ovr).join('" + "')}" above.`, event, rule);
            }
          }
        });
      }];
      CSSLint.addRule["simple-not"] = [{
        name: "Require use of simple selectors inside :not()",
        desc: "A complex selector inside :not() is only supported by CSS4-compliant browsers.",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("startrule", (e) => {
          let pp, p;
          for (const sel of e.selectors) {
            for (const part of sel.parts) {
              if (!part.modifiers)
                continue;
              for (const { name, args } of part.modifiers) {
                if (name === "not" && args[0] && (args[1] || (pp = args[0].parts)[1] || (p = pp[0]).modifiers.length + (p.elementName ? 1 : 0) > 1))
                  reporter.report("Complex selector inside :not().", args[0], rule);
              }
            }
          }
        });
      }];
      CSSLint.addRule["star-property-hack"] = [{
        name: "Disallow properties with a star prefix",
        desc: "Checks for the star property hack (targets IE6/7)",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-star-hack",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("property", ({ property }) => {
          if (property.hack === "*") {
            reporter.report("IE star prefix.", property, rule);
          }
        });
      }];
      CSSLint.addRule["text-indent"] = [{
        name: "Disallow negative text-indent",
        desc: "Checks for text indent less than -99px",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-negative-text-indent",
        browsers: "All"
      }, (rule, parser, reporter) => {
        let textIndent, isLtr;
        CSSLint.Util.registerRuleEvents(parser, {
          start() {
            textIndent = false;
            isLtr = false;
          },
          property(event) {
            if (event.inParens)
              return;
            const name = CSSLint.Util.getPropName(event.property);
            const value = event.value;
            if (name === "text-indent" && value.parts[0].number < -99) {
              textIndent = event.property;
            } else if (name === "direction" && /^ltr$/i.test(value)) {
              isLtr = true;
            }
          },
          end() {
            if (textIndent && !isLtr) {
              reporter.report(`Negative "text-indent" doesn't work well with RTL. If you use "text-indent" for image replacement, explicitly set "direction" for that item to "ltr".`, textIndent, rule);
            }
          }
        });
      }];
      CSSLint.addRule["underscore-property-hack"] = [{
        name: "Disallow properties with an underscore prefix",
        desc: "Checks for the underscore property hack (targets IE6)",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-underscore-hack",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("property", ({ property }) => {
          if (property.hack === "_") {
            reporter.report("IE underscore prefix.", property, rule);
          }
        });
      }];
      CSSLint.addRule["unique-headings"] = [{
        name: "Headings should only be defined once",
        desc: "Headings should be defined only once.",
        url: "https://github.com/CSSLint/csslint/wiki/Headings-should-only-be-defined-once",
        browsers: "All"
      }, (rule, parser, reporter) => {
        const headings = new Array(6).fill(0);
        parser.addListener("startrule", (event) => {
          for (const { parts } of event.selectors) {
            const p = parts[parts.length - 1];
            if (/h([1-6])/i.test(p.elementName) && !p.modifiers.some((mod) => mod.type === "pseudo") && ++headings[RegExp.$1 - 1] > 1) {
              reporter.report(`Heading ${p.elementName} has already been defined.`, p, rule);
            }
          }
        });
        parser.addListener("endstylesheet", () => {
          const stats = headings.filter((h) => h > 1).map((h, i) => `${h} H${i + 1}s`);
          if (stats.length) {
            reporter.rollupWarn(stats.join(", "), rule);
          }
        });
      }];
      CSSLint.addRule["universal-selector"] = [{
        name: "Disallow universal selector",
        desc: "The universal selector (*) is known to be slow.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-universal-selector",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("startrule", (event) => {
          for (const { parts } of event.selectors) {
            const part = parts[parts.length - 1];
            if (part.elementName === "*") {
              reporter.report(rule.desc, part, rule);
            }
          }
        });
      }];
      CSSLint.addRule["unqualified-attributes"] = [{
        name: "Disallow unqualified attribute selectors",
        desc: "Unqualified attribute selectors are known to be slow.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-unqualified-attribute-selectors",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("startrule", (event) => {
          event.selectors.forEach(({ parts }) => {
            const part = parts[parts.length - 1];
            const mods = part.modifiers;
            if (mods && (part.elementName || "*") === "*") {
              let attr;
              for (const m of mods) {
                if (m.type === "class" || m.type === "id")
                  return;
                if (m.type === "attribute")
                  attr = m;
              }
              if (attr)
                reporter.report(rule.desc, attr, rule);
            }
          });
        });
      }];
      CSSLint.addRule["vendor-prefix"] = [{
        name: "Require standard property with vendor prefix",
        desc: "When using a vendor-prefixed property, make sure to include the standard one.",
        url: "https://github.com/CSSLint/csslint/wiki/Require-standard-property-with-vendor-prefix",
        browsers: "All"
      }, (rule, parser, reporter) => {
        const propertiesToCheck = {
          "-webkit-border-radius": "border-radius",
          "-webkit-border-top-left-radius": "border-top-left-radius",
          "-webkit-border-top-right-radius": "border-top-right-radius",
          "-webkit-border-bottom-left-radius": "border-bottom-left-radius",
          "-webkit-border-bottom-right-radius": "border-bottom-right-radius",
          "-o-border-radius": "border-radius",
          "-o-border-top-left-radius": "border-top-left-radius",
          "-o-border-top-right-radius": "border-top-right-radius",
          "-o-border-bottom-left-radius": "border-bottom-left-radius",
          "-o-border-bottom-right-radius": "border-bottom-right-radius",
          "-moz-border-radius": "border-radius",
          "-moz-border-radius-topleft": "border-top-left-radius",
          "-moz-border-radius-topright": "border-top-right-radius",
          "-moz-border-radius-bottomleft": "border-bottom-left-radius",
          "-moz-border-radius-bottomright": "border-bottom-right-radius",
          "-moz-column-count": "column-count",
          "-webkit-column-count": "column-count",
          "-moz-column-gap": "column-gap",
          "-webkit-column-gap": "column-gap",
          "-moz-column-rule": "column-rule",
          "-webkit-column-rule": "column-rule",
          "-moz-column-rule-style": "column-rule-style",
          "-webkit-column-rule-style": "column-rule-style",
          "-moz-column-rule-color": "column-rule-color",
          "-webkit-column-rule-color": "column-rule-color",
          "-moz-column-rule-width": "column-rule-width",
          "-webkit-column-rule-width": "column-rule-width",
          "-moz-column-width": "column-width",
          "-webkit-column-width": "column-width",
          "-webkit-column-span": "column-span",
          "-webkit-columns": "columns",
          "-moz-box-shadow": "box-shadow",
          "-webkit-box-shadow": "box-shadow",
          "-moz-transform": "transform",
          "-webkit-transform": "transform",
          "-o-transform": "transform",
          "-ms-transform": "transform",
          "-moz-transform-origin": "transform-origin",
          "-webkit-transform-origin": "transform-origin",
          "-o-transform-origin": "transform-origin",
          "-ms-transform-origin": "transform-origin",
          "-moz-box-sizing": "box-sizing",
          "-webkit-box-sizing": "box-sizing"
        };
        let properties, num, inRule;
        CSSLint.Util.registerRuleEvents(parser, {
          start() {
            inRule = true;
            properties = {};
            num = 1;
          },
          property(event) {
            if (!inRule || event.inParens)
              return;
            const name = CSSLint.Util.getPropName(event.property);
            let prop = properties[name];
            if (!prop)
              prop = properties[name] = [];
            prop.push({
              name: event.property,
              value: event.value,
              pos: num++
            });
          },
          end() {
            inRule = false;
            const needsStandard = [];
            for (const prop in properties) {
              if (prop in propertiesToCheck) {
                needsStandard.push({
                  actual: prop,
                  needed: propertiesToCheck[prop]
                });
              }
            }
            for (const { needed, actual } of needsStandard) {
              const unit = properties[actual][0].name;
              if (!properties[needed]) {
                reporter.report(`Missing standard property "${needed}" to go along with "${actual}".`, unit, rule);
              } else if (properties[needed][0].pos < properties[actual][0].pos) {
                reporter.report(`Standard property "${needed}" should come after vendor-prefixed property "${actual}".`, unit, rule);
              }
            }
          }
        });
      }];
      CSSLint.addRule["warnings"] = [{
        name: "Parsing warnings",
        desc: "This rule looks for parser warnings.",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("warning", (e) => reporter.report(e.message, e, rule));
      }];
      CSSLint.addRule["zero-units"] = [{
        name: "Disallow units for 0 values",
        desc: "You don't need to specify units when a value is 0.",
        url: "https://github.com/CSSLint/csslint/wiki/Disallow-units-for-zero-values",
        browsers: "All"
      }, (rule, parser, reporter) => {
        parser.addListener("property", (event) => {
          for (const p of event.value.parts) {
            if (p.is0 && p.units && p.type !== "time") {
              reporter.report('"0" value with redundant units.', p, rule);
            }
          }
        });
      }];
      if (typeof self !== "undefined")
        self.CSSLint = CSSLint;
      else
        module.exports = CSSLint;
    }
  });

  // js/vendor/csslint/csslint.export.js
  var csslint_export_exports = {};
  __reExport(csslint_export_exports, __toESM(require_csslint()));
  __reExport(csslint_export_exports, __toESM(require_parserlib()));
})();
