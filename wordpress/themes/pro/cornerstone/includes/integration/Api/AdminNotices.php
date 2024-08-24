<?php

/**
 * XML Change Error notice
 */
add_action( 'admin_notices', function () {
  if (!cs_stack_get_value('cs_api_xml_legacy_mode')) {
    return;
  }


  $message = __( 'External REST API XML parser has added a breaking change and you are running legacy mode. If you are not using any XML in your External API, go into the Theme Options and disable "XML Legacy Mode". Please see the guide here on how to migrate your usage of XML <a href="https://theme.co/docs/external-api-xml-change" target="_blank" rel="noreferrer noopener">here</a>', 'cornerstone' );

  echo "
    <div class='error notice'>
        <p>$message</p>
    </div>
  ";

});
