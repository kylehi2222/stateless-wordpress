<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb;
$wpaicg_embedding_page = isset($_GET['wpage']) && !empty($_GET['wpage']) ? sanitize_text_field($_GET['wpage']) : 1;
$wpaicg_sub_action = isset($_GET['sub']) && !empty($_GET['sub']) ? sanitize_text_field($_GET['sub']) : false;
if($wpaicg_sub_action == 'deleteall'){
    $ids = $wpdb->get_results("SELECT ID FROM ".$wpdb->posts." WHERE post_type='wpaicg_pdfembed'");
    $ids = wp_list_pluck($ids,'ID');
    if(count($ids)) {
        WPAICG\WPAICG_PDF::get_instance()->wpaicg_delete_embeddings_ids($ids);
    }
    echo '<script>window.location.href = "'.admin_url('admin.php?page=wpaicg_chatgpt&action=pdf').'";</script>';
    exit;
}
$wpaicg_embeddings = new WP_Query(array(
    'post_type' => 'wpaicg_pdfembed',
    'posts_per_page' => 40,
    'paged' => $wpaicg_embedding_page,
    'order' => 'DESC',
    'orderby' => 'date'
));
?>
<style>
    .wpaicg_modal{
        top: 5%;
        height: 90%;
        position: relative;
    }
    .wpaicg_modal_content{
        max-height: calc(100% - 103px);
        overflow-y: auto;
    }
    .wp-core-ui .button.wpaicg-danger-btn{
        background: #c90000;
        color: #fff;
        border-color: #cb0000;
    }
</style>
<?php
if($wpaicg_embeddings->have_posts()):
    ?>
    <div class="tablenav top wpaicg-mb-10">
        <div class="alignleft actions bulkactions">
            <a onclick="return confirm('<?php echo esc_html__('Warning! All indexes will be deleted from Pinecone and elsewhere. Are you sure?','gpt3-ai-content-generator')?>')" href="<?php echo admin_url('admin.php?page=wpaicg_chatgpt&action=pdf&sub=deleteall')?>" class="button wpaicg-danger-btn"><?php echo esc_html__('Delete Everything','gpt3-ai-content-generator')?></a>
            <button class="button btn-delete-embeddings wpaicg-danger-btn"><?php echo esc_html__('Delete Selected','gpt3-ai-content-generator')?></button>
        </div>
    </div>
