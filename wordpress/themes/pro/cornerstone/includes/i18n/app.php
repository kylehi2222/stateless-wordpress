<?php

// =============================================================================
// CORNERSTONE/INCLUDES/I18N/APP.PHP
// -----------------------------------------------------------------------------
// Localization strings.
// =============================================================================

// =============================================================================
// TABLE OF CONTENTS
// -----------------------------------------------------------------------------
//   01. Messaging
//   02. General Purpose
//   03. Status
//   04. Accessibility
//   05. Formatting
//   06. Breakpoints
//   07. Titles
//   08. Content
//   09. Inspector
//   10. Elements
//   11. Forms
//   12. Controls
//   13. Notifications
//   14. Assignments
//   15. Presets
//   16. Confirm
//   17. Options
//   18. Manage
//   19. Templates
//   20. Design Cloud
//   21. Fonts
//   22. Colors
//   23. Responsive Text
//   24. Font Weights
//   25. Custom Code
//   26. Choices
//   27. Sort
//   28. Actions
//   29. Errors
//   30. Preferences
//   31. Skeleton Mode
//   32. Dynamic Content
//   33. Global Blocks
//   34. Launchpad
//   35. Regions
//   36. History
// =============================================================================

return array(

  'app.powered-by-themeco'                            => __( 'Powered by Themeco', 'cornerstone' ),


  // Messaging
  // ---------

  'app.apply'                                         => __( 'Apply', 'cornerstone' ),
  'app.loading'                                       => __( 'Loading…', 'cornerstone' ),
  'app.unauthorized'                                  => __( 'You don&apos;t have permission to do that.', 'cornerstone' ),
  'app.wordpress-admin'                               => __( 'WordPress Admin', 'cornerstone' ),
  'app.exit'                                          => __( 'Exit', 'cornerstone' ),
  'app.unsaved-warning'                               => __( 'You have unsaved changes that will be lost. Would you like to proceed?', 'cornerstone' ),


  // General Purpose
  // ---------------

  'app.global'                                        => __( 'Global', 'cornerstone' ),
  'app.blank'                                         => __( 'Blank', 'cornerstone' ),
  'app.copy'                                          => __( 'Copy', 'cornerstone' ),
  'app.copy-thing'                                    => __( 'Copy {{context}}', 'cornerstone' ),
  'app.copy-of'                                       => __( 'Copy of {{title}}', 'cornerstone' ),
  'app.copy-of-numeric'                               => __( '{{title}} ({{index}})', 'cornerstone' ),
  'app.copied'                                        => __( '{{title}} (Copy)', 'cornerstone' ),
  'app.indexed'                                       => __( '{{label}} {{index}}', 'cornerstone'),
  'app.labeled'                                       => __( '{{context}} {{label}}', 'cornerstone'),
  'app.with-context'                                  => __( '{{label}} ({{context}})', 'cornerstone'),
  'app.search'                                        => __( 'Search', 'cornerstone' ),
  'app.search-thing'                                  => __( 'Search {{context}}', 'cornerstone' ),
  'app.go-validate'                                   => __( 'Go Validate', 'cornerstone' ),
  'app.validation-required'                           => __( 'Your license must be validated before installing.', 'cornerstone' ),
  'app.title'                                         => __( 'Title', 'cornerstone' ),
  'app.thing-title'                                   => __( '{{context}} Title', 'cornerstone' ),
  'app.back'                                          => __( 'Back', 'cornerstone' ),
  'app.back-to'                                       => __( 'Back to {{to}}', 'cornerstone' ),
  'app.item'                                          => __( 'Item', 'cornerstone'),
  'app.items'                                         => __( 'Items', 'cornerstone'),
  'app.add-thing'                                     => __( 'Add {{context}}', 'cornerstone' ),
  'app.add-item'                                      => __( 'Add Item', 'cornerstone' ),
  'app.add'                                           => __( 'Add', 'cornerstone' ),
  'app.add-new'                                       => __( 'Add New', 'cornerstone' ),
  'app.any'                                           => __( 'Any', 'cornerstone' ),
  'app.untitled'                                      => __( 'Untitled', 'cornerstone' ),
  'app.untitled-thing'                                => __( 'Untitled {{context}}', 'cornerstone' ),
  'app.document'                                      => __( 'Document', 'cornerstone' ),
  'app.documents'                                     => __( 'Documents', 'cornerstone' ),
  'app.edit'                                          => __( 'Edit', 'cornerstone' ),
  'app.edit-thing'                                    => __( 'Edit {{context}}', 'cornerstone' ),
  'app.edit-another'                                  => __( 'Would you like to go edit this {{context}}?', 'cornerstone' ),
  'app.unassigned'                                    => __( 'Unassigned', 'cornerstone' ),
  'app.select'                                        => __( 'Select', 'cornerstone' ),
  'app.select-thing'                                  => __( 'Select {{context}}', 'cornerstone' ),
  'app.selected'                                      => __( '{{context}} Selected', 'cornerstone' ),
  'app.choose'                                        => __( '– Choose –', 'cornerstone' ),
  'app.custom-abbr'                                   => __( 'Cust.', 'cornerstone' ),
  'app.custom'                                        => __( 'Custom', 'cornerstone' ),
  'app.save'                                          => __( 'Save', 'cornerstone' ),
  'app.save-thing'                                    => __( 'Save {{context}}', 'cornerstone' ),
  'app.save-and-name'                                 => __( 'Save and Name', 'cornerstone' ),
  'app.create'                                        => __( 'Create', 'cornerstone' ),
  'app.created-new'                                   => __( 'Created New {{context}}', 'cornerstone' ),
  'app.remove'                                        => __( 'Remove', 'cornerstone' ),
  'app.create-thing'                                  => __( 'Create {{context}}', 'cornerstone' ),
  'app.insert'                                        => __( 'Insert', 'cornerstone' ),
  'app.insert-thing'                                  => __( 'Insert {{context}}', 'cornerstone' ),
  'app.default'                                       => __( 'Default', 'cornerstone' ),
  'app.default-thing'                                 => __( 'Default {{context}}', 'cornerstone' ),
  'app.name'                                          => __( 'Name', 'cornerstone' ),
  'app.thing-name'                                    => __( '{{context}} Name', 'cornerstone' ),
  'app.name-thing'                                    => __( 'Name {{context}}', 'cornerstone' ),
  'app.no-things'                                     => __( 'No {{context}}', 'cornerstone' ),
  'app.no-things-found'                               => __( 'No {{context}} Found', 'cornerstone' ),
  'app.refine-search'                                 => __( 'Try refining your search to locate your desired {{context}}.', 'cornerstone' ),
  'app.plus-create'                                   => __( 'Click + to create new {{context}}', 'cornerstone' ),
  'app.settings-format'                               => __( '{{type}} Settings', 'cornerstone' ),
  'app.context-css'                                   => __( '{{context}} CSS', 'cornerstone' ),
  'app.context-js'                                    => __( '{{context}} JS', 'cornerstone' ),
  'app.css-tooltip'                                   => __( 'CSS', 'cornerstone' ),
  'app.js-tooltip'                                    => __( 'JavaScript', 'cornerstone' ),
  'app.post-type'                                     => __( 'Post Type', 'cornerstone' ),
  'app.page-template'                                 => __( 'Page Template', 'cornerstone' ),
  'app.slug'                                          => __( 'Slug', 'cornerstone' ),
  'app.created'                                       => __( '{{context}} Created', 'cornerstone' ),
  'app.added'                                         => __( '{{context}} Added', 'cornerstone' ),
  'app.moved'                                         => __( '{{context}} Moved', 'cornerstone' ),
  'app.duplicated'                                    => __( '{{context}} Duplicated', 'cornerstone' ),
  'app.deleted'                                       => __( '{{context}} Deleted', 'cornerstone' ),
  'app.updated'                                       => __( '{{context}} Updated', 'cornerstone' ),
  'app.undo'                                          => __( 'Undo', 'cornerstone' ),
  'app.redo'                                          => __( 'Redo', 'cornerstone' ),
  'app.inherit'                                       => __( 'Inherit', 'cornerstone' ),
  'app.run'                                           => __( 'Run', 'cornerstone' ),
  'app.type'                                          => __( 'Type', 'cornerstone' ),
  'app.download'                                      => __( 'Download', 'cornerstone'),
  'app.download-count'                                => __( 'Download ({{count}})', 'cornerstone'),
  'app.delete-count'                                  => __( 'Delete ({{count}})', 'cornerstone'),
  'app.context-subcontext'                            => __( '{{context}} {{subcontext}}', 'cornerstone'),
  'app.last-modified'                                 => __( 'Last Modified', 'cornerstone' ),
  'app.ascending'                                     => __( 'Ascending', 'cornerstone' ),
  'app.descending'                                    => __( 'Descending', 'cornerstone' ),
  'app.import'                                        => __( 'Import', 'cornerstone' ),
  'app.export'                                        => __( 'Export', 'cornerstone' ),
  'app.export-site'                                   => __( 'Export Documents', 'cornerstone' ),
  'app.export-site-description'                       => __( 'Alpha feature, this is not going to work on large sites', 'cornerstone' ),
  'app.existing'                                      => __( 'Existing {{context}}', 'cornerstone' ),
  'app.multiple'                                      => __( 'Multiple', 'cornerstone'),
  'app.yes'                                           => __( 'Yes', 'cornerstone'),
  'app.no'                                            => __( 'No', 'cornerstone'),
  'app.none'                                          => __( 'None', 'cornerstone'),
  'app.launch'                                        => __( 'Launch', 'cornerstone'),
  'app.update'                                        => __( 'Update', 'cornerstone'),
  'app.group'                                         => __( 'Group', 'cornerstone'),
  'app.all-documents'                                 => __( 'All Documents', 'cornerstone'),
  'app.disabled'                                      => __( 'Disabled', 'cornerstone' ),

  // Status
  // ------

  'app.status'                                        => __( 'Status', 'cornerstone' ),
  'app.status.draft'                                  => __( 'Draft', 'cornerstone`' ),
  'app.status.publish'                                => __( 'Publish', 'cornerstone' ),
  'app.status.private'                                => __( 'Private', 'cornerstone' ),


  // Accessibility
  // -------------

  'app.a11y.resize'                                   => __( 'Resize', 'cornerstone' ),
  'app.a11y.close'                                    => __( 'Close', 'cornerstone' ),
  'app.a11y.expand'                                   => __( 'Expand', 'cornerstone' ),
  'app.a11y.collapse'                                 => __( 'Collapse', 'cornerstone' ),

  // Breakpoints
  // -----------

  'app.breakpoints.tooltip'                           => __( 'Preview Size', 'cornerstone' ),
  'app.breakpoints.size.xl'                           => __( 'Extra Large', 'cornerstone' ),
  'app.breakpoints.size.lg'                           => __( 'Large', 'cornerstone' ),
  'app.breakpoints.size.md'                           => __( 'Medium', 'cornerstone' ),
  'app.breakpoints.size.sm'                           => __( 'Small', 'cornerstone' ),
  'app.breakpoints.size.xs'                           => __( 'Extra Small', 'cornerstone' ),
  'app.breakpoints.size-abbr.xl'                      => __( 'XL', 'cornerstone' ),
  'app.breakpoints.size-abbr.lg'                      => __( 'LG', 'cornerstone' ),
  'app.breakpoints.size-abbr.md'                      => __( 'MD', 'cornerstone' ),
  'app.breakpoints.size-abbr.sm'                      => __( 'SM', 'cornerstone' ),
  'app.breakpoints.size-abbr.xs'                      => __( 'XS', 'cornerstone' ),
  'app.breakpoints.desc.xl'                           => __( '1200px &amp; Up', 'cornerstone' ),
  'app.breakpoints.desc.lg'                           => __( '979px-1200px', 'cornerstone' ),
  'app.breakpoints.desc.md'                           => __( '767px-979px', 'cornerstone' ),
  'app.breakpoints.desc.sm'                           => __( '480px-767px', 'cornerstone' ),
  'app.breakpoints.desc.xs'                           => __( '320px-480px', 'cornerstone' ),
  'app.breakpoints.format'                            => __( '{{size}} <span>{{desc}}</span>', 'cornerstone'),

  // Titles
  // ------

  'app.home.title'                                    => __( 'Home', 'cornerstone' ),
  'app.managers.title'                                => __( 'Managers', 'cornerstone' ),
  'app.document.title'                                => __( 'Document', 'cornerstone' ),
  'app.outline.title'                                 => __( 'Outline', 'cornerstone' ),
  'app.inspector.title'                               => __( 'Inspector', 'cornerstone' ),
  'app.globals.title'                                 => __( 'Globals', 'cornerstone' ),
  'app.settings.title'                                => __( 'Settings', 'cornerstone' ),
  'app.elements.title'                                => __( 'Elements', 'cornerstone' ),
  'app.cheatsheet.title'                              => __( 'Cheatsheet', 'cornerstone' ),
  'app.keyboard-shortcuts.title'                      => __( 'Shortcuts', 'cornerstone' ),
  'app.support.title'                                 => __( 'Support', 'cornerstone' ),
  'app.forum.title'                                   => __( 'Forum', 'cornerstone' ),
  'app.help.title'                                    => __( 'Help', 'cornerstone' ),

  // Docs
  'app.docs.title'                                    => __( 'Docs', 'cornerstone' ),
  'app.docs.info' => __('Search our thorough online documenation to answer any question you might have.', 'cornerstone'),

  // Videos
  'app.videos.title'                                  => __( 'Videos', 'cornerstone' ),
  'app.videos.info' => __('Go deeper with an extensive library of helpful content demonstrating new tips and tricks.', 'cornerstone'),

  // Support
  'app.support.title'                                 => __( 'Support', 'cornerstone' ),
  'app.support.info' => __('Still having trouble? Our team is available to help 24/7/365 in our online forum.', 'cornerstone'),

  'app.theme-options.title'                           => __( 'Theme Options', 'cornerstone' ),
  'app.theme-options.import-success'                  => __( 'Theme Options imported successfully. Please reload Cornerstone', 'cornerstone' ),

  // Error
  // ---------

  'app.error-title'    => __('Uh oh!', 'cornerstone'),
  'app.error-message'  => __('Details helpful to Themeco support can be found in the browser&apos;s developer tools console.', 'cornerstone'),
  'app.error-document' => __('Document Error: {{context}}', 'cornerstone'),

  'app.errors.parse-json' => __('Could not parse JSON', 'cornerstone'),


  // Content
  // -------

  'app.content.sections'                              => __( 'Sections', 'cornerstone' ),
  'app.content.rows'                                  => __( 'Rows', 'cornerstone' ),
  'app.content.columns'                               => __( 'Columns', 'cornerstone' ),
  'app.content.first-row'                             => __( 'Add your first Row to this Section.', 'cornerstone' ),
  'app.content.notify-before-save-template'           => __( 'Add some Sections before saving a Template', 'cornerstone' ),
  'app.content.remove-spacing'                        => __( 'Remove Spacing', 'cornerstone' ),
  'app.content.remove-spacing-confirm'                => __( 'Would you like to remove all margin and padding from this section, its rows and its columns?', 'cornerstone' ),


  // Elements
  // --------

  'app.elements.entity'                               => __( 'Element', 'cornerstone' ),
  'app.elements.entities'                             => __( 'Elements', 'cornerstone' ),
  'app.elements.classic-title'                        => __( 'Classic Elements', 'cornerstone' ),
  'app.elements.classic-description'                  => __( 'Does not include advanced controls.', 'cornerstone' ),

  'app.elements-confirm-delete'                       => __( 'Are you sure you want to delete this {{title}}?', 'cornerstone' ),
  'app.elements-confirm-erase'                        => __( 'Are you sure you want to delete this element&apos;s contents?', 'cornerstone' ),

  'app.elements.favorites'                            => __( 'Favorites', 'cornerstone' ),
  'app.elements.standard'                             => __( 'Standard', 'cornerstone' ),
  'app.elements.template'                             => __( 'Element Template', 'cornerstone' ),

  'app.elements.failed-to-render'                     => __( 'This element could not render due to invalid template markup. This could be due to changes introduced by a third party plugin or invalid HTML. The front end of your site should still function normally', 'cornerstone' ),


  // Document Workspace

  'app.doc-workspace.na-title'                        => __( 'No Active Document', 'cornerstone' ),
  'app.doc-workspace.na-message'                      => __( 'Use the Cornerstone menu to open a Document or click + to create a new one.', 'cornerstone' ),

  // Inspector
  // ---------

  'app.inspector.na-title'                            => __( 'Nothing Selected', 'cornerstone' ),
  'app.inspector.na-message'                          => __( 'Click an element in the site preview, or use the magnifying glass icon on elements in the <strong>Outline</strong>', 'cornerstone' ),
  'app.inspector.search'                              => __( 'Search Inspector...', 'cornerstone' ),
  'app.inspector.apply-preset-warning'                => __( 'This action will replace all element styling. Proceed?', 'cornerstone' ),

  'app.inspector.undefined-title'                     => __( 'Undefined Element', 'cornerstone' ),
  'app.inspector.undefined-message'                   => __( 'The definition for this element could not be located. You may need to activate a plugin. The type declared for this element is: <strong>{{type}}</strong>', 'cornerstone' ),
  'app.inspector.no-controls-message'                 => __( 'This element does not have any Inspector controls.', 'cornerstone' ),

  'app.inspector.locked-title'                        => __( 'This Element is Locked', 'cornerstone' ),
  'app.inspector.no-inspect-permission'               => __( 'You do not have permission to inspect this element type', 'cornerstone' ),
  'app.inspector.unlock-title'                        => __( 'Unlock', 'cornerstone' ),
  'app.inspector.locked-message'                      => __( 'Locked Elements are closed off to internal edits and cannot be duplicated or deleted. Go to Element Manager to unlock.', 'cornerstone' ),

  'app.inspector.classic-element-title'               => __( 'Classic Element', 'cornerstone' ),
  'app.inspector.classic-element-message'             => __( 'This is a classic element. These are supported, but have less controls to configure.', 'cornerstone' ),

  'app.inspector.group.content'                       => __( 'Content', 'cornerstone' ),
  'app.inspector.group.design'                        => __( 'Design', 'cornerstone' ),
  'app.inspector.group.customize'                     => __( 'Customize', 'cornerstone' ),


  // Forms
  // -----

  'app.forms.icon-picker.blank'                       => __( 'No icons found.', 'cornerstone' ),
  'app.forms.icon-picker.search-context'              => __( 'Icons', 'cornerstone' ),
  'app.forms.label-input.placeholder'                 => __( 'Double click to edit.', 'cornerstone'),

  'app.forms.toggle.on'                               => __( 'On', 'cornerstone'),
  'app.forms.toggle.off'                              => __( 'Off', 'cornerstone'),

  'app.forms.flex.standard'                           => __( 'Standard', 'cornerstone' ),
  'app.forms.flex.no-shrink'                          => __( 'No Shrink', 'cornerstone' ),
  'app.forms.flex.fill-space'                         => __( 'Fill Space', 'cornerstone' ),
  'app.forms.flex.fill-space-equally'                 => __( 'Fill Space Equally', 'cornerstone' ),
  'app.forms.flex.flex-grow'                          => __( 'Grow', 'cornerstone' ),
  'app.forms.flex.flex-shrink'                        => __( 'Shrink', 'cornerstone' ),
  'app.forms.flex.flex-basis'                         => __( 'Basis', 'cornerstone' ),
  'app.forms.flex.start'                              => __( 'Start', 'cornerstone' ),
  'app.forms.flex.flex-start'                         => __( 'Start', 'cornerstone' ),
  'app.forms.flex.center'                             => __( 'Center', 'cornerstone' ),
  'app.forms.flex.end'                                => __( 'End', 'cornerstone' ),
  'app.forms.flex.flex-end'                           => __( 'End', 'cornerstone' ),
  'app.forms.flex.space-around'                       => __( 'Space Around', 'cornerstone' ),
  'app.forms.flex.space-between'                      => __( 'Space Between', 'cornerstone' ),
  'app.forms.flex.space-evenly'                       => __( 'Space Evenly', 'cornerstone' ),
  'app.forms.flex.baseline'                           => __( 'Baseline', 'cornerstone' ),
  'app.forms.flex.stretch'                            => __( 'Stretch', 'cornerstone' ),
  'app.forms.flex.auto'                               => __( 'Auto', 'cornerstone' ),

  'app.border.none'                                   => __( 'None', 'cornerstone' ),
  'app.border.solid'                                  => __( 'Solid', 'cornerstone' ),
  'app.border.dotted'                                 => __( 'Dotted', 'cornerstone' ),
  'app.border.dashed'                                 => __( 'Dashed', 'cornerstone' ),
  'app.border.double'                                 => __( 'Double', 'cornerstone' ),
  'app.border.groove'                                 => __( 'Groove', 'cornerstone' ),
  'app.border.ridge'                                  => __( 'Ridge', 'cornerstone' ),
  'app.border.inset'                                  => __( 'Inset', 'cornerstone' ),
  'app.border.outset'                                 => __( 'Outset', 'cornerstone' ),

  // Stack
  // --------
  'app.stack'                                 => __( 'Stack', 'cornerstone' ),
  'app.stack.starter-install'                 => __( 'Would you like to install the Starter Site? This gives you example pages, layouts, and content.', 'cornerstone' ),
  'app.stack.starter-install-decline'         => __( 'No, just change my stack', 'cornerstone' ),


  // Controls
  // --------

  'app.controls.setup'                                => __( 'Setup', 'cornerstone' ),
  'app.controls.id'                                   => __( 'ID', 'cornerstone' ),
  'app.controls.class'                                => __( 'Class', 'cornerstone' ),
  'app.controls.style'                                => __( 'Style', 'cornerstone' ),
  'app.controls.inline-css'                           => __( 'Inline CSS', 'cornerstone' ),
  'app.controls.edit'                                 => __( 'Edit', 'cornerstone' ),
  'app.controls.edit-css'                             => __( 'Edit CSS', 'cornerstone' ),
  'app.controls.edit-parameters'                      => __( 'Edit Parameters', 'cornerstone' ),
  'app.controls.show-condition'                       => __( 'Conditions', 'cornerstone' ),
  'app.controls.element-css'                          => __( 'Element CSS', 'cornerstone' ),
  'app.controls.element-css-placeholder'              => __( '/*\n\nUse "$el" in this editor to\ntarget this element (it will\nbecome the generated class).\n\ne.g.\n\n$el    { property: value; }\n↓\n.el123 { property: value; }\n\n*/', 'cornerstone' ),
  'app.controls.toggle-hash'                          => __( 'Toggle Hash', 'cornerstone' ),
  'app.controls.toggle-hash-description'              => __( 'Open this toggleable via the # in the url. So open-toggle as the toggle hash would then be opened from the URL #open-toggle', 'cornerstone' ),
  'app.controls.hide-bp'                              => __( 'Hide During<br>Breakpoints', 'cornerstone' ),
  'app.controls.base'                                 => __( 'Base', 'cornerstone' ),
  'app.controls.hover'                                => __( 'Hover', 'cornerstone' ),
  'app.controls.interaction'                          => __( 'Interaction', 'cornerstone' ),

  'app.controls.font-family.select'                   => __( '{{family}} ({{source}})', 'cornerstone' ),
  'app.controls.font-family.sample-display'           => __( 'Should auld acquaintance be forgot, and never brought to mind? The flames of Love extinguished, and fully past and gone', 'cornerstone' ),
  'app.controls.font-family.none-found'               => __( 'Not Fonts found for this search query', 'cornerstone' ),
  'app.controls.text-editor.click-to-edit'            => __( 'Click to Edit', 'cornerstone' ),
  'app.controls.text-editor.edit-text'                => __( 'Edit Text', 'cornerstone' ),
  'app.controls.text-editor.mode-html'                => __( 'HTML', 'cornerstone' ),
  'app.controls.text-editor.mode-raw'                 => __( 'Raw', 'cornerstone' ),
  'app.controls.text-editor.mode-rich-text'           => __( 'Rich Text', 'cornerstone' ),
  'app.controls.classic.title'                        => __( 'Title', 'cornerstone' ),

  'app.controls.unlinked'                             => __( 'Unlinked', 'cornerstone' ),
  'app.controls.linked'                               => __( 'Linked', 'cornerstone' ),
  'app.controls.link-sides'                           => __( 'Link Sides', 'cornerstone' ),
  'app.controls.side'                                 => __( 'Side', 'cornerstone' ),
  'app.controls.width'                                => __( 'Width', 'cornerstone' ),
  'app.controls.color'                                => __( 'Color', 'cornerstone' ),
  'app.controls.blur'                                 => __( 'Blur', 'cornerstone' ),
  'app.controls.x-offset'                             => __( 'X Offset', 'cornerstone' ),
  'app.controls.y-offset'                             => __( 'Y Offset', 'cornerstone' ),
  'app.controls.spread-position'                      => __( 'Spread &amp;<br>Position', 'cornerstone' ),
  'app.controls.spread'                               => __( 'Spread', 'cornerstone' ),
  'app.controls.position'                             => __( 'Position', 'cornerstone' ),
  'app.controls.outside'                              => __( 'Outside', 'cornerstone' ),
  'app.controls.inside'                               => __( 'Inside', 'cornerstone' ),

  'app.controls.box.top'                              => __( 'Top', 'cornerstone' ),
  'app.controls.box.right'                            => __( 'Right', 'cornerstone' ),
  'app.controls.box.bottom'                           => __( 'Bottom', 'cornerstone' ),
  'app.controls.box.bttm'                             => _x( 'Bttm', 'Short version of bottom for dimension control', 'cornerstone' ),
  'app.controls.box.left'                             => __( 'Left', 'cornerstone' ),

  'app.controls.box.top-left'                         => __( 'Top Left', 'cornerstone' ),
  'app.controls.box.top-right'                        => __( 'Top Right', 'cornerstone' ),
  'app.controls.box.bottom-right'                     => __( 'Bottom Right', 'cornerstone' ),
  'app.controls.box.bottom-left'                      => __( 'Bottom Left', 'cornerstone' ),
  'app.controls.box.bttm-right'                       => _x( 'Bttm Right', 'Short version of bottom for dimension control', 'cornerstone' ),
  'app.controls.box.bttm-left'                        => _x( 'Bttm Left', 'Short version of bottom for dimension control', 'cornerstone' ),


  'app.controls.font-style'                           => __( 'Style', 'cornerstone' ),
  'app.controls.font-style.normal'                    => __( 'Normal', 'cornerstone' ),
  'app.controls.font-style.italic'                    => __( 'Italic', 'cornerstone' ),

  'app.controls.info.type'                            => __( 'Type', 'cornerstone' ),
  'app.controls.info.normal'                          => __( 'Normal', 'cornerstone' ),
  'app.controls.info.popover'                         => __( 'Popover', 'cornerstone' ),
  'app.controls.info.placement'                       => __( 'Placement', 'cornerstone' ),
  'app.controls.info.trigger'                         => __( 'Trigger', 'cornerstone' ),
  'app.controls.info.hover'                           => __( 'Hover', 'cornerstone' ),
  'app.controls.info.focus'                           => __( 'Focus', 'cornerstone' ),
  'app.controls.info.click'                           => __( 'Click', 'cornerstone' ),
  'app.controls.info.title'                           => __( 'Title', 'cornerstone' ),
  'app.controls.info.content'                         => __( 'Content', 'cornerstone' ),

  'app.controls.media.audio-url'                      => __( 'Audio URL', 'cornerstone' ),
  'app.controls.media.preload-content'                => __( 'Preload<br>Content', 'cornerstone' ),
  'app.controls.media.none'                           => __( 'None', 'cornerstone' ),
  'app.controls.media.auto'                           => __( 'Auto', 'cornerstone' ),
  'app.controls.media.metadata'                       => __( 'Metadata', 'cornerstone' ),
  'app.controls.media.advanced'                       => __( 'Advanced', 'cornerstone' ),
  'app.controls.media.loop'                           => __( 'Loop', 'cornerstone' ),
  'app.controls.media.autoplay'                       => __( 'Autoplay', 'cornerstone' ),
  'app.controls.media.display-function'               => __( 'Display &<br>Function', 'cornerstone' ),

  'app.controls.sidebar.select'                       => __( 'Select Widget Area', 'cornerstone' ),

  'app.controls.text-align.label'                     => __( 'Align', 'cornerstone' ),
  'app.controls.text-align.left'                      => __( 'Left', 'cornerstone' ),
  'app.controls.text-align.center'                    => __( 'Center', 'cornerstone' ),
  'app.controls.text-align.right'                     => __( 'Right', 'cornerstone' ),
  'app.controls.text-align.justify'                   => __( 'Justify', 'cornerstone' ),
  'app.controls.text.html-tag'                        => __( 'HTML Tag', 'cornerstone' ),
  'app.controls.text.looks-like'                      => __( 'Looks Like', 'cornerstone' ),
  'app.controls.text.label'                           => __( 'Text', 'cornerstone' ),
  'app.controls.text.color'                           => __( 'Color', 'cornerstone' ),

  'app.controls.text-decoration.label'                => __( 'Decoration', 'cornerstone' ),
  'app.controls.text-decoration.underline'            => __( 'Underline', 'cornerstone' ),
  'app.controls.text-decoration.line-through'         => __( 'Line Through', 'cornerstone' ),

  'app.controls.text-format.font-family'              => __( 'Font', 'cornerstone' ),
  'app.controls.text-format.font-weight'              => __( 'Weight', 'cornerstone' ),
  'app.controls.text-format.font-size'                => __( 'Size', 'cornerstone' ),
  'app.controls.text-format.letter-spacing'           => __( 'Spacing', 'cornerstone' ),
  'app.controls.text-format.line-height'              => __( 'Height', 'cornerstone' ),

  'app.controls.text-transform.label'                 => __( 'Transform', 'cornerstone' ),
  'app.controls.text-transform.uppercase'             => __( 'Uppercase', 'cornerstone' ),
  'app.controls.text-transform.capitalize'            => __( 'Capitalize', 'cornerstone' ),
  'app.controls.text-transform.lowercase'             => __( 'Lowercase', 'cornerstone' ),

  'app.controls.image.retina'                         => __( 'Dimensions', 'cornerstone' ),
  'app.controls.image.dim-preview'                    => __( 'Dimensions Preview', 'cornerstone' ),
  'app.controls.image.alt-text'                       => __( 'Alt Text', 'cornerstone' ),
  'app.controls.image.alt-text-placeholder'           => __( 'Describe Your Image', 'cornerstone' ),
  'app.controls.image.source'                         => __( 'Source', 'cornerstone' ),
  'app.controls.image.link'                           => __( 'Link', 'cornerstone' ),
  'app.controls.image.info'                           => __( 'Info', 'cornerstone' ),
  'app.controls.image.w'                              => __( 'W', 'cornerstone' ),
  'app.controls.image.h'                              => __( 'H', 'cornerstone' ),
  'app.controls.image.object-fit'                     => __( 'Object Fit', 'cornerstone' ),
  'app.controls.image.object-position'                => __( 'Position', 'cornerstone' ),
  'app.controls.image.contain'                        => __( 'Contain', 'cornerstone' ),
  'app.controls.image.attachment-srcset'              => __( 'Attachment Srcset', 'cornerstone' ),
  'app.controls.image.attachment-srcset.description'  => __( 'If using an Attachment ID, this will use srcset based on your configured image sizes', 'cornerstone' ),
  'app.controls.image.cover'                          => __( 'Cover', 'cornerstone' ),
  'app.controls.image.fill'                           => __( 'Fill', 'cornerstone' ),
  'app.controls.image.none'                           => __( 'None', 'cornerstone' ),
  'app.controls.image.scale-down'                     => __( 'Scale Down', 'cornerstone' ),

  'app.controls.flex.direction'                       => __( 'Direction', 'cornerstone' ),
  'app.controls.flex.row'                             => __( 'Row', 'cornerstone' ),
  'app.controls.flex.column'                          => __( 'Column', 'cornerstone' ),
  'app.controls.flex.row-reverse'                     => __( 'Row Reverse', 'cornerstone' ),
  'app.controls.flex.column-reverse'                  => __( 'Column Reverse', 'cornerstone' ),
  'app.controls.flex.self'                            => __( 'Self Flex', 'cornerstone' ),
  'app.controls.flex.reverse'                         => __( 'Reverse', 'cornerstone' ),
  'app.controls.flex.wrap'                            => __( 'Wrap', 'cornerstone' ),
  'app.controls.flex.align-h'                         => __( 'Horizontal', 'cornerstone' ),
  'app.controls.flex.align-v'                         => __( 'Vertical', 'cornerstone' ),
  'app.controls.flex.gap'                             => __( 'Gap', 'cornerstone' ),

  'app.controls.link.preview'                         => __( 'Preview', 'cornerstone' ),
  'app.controls.link.type'                            => __( 'Type', 'cornerstone' ),
  'app.controls.link.content'                         => __( 'Content', 'cornerstone' ),
  'app.controls.link.url'                             => __( 'URL', 'cornerstone' ),
  'app.controls.link.url-placeholder'                 => __( 'e.g. http://theme.co/', 'cornerstone' ),
  'app.controls.link.new-tab'                         => __( 'New Tab', 'cornerstone' ),
  'app.controls.link.nofollow'                        => __( 'nofollow', 'cornerstone' ),
  'app.controls.link.email'                           => __( 'Email', 'cornerstone' ),
  'app.controls.link.email-placeholder'               => __( 'e.g. hello@example.com', 'cornerstone' ),
  'app.controls.link.email-subject'                   => __( 'Subject', 'cornerstone' ),
  'app.controls.link.email-subject-placeholder'       => __( 'e.g. Howdy!', 'cornerstone' ),
  'app.controls.link.phone'                           => __( 'Phone', 'cornerstone' ),
  'app.controls.link.phone-placeholder'               => __( 'e.g. 18885551234', 'cornerstone' ),

  'app.controls.share'                                => __( 'Share', 'cornerstone' ),
  'app.controls.share.title'                          => __( 'Title', 'cornerstone' ),
  'app.controls.share.link'                           => __( 'Link', 'cornerstone' ),
  'app.controls.share.type'                           => __( 'Type', 'cornerstone' ),

  'app.controls.bg'                                   => __( 'Background', 'cornerstone' ),


  // Notifications
  // -------------

  'app.notification-notice'                           => __( 'Hey!', 'cornerstone' ),
  'app.notification-success'                          => __( 'Awesome!', 'cornerstone' ),
  'app.notification-error'                            => __( 'Uh oh!', 'cornerstone' ),
  'app.notification-commence'                         => __( 'Here we go!', 'cornerstone' ),
  'app.notification-done'                             => __( 'All done!', 'cornerstone' ),

  'app.notification-refreshing-preview'               => __( 'Refreshing preview.', 'cornerstone' ),
  'app.notification-refreshing-preview-save-reminder' => __( 'Refreshing preview. Don\'t forget to save.', 'cornerstone' ),

  'app.notify.saved-all'                              => __( 'Saved!', 'cornerstone' ),
  'app.notify.failed-to-save-all'                     => __( 'Failed to save.', 'cornerstone' ),
  'app.notify.saved'                                  => __( 'Saved {{context}}!', 'cornerstone' ),
  'app.notify.failed-to-save'                         => __( 'Failed to save {{context}}.', 'cornerstone' ),
  'app.notify.downloaded'                             => __( 'Downloaded {{context}}!', 'cornerstone' ),
  'app.notify.failed-to-download'                     => __( 'Failed to download {{context}}.', 'cornerstone' ),
  'app.notify.failed-to-download-with-message'        => __( 'Failed to download {{context}}. {{message}}', 'cornerstone' ),
  'app.notify.delete-confirm'                         => __( 'Are you sure you want to delete this {{context}}?', 'cornerstone' ),
  'app.notify.delete-confirm-perm'                    => __( 'Are you sure you want to delete this {{context}}? This can not be undone.', 'cornerstone' ),
  'app.notify.duplicated'                             => __( 'Duplicated {{context}}.', 'cornerstone' ),
  'app.notify.failed-to-duplicate'                    => __( 'Failed to duplicate {{context}}.', 'cornerstone' ),
  'app.notify.deleted'                                => __( 'Deleted {{context}}.', 'cornerstone' ),
  'app.notify.failed-to-delete'                       => __( 'Failed to delete {{context}}.', 'cornerstone' ),
  'app.notify.title-updated'                          => __( '{{context}} title updated.', 'cornerstone' ),
  'app.notify.failed-to-update-title'                 => __( 'Failed to update {{context}} title.', 'cornerstone' ),
  'app.notify.created'                                => __( '{{context}} created!', 'cornerstone' ),
  'app.notify.failed-to-create'                       => __( 'Failed to create {{context}}. {{message}}', 'cornerstone' ),
  'app.notify.updated'                                => __( 'Updated {{context}}!', 'cornerstone' ),
  'app.notify.failed-to-update'                       => __( 'Failed to update {{context}}.', 'cornerstone' ),
  'app.notify.loading'                                => __( 'Loading {{context}}.', 'cornerstone' ),
  'app.notify.inserted'                               => __( '{{context}} inserted!', 'cornerstone' ),
  'app.notify.template-inserted'                      => __( '{{context}} inserted at the bottom of this document!', 'cornerstone' ),
  'app.notify.failed-to-insert'                       => __( 'Failed to insert {{context}}.', 'cornerstone' ),
  'app.notify.installing'                             => __( 'Installing {{context}}.', 'cornerstone' ),
  'app.notify.install-cancel'                         => __( 'Finishing Install.', 'cornerstone' ),
  'app.notify.installed'                              => __( '{{context}} installed!', 'cornerstone' ),
  'app.notify.failed-to-install'                      => __( 'Failed to install {{context}}.', 'cornerstone' ),
  'app.notify.title-required'                         => __( 'Your {{context}} needs a title.', 'cornerstone' ),
  'app.notify.name-required'                          => __( 'Your {{context}} needs a name.', 'cornerstone' ),
  'app.notify.name-overwrite-confirm'                 => __( 'An existing {{context}} already has that name. Would you like to overwrite it?', 'cornerstone' ),
  'app.notify.preview-updating'                       => __( 'Preview Updating.', 'cornerstone' ),
  'app.notify.elements-max-children'                  => __( 'This element already has the maximum number of children.', 'cornerstone' ),
  'app.notify.elements-min-children'                  => __( 'This element requires a minimum number of children.', 'cornerstone' ),
  'app.notify.tinymce-failed'                         => __( 'Rich Text Editing is disabled because we found a problem with your Wordpress Visual Editor. You can still edit the text in HTML mode.', 'cornerstone' ),
  'app.notify.cloned-elements'                        => __( '{{context}} elements have been cloned!', 'cornerstone' ),
  'app.notify.failed-to-clone-elements'               => __( 'Failed to clone {{context}} elements.', 'cornerstone' ),
  'app.notify.no-elements-to-clone'                   => __( 'No elements found in this {{context}}.', 'cornerstone' ),

  // Presets
  // -------

  'app.presets.placeholder'                           => __( 'Nothing Selected', 'cornerstone' ),
  'app.presets.na'                                    => __( 'No Presets', 'cornerstone' ),
  'app.presets.save'                                  => __( 'Save Preset', 'cornerstone' ),
  'app.presets.apply'                                 => __( 'Apply Preset', 'cornerstone' ),
  'app.presets.replace-content'                       => __( 'Replace Content', 'cornerstone' ),
  'app.presets.apply-confirm'                         => __( 'Yes, Apply', 'cornerstone' ),
  'app.presets.apply-decline'                         => __( 'No thanks', 'cornerstone' ),


  // Confirm
  // -------

  'app.confirm-yep'                                   => __( 'Yes, Proceed', 'cornerstone' ),
  'app.confirm-no-thanks'                             => __( 'No Thanks', 'cornerstone' ),
  'app.confirm-nope'                                  => __( 'No, Go Back', 'cornerstone' ),


  // Options
  // -------

  'app.options.entities'                              => __( 'Options', 'cornerstone' ),
  'app.options.info'                                  => __( 'Info:', 'cornerstone' ),


  // Templates
  // ---------

  'app.template.doc-type-message' => __( 'Current document type <strong>{{context}}</strong><br/>Only compatible templates are shown.', 'cornerstone' ),
  'app.template.no-doc-message' => __( 'Open a document to view available templates.', 'cornerstone' ),





  'app.templates.manager'                             => __( 'Template Manager', 'cornerstone' ),
  'app.templates.entity'                              => __( 'Template', 'cornerstone'),
  'app.templates.entities'                            => __( 'Templates', 'cornerstone'),
  'app.templates.entity-template'                     => __( '{{context}} Template', 'cornerstone' ),
  'app.templates.entities-template'                   => __( '{{context}} Templates', 'cornerstone'),
  'app.templates.preset.entity'                       => __( 'Preset', 'cornerstone'),
  'app.templates.preset.entities'                     => __( 'Presets', 'cornerstone'),
  'app.templates.save-as'                             => __( 'Save as Template', 'cornerstone'),
  'app.templates.load'                                => __( 'Load Template', 'cornerstone' ),
  'app.templates.save'                                => __( 'Save Template', 'cornerstone' ),
  'app.templates.save-snapshot'                       => __( 'Save a snapshot of this element into your Library', 'cornerstone' ),
  'app.templates.no-templates'                        => __( 'No Templates', 'cornerstone'),
  'app.templates.no-thing-templates'                  => __( 'No {{context}} Templates', 'cornerstone'),
  'app.templates.visit-manager'                       => __( 'Visit Template Manager', 'cornerstone'),
  'app.templates.blank-welcome'                       => __( 'Begin with a blank slate.', 'cornerstone'),

  'app.templates.delete-popover.one'                  => __( 'Are you sure you want to delete the selected template?', 'cornerstone'),
  'app.templates.delete-popover.n'                    => __( 'Are you sure you want to delete the {{count}} selected templates?', 'cornerstone'),

  'app.templates.delete-thumbnail'                     => __( 'Are you sure you want to remove the thumbnail? Auto-generated thumbnails can not be restored', 'cornerstone'),

  'app.templates.filter-all'                          => __( 'All', 'cornerstone'),
  'app.templates.filter-site'                         => __( 'Sites', 'cornerstone'),
  'app.templates.filter-header'                       => __( 'Headers', 'cornerstone'),
  'app.templates.filter-footer'                       => __( 'Footers', 'cornerstone'),
  'app.templates.filter-content'                      => __( 'Content', 'cornerstone'),
  'app.templates.filter-layout'                       => __( 'Layouts', 'cornerstone'),
  'app.templates.filter-preset'                       => __( 'Presets', 'cornerstone'),
  'app.templates.filter-my-templates'                 => __( 'My Templates', 'cornerstone'),
  'app.templates.filter-themeco-templates'            => __( 'Themeco Templates', 'cornerstone'),


  'app.templates.type-site'                           => __( 'Site', 'cornerstone'),
  'app.templates.type-header'                         => __( 'Header', 'cornerstone'),
  'app.templates.type-footer'                         => __( 'Footer', 'cornerstone'),
  'app.templates.type-content'                        => __( 'Content', 'cornerstone'),
  'app.templates.type-layout'                         => __( 'Layout', 'cornerstone'),
  'app.templates.type-preset'                         => __( 'Preset', 'cornerstone'),
  'app.templates.subtype-format'                      => __( '<strong>{{type}}</strong>: {{subtype}}', 'cornerstone'),
  'app.templates.element-defaults.entity'             => __( 'Element Defaults', 'cornerstone' ),
  'app.templates.element-defaults.message'            => __( 'When adding new Elements in the builders they will start with the Preset you assign.', 'cornerstone' ),

  'app.templates.import.begin'                        => __( 'Importing Template File', 'cornerstone' ),
  'app.templates.import.demo'                         => __( 'Adding Template File', 'cornerstone' ),
  'app.templates.import.done'                         => __( 'File Imported!', 'cornerstone' ),
  'app.templates.import.error'                        => __( 'This file you chose was not valid and could not be imported.', 'cornerstone' ),
  'app.templates.import.failure'                      => __( 'File Failed to import. {{message}}', 'cornerstone' ),
  'app.templates.import.image-failure'                => __( 'Failed to import image batch. Your server upload limit is probably preventing this upload from happening', 'cornerstone' ),

  'app.templates.confirm-reimport'                    => __( 'It looks like you have imported this template package before. Would you like to import it again?', 'cornerstone' ),
  'app.templates.unauthorized'                        => __( 'View your Templates to the right.', 'cornerstone' ),

  'app.templates.remove-preview-image'                => __( 'Remove Preview Image', 'cornerstone'),

  'app.templates.site-import.timeout1'             => __( 'Working on it...', 'cornerstone' ),
  'app.templates.site-import.timeout2'             => __( 'Hang in there, trying to reconnect...', 'cornerstone' ),
  'app.templates.site-import.timeout3'             => __( 'Experiencing technical difficulties...', 'cornerstone' ),
  'app.templates.site-import.failure'              => __( 'We&apos;re sorry, the demo failed to finish importing.', 'cornerstone' ),

  // Packs
  'app.packs' => __('Packs', CS_LOCALIZE),

  // Max
  // -----
  'app.max.action.add-document'                    => __( 'to Document', 'cornerstone' ),
  'app.max.action.add'                             => __( 'to Library', 'cornerstone' ),
  'app.max.action.import-site'                     => __( 'Import Site', 'cornerstone' ),
  'app.max.action.import-site-templates'           => __( 'Importing Site Templates', 'cornerstone' ),
  'app.max.action.add-all'                         => __( 'All to Library', 'cornerstone' ),
  'app.max.action.demo'                            => __( '+ Document', 'cornerstone' ),
  'app.max.action.cache-reset-text'                => __( 'Click this to reset the Max cache. It will fetch the latest purchase history and course data', 'cornerstone' ),
  'app.max.template.demo'                          => __( 'Asset Added to Document.', 'cornerstone' ),
  'app.max.template.add'                           => __( 'Asset Added to Library', 'cornerstone' ),
  'app.max.template.exists'                        => __( 'This template name already exists ({{name}}) do you want to import another copy?', 'cornerstone' ),
  'app.max.unlock.learn-more'                      => __( 'About Max', 'cornerstone' ),
  'app.max.unlock.about'                           => __( 'About', 'cornerstone' ),
  'app.max.unlock.get-access'                      => __( 'Or Get Access to {{title}} + All Courses, Expansion Packs, & Plugins When You ', 'cornerstone' ),
  'app.max.unlock.subscribe-to-max'                => __( 'Subscribe to Max', 'cornerstone' ),

  // Template Section
  // -----
  'app.templates.section.exit'                     => __( 'Exit Selection', 'cornerstone' ),
  'app.templates.section.create'                   => __( 'Make Section Template', 'cornerstone' ),

  // Fonts
  // -----

  'app.fonts.entity'                                  => __( 'Font', 'cornerstone' ),
  'app.fonts.entities'                                => __( 'Fonts', 'cornerstone' ),
  'app.fonts.entity-selection'                        => __( 'Font Selection', 'cornerstone' ),
  'app.fonts.manager'                                 => __( 'Font Manager', 'cornerstone' ),
  'app.fonts.description'                             => __( 'Once a new font has been added, click the arrow to the side of the label to reveal the selection tool for font families and weights. Double-click the label to rename the added font if desired. Make sure to save all changes before exiting.', 'cornerstone' ),

  'app.fonts.duplicate-name-error'                    => __( 'Two fonts selections can not share the same name.', 'cornerstone' ),
  'app.fonts.min-one-error'                           => __( 'You need at least one font. Create another before deleting this one.', 'cornerstone' ),

  'app.fonts.select-family'                           => __( 'Select a Font Family', 'cornerstone' ),
  'app.fonts.preview'                                 => __( 'Preview', 'cornerstone' ),
  'app.fonts.preview-text'                            => __( 'Type here to change preview text...', 'cornerstone' ),
  'app.fonts.upload'                                  => __( 'Upload Fonts', 'cornerstone' ),
  'app.fonts.select-files'                            => __( 'Select Font File(s)', 'cornerstone' ),

  'app.fonts.normal'                                  => __( 'Normal', 'cornerstone' ),
  'app.fonts.bold'                                    => __( 'Bold', 'cornerstone' ),
  'app.fonts.normal-formatted'                        => __( 'Normal ({{weight}})', 'cornerstone' ),
  'app.fonts.bold-formatted'                          => __( 'Bold ({{weight}})', 'cornerstone' ),
  'app.fonts.regular'                                 => __( 'Regular', 'cornerstone' ),
  'app.fonts.italic'                                  => __( 'Italic', 'cornerstone' ),
  'app.fonts.import-files'                            => __( 'Import Font Files', 'cornerstone' ),
  'app.fonts.manager-add'                             => __( 'Add to Font Manager', 'cornerstone' ),
  'app.fonts.item-name'                               => __( 'Font Item Name', 'cornerstone' ),
  'app.palette-fonts'                                 => __( 'Palette Fonts', 'cornerstone' ),
  'app.managed-weights'                               => __( 'Managed Weights', 'cornerstone' ),
  'app.parent-font'                                   => __( 'Parent Font', 'cornerstone' ),
  'app.parent-weight'                                 => __( 'Parent Weight', 'cornerstone' ),
  'app.exact-weights'                                 => __( 'Exact Weights', 'cornerstone' ),
  'app.exact-weight'                                  => __( 'Exact Weight', 'cornerstone' ),

  'app.fonts.google.subsets'                          => __( 'Enable Subsets', 'cornerstone' ),
  'app.fonts.google.placeholder'                      => __( 'Additional Subsets', 'cornerstone' ),

  'app.fonts.google.url'                              => __( 'Google Fonts URL', 'cornerstone' ),
  'app.fonts.google.url-description'                  => __( 'Use another service that is supports the Google Fonts format. Such as bunnyfonts (https://fonts.bunny.net/css)', 'cornerstone' ),

  'app.fonts.adobe.project-id'                        => __( 'Project ID', 'cornerstone' ),
  'app.fonts.adobe.load-as-css'                       => __( 'Load as CSS', 'cornerstone' ),
  'app.fonts.adobe.load-as-css-description'           => __( 'Load through an async JS script or load through a CSS import tag', 'cornerstone' ),
  'app.fonts.adobe.available'                         => __( 'Available', 'cornerstone' ),
  'app.fonts.adobe.fonts'                             => __( 'Fonts', 'cornerstone' ),
  'app.fonts.adobe.weights'                           => __( 'Weights', 'cornerstone' ),
  'app.fonts.adobe.enter-id'                          => __( 'Enter your Adobe Fonts project ID above and click refresh.', 'cornerstone' ),
  'app.fonts.adobe.not-found'                         => __( 'Your project was not found. Please check to make sure the project ID is correct and try again.', 'cornerstone' ),
  'app.fonts.adobe.refreshing'                        => __( 'Refreshing&hellip;', 'cornerstone' ),
  'app.fonts.adobe.refresh'                           => __( 'Refresh', 'cornerstone' ),

  'app.fonts.group.custom'                            => __( 'Custom Fonts', 'cornerstone' ),
  'app.fonts.group.adobe'                             => __( 'Adobe Fonts (Typekit)', 'cornerstone' ),
  'app.fonts.group.system'                            => __( 'System Fonts', 'cornerstone' ),
  'app.fonts.group.google'                            => __( 'Google Fonts', 'cornerstone' ),

  'app.fonts.google.title'                            => __( 'Google', 'cornerstone' ),
  'app.fonts.google.description'                      => __( 'Specify which character subsets you would like to enable below. All subsets included extended support. Latin is always loaded by default.', 'cornerstone' ),
  'app.fonts.adobe.title'                             => __( 'Adobe Fonts (Typekit)', 'cornerstone' ),
  'app.fonts.adobe.description'                       => __( 'To integrate your Adobe Fonts projects on this website, enter a project ID into the input below. You can find this by logging into Adobe Fonts, clicking on <strong>Web Projects</strong> and then looking for the <strong>Project ID</strong>.', 'cornerstone' ),
  'app.fonts.custom.title'                            => __( 'Custom Fonts', 'cornerstone' ),
  'app.fonts.custom.description'                      => __( 'Use this section to create custom font families. Upload custom font files to a family, then assign a weight and style. These families will become selectable in the Font Manager above. We recommend using the .woff or .woff2 file format.', 'cornerstone' ),
  'app.fonts.custom.default'                          => __( 'Custom Font Family', 'cornerstone' ),

  'app.fonts.body-copy'                               => __( 'Body Copy', 'cornerstone' ),
  'app.fonts.headings'                                => __( 'Headings', 'cornerstone' ),

  'app.fonts.display.title'                           => __( 'Font Display', 'cornerstone' ),
  'app.fonts.display.description'                     => __( 'The <code>font-display</code> property gives users control over how the timeline for fonts being loaded into the browser should be executed. The value selected will be utilized in supported browsers where applicable across your site.', 'cornerstone' ),
  'app.fonts.display.label'                           => __( 'Select Value', 'cornerstone' ),


  // Colors
  // ------

  'app.colors.entity'                                 => __( 'Color', 'cornerstone' ),
  'app.colors.title'                                  => __( 'Color Manager', 'cornerstone' ),
  'app.colors.description'                            => __( 'Once a new color has been added, clicking it will reveal the color-picker in addition to an input where you can rename each color for clearer labeling. Duplicate and delete buttons are visible on hover. Make sure to save all changes before exiting.', 'cornerstone' ),
  'app.colors.duplicate-name-error'                   => __( 'Two colors selections can not share the same name.', 'cornerstone' ),

  'app.colors.brand-primary'                          => __( 'Brand Primary', 'cornerstone' ),
  'app.colors.brand-secondary'                        => __( 'Brand Secondary', 'cornerstone' ),
  'app.colors.link'                                   => __( 'Link', 'cornerstone' ),
  'app.colors.link-interaction'                       => __( 'Link Interaction', 'cornerstone' ),


  // Responsive Text
  // ---------------

  'app.responsive-text.label'                         => __( 'Responsive Text', 'cornerstone' ),
  'app.responsive-text.selector'                      => __( 'Selector', 'cornerstone' ),
  'app.responsive-text.compress'                      => __( 'Compress', 'cornerstone' ),
  'app.responsive-text.min-size'                      => __( 'Minimum Size', 'cornerstone' ),
  'app.responsive-text.max-size'                      => __( 'Maximum Size', 'cornerstone' ),


  // Font Weights
  // ------------

  'app.font-weight.100'                               => __( '100 - Ultra Light', 'cornerstone' ),
  'app.font-weight.100italic'                         => __( '100 - Ultra Light (Italic)', 'cornerstone' ),
  'app.font-weight.200'                               => __( '200 - Light', 'cornerstone' ),
  'app.font-weight.200italic'                         => __( '200 - Light (Italic)', 'cornerstone' ),
  'app.font-weight.300'                               => __( '300 - Book', 'cornerstone' ),
  'app.font-weight.300italic'                         => __( '300 - Book (Italic)', 'cornerstone' ),
  'app.font-weight.400'                               => __( '400 - Regular', 'cornerstone' ),
  'app.font-weight.400italic'                         => __( '400 - Regular (Italic)', 'cornerstone' ),
  'app.font-weight.500'                               => __( '500 - Medium', 'cornerstone' ),
  'app.font-weight.500italic'                         => __( '500 - Medium (Italic)', 'cornerstone' ),
  'app.font-weight.600'                               => __( '600 - Semi-Bold', 'cornerstone' ),
  'app.font-weight.600italic'                         => __( '600 - Semi-Bold (Italic)', 'cornerstone' ),
  'app.font-weight.700'                               => __( '700 - Bold', 'cornerstone' ),
  'app.font-weight.700italic'                         => __( '700 - Bold (Italic)', 'cornerstone' ),
  'app.font-weight.800'                               => __( '800 - Extra Bold', 'cornerstone' ),
  'app.font-weight.800italic'                         => __( '800 - Extra Bold (Italic)', 'cornerstone' ),
  'app.font-weight.900'                               => __( '900 - Ultra Bold', 'cornerstone' ),
  'app.font-weight.900italic'                         => __( '900 - Ultra Bold (Italic)', 'cornerstone' ),


  // Custom Code
  // -----------

  'app.code-editors.css_placeholder_context'          => __( '/* Enter CSS you would like added only for this {{context}}. */ ', 'cornerstone' ),
  'app.code-editors.css_placeholder_global'           => __( '/* Enter CSS you would like added to your entire site. */ ', 'cornerstone' ),
  'app.code-editors.js_placeholder_context'          => __( '/* Enter Javascript you would like added only for this {{context}}. */ ', 'cornerstone' ),
  'app.code-editors.js_placeholder_global'           => __( '/* Enter Javascript you would like added to your entire site. */ ', 'cornerstone' ),


  // Choices
  // -------

  'app.choices.menu-named'                            => __('Menu: %s', 'cornerstone'),
  'app.choices.menu-location'                         => __('Location: %s', 'cornerstone'),

  'app.choices.menu-sample-default'                   => __( 'Sample', 'cornerstone' ),
  'app.choices.menu-sample-no-dropdowns'              => __( 'Sample (No Dropdowns)', 'cornerstone' ),
  'app.choices.menu-sample-split-1'                   => __( 'Sample (Split #1)', 'cornerstone' ),
  'app.choices.menu-sample-split-2'                   => __( 'Sample (Split #2)', 'cornerstone' ),


  // Sort
  // ----

  'app.sort.new-old'                                  => __( 'Newest', 'cornerstone' ),
  'app.sort.old-new'                                  => __( 'Oldest', 'cornerstone' ),
  'app.sort.a-z'                                      => __( 'A-Z', 'cornerstone' ),
  'app.sort.z-a'                                      => __( 'Z-A', 'cornerstone' ),
  'app.sort.popular'                                  => __( 'Popular', 'cornerstone' ),


  // Actions
  // -------

  'app.inspect'                                       => __( 'Inspect', 'cornerstone' ),
  'app.duplicate'                                     => __( 'Duplicate', 'cornerstone' ),
  'app.delete'                                        => __( 'Delete', 'cornerstone' ),
  'app.really-delete'                                 => __( 'Really Delete?', 'cornerstone' ),
  'app.erase'                                         => __( 'Erase', 'cornerstone' ),
  'app.really-erase'                                  => __( 'Really Erase?', 'cornerstone' ),
  'app.manage-layout'                                 => __( 'Manage Layout', 'cornerstone' ),


  // Errors
  // ------

  'app.preview-error.missing-zone.cs_masthead'         => __('No suitable preview area found. This is most common when you are using a "No Header" page template or layout. Try changing the page template, or assigning this header to a context where the template allows the header output.', 'cornerstone'),
  'app.preview-error.missing-zone.cs_colophon'         => __('No suitable preview area found. This is most common when you are using a "No Footer" page template or layout. Try changing the page template, or assigning this footer to a context where the template allows the footer output.', 'cornerstone'),
  'app.preview-error.missing-zone.cs_content'         => __('No suitable preview area found. This could happen when a third party plugin is overriding the content area or "The Content" is not being output by the current layout.', 'cornerstone'),
  'app.preview-error.missing-zone.cs_layout'          => __('No suitable preview area found. For layouts this can happen when a third party plugin is overidding the content area or the page you are viewing is not a valid type. Example you are working on an Archive, but viewing a single layout in the preview.', 'cornerstone'),

  'app.preview-error.load.default'                    => __('The preview could not load. This is most often related to a plugin conflict or aggressive page caching. Checking the developer console for errors could indicate what went wrong.', 'cornerstone'),
  'app.preview-error.load.https-mismatch'             => __('The preview could not load due to a http/https mismatch. Please check that HTTPS is properly configured on your site.', 'cornerstone'),
  'app.preview-error.load.cross-origin'               => __('The preview could not load due to misconfigured URLs. This could happen if you are using multiple environments and the site URL was not updated after migrating.', 'cornerstone'),
  'app.preview-error.load.incomplete'                 => __('The preview could not load due to the iframe response being incomplete. This is most often related to a plugin conflict, or customizations introducing a PHP error.', 'cornerstone'),
  'app.preview-error.load.timeout'                    => __('The preview was unresponsive after loading. This is most often related to a plugin conflict or aggressive page caching.', 'cornerstone'),

  'app.preview-error.loading.incomplete-html'         => __('The preview HTML did not include a closing </html>; tag. It may fail to load or work properly.', 'cornerstone'),


  // Preferences
  // -----------

  'app.preferences.title'                             => __('Preferences', 'cornerstone'),
  'app.preferences.description'                       => __('Configure how Cornerstone should look and behave. These settings are specific to your user account.', 'cornerstone'),
  'app.preferences.wp-toolbar-on'                     => __('The WordPress toolbar will be shown on next reload.', 'cornerstone'),
  'app.preferences.wp-toolbar-off'                    => __('The WordPress toolbar will be hidden on next reload.', 'cornerstone'),


  // Dynamic Content
  // ---------------

  'app.dc.field'                                      => __( 'Field', 'cornerstone'),
  'app.dc.title'                                      => __( 'Title', 'cornerstone'),
  'app.dc.name'                                       => __( 'Name', 'cornerstone'),
  'app.dc.slug'                                       => __( 'Slug', 'cornerstone'),
  'app.dc.description'                                => __( 'Description', 'cornerstone' ),
  'app.dc.url'                                        => __( 'URL', 'cornerstone' ),
  'app.dc.meta-key'                                   => __( 'Meta Key', 'cornerstone' ),
  'app.dc.meta'                                       => __( 'Meta', 'cornerstone' ),
  'app.dc.key'                                        => __( 'Key', 'cornerstone' ),
  'app.dc.index'                                      => __( 'Index', 'cornerstone' ),
  'app.dc.fallback'                                   => __( 'Fallback', 'cornerstone' ),
  'app.dc.id'                                         => __( 'ID', 'cornerstone' ),
  'app.dc.length'                                     => __( 'Length', 'cornerstone' ),
  'app.dc.count'                                      => __( 'Count', 'cornerstone'),
  'app.dc.redirect'                                   => __( 'Redirect', 'cornerstone'),
  'app.dc.total_count'                                => __( 'Total Count', 'cornerstone' ),

  'app.dc.group-title-post'                           => __( 'Post', 'cornerstone' ),
  'app.dc.group-title-archive'                        => __( 'Archive', 'cornerstone' ),
  'app.dc.group-title-term'                           => __( 'Term', 'cornerstone' ),
  'app.dc.group-title-global'                         => __( 'Global', 'cornerstone' ),
  'app.dc.group-title-parameters'                     => __( 'Parameters', 'cornerstone' ),
  'app.dc.group-title-url'                            => __( 'Url', 'cornerstone' ),
  'app.dc.group-title-user'                           => __( 'User', 'cornerstone' ),
  'app.dc.group-title-author'                         => __( 'Author', 'cornerstone' ),
  'app.dc.group-title-acf'                            => __( 'ACF', 'cornerstone' ),
  'app.dc.group-title-toolset'                        => __( 'Toolset', 'cornerstone' ),
  'app.dc.group-title-woocommerce'                    => __( 'WooCommerce', 'cornerstone' ),
  'app.dc.group-title-looper'                         => __( 'Looper', 'cornerstone' ),
  'app.dc.group-title-query'                          => __( 'Query', 'cornerstone' ),
  'app.dc.group-title-rivet'                          => __( 'Rivet', 'cornerstone' ),

  'app.dc.global.site-title'                          => __( 'Site Title', 'cornerstone' ),
  'app.dc.global.site-tagline'                        => __( 'Site Tagline', 'cornerstone' ),
  'app.dc.global.home-url'                            => __( 'Home URL', 'cornerstone' ),
  'app.dc.global.blog-url'                            => __( 'Blog URL', 'cornerstone' ),
  'app.dc.global.admin-url'                           => __( 'Admin URL', 'cornerstone' ),
  'app.dc.global.login-url'                           => __( 'Login URL', 'cornerstone' ),
  'app.dc.global.logout-url'                          => __( 'Logout URL', 'cornerstone' ),
  'app.dc.global.registration-url'                    => __( 'Registration URL', 'cornerstone' ),
  'app.dc.global.privacy-url'                         => __( 'Privacy Page URL', 'cornerstone' ),
  'app.dc.global.current-date'                        => __( 'Current Date', 'cornerstone' ),
  'app.dc.global.current-time'                        => __( 'Current Time', 'cornerstone' ),

  'app.dc.url.path'    => __( 'Path', 'cornerstone' ),
  'app.dc.url.param'   => __( 'Query String Parameter', 'cornerstone' ),
  'app.dc.url.segment' => __( 'Segment', 'cornerstone' ),
  'app.dc.url.full_path' => __( 'Full Path', 'cornerstone' ),

  'app.dc.user.display-name'                        => __( 'Display Name', 'cornerstone' ),
  'app.dc.user.email-address'                       => __( 'Email Address', 'cornerstone' ),
  'app.dc.user.gravatar-url'                        => __( 'Gravatar URL', 'cornerstone' ),
  'app.dc.user.registration-date'                   => __( 'Registration Date', 'cornerstone' ),
  'app.dc.user.registration-time'                   => __( 'Registration Time', 'cornerstone' ),
  'app.dc.user.author-url'                          => __( 'Author URL', 'cornerstone' ),
  'app.dc.user.website-url'                         => __( 'Website URL', 'cornerstone' ),
  'app.dc.user.bio'                                 => __( 'Biographical Info', 'cornerstone' ),
  'app.dc.user.usermeta'                            => __( 'Usermeta', 'cornerstone' ),

  'app.dc.wc.page-title'                              => __( 'Page Title', 'cornerstone' ),
  'app.dc.wc.shop-url'                                => __( 'Shop URL', 'cornerstone' ),
  'app.dc.wc.cart-url'                                => __( 'Cart URL', 'cornerstone' ),
  'app.dc.wc.checkout-url'                            => __( 'Checkout URL', 'cornerstone' ),
  'app.dc.wc.account-url'                             => __( 'Account URL', 'cornerstone' ),
  'app.dc.wc.terms-url'                               => __( 'Terms URL', 'cornerstone' ),
  'app.dc.wc.fallback-image'                          => __( 'Fallback Image', 'cornerstone' ),
  'app.dc.wc.cart-item-count-raw'                     => __( 'Cart Item Count (Raw)', 'cornerstone' ),
  'app.dc.wc.cart-item-count'                         => __( 'Cart Item Count (Realtime)', 'cornerstone' ),
  'app.dc.wc.cart-total-raw'                          => __( 'Cart Total (Raw)', 'cornerstone' ),
  'app.dc.wc.cart-total'                              => __( 'Cart Total (Realtime)', 'cornerstone' ),
  'app.dc.wc.cart-subtotal-raw'                       => __( 'Cart Subtotal (Raw)', 'cornerstone' ),
  'app.dc.wc.cart-subtotal'                           => __( 'Cart Subtotal (Realtime)', 'cornerstone' ),
  'app.dc.wc.currency-symbol'                         => __( 'Currency Symbol', 'cornerstone' ),
  'app.dc.wc.product-price'                           => __( 'Product Price', 'cornerstone' ),
  'app.dc.wc.product-price-html'                      => __( 'Product Price HTML', 'cornerstone' ),
  'app.dc.wc.product-regular-price'                   => __( 'Product Regular Price', 'cornerstone' ),
  'app.dc.wc.product-sale-price'                      => __( 'Product Sale Price', 'cornerstone' ),
  'app.dc.wc.product-sale-percentage-off'             => __( 'Product Sale Percentage Off', 'cornerstone' ),
  'app.dc.wc.product-id'                              => __( 'Product ID', 'cornerstone' ),
  'app.dc.wc.product-class'                           => __( 'Product Class', 'cornerstone' ),
  'app.dc.wc.product-sku'                             => __( 'Product SKU', 'cornerstone' ),
  'app.dc.wc.product-title'                           => __( 'Product Title', 'cornerstone' ),
  'app.dc.wc.product-url'                             => __( 'Product Url', 'cornerstone' ),
  'app.dc.wc.product-short-description'               => __( 'Product Short Description', 'cornerstone' ),
  'app.dc.wc.product-image-id'                        => __( 'Product Image', 'cornerstone' ),
  'app.dc.wc.product-image'                           => __( 'Product Image Url', 'cornerstone' ),
  'app.dc.wc.product-gallery-ids'                     => __( 'Product Gallery IDs', 'cornerstone' ),
  'app.dc.wc.product-stock'                           => __( 'Product Stock', 'cornerstone' ),
  'app.dc.wc.product-rating-count'                    => __( 'Product Rating Count', 'cornerstone' ),
  'app.dc.wc.product-average-rating'                  => __( 'Product Average Rating', 'cornerstone' ),
  'app.dc.wc.product-review-count'                    => __( 'Product Review Count', 'cornerstone' ),
  'app.dc.wc.product-description'                     => __( 'Product Description', 'cornerstone' ),
  'app.dc.wc.product-additional-information'          => __( 'Product Additional Information', 'cornerstone' ),
  'app.dc.wc.product-reviews'                         => __( 'Product Reviews', 'cornerstone' ),

  'app.dc.wc.product-weight'                          => __( 'Product Weight', 'cornerstone' ),
  'app.dc.wc.product-length'                          => __( 'Product Length', 'cornerstone' ),
  'app.dc.wc.product-width'                           => __( 'Product Width', 'cornerstone' ),
  'app.dc.wc.product-height'                          => __( 'Product Height', 'cornerstone' ),
  'app.dc.wc.product-dimensions'                      => __( 'Product Dimensions', 'cornerstone' ),
  'app.dc.wc.product-shipping-class'                  => __( 'Product Shipping Class', 'cornerstone' ),
  'app.dc.wc.product-shipping-class-slug'             => __( 'Product Shipping Class Slug', 'cornerstone' ),
  'app.dc.wc.product-type'                            => __( 'Product Type', 'cornerstone' ),

  'app.dc.looper.field'                               => __( 'Field', 'cornerstone' ),
  'app.dc.looper.item'                                => __( 'Current Item', 'cornerstone' ),
  'app.dc.looper.index'                               => __( 'Current Item Index', 'cornerstone' ),
  'app.dc.looper.count'                               => __( 'Total Item Count', 'cornerstone' ),
  'app.dc.looper.debug-provider'                      => __( 'Debug Provider', 'cornerstone' ),
  'app.dc.looper.debug-consumer'                      => __( 'Debug Consumer', 'cornerstone' ),

  'app.dc.query.current-page'                         => __( 'Current Page Number', 'cornerstone' ),
  'app.dc.query.found-posts'                          => __( 'Total Posts Found', 'cornerstone' ),
  'app.dc.query.total-pages'                          => __( 'Total Pages', 'cornerstone' ),
  'app.dc.query.search-query'                         => __( 'Search Query', 'cornerstone' ),
  'app.dc.query.query-var'                            => __( 'Query Var', 'cornerstone' ),

  'app.dc.parameters.element'                       => __( 'Element Parameters', 'cornerstone' ),

  // Components
  // -------------

  'app.components.add-placeholder'                 => __( 'Global Block Name', 'cornerstone' ),
  'app.components.blank'                           => __( 'Click + to create your first Component set.', 'cornerstone' ),
  'app.components.no-preview-available'            => __( 'No Preview Available', 'cornerstone' ),
  'app.components.click-to-edit'                   => __( 'Click {{icon}} to edit.', 'cornerstone' ),
  'app.components.entities'                        => __( 'Elements', 'cornerstone' ),

  'app.components.error.passthru'                  => __( 'Parameters can not be added to pass through components.', 'cornerstone' ),
  'app.components.error.locked'                    => __( 'Parameters can not be modified on locked elements.', 'cornerstone' ),
  'app.components.error.no-lock-passthru'          => __( "Virtual component elements like Slots and Pass Through\n components can not be locked", 'cornerstone' ),

  // Parameters
  // -------------
  'app.parameters.abstract-complexity'             => __( "Abstract away complexity by adding custom parameters to your Element and\n simplifying your interface.", 'cornerstone' ),
  'app.parameters.locked-error'                    => __( "Locked Elements are closed off to internal edits and cannot be duplicated or deleted.", 'cornerstone' ),


  // Translation (WPML)
  // ------------------

  'app.translate'                                     => __( 'Translate', 'cornerstone' ),
  'app.translation.entity'                            => __( 'Translation', 'cornerstone' ),
  'app.translation.blank'                             => __( 'This content has not been translated into the active language.', 'cornerstone' ),
  'app.translation.start-blank'                       => __( 'Start Blank', 'cornerstone' ),
  'app.translation.or'                                => __( 'or', 'cornerstone' ),
  'app.translation.create-message'                    => __( 'This <strong>{{context}}</strong> does not have a <strong>{{lang}}</strong> translation. Start with a blank slate or choose an existing translation to begin with. Please save before you do this.', 'cornerstone' ),
  'app.translation.failed-to-create'                  => __( 'Failed to create {{context}}. {{message}}', 'cornerstone' ),


  // Regions
  // -------

  'app.regions.top'                                   => __( 'Top', 'cornerstone' ),
  'app.regions.left'                                  => __( 'Left', 'cornerstone' ),
  'app.regions.right'                                 => __( 'Right', 'cornerstone' ),
  'app.regions.bottom'                                => __( 'Bottom', 'cornerstone' ),
  'app.regions.footer'                                => __( 'Footer', 'cornerstone' ),
  'app.regions.no-bars'                               => __( 'This Region has no Bars.', 'cornerstone' ),
  'app.regions.single-bar'                            => __( 'This Region only supports a single Bar.', 'cornerstone' ),


  // History
  // -------

  'app.history.title'                                 => __( 'History', 'cornerstone' ),
  'app.history.builder-loaded'                        => __( 'Builder Loaded', 'cornerstone' ),
  'app.history.column-layout-updated'                 => __( 'Column Layout Updated', 'cornerstone' ),
  'app.history.contents-deleted'                      => __( '{{context}} Contents Deleted', 'cornerstone' ),
  'app.history.spacing-removed'                       => __( '{{context}} Spacing Removed', 'cornerstone' ),
  'app.history.reset-style'                           => __( '{{context}} Style Reset', 'cornerstone' ),
  'app.history.set-label'                             => __( '{{context}} Renamed', 'cornerstone' ),
  'app.history.create-row-layout'                     => __( 'Row Layout Created', 'cornerstone' ),
  'app.history.create-grid-layout'                    => __( 'Grid Layout Created', 'cornerstone' ),

  // Conditions
  // ----------

  'app.conditions.is'               => __('is', 'cornerstone'),
  'app.conditions.is-not'           => __('is not', 'cornerstone'),
  'app.conditions.is-condition'     => __('is %s', 'cornerstone'),
  'app.conditions.is-not-condition' => __('is not %s', 'cornerstone'),
  'app.conditions.before'           => __('before', 'cornerstone'),
  'app.conditions.after'            => __('after', 'cornerstone'),
  'app.conditions.being-viewed'     => __('being viewed', 'cornerstone'),
  'app.conditions.initial'          => __('initial', 'cornerstone'),
  'app.conditions.empty'            => __('empty', 'cornerstone'),
  'app.conditions.repeated'         => __('repeated', 'cornerstone'),
  'app.conditions.set'              => __('set', 'cornerstone'),
  'app.conditions.true'             => __('true', 'cornerstone'),
  'app.conditions.logged-in'        => __('logged in', 'cornerstone'),
  'app.conditions.required'         => __('Required', 'cornerstone'),

  // Date
  // ----

  'app.date.before' => __( 'Before: {{date}}', 'cornerstone' ),
  'app.date.after'  => __( 'After: {{date}}', 'cornerstone' ),
  'app.date.range'  => __( '{{before}} - {{after}}', 'cornerstone' ),

  // Element Statuses
  // ----------------

  'app.status.component-export'    => __( 'Component Export', 'cornerstone' ),
  'app.status.component-slot'      => __( 'Component Slot', 'cornerstone' ),
  'app.status.component-thru'      => __( 'Component Pass Through', 'cornerstone' ),
  'app.status.looper-provider'     => __( 'Looper Provider', 'cornerstone' ),
  'app.status.looper-consumer'     => __( 'Looper Consumer', 'cornerstone' ),
  'app.status.conditions'          => __( 'Conditions', 'cornerstone' ),
  'app.status.custom-atts'         => __( 'Custom Attributes', 'cornerstone' ),
  'app.status.element-css'         => __( 'Element CSS', 'cornerstone' ),
  'app.status.hide-bp'             => __( 'Hide During Breakpoints', 'cornerstone' ),
  'app.status.dynamic-content'     => __( 'Dynamic Content', 'cornerstone' ),
  'app.status.scroll-effects'      => __( 'Scroll Effects', 'cornerstone' ),
  'app.status.interaction-effects' => __( 'Interaction Effects', 'cornerstone' ),
  'app.status.effects-provider'    => __( 'Link Child Interactions', 'cornerstone' ),
  'app.status.label'               => __( 'Label: {{label}}', 'cornerstone' ),
  'app.status.parameters'          => __( 'Parameters', 'cornerstone' ),

  'app.element-status.component-export.description'    => __( 'This element is exported as a Component to be used in other builders and thru shortcode. Right click to copy the text.', 'cornerstone' ),
  'app.element-status.component-slot.description'      => __( 'This element is part of a Component and provides a location to place child elements.', 'cornerstone' ),
  'app.element-status.looper-provider.description'     => __( 'Provides a new data source (Posts, custom data, etc.) to be used by Looper Consumers.', 'cornerstone' ),
  'app.element-status.looper-consumer.description'     => __( 'Iterates over one or more items of a Looper Provider data source, making the item available via Dynamic Content.', 'cornerstone' ),
  'app.element-status.conditions.description'          => __( 'Unless the given rules apply, this element is not output and its HTML is omitted from the DOM.', 'cornerstone' ),
  'app.element-status.custom-atts.description'         => __( 'The root HTML tag of this element includes additional attributes.', 'cornerstone' ),
  'app.element-status.element-css.description'         => __( 'Additional CSS scoped to the element\'s root HTML tag is being applied.', 'cornerstone' ),
  'app.element-status.hide-bp.description'             => __( 'On selected screen sizes, visually hide this element.', 'cornerstone' ),
  'app.element-status.dynamic-content.description'     => __( 'The content of this element is being retrieved from a data source.', 'cornerstone' ),
  'app.element-status.scroll-effects.description'      => __( 'This element modifies its presentation based on being present in the viewport or not.', 'cornerstone' ),
  'app.element-status.interaction-effects.description' => __( 'Interacting (hover, focus, etc.) with this element will trigger.', 'cornerstone' ),
  'app.element-status.effects-provider.description'    => __( 'Interacting with this element, will cause Interaction Effects on descendants to be triggered.', 'cornerstone' ),
  'app.element-status.parameters.description'    => __( 'This element has custom parameters created for it.', 'cornerstone' ),

);
