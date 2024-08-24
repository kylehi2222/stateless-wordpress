<?php

class Cornerstone_Looper_Provider_Dynamic_Content extends Cornerstone_Looper_Provider_Generic_Array {

  public function get_array_items( $element ) {
    return cs_dynamic_content( $element['looper_provider_dc'], false );
  }

}
