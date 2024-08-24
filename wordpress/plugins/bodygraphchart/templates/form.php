<div id="bgc-form">
    <div class="form-wrapper">
        <form action="?bgc-generate=1" method="post" onsubmit="disableSubmitButton()">
            <div class="input-field">
                <?php if ( $atts['action'] == 'create_post' ) : ?>
                <div class="input-field">
                    <!-- <label for="name"><?php _e('Name', 'bgc'); ?></label> -->
                    <input type="text" name="_name" id="name" placeholder="Name" required>
                </div>
                <?php endif; ?>
                <!-- <label for="year"><?php _e('Select Year', 'bgc'); ?></label> -->
                <select name="_year" id="year" required>
                    <option value=""><?php _e('Birth Year', 'bgc'); ?></option>
                    <?php for ($i = date('Y'); $i >= date('Y', strtotime('-150 years')); $i--) : ?>
                        <option value="<?php echo $i; ?>" <?php selected($i, (isset($chart['date']) ? date('Y', strtotime($chart['date'])) : '')); ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="input-field">
             <!-- <label for="month"><?php _e('Month', 'bgc'); ?></label> -->
                <select name="_month" id="month" required>
                    <option value=""><?php _e('Birth Month', 'bgc'); ?></option>
                    <?php for ($i = 1; $i <= 12; $i++) : ?>
                        <option value="<?php echo str_pad($i, 2, 0, STR_PAD_LEFT); ?>" <?php selected(str_pad($i, 2, 0, STR_PAD_LEFT), (isset($chart['date']) ? date('m', strtotime($chart['date'])) : '')); ?>>
                            <?php echo date_i18n('F', mktime(0, 0, 0, $i, 10)); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="input-field">
                <!-- <label for="day"><?php _e('Day', 'bgc'); ?></label> -->
                <select name="_day" id="day" required>
                    <option value=""><?php _e('Birth Day', 'bgc'); ?></option>
                    <?php for ($i = 1; $i <= 31; $i++) : ?>
                        <?php $i = str_pad($i, 2, 0, STR_PAD_LEFT); ?>
                        <option value="<?php echo $i; ?>" <?php selected($i, (isset($chart['date']) ? date('d', strtotime($chart['date'])) : '')); ?>>
                            <?php echo $i; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="grid-row margin-no">
                <div class="grid-6 padding-l">
                    <div class="input-field">
                 <!-- <label for="hour"><?php _e('Select Hour (24HR Format)', 'bgc'); ?></label> -->
                        <select name="_hour" id="hour" required>
                            <option value="">Birth Hour (24HR Format)</option>
                            <?php for ($i = 0; $i <= 23; $i++) : ?>
                                <?php $i = str_pad($i, 2, 0, STR_PAD_LEFT); ?>
                                <option value="<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="grid-6 padding-r">
                    <div class="input-field">
                <!--        <label for="minutes"><?php _e('Minutes', 'bgc'); ?></label> -->
                        <select name="_minutes" id="minutes" required>
                            <option value="">Birth Minutes</option>
                            <?php for ($i = 0; $i <= 59; $i++) : ?>
                                <?php $i = str_pad($i, 2, 0, STR_PAD_LEFT); ?>
                                <option value="<?php echo $i; ?>">
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="input-field">
             <!--   <label for="location"><?php _e('Birth Location', 'bgc'); ?></label> -->
                <input type="text" placeholder="Birth City.." name="_location" id="location" data-api-key="<?php echo $api_key; ?>" required>
                <input type="hidden" name="_timezone" id="timezone">
            </div>
            <div class="buttons">
                <input type="hidden" name="action" value="<?php echo esc_attr($atts['action']); ?>">
                <input type="submit" id="submit-button" class="" value="<?php _e('Continue..', 'bgc'); ?>">
            </div>
        </form>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    if ($('#year').selectize()[0]) $('#year')[0].selectize.destroy();
    if ($('#month').selectize()[0]) $('#month')[0].selectize.destroy();
    if ($('#day').selectize()[0]) $('#day')[0].selectize.destroy();

    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input[required], select[required]');
    const submitButton = document.getElementById('submit-button');

    function checkInputs() {
        let allFilled = true;
        inputs.forEach(input => {
            if (!input.value) {
                allFilled = false;
            }
        });
        submitButton.disabled = !allFilled;
    }

    inputs.forEach(input => {
        input.addEventListener('input', checkInputs);
    });

    checkInputs(); // Initial check to set the button state
});

function disableSubmitButton() {
    var submitButton = document.getElementById('submit-button');
    submitButton.disabled = true;
    submitButton.value = 'Processing...';
}
</script>
