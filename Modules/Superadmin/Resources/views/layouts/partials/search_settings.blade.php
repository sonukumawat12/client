<div class="row">
    <div class="col-md-12 col-xs-12">
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-search"></i></span>
            <select id="search_settings" class="form-control" style="width: 100%;">
            </select>
        </div>
        
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        
        //Search Settings
        //Set all labels as select2 options
        label_objects = [];
        search_options = [{
            id: '',
            text: ''
        }];
        var i = 0;
        $('.content .search_label').each( function(){
            
            label_objects.push($(this));
            var label_text = $(this).text().trim().replace(":", "").replace("*", "");
            search_options.push(
                {
                    id: i,
                    text: label_text
                }
            );
            
            $('#search_settings').append('<option value="'+i+'">'+label_text+'</option>');
            
            i++;
        });
        
        setTimeout(function() {
            $('#search_settings').select2();
        }, 1000);
        
        // $('#search_settings').select2();
        
        $('#search_settings').change( function(){
            console.log("triggered",$(this).val());
            
            
            //Get label position and add active class to the tab
            $('.search_label').css('background-color', '');
            var label_index = $(this).val();
            var label = label_objects[label_index];
            $('.pos-tab-content.active').removeClass('active');
            var tab_content = label.closest('.pos-tab-content');
            tab_content.addClass('active');
            tab_index = $('.pos-tab-content').index(tab_content);
            $('.list-group-item.active').removeClass('active');
            $('.list-group-item').eq(tab_index).addClass('active');

            //Highlight the label for three seconds
            $([document.documentElement, document.body]).animate({
		        scrollTop: label.offset().top - 100
		    }, 500);
            label.css('background-color', 'yellow');
            // setTimeout(function(){ 
            //     label.css('background-color', ''); 
            // }, 3000);
        });
    });
</script>