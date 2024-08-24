<tr>
    <th scope="row"><?php _e('Button skin', 'nextend-facebook-connect'); ?></th>
    <td>
        <fieldset>
            <label>
                <input type="radio" name="skin"
                       value="green" <?php if ($settings->get('skin') == 'green') : ?> checked="checked" <?php endif; ?>>
                <span><?php _e('Green', 'nextend-facebook-connect'); ?></span><br/>
                <img src="<?php echo plugins_url('images/spotify/green.png', NSL_ADMIN_PATH) ?>"/>
            </label>
            <label>
                <input type="radio" name="skin"
                       value="black" <?php if ($settings->get('skin') == 'black') : ?> checked="checked" <?php endif; ?>>
                <span><?php _e('Black', 'nextend-facebook-connect'); ?></span><br/>
                <img src="<?php echo plugins_url('images/spotify/black.png', NSL_ADMIN_PATH) ?>"/>
            </label>
            <label>
                <input type="radio" name="skin"
                       value="white" <?php if ($settings->get('skin') == 'white') : ?> checked="checked" <?php endif; ?>>
                <span><?php _e('White', 'nextend-facebook-connect'); ?></span><br/>
                <img src="<?php echo plugins_url('images/spotify/white.png', NSL_ADMIN_PATH) ?>"/>
            </label>

        </fieldset>
    </td>
</tr>