<?php
endif;
?>
<table class="wp-list-table widefat fixed striped table-view-list posts">
    <thead>
    <tr>
        <td id="cb" class="manage-column column-cb check-column" scope="col"><input type="checkbox" class="wpaicg-select-all"></td>
        <th scope="col"><?php echo esc_html__('Content','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Token','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Estimated','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('AI Provider','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Model','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Vector DB','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Date','gpt3-ai-content-generator')?></th>
        <th scope="col"><?php echo esc_html__('Action','gpt3-ai-content-generator')?></th>
    </tr>
    </thead>
    <tbody>
    <?php
    if($wpaicg_embeddings->have_posts()){
        foreach ($wpaicg_embeddings->posts as $wpaicg_embedding){
            $token = get_post_meta($wpaicg_embedding->ID,'wpaicg_embedding_token',true);
            $wpaicg_embedding_type = get_post_meta($wpaicg_embedding->ID,'wpaicg_embedding_type',true);
            $wpaicg_embedding_status = get_post_meta($wpaicg_embedding->ID,'wpaicg_embeddings_reindex',true);
            $wpaicg_provider = get_post_meta($wpaicg_embedding->ID, 'wpaicg_provider', true);
            $wpaicg_index = get_post_meta($wpaicg_embedding->ID, 'wpaicg_index', true);
            // Check if wpaicg_index exists and is not empty, then proceed to determine the DB provider
            if (!empty($wpaicg_index)) {
                // Check if wpaicg_index contains 'pinecone.io'
                if (strpos($wpaicg_index, 'pinecone.io') !== false) {
                    $dbProvider = 'Pinecone';
                    // Parse Vector DB information specific to Pinecone
                    $parts = explode('-', $wpaicg_index);
                    $indexName = $parts[0];
                    $projectName = substr($parts[1], 0, strpos($parts[1], '.svc'));
                    $vectorDBInfo = "<div style='font-size: 90%;'><strong>DB:</strong> $dbProvider<br><strong>Project:</strong> $projectName<br><strong>Index:</strong> $indexName</div>";
                } else {
                    // Default to Qdrant if 'pinecone.io' is not found
                    $dbProvider = 'Qdrant';
                    $vectorDBInfo = "<div style='font-size: 90%;'><strong>DB:</strong> $dbProvider<br><strong>Collection:</strong> $wpaicg_index</div>";
                }
            } else {
                // Assign to Pinecone by default if wpaicg_index is not set or empty
                $dbProvider = 'Pinecone';
                $vectorDBInfo = "<div style='font-size: 90%;'><strong>DB:</strong> Pinecone</div>";
            }

            // Define allowed HTML tags for wp_kses
            $allowed_html = array(
                'div' => array(
                    'style' => array()
                ),
                'strong' => array(),
                'br' => array(),
            );
            $wpaicg_emb_model = get_post_meta($wpaicg_embedding->ID, 'wpaicg_model', true);

            // Display empty or placeholder if fields are not available
            $wpaicg_provider_display = !empty($wpaicg_provider) ? esc_html($wpaicg_provider) : '';
            
            $wpaicg_emb_model_display = !empty($wpaicg_emb_model) ? esc_html($wpaicg_emb_model) : 'text-embedding-ada-002';

            // Calculate estimated cost based on the model
            if (!empty($wpaicg_emb_model)) {
                switch ($wpaicg_emb_model) {
                    case 'text-embedding-3-small':
                        $costPerToken = 0.00002 / 1000;
                        break;
                    case 'text-embedding-3-large':
                        $costPerToken = 0.00013 / 1000;
                        break;
                    default:
                        // Default to the cost of 'text-embedding-ada-002' if the model is not recognized
                        $costPerToken = 0.00010 / 1000;
                }
            } else {
                // Use 'text-embedding-ada-002' cost if model is empty
                $costPerToken = 0.00010 / 1000;
            }
            $estimatedCost = !empty($token) ? number_format((int)esc_html($token) * $costPerToken, 8) . '$' : '--';
            ?>
            <tr id="wpaicg-builder-<?php echo esc_html($wpaicg_embedding->ID)?>">
                <th scope="row" class="check-column">
                    <input class="cb-select-embedding" id="cb-select-<?php echo esc_html($wpaicg_embedding->ID);?>" type="checkbox" name="ids[]" value="<?php echo esc_html($wpaicg_embedding->ID);?>">
                </th>
                <td><a data-content="<?php echo htmlentities(wp_kses_post($wpaicg_embedding->post_content),ENT_QUOTES,'UTF-8')?>" href="javascript:void(0)" class="wpaicg-embedding-content"><?php echo esc_html($wpaicg_embedding->post_title)?>..</a></td>
                <td><?php echo esc_html($token)?></td>
                <td><?php echo esc_html($estimatedCost)?></td>
                <td><?php echo esc_html($wpaicg_provider_display) ?></td>
                <td><?php echo esc_html($wpaicg_emb_model_display) ?></td>
                <td><?php echo wp_kses($vectorDBInfo, $allowed_html); ?></td>
                <td><?php echo esc_html($wpaicg_embedding->post_date)?></td>
                <td><button data-id="<?php echo esc_html($wpaicg_embedding->ID)?>" class="button button-link-delete wpaicg_delete button-small"><?php echo esc_html__('Delete','gpt3-ai-content-generator')?></button></td>
            </tr>
            <?php
        }
    }
    ?>
    </tbody>
</table>
<div class="wpaicg-paginate">
    <?php
    echo paginate_links( array(
        'base'         => admin_url('admin.php?page=wpaicg_chatgpt&action=pdf&wpage=%#%'),
        'total'        => $wpaicg_embeddings->max_num_pages,
        'current'      => $wpaicg_embedding_page,
        'format'       => '?wpage=%#%',
        'show_all'     => false,
        'prev_next'    => false,
        'add_args'     => false,
    ));
    ?>
</div>
<script>
    jQuery(document).ready(function ($) {
        function wpaicgLoading(btn) {
            btn.attr('disabled', 'disabled');
            if (!btn.find('spinner').length) {
                btn.append('<span class="spinner"></span>');
            }
            btn.find('.spinner').css('visibility', 'unset');
        }

        function wpaicgRmLoading(btn) {
            btn.removeAttr('disabled');
            btn.find('.spinner').remove();
        }

        $('.wpaicg_modal_close').click(function () {
            $('.wpaicg_modal_close').closest('.wpaicg_modal').hide();
            $('.wpaicg-overlay').hide();
        })
        $('.wpaicg-embedding-content').click(function () {
            var content = $(this).attr('data-content');
            content = content.replace(/\n/g, "<br />");
            $('.wpaicg_modal_title').html('<?php echo esc_html__('Embedding Content', 'gpt3-ai-content-generator')?>');
            $('.wpaicg_modal_content').html(content);
            $('.wpaicg-overlay').show();
            $('.wpaicg_modal').show();
        });
        $('.btn-delete-embeddings').click(function (){
            var conf = confirm('<?php echo esc_html__('Warning! Entries will be deleted from Pinecone and elsewhere. Are you sure?','gpt3-ai-content-generator')?>')
            if(conf) {
                var btn = $(this);
                var ids = [];
                $('.cb-select-embedding:checked').each(function (idx, item) {
                    ids.push($(item).val())
                });
                if (ids.length) {
                    $.ajax({
                        url: '<?php echo admin_url('admin-ajax.php')?>',
                        data: {action: 'wpaicg_pdfs_delete', ids: ids,'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                        dataType: 'JSON',
                        type: 'POST',
                        beforeSend: function () {
                            wpaicgLoading(btn);
                        },
                        success: function (res) {
                            window.location.reload();
                        },
                        error: function () {

                        }
                    });
                } else {
                    alert('<?php echo esc_html__('Nothing to do','gpt3-ai-content-generator')?>');
                }
            }
        });
        $(document).on('click','.wpaicg_delete' ,function (e){
            var btn = $(e.currentTarget);
            var id = btn.attr('data-id');
            var conf = confirm('<?php echo esc_html__('Are you sure?','gpt3-ai-content-generator')?>');
            if(conf){
                $.ajax({
                    url: '<?php echo admin_url('admin-ajax.php')?>',
                    data: {action: 'wpaicg_pdfs_delete', ids: [id],'nonce': '<?php echo wp_create_nonce('wpaicg-ajax-nonce')?>'},
                    dataType: 'JSON',
                    type: 'POST',
                    beforeSend: function (){
                        wpaicgLoading(btn);
                    },
                    success: function (res){
                        wpaicgRmLoading(btn);
                        if(res.status === 'success'){
                            $('#wpaicg-builder-'+id).remove();
                        }
                        else{
                            alert(res.msg);
                        }
                    },
                    error: function (){
                        wpaicgRmLoading(btn);
                        alert('<?php echo esc_html__('Something went wrong','gpt3-ai-content-generator')?>');
                    }
                })
            }
        });
    });
</script>
