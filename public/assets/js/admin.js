$(document).ready(function () {
    if ($('*[data-delete-confirm]').length > 0)
    {
        $('*[data-delete-confirm]').easyconfirm(
            {
                locale: {
                    title: CONFIRM_TITLE_DIALOG,
                    text: CONFIRM_DELETE_MSG,
                    button: [BTN_DIALOG_CANCEL, BTN_DIALOG_OK]
                }
            }
        );
    }

    

    // UDATE STATUS
	$('input[data-status]').click(function(e) {
		e.preventDefault();
		var $this = $(this);
		/*var status = $this.hasClass('publish') ? 0 : 1;*/
		var removeClass = $this.hasClass('publish') ? 'publish' : 'unpublish';
		var addClass = $this.hasClass('publish') ? 'unpublish' : 'publish';
		var url = $this.attr('data-status');
		var status = parseInt( $this.attr('val') );
		var id = $this.attr('data-row');
		$this.addClass('processing');
		$.ajax({
			url: url,
            cache:false,
			data: { 'status': status, id: id },
			dataType: 'json',
			success: function(data) {
				$this.removeClass('processing');
				
				//var obj = $.parseJSON(data);
				//if (data.STATUS) {
					$this.removeClass(removeClass).addClass(addClass);
					status = (status == 0) ? 1 : 0;
					$this.attr('val', status);
				//}
			}
		});
	});
	
	//DATA DATEPICKER
		//$('*[data-datepicker]').datepicker({
			//dateFormat: "dd-mm-yy"
		//});


	
	//BUTTON HREF
	$('*[data-href]').click(function(e) {
	    e.preventDefault();
	    var url = $(this).attr('data-href');
	    if(url) {
	    	window.location.href = url;
	    	return false;
	    }
	    	
	});
	
	//FILTER ONCHANGE
	$('*[data-filter]').on('change', function() {
		var $this = $(this);
		var value = $this.val();
		var name = $this.attr('name');
		var url = $this.attr('data-filter');
		var new_patt = name + "=" + value + "&";
		var patt = new RegExp(name + "=[A-z0-9-]*&");
		if( url && patt.test(url) ) {
			url = url.replace(patt, new_patt);
			window.location.href = url;
			return false;
		}
		patt = new RegExp(name + "=[A-z0-9-]*$");
		if( url && patt.test(url) ){
			new_patt = name + "=" + value;
			url = url.replace(patt, new_patt);
			window.location.href = url;
			return false;
		}
	});
	
	//SEARCH
	$('*[data-search]').on('click', function (){
		var searchID = $(this).attr('data-search');
		var search = $(searchID);
		var value = search.val();
		var name = search.attr('name');
		var url = search.attr('href');
		var new_patt = name + "=" + value + "&";
		var patt = new RegExp(name + "=[A-z0-9-]*&");
		if( url && patt.test(url) ) {
			url = url.replace(patt, new_patt);
			window.location.href = url;
			return false;
		}
	});
	
	//PAGINATION
	$('#dynamic_paginate a').on('click', function() {
		var $this = $(this);
		var url = $('#dynamic_paginate').attr('data-href');
		var value = $this.attr('tabindex');
		var new_patt = "page=" + value + "&";
		var patt = new RegExp("page=[A-z0-9-]*&");
		if( url && patt.test(url) ) {
			url = url.replace(patt, new_patt);
			window.location.href = url;
			return false;
		}
	});
	
	
	//DELETE
	$('*[data-delete-selected]').on('click', function(e) {
		var input = $("<input>").attr("type", "hidden").attr("name", "type").val("delete");
		$('form').append($(input));
	    $('form').submit();
	});
	
	//ALIAS
	$(':text[data-for]').blur(function() {
		var $this = $(this);
		var $alias = $( $this.attr('data-for') );
		if ( !$alias.val() ) {
			$alias.attr('disabled', 'disabled');
			$.ajax({
				url: '/index.php/admin/ajax/alias',
				data: { 'title': $this.val() },
				success: function(data) {
					$alias.val(data).removeAttr('disabled');
				}
			});
		}
	});

	//CK EDITOR
	$('*[data-editor]').each(function() {
	    var id = $(this).attr('data-editor');
	    CKEDITOR.replace(id,
            {
                allowedContent: {
                    // Allow all content.
                    $1: {
                        elements: CKEDITOR.dtd,
                        attributes: true,
                        styles: true,
                        classes: true
                    }
                },
	        }
        );

	});

	$('.gallery_list').each(function() {
		var id = $(this).attr('id');
		$('#'+id).sortable();
	});

	//GALLERY
	function openKCFinder(lang) {
		window.KCFinder = {
	        callBackMultiple: function(files) {
	            window.KCFinder = null;
	            if(lang) {
	            	for(var i = 0; i < files.length; i++) {
		            	var li = liHTML(files[i]);
		            	$('#gallery_' + lang + '_list').append(li);
		            }
	            }else{
	            	var img = "<img src='" + files[0] + "' />";
	            	$('.featured_photo').html(img);
	            	$('#featured_photo').val(files[0]);
	            }

                save();
	        }
	    };
	   	window.open('http://'+window.location.host +'/'+'media/plugin/kcfinder/browse.php?type=images&dir=files/public',
	        'kcfinder_multiple', 'status=0, toolbar=0, location=0, menubar=0, ' +
	        'directories=0, resizable=1, scrollbars=0, width=800, height=600'
	    );
	} 
	
	$(document).on('click', '.gallery_list li .remove', function() {
	    var li = $(this).parent();
	    li.remove();
        save();
	});
	$(document).on('change', '.gallery_list li .config input', function() {
        save();
	});
		$(document).on('change', '.gallery_list li .config textarea', function() {
        save();
	});
	function liHTML(fileName) {
		var HTML = '<li>';
		HTML += '<div class=\'image\'>' + '<img src=\'' + fileName + '\'></div>';
		HTML += '<div class=\'config\'>';
        HTML +=	'<textarea placeholder=\'title\' class=\'title\'></textarea>';
		HTML +=	'<textarea placeholder=\'tag\' class=\'tag\'></textarea>';
        HTML += '<textarea placeholder=\'alt\' class=\'alt\'></textarea>';
		HTML +=	'<textarea placeholder=\'w\' class=\'width\'></textarea>';
		HTML += '<textarea placeholder=\'h\' class=\'height\'></textarea>';
		HTML += '<textarea placeholder=\'http://\' class=\'anchor\'></textarea></div>';
		HTML += '<a href=\'#\' class=\'remove\'>X</a>';
		HTML += '</li>';
		return HTML;
	}

	function store(el) {
		var lang = el.attr('lang');
		var id = el.attr('id');
		var list = [];
		$('#' + id + ' li').each(function() {
			var img = $(this).find('img').attr('src');
			var tag = $(this).find('.tag').val();
            var t = $(this).find('.title').val();
            var alt = $(this).find('.alt').val();
			var w = $(this).find('.width').val();
			var h = $(this).find('.height').val();
			var a = $(this).find('.anchor').val();
			var obj = {
				img:img,
				tag:tag,
				width:w,
				height:h,
				href:a,
                title:t,
                alt:alt
			};
			list.push(obj);
		});
		if(lang!="")
			$('#gallery_' + lang).val( JSON.stringify(list) );
		else $('#gallery').val( JSON.stringify(list) );
	}

    function save () {
        $('.gallery_list').each(function() {
            var el = $(this);
            store(el)
        });
    }
	$('.gallery_submit').click(function(e) {
		e.preventDefault();
        save();
		$('form').submit();
	});
	
	$('.upload-gallery').click(function(e) {
		e.preventDefault();
		var lang = $(this).attr('rel');
		openKCFinder(lang);
	});
	
	//CLOSE FLASH
	setTimeout(function() {
		$('.flash').fadeOut();
	}, 3000);
});