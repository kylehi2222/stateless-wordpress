<div class="darkmysite_license_form_main">


    <div class="darkmysite_license_form_header">
        <div class="darkmysite_icon darkmysite_ignore"></div>
    </div>


    <div class="darkmysite_license_form_body">
        <div class="darkmysite_license_form_body_container">


            <div class="darkmysite_license_form_main_area">
                <div class="darkmysite_license_form_main_area_header">
                    <div class="darkmysite_license_form_main_area_header_details">
                        <h2>License Activation</h2>
                        <p>Activate DarkMySite Pro with License Code</p>
                    </div>
                </div>

                <div class="darkmysite_license_form_main_area_body">
                    <div class="darkmysite_license_form_main_area_body_container">

                        <div class="darkmysite_license_form_error_msg">
                            Invalid License Code or Email Address.
                        </div>

                        <div class="darkmysite_license_form_single_form_element">
                            <label for="darkmysite_license_form_email_address">Email</label>
                            <input type="email" id="darkmysite_license_form_email_address"/>
                            <p>Enter the email address used to purchase the license.</p>
                        </div>

                        <div class="darkmysite_license_form_single_form_element">
                            <label for="darkmysite_license_form_license_code">License Code</label>
                            <input type="text" id="darkmysite_license_form_license_code"/>
                            <p>Enter the License Code received by email after purchasing.</p>
                        </div>

                        <div class="darkmysite_license_form_single_form_element">
                            <button onclick="darkmysite_license_activate(`<?php echo esc_url(DARKMYSITE_PRO_URL); ?>`)">Activate</button>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </div>


</div>

