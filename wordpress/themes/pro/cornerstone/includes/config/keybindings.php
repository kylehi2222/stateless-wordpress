<?php

/**
 * Can be modifed via this filter: cornerstone_keybindings
 *
 * For single keys, you can add a prefix to ensure a behavior
 * keydown:
 * keyup:
 *
 * Be careful. Everything is bound as "Global" meaning it will take
 * effect even is a user is working in a textarea or text input.
 */

return apply_filters('cornerstone_keybindings', array(
  'save'                   => [ 'mod+s',        __( 'Save', 'cornerstone' ) ],
  'undo'                   => [ 'mod+z',        __( 'Undo', 'cornerstone' ) ],
  'redo'                   => [ 'mod+shift+z',  __( 'Redo', 'cornerstone' ) ],
  'search-documents'       => [ 'mod+shift+f',  __( 'Search Documents', 'cornerstone' ) ],
  'workspace-outline'      => [ 'mod+option+1', __( 'Outline', 'cornerstone' ) ],
  'workspace-inspector'    => [ 'mod+option+2', __( 'Inspector', 'cornerstone' ) ],
  'workspace-elements'     => [ 'mod+option+3', __( 'Element Library', 'cornerstone' ) ],
  'workspace-templates'    => [ 'mod+option+4', __( 'Settings', 'cornerstone' ) ],
  'workspace-global'       => [ 'mod+option+5', __( 'Theme Options', 'cornerstone' ) ],
  'goto-fonts'             => [ 'mod+option+t', __( 'Open Fonts ', 'cornerstone' ) ],
  'goto-colors'            => [ 'mod+option+k', __( 'Open Colors ', 'cornerstone' ) ],
  'toggle-full-collapse'   => [ 'mod+shift+a',  __( 'Hide/Show UI', 'cornerstone' ) ],
  'inspector-breakout-mode'=> [ 'mod+alt+b',    __( 'Inspector Breakout Mode', 'cornerstone' ) ],
  'delete'                 => [ 'delete',       __( 'Delete Element', 'cornerstone' ) ],
  'duplicate'              => [ 'mod+d',        __( 'Duplicate Element', 'cornerstone' ) ],
  'copy'                   => [ 'mod+c',        __( 'Copy Element', 'cornerstone' ) ],
  'paste'                  => [ 'mod+v',        __( 'Paste Element', 'cornerstone' ) ],
  'paste-style'            => [ 'mod+shift+v',  __( 'Paste Element Style', 'cornerstone' ) ],
  'find'                   => [ 'mod+f',        __( 'Find (focus available search)', 'cornerstone' ) ],
  'esc'                    => [ 'esc',          __( 'Close Open Window', 'cornerstone' ) ],

  // Window openers
  'open:element-manager'   => [ 'mod+option+m', __( 'Open Element Manager', 'cornerstone' ) ],
  'open:dev-toolkit'       => [ 'mod+option+d', __( 'Open Dev Toolkit', 'cornerstone' ) ],
  'open:code-editors'      => [ 'mod+option+c', __( 'Open Code Editors', 'cornerstone' ) ],
  'open:max-manager'       => [ 'mod+option+x', __( 'Open Max', 'cornerstone' ) ],

  'open:parameters-json-theme-options' => [ 'mod+option+p', __( 'Open Global Parameters', 'cornerstone' ) ],
  'open:parameters-elements-json' => [ 'mod+option+j', __( 'Open Element Parameters', 'cornerstone' ) ],

  'open:action-history' => [ 'mod+option+h', __( 'Open Action History', 'cornerstone' ) ],
) );
