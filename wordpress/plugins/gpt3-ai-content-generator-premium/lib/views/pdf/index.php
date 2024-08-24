<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wpaicg_pdf">
    <div class="nice-form-group">
        <label for="wpaicg_pdf_file"><?php echo esc_html__('Upload PDF','gpt3-ai-content-generator')?></label>
        <input type="file" class="wpaicg_pdf_file" accept="application/pdf" style="display: inline;width: 300px;padding: 0.5em;">
        <button style="padding: 0.7em 1.7em;" class="button button-primary wpaicg_pdf_start"><?php echo esc_html__('Start','gpt3-ai-content-generator')?></button>
    </div>
</div>
<div class="wpaicg_pdf_progress" style="display: none">
    <span></span>
</div>
<p></p>
<div class="wpaicg_pdf_message"></div>

<script>
    jQuery(document).ready(function ($){
        function wpaicgLoading(btn){
            btn.attr('disabled','disabled');
            if(!btn.find('spinner').length){
                btn.append('<span class="spinner"></span>');
            }
            btn.find('.spinner').css('visibility','unset');
        }
        function wpaicgRmLoading(btn){
            btn.removeAttr('disabled');
            btn.find('.spinner').remove();
        }
        var wpaicg_pdf_start = $('.wpaicg_pdf_start');
        var wpaicg_pdf_file = $('.wpaicg_pdf_file');
        var wpaicg_pdf_message = $('.wpaicg_pdf_message');
        var wpaicg_pdf_progress = $('.wpaicg_pdf_progress');
        function wpaicgDoPDFPage(start,filename, contents,callback){
            var content = contents[start];
            var page = start+1;
            $.ajax({
                url: '<?php echo admin_url('admin-ajax.php')?>',
                type:'POST',
                dataType: 'JSON',
                beforeSend: function(){
                    wpaicg_pdf_message.html('<?php echo esc_html__('Uploading page: ','gpt3-ai-content-generator')?>'+page);
                },
                data: {
                    nonce: '<?php echo wp_create_nonce('wpaicg-ajax-action')?>',
                    content: content,
                    action: 'wpaicg_admin_pdf',
                    page: page,
                    filename: filename
                },
                success: function(res){
                    if(res.status === 'success'){
                        var width = wpaicg_pdf_progress.width();
                        var readWidth = width*0.1;
                        var leftWidth = width - (width*0.1);
                        var perWidth = leftWidth/contents.length;
                        var progressWidth = readWidth+(perWidth*page);
                        wpaicg_pdf_progress.find('span').width(progressWidth);
                        if(page === contents.length){
                            callback(res);
                        }
                        else{
                            wpaicgDoPDFPage(page,filename,contents,callback);
                        }
                    }
                    else{
                        wpaicgRmLoading(wpaicg_pdf_start);
                        wpaicg_pdf_file.val('');
                        wpaicg_pdf_progress.addClass('wpaicg_error');
                        wpaicg_pdf_message.html(res.msg);
                    }
                }
            })
        }
        wpaicg_pdf_start.on('click',async function (){
            if(wpaicg_pdf_file[0].files.length){
                var file = wpaicg_pdf_file[0].files[0];
                if(file.type === 'application/pdf'){
                    wpaicgLoading(wpaicg_pdf_start);
                    wpaicg_pdf_message.show();
                    wpaicg_pdf_progress.show();
                    wpaicg_pdf_message.html('<?php echo esc_html__('Reading PDF file','gpt3-ai-content-generator')?>');
                    wpaicg_pdf_progress.removeClass('wpaicg_error');
                    wpaicg_pdf_progress.find('span').width('10%');
                    var _OBJECT_URL = URL.createObjectURL(file);
                    var loadingTask = pdfjsLib.getDocument({url: _OBJECT_URL});
                    var pageContents = [];
                    var pageNumbers = 0;
                    await loadingTask.promise.then(async function (pdf) {
                        pageNumbers = pdf.numPages;
                        for (var i = 1; i <= pageNumbers; i++) {
                            var page = await pdf.getPage(i);
                            var textContent = await page.getTextContent();
                            pageContents.push(textContent.items.map(u => u.str).join("\n"));
                        }
                    });
                    if(pageContents.length) {
                        wpaicgDoPDFPage(0,file.name,pageContents,function(res){
                            wpaicgRmLoading(wpaicg_pdf_start);
                            wpaicg_pdf_file.val('');
                            if(res.status === 'success'){
                                // Display success message
                                document.querySelector('.wpaicg-embedding-success-message').style.display = 'block';
                                setTimeout(function() {
                                    document.querySelector('.wpaicg-embedding-success-message').style.display = 'none';
                                }, 2000);

                                // trigger reload
                                document.getElementById('reload-items').click();

                                // hide wpaicg_pdf_progress
                                wpaicg_pdf_progress.hide();

                                // hide wpaicg_pdf_message
                                wpaicg_pdf_message.hide();
                            }
                            else{
                                wpaicg_pdf_progress.addClass('wpaicg_error');
                                wpaicg_pdf_message.html(res.msg);
                            }
                        })
                    }
                    else{
                        wpaicgRmLoading(wpaicg_pdf_start);
                        wpaicg_pdf_file.val('');
                        alert('<?php echo esc_html__('Your PDF file is empty','gpt3-ai-content-generator')?>')
                    }
                }
                else{
                    alert('<?php echo esc_html__('Please select a PDF file','gpt3-ai-content-generator')?>')
                }
            }
            else{
                alert('<?php echo esc_html__('Please select a PDF file before starting.','gpt3-ai-content-generator')?>')
            }
        });
    })
</script>
