<script type="text/javascript">
/* <![CDATA[ */

//ajax call parameter
var csp_ajax_params = {
	'action'         : '',
	'file'           : '',
	'type'           : '',
	'name'           : '',
	'row'            : '',
	'path'           : '',
	'subpath'        : '',
	'existing'       : '',
	'simplefilename' : '',
	'transtemplate'  : '',
	'textdomain'     : '',
	'denyscan'       : '',
	'timestamp'      : '',
	'translator'     : '',
	'language'       : '',
	'numlangs'       : '',
	'pofile'         : '',
	'potfile'        : '',
	'num'            : '',
	'cnt'            : '',
	'php'            : '',
	'isplural'       : '',
	'msgid'          : '',
	'msgstr'         : '',
	'msgidx'         : '',
	'destlang'       : ''

};

Object.extend( Array.prototype, {
	intersect: function(array){
		return this.findAll( function(token){ return array.include(token) } );
	}
} );

//write mofile indication
$('csp-generate-mofile').hide();

//--- management based functions ---
function csp_make_writable(elem, file, success_class) {
	elem = $(elem);
	elem.blur();

	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name' );
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	}else{
		csp_ajax_params.action = 'csp_po_change_permission';
		csp_ajax_params.file = file;
	}

	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				elem.className=success_class;
				elem.title=transport.responseJSON.title;
				elem.onclick = null;
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__( 'User Credentials required', 'translation-toolkit' )); ?></b>',
						buttons: {
							"<?php echo esc_js(__( 'Ok', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click' );
							},
							"<?php echo esc_js(__( 'Cancel', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;
}

function csp_add_language(elem, type, name, row, path, subpath, existing, type, simplefilename, transtemplate, textdomain, denyscan) {
	elem = $(elem);
	elem.blur();
	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: {
				action: 'csp_po_dlg_new',
				type: type,
				name: name,
				row: row,
				path: path,
				subpath: subpath,
				existing: existing,
				type: type,
				simplefilename: simplefilename,
				transtemplate: transtemplate,
				textdomain: textdomain,
				denyscan: denyscan
			},
			onSuccess: function(transport) {
				$('csp-dialog-caption').update("<?php _e( 'Add New Language', 'translation-toolkit' ); ?>");
				$("csp-dialog-body").update(transport.responseText).setStyle({'padding' : '10px'});
				tb_show(null,"#TB_inline?height=530&width=500&inlineId=csp-dialog-container&modal=true",false);
			}
		}
	);
	return false;
}

function csp_merge_maintheme_languages(elem, source, dest, basepath, textdomain, molist) {

	elem = $(elem);
	elem.blur();

	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name' );
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	}else{
		csp_ajax_params.action = 'csp_po_merge_from_maintheme';
		csp_ajax_params.source = source;
		csp_ajax_params.dest = dest;
		csp_ajax_params.basepath = basepath;
		csp_ajax_params.textdomain = textdomain;
		csp_ajax_params.molist = molist;
	}
	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				//remeber the last edited component by id hash
				//old jquery is unable to do that in WP 2.5
				csp_ajax_params.action = '';
				try{ window.location.hash = csp_ajax_params.molist; } catch(e) {}
				window.location.reload();
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__( 'User Credentials required', 'translation-toolkit' )); ?></b>',
						buttons: {
							"<?php echo esc_js(__( 'Ok', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click' );
							},
							"<?php echo esc_js(__( 'Cancel', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
}

function csp_create_new_pofile(elem, type){
	elem = $(elem);
	elem.blur();

	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name' );
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	} else {
		csp_ajax_params.action = 'csp_po_create';
		csp_ajax_params.name = $('csp-dialog-name').value;
		csp_ajax_params.timestamp = $('csp-dialog-timestamp').value,
		csp_ajax_params.translator = $('csp-dialog-translator').value,
		csp_ajax_params.path = $('csp-dialog-path').value,
		csp_ajax_params.subpath = $('csp-dialog-subpath').value,
		csp_ajax_params.language = $('csp-dialog-language').value,
		csp_ajax_params.row = $('csp-dialog-row').value,
		csp_ajax_params.numlangs = $('csp-dialog-numlangs').value,
		csp_ajax_params.type  = type,
		csp_ajax_params.simplefilename = $('csp-dialog-simplefilename').value,
		csp_ajax_params.transtemplate  =  $('csp-dialog-transtemplate').value,
		csp_ajax_params.textdomain  =  $('csp-dialog-textdomain').value,
		csp_ajax_params.denyscan = $('csp-dialog-denyscan').value
	}

	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				jQuery('#'+transport.responseJSON.row+' .mo-list-head  td.csp-ta-right').html(transport.responseJSON.head);
				rel = $$('#'+transport.responseJSON.row+' .mo-list-head').first().down(2).rel;
				$$('#'+transport.responseJSON.row+' .mo-list-head').first().down(2).rel += ((rel.empty() ? '' : "|" ) + transport.responseJSON.language);
				elem_after = null;

				content = "<tr class=\"mo-file\" lang=\""+transport.responseJSON.language+"\">"+
					"<td nowrap=\"nowrap\" width=\"16px\" align=\"center\"><img src=\"<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/'; ?>"+transport.responseJSON.google+".png\" /></td>"+
					"<td nowrap=\"nowrap\" width=\"16px\" align=\"center\" class=\"lang-info-api\"><img src=\"<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/'; ?>"+transport.responseJSON.microsoft+".png\" /></td>"+
					"<td nowrap=\"nowrap\" width=\"100%\"  class=\"lang-info-desc\">"+
						"<img title=\"<?php _e( 'Locale', 'translation-toolkit' ); ?>: "+transport.responseJSON.language+"\" alt=\"(locale: "+transport.responseJSON.language+")\" src=\""+transport.responseJSON.image+"\" />" +
						("<?php echo get_locale(); ?>" == transport.responseJSON.language ? "<strong>" : "") +
						"&nbsp;" + transport.responseJSON.lang_native +
						("<?php echo get_locale(); ?>" == transport.responseJSON.language ? "</strong>" : "") +
					"</td>"+
					"<td align=\"center\">"+
						"<div style=\"width:44px\">"+
						"<a class=\"csp-filetype-po-rw\" title=\""+transport.responseJSON.permissions+"\">&nbsp;</a>"+
						"<a class=\"csp-filetype-mo\" title=\"<?php _e( '-n.a.-', 'translation-toolkit' ); ?> [---|---|---]\">&nbsp;</a>"+
						"</div>"+
					"</td>"+
					"<td nowrap=\"nowrap\">"+
						"<a class=\"clickable button\" onclick=\"csp_launch_editor(this, '"+transport.responseJSON.subpath+transport.responseJSON.language+".po"+"', '"+transport.responseJSON.path+"','"+transport.responseJSON.textdomain+"' );\"><?php _e( 'Edit', 'translation-toolkit' ); ?></a>"+
						"\n<span>&nbsp;</span>\n"+(transport.responseJSON.denyscan == false ?
						"<a class=\"clickable button\" onclick=\"csp_rescan_language(this,'"+escape(transport.responseJSON.name)+"','"+transport.responseJSON.row+"','"+transport.responseJSON.path+"','"+transport.responseJSON.subpath+"','"+transport.responseJSON.language+"','"+transport.responseJSON.type+"','"+transport.responseJSON.simplefilename+"')\"><?php _e( 'Rescan', 'translation-toolkit' ); ?></a>"+
						"\n<span>&nbsp;</span>\n"
						:
						"<span style=\"text-decoration: line-through;\"><?php _e( 'Rescan', 'translation-toolkit' ); ?></span>"+
						"\n<span>&nbsp;</span>\n"
						) +
						"<a class=\"clickable button\" onclick=\"csp_remove_language(this,'"+escape(transport.responseJSON.name)+"','"+transport.responseJSON.row+"','"+transport.responseJSON.path+"','"+transport.responseJSON.subpath+"','"+transport.responseJSON.language+"' );\"><?php _e( 'Delete', 'translation-toolkit' ); ?></a>"+
					"</td>"+
					"</tr>";
				$$('#'+transport.responseJSON.row+' .mo-file').each(function(tr) {
					if ((tr.lang > transport.responseJSON.language) && !Object.isElement(elem_after)) {	elem_after = tr; }
				});
				ne = null;
				if (Object.isElement(elem_after)) { ne = elem_after.insert({ 'before' : content }).previous(); }
				else { ne = $$('#'+transport.responseJSON.row+' tbody').first().insert(content).childElements().last(); }
				new Effect.Highlight(ne, { startcolor: '#25FF00', endcolor: '#FFFFCF' });
				csp_ajax_params.action = ''; //reset
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__( 'User Credentials required', 'translation-toolkit' )); ?></b>',
						buttons: {
							"<?php echo esc_js(__( 'Ok', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click' );
							},
							"<?php echo esc_js(__( 'Cancel', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
	csp_cancel_dialog();
	return false;
}

function csp_remove_language(elem, name, row, path, subpath, language) {
	elem = $(elem);
	elem.blur();
	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: {
				action: 'csp_po_dlg_delete',
				name: name,
				row: row,
				path: path,
				subpath: subpath,
				language: language,
				numlangs: $$('#'+row+' .mo-list-head').first().down(2).rel.split('|').size()
			},
			onSuccess: function(transport) {
				$('csp-dialog-caption').update("<?php _e( 'Confirm Delete Language', 'translation-toolkit' ); ?>");
				$("csp-dialog-body").update(transport.responseText).setStyle({'padding' : '10px'});
				tb_show.defer(null,"#TB_inline?height=180&width=300&inlineId=csp-dialog-container&modal=true",false);
			}
		}
	);
	return false;
}

function csp_destroy_files(elem, name, row, path, subpath, language, numlangs){
	elem = $(elem);
	elem.blur();
	csp_cancel_dialog();

	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name' );
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	}
	else{
		csp_ajax_params.action = 'csp_po_destroy';
		csp_ajax_params.name = name;
		csp_ajax_params.row = row;
		csp_ajax_params.path = path;
		csp_ajax_params.subpath = subpath;
		csp_ajax_params.language = language;
		csp_ajax_params.numlangs = numlangs;
	}
	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				$$('#'+transport.responseJSON.row+' .mo-file').each(function(tr) {
					if (tr.lang == transport.responseJSON.language) {
						new Effect.Highlight(tr, {
							startcolor: '#FF7A0F',
							endcolor: '#FFFFCF',
							duration: 1,
							afterFinish: function(obj) {
								jQuery('#'+transport.responseJSON.row+' .mo-list-head  td.csp-ta-right').html(transport.responseJSON.head);
								a = $$('#'+transport.responseJSON.row+' .mo-list-head').first().down(2).rel.split('|').without(transport.responseJSON.language);
								$$('#'+transport.responseJSON.row+' .mo-list-head').first().down(2).rel = a.join('|' );
								obj.element.remove();
							}
						});
					}
				});
				csp_ajax_params.action = ''; //reset
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__( 'User Credentials required', 'translation-toolkit' )); ?></b>',
						buttons: {
							"<?php echo esc_js(__( 'Ok', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click' );
							},
							"<?php echo esc_js(__( 'Cancel', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				} else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;
}

function csp_rescan_language(elem, name, row, path, subpath, language, type, simplefilename, themetemplate) {
	elem = $(elem);
	elem.blur();
	var a = elem.up('table').summary.split('|' );
	actual_domain = a[0];
	$('prj-id-ver').update(a[2]);
	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: {
				action: 'csp_po_dlg_rescan',
				name: name,
				row: row,
				path: path,
				subpath: subpath,
				language: language,
				numlangs: $$('#'+row+' .mo-list-head').first().down(2).rel.split('|').size(),
				type: type,
				textdomain: actual_domain,
				simplefilename: simplefilename,
				themetemplate: themetemplate
			},
			onSuccess: function(transport) {
				$('csp-dialog-caption').update("<?php _e( 'Rescanning PHP Source Files', 'translation-toolkit' ); ?>");
				$("csp-dialog-body").update(transport.responseText).setStyle({'padding' : '10px'});
				tb_show.defer(null,"#TB_inline?height=230&width=510&inlineId=csp-dialog-container&modal=true",false);
			}
		}
	);
	return false;
}

var csp_php_source_json = 0;
var csp_chuck_size = <?php if ( get_option( 'translation-toolkit.low-memory' ) == 1 ) { echo 1; } else { echo 20; } ?>;

function csp_scan_source_files() {
	if (csp_php_source_json == 0) {
		$('csp-dialog-rescan').hide();
		$('csp-dialog-cancel').hide();
		$('csp-dialog-scan-info').show();
		csp_php_source_json = $('csp-dialog-source-file-json').value.evalJSON();
	}
	if (csp_php_source_json.next >= csp_php_source_json.files.size()) {
		if ($('csp-dialog-cancel').visible()) {
			csp_cancel_dialog();
			csp_php_source_json = 0;
			csp_ajax_params.action = '';
			return false;
		}
		$('csp-dialog-scan-info').hide();
		$('csp-dialog-rescan').show().writeAttribute({'value' : '<?php _e( 'finished', 'translation-toolkit' ); ?>' });
		$('csp-dialog-cancel').show();
		$('csp-dialog-progressfile').update('&nbsp;' );
		elem = $$("#"+csp_php_source_json.row+" .mo-file[lang=\""+csp_php_source_json.language+"\"] div a").first();
		elem.className = "csp-filetype-po-rw";
		elem.title = csp_php_source_json.title;
		return false;
	}

	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name' );
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	} else {
		csp_ajax_params.action = 'csp_po_scan_source_file';
		csp_ajax_params.name = csp_php_source_json.name;
		csp_ajax_params.type = csp_php_source_json.type;
		csp_ajax_params.pofile = csp_php_source_json.pofile;
		csp_ajax_params.textdomain = csp_php_source_json.textdomain;
		csp_ajax_params.num = csp_php_source_json.next;
		csp_ajax_params.cnt = csp_chuck_size;
		csp_ajax_params.path = csp_php_source_json.path;
		csp_ajax_params.php = csp_php_source_json.files.join("|");
	}

	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				try{
					csp_php_source_json.title = transport.responseJSON.title;
				} catch(e) {
					$('csp-dialog-scan-info').hide();
					$('csp-dialog-rescan').show().writeAttribute({'value' : '<?php _e( 'finished', 'translation-toolkit' ); ?>' });
					$('csp-dialog-cancel').show();
					csp_php_source_json = 0;
					var mem_reg = /Allowed memory size of (\d+) bytes exhausted/;
					mem_reg.exec(transport.responseText);
					error_text = "<?php _e( 'You are trying to rescan files which expands above your PHP Memory Limit at %s MB during the analysis.<br/>Please enable the <em>low memory mode</em> for scanning this component.', 'translation-toolkit' ); ?>";
					csp_show_error(error_text.replace('%s', RegExp.$1 / 1024.0 / 1024.0));
					csp_ajax_params.action = '';
				}
				csp_php_source_json.next += csp_chuck_size;
				csp_ajax_params.num = csp_php_source_json.next;
				var perc = Math.min(Math.round(csp_php_source_json.next*1000.0/csp_php_source_json.files.size())/10.0, 100.00);
				$('csp-dialog-progressvalue').update(Math.min(csp_php_source_json.next, csp_php_source_json.files.size()));
				$('csp-dialog-progressbar').setStyle({'width' : ''+perc+'%'});
				if (csp_php_source_json.files[csp_php_source_json.next-csp_chuck_size]) $('csp-dialog-progressfile').update("<?php _e( 'File:', 'translation-toolkit' ); ?>&nbsp;"+csp_php_source_json.files[csp_php_source_json.next-csp_chuck_size].replace(csp_php_source_json.path,""));
				csp_scan_source_files().delay(0.1);
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__( 'User Credentials required', 'translation-toolkit' )); ?></b>',
						buttons: {
							"<?php echo esc_js(__( 'Ok', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_scan_source_files();
							},
							"<?php echo esc_js(__( 'Cancel', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
								csp_php_source_json = 0;
								csp_cancel_dialog();
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				} else {
					$('csp-dialog-scan-info').hide();
					$('csp-dialog-rescan').show().writeAttribute({'value' : '<?php _e( 'finished', 'translation-toolkit' ); ?>' });
					$('csp-dialog-cancel').show();
					csp_php_source_json = 0;
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;
}

//--- editor based functions ---
var csp_pagesize = 100;
var csp_pagenum = 1;
var csp_search_timer = null;
var csp_search_interval = Prototype.Browser.IE ? 0.3 : 0.1;

var csp_destlang = 'de';
var csp_api_type = 'none';
var csp_path = '';
var csp_file = '';
var csp_num_plurals = 2;
var csp_func_plurals = '';
var csp_idx = {	'total' : [], 'plurals' : [], 'open' : [], 'rem' : [], 'code' : [], 'ctx' : [], 'cur' : [] , 'ltd' : [] , 'trail' : [] }
var csp_searchbase = [];
var csp_pofile = [];
var csp_textdomains = [];
var csp_actual_type = '';

function csp_init_editor(actual_domain, actual_type) {
	//list all contained text domains
	opt_list = '';
	csp_actual_type = actual_type;
	tderror = true;
	tdmixed = new Array();
	for (i=0; i<csp_textdomains.size(); i++) {
		tderror = tderror && (csp_textdomains[i] != actual_domain);
		if (csp_textdomains[i] != 'default' && csp_textdomains[i] != actual_domain && csp_textdomains[i] != '{bug-detected}') tdmixed.push(csp_textdomains[i]);
		opt_list += '<option value="'+csp_textdomains[i]+'"'+(csp_textdomains[i] == actual_domain ? ' selected="selected"' : '')+'>'+(csp_textdomains[i].empty() ? 'default' : csp_textdomains[i])+'</option>';
	}
	initial_domain = $('csp-mo-textdomain-val').update(opt_list).value;
	if(tderror && (csp_actual_type != 'wordpress')) {
		$('textdomain-error').removeClassName('hidden' );
		$$("#textdomain-error span").first().update(actual_domain);
	} else {
		$('textdomain-error').addClassName('hidden' );
	}
	if (csp_actual_type != 'wordpress') {
		if (tdmixed.length) {
			$$("#textdomain-warning span").first().update(tdmixed.join(', '));
			$('textdomain-warning').removeClassName('hidden' );
		}else {
			$('textdomain-warning').addClassName('hidden' );
		}
	} else{
		$('textdomain-warning').addClassName('hidden' );
	}

	//setup all indizee register
	for (i=0; i<csp_pofile.size(); i++) {
		csp_idx.total.push(i);
		if (Object.isArray(csp_pofile[i].key)) {
			if (csp_pofile[i].key[0].match(/\s+$/g) || csp_pofile[i].key[1].match(/\s+$/g)) {
				csp_idx.trail.push(i);
			}

			if (!Object.isArray(csp_pofile[i].val)) {
				if(csp_pofile[i].val.blank()) csp_idx.open.push(i);
			} else{
				if(csp_pofile[i].val.join('').blank()) csp_idx.open.push(i);
			}
			csp_idx.plurals.push(i);
		} else{
			if (csp_pofile[i].key.match(/\s+$/g)) {
				csp_idx.trail.push(i);
			}

			if(csp_pofile[i].val.empty()) {
				csp_idx.open.push(i);
			}
		}
		if(!csp_pofile[i].rem.empty()) csp_idx.rem.push(i);
		if(csp_pofile[i].ctx) csp_idx.ctx.push(i);
		if(csp_pofile[i].code) csp_idx.code.push(i);
		if(csp_pofile[i].ltd.indexOf(initial_domain) != -1) csp_idx.ltd.push(i);
	}
//$	csp_idx.cur = csp_idx.total;
	csp_idx.cur = csp_idx.ltd.intersect(csp_idx.total);
	csp_searchbase = csp_idx.cur;
/*
	if(csp_textdomains[0] != '{php-code}'){
		$('csp-write-mo-file').show();
	}else{
		$('csp-write-mo-file').hide();
	}
*/
	csp_change_pagesize(100);
	window.scrollTo(0,0);
	$('s_original').value="";
	$('s_original').autoComplete="off";
	$('s_translation').value="";
	$('s_translation').autoComplete="off";
	csp_change_textdomain_view(initial_domain);
}

function csp_change_textdomain_view(textdomain) {
	csp_idx.ltd = [];
	for (i=0; i<csp_pofile.size(); i++) {
		if (csp_pofile[i].ltd.indexOf(textdomain) != -1) csp_idx.ltd.push(i);
	}
	csp_idx.cur = csp_idx.ltd.intersect(csp_idx.total);
	csp_searchbase = csp_idx.cur;
	$$("a.csp-filter").each(function(e) { e.removeClassName('current')});
	$('csp-filter-all').addClassName('current' );
	hide = false;
	if (textdomain == '{php-code}' || textdomain == '{bug-detected}') { hide = true; }
	else if(textdomain == 'default') {
		hide = true;
		//special bbPress on BuddyPress test because of default domain too
		reg = /\/bp-forums\/bbpress\/$/;
		if ((csp_actual_type == 'wordpress')||reg.test(csp_path)) { hide = false; }
	}
	if (hide) {
		$('csp-write-mo-file').hide();
	}
	else {
		$('csp-write-mo-file').show();
	}
	csp_filter_result('csp-filter-all', csp_idx.total);
}

function csp_show_error(message) {
	error = "<div style=\"text-align:center\"><img src=\"<?php echo plugin_dir_url( TranslationToolkit::get_file() ) . 'images/error.gif'; ?>\" align=\"left\" />"+message+
			"<p style=\"margin:15px 0 0 0;text-align:center; padding-top: 5px;border-top: solid 1px #aaa;\">"+
			"<input class=\"button\" type=\"submit\" onclick=\"return csp_cancel_dialog();\" value=\"  Ok  \"/>"+
			"</p>"+
			"</div>";
	$('csp-dialog-caption').update("CodeStyling Localization - <?php _e( 'Access Error', 'translation-toolkit' ); ?>");
	$("csp-dialog-body").update(error).setStyle({'padding' : '10px'});
	if ($('csp-dialog-saving')) $('csp-dialog-saving').hide();
	tb_show.defer(null,"#TB_inline?height=140&width=510&inlineId=csp-dialog-container&modal=true",false);
}

function csp_cancel_dialog(){
	tb_remove();
	$('csp-dialog-body').update("");
	$$('.highlight-editing').each(function(e) {
		e.removeClassName('highlight-editing' );
	});
}

function csp_launch_editor(elem, file, path, textdomain) {
	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name' );
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	}
	else{
		var a = $(elem).up('table').summary.split('|' );
		$('csp-wrap-main').hide();
		$('csp-wrap-editor').show();
		$('prj-id-ver').update(a[2]);

		csp_ajax_params.action = 'csp_po_launch_editor';
		csp_ajax_params.basepath = path;
		csp_ajax_params.file = file;
		csp_ajax_params.textdomain = textdomain;
		csp_ajax_params.type = a[1];

		//remeber the last edited component by id hash
		//old jquery is unable to do that in WP 2.5
		try{ window.location.hash = jQuery(elem).closest('table').attr('id' ); } catch(e) {}
	}
	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				//switch to editor now
				try{
					$('csp-json-header').insert(transport.responseJSON.header);
				}catch(e) {
					var mem_reg = /Allowed memory size of (\d+) bytes exhausted/;
					mem_reg.exec(transport.responseText);
					error_text = "<?php _e( 'You are trying to open a translation catalog which expands above your PHP Memory Limit at %s MB during read.<br/>Please enable the <em>low memory mode</em> for opening this components catalog.', 'translation-toolkit' ); ?>";
					$('catalog-body').update('<tr><td colspan="4" align="center" style="color:#f00; ?>'+error_text.replace('%s', RegExp.$1 / 1024.0 / 1024.0)+'</td></tr>' );
				}
				$('catalog-last-saved').update(transport.responseJSON.last_saved);
				$$('#csp-json-header a')[0].update(transport.responseJSON.file);
				csp_destlang = transport.responseJSON.destlang;
				csp_api_type = transport.responseJSON.api_type;
				if (csp_api_type == 'none') csp_destlang = '';
				csp_path = transport.responseJSON.path;
				csp_file = transport.responseJSON.file;
				csp_num_plurals = transport.responseJSON.plurals_num;
				csp_func_plurals = transport.responseJSON.plurals_func;
				csp_idx = transport.responseJSON.index;
				csp_pofile = transport.responseJSON.content;
				csp_textdomains = transport.responseJSON.textdomains;
				csp_init_editor(a[0], a[1]);
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__( 'User Credentials required', 'translation-toolkit' )); ?></b>',
						buttons: {
							"<?php echo esc_js(__( 'Ok', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click' );
							},
							"<?php echo esc_js(__( 'Cancel', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				}else {
					$('catalog-body').update('<tr><td colspan="4" align="center" style="color:#f00; ?>'+transport.responseText+'</td></tr>' );
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;
}

function csp_toggle_header(host, elem) {
	$(host).up().toggleClassName('po-header-collapse' );
	$(elem).toggle();
}

function csp_change_pagesize(newsize) {
	csp_pagesize = parseInt(newsize);
	csp_change_pagenum(1);
}

function csp_change_pagenum(newpage) {
	csp_pagenum = newpage;
	var cp = $('catalog-pages-top' );
	var cb = $('catalog-body')

	var inner = '';

	var cnt = Math.round(csp_idx.cur.size() * 1.0 / csp_pagesize + 0.499);
	if (cnt > 1) {

		if (csp_pagenum > 1) { inner += "<a class=\"next page-numbers\" onclick=\"csp_change_pagenum("+(csp_pagenum-1)+")\"><?php _e( '&laquo; Previous', 'translation-toolkit' ); ?></a>"; }
		var low = Math.max(csp_pagenum - 5,1);
		if (low > 1) inner += "<span>&nbsp;...&nbsp;</span>";
		for (i=low; i<=Math.min(low+10,cnt); i++) {
			inner += "<a class=\"page-numbers"+(i==csp_pagenum ? ' current' : '')+"\" onclick=\"csp_change_pagenum("+i+")\">"+i+"</a>";
		}
		if (Math.min(low+10,cnt) < cnt) inner += "<span>&nbsp;...&nbsp;</span>";
		if (csp_pagenum < cnt) { inner += "<a class=\"next page-numbers\" onclick=\"csp_change_pagenum("+(csp_pagenum+1)+")\"><?php _e( 'Next &raquo;', 'translation-toolkit' ); ?></a>"; }
	}
	cp.update(inner);
	$('catalog-pages-bottom').update(inner);

	inner = '';

	for (var i=(csp_pagenum-1)*csp_pagesize; i<Math.min(csp_pagenum * csp_pagesize, csp_idx.cur.size());i++) {
		inner += "<tr"+(i % 2 == 0 ? '' : ' class="odd"')+" id=\"msg-row-"+csp_idx.cur[i]+"\">";
		var tooltip = [];
		if (!csp_pofile[csp_idx.cur[i]].rem.empty()) tooltip.push(String.fromCharCode(3)+"<?php _e( 'Comment', 'translation-toolkit' ); ?>"+String.fromCharCode(4)+csp_pofile[csp_idx.cur[i]].rem);
		if (csp_pofile[csp_idx.cur[i]].code) tooltip.push(String.fromCharCode(3)+"<?php _e( 'Code Hint', 'translation-toolkit' ); ?>"+String.fromCharCode(4)+csp_pofile[csp_idx.cur[i]].code);
		if (tooltip.size() > 0) {
			tooltip = tooltip.join(String.fromCharCode(1)).replace("\n", String.fromCharCode(1)).escapeHTML();
			tooltip = tooltip.replace(/\1/g, '<br/>').replace(/\3/g, '<strong>').replace(/\4/g, '</strong>' );
		} else { tooltip = '' };
		inner += "<td nowrap=\"nowrap\">";
		if(csp_pofile[csp_idx.cur[i]].ref.size() > 0) {
			inner += "<a class=\"csp-msg-tip\"><img alt=\"\" src=\"<?php echo plugin_dir_url( TranslationToolkit::get_file() ); ?>images/php.gif\" /><span><strong><?php _e( 'Files:', 'translation-toolkit' ); ?></strong>";
			csp_pofile[csp_idx.cur[i]].ref.each(function(r) {
				inner += "<em onclick=\"csp_view_phpfile(this, '"+r+"', "+csp_idx.cur[i]+")\">"+r+"</em><br />";
			});
			inner += "</span></a>";
		}
		inner += (tooltip.empty() ? '' : "<a class=\"csp-msg-tip\"><img alt=\"\" src=\"<?php echo plugin_dir_url( TranslationToolkit::get_file() ); ?>images/comment.gif\" /><span>"+tooltip+"</span></a>");
		inner += "</td>";
		ctx_str = '';
		if (csp_pofile[csp_idx.cur[i]].ctx) {
			ctx_str = "<div><b style=\"border-bottom: 1px dotted #000;\"><?php _e( 'Context', 'translation-toolkit' ); ?>:</b>&nbsp;<span style=\"color:#f00;\">"+csp_pofile[csp_idx.cur[i]].ctx+"</span></div>";
		}
		if (Object.isArray(csp_pofile[csp_idx.cur[i]].key)) {
			inner +=
				"<td>"+ctx_str+"<div><span class=\"csp-pl-form\"><?php _e( 'Singular:', 'translation-toolkit' ); ?> </span>"+csp_pofile[csp_idx.cur[i]].key[0].escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080; ?>&nbsp;</span>')+"</div><div><span class=\"csp-pl-form\"><?php _e( 'Plural:', 'translation-toolkit' ); ?> </span>"+csp_pofile[csp_idx.cur[i]].key[1].escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080; ?>&nbsp;</span>')+"</div></td>"+
				"<td>"+ctx_str;
			for (pl=0;pl<csp_num_plurals; pl++) {
				if (csp_num_plurals == 1) {
					inner += "<div><span class=\"csp-pl-form\"><?php _e( 'Plural Index Result =', 'translation-toolkit' ); ?> "+pl+" </span>"+(!csp_pofile[csp_idx.cur[i]].val.empty() ? csp_pofile[csp_idx.cur[i]].val.escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080; ?>&nbsp;</span>') : '&nbsp;')+"</div>"
				} else{
					inner += "<div><span class=\"csp-pl-form\"><?php _e( 'Plural Index Result =', 'translation-toolkit' ); ?> "+pl+" </span>"+(!csp_pofile[csp_idx.cur[i]].val[pl].empty() ? csp_pofile[csp_idx.cur[i]].val[pl].escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080; ?>&nbsp;</span>') : '&nbsp;')+"</div>"
				}
			}
			inner += "</td>";
		} else {
			inner +=
				"<td>"+ctx_str+csp_pofile[csp_idx.cur[i]].key.escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080; ?>&nbsp;</span>')+"</td>"+
				"<td>"+ctx_str+(csp_pofile[csp_idx.cur[i]].val.empty() ? '&nbsp;' : csp_pofile[csp_idx.cur[i]].val.escapeHTML().replace(/\s+$/g,'<span style="border: solid 1px #FF8080; ?>&nbsp;</span>'))+"</td>";
		}
		inner +=
			"<td nowrap=\"nowrap\">"+
			  "<a class=\"tr-edit-link\" onclick=\"return csp_edit_catalog(this);\"><?php _e( 'Edit', 'translation-toolkit' ); ?></a>&nbsp;|&nbsp;"+
			  "<a onclick=\"return csp_copy_catalog(this);\"><?php _e( 'Copy', 'translation-toolkit' ); ?></a>"; // TODO: add here comment editing link
		inner += "</td></tr>";
	}
	cb.replace("<tbody id=\"catalog-body\">"+inner+"</tbody>");

	$$("#csp-filter-all span").first().update(csp_idx.cur.size() + " / " + csp_idx.total.size());
	$$("#csp-filter-plurals span").first().update(csp_idx.plurals.size());
	$$("#csp-filter-open span").first().update(csp_idx.open.size());
	$$("#csp-filter-rem span").first().update(csp_idx.rem.size());
	$$("#csp-filter-code span").first().update(csp_idx.code.size());
	$$("#csp-filter-ctx span").first().update(csp_idx.ctx.size());
	$$("#csp-filter-trail span").first().update(csp_idx.trail.size());
	$$("#csp-filter-search span").first().update(csp_idx.cur.size());
	$$("#csp-filter-regexp span").first().update(csp_idx.cur.size());
}

function csp_filter_result(elem, set) {
	$$("a.csp-filter").each(function(e) { e.removeClassName('current')});
	$(elem).addClassName('current' );
	$('s_original').clear();
	$('s_translation').clear();
	$('csp-filter-search').up().hide();
	$('csp-filter-regexp').up().hide();
//$	csp_idx.cur = set;
	csp_idx.cur = csp_idx.ltd.intersect(set);
	csp_searchbase = csp_idx.cur;
	csp_change_pagenum(1);
}

function csp_search_key(elem, expr) {
	var term = $(elem).value;
	var ignore_case = $('ignorecase_key').checked;
	var is_expr = (typeof(expr) == "object");
	if (is_expr) {
		term = expr; ignore_case = false;
		$('s_original').clear();
	}
	else {
		if (ignore_case) term = term.toLowerCase();
	}
	$('s_translation').clear();
	$$("a.csp-filter").each(function(e) { e.removeClassName('current')});
	csp_idx.cur = [];
	try{
		for (i=0; i<csp_searchbase.size(); i++) {
			if (Object.isArray(csp_pofile[csp_searchbase[i]].key)) {
				if (csp_pofile[csp_searchbase[i]].key.find(function(s){ return (ignore_case ? s.toLowerCase().include(term) : s.match(term)); })) csp_idx.cur.push(csp_searchbase[i]);
			} else{
				if ( (ignore_case ? csp_pofile[csp_searchbase[i]].key.toLowerCase().include(term) : csp_pofile[csp_searchbase[i]].key.match(term) ) ) csp_idx.cur.push(csp_searchbase[i]);
			}
		}
	}catch(e) {
		//in case of half ready typed regexp catch it silently
		csp_idx.cur = csp_idx.total;
	}
	$('csp-filter-search').up().hide();
	$('csp-filter-regexp').up().hide();
	if (term) {
		if (is_expr) $('csp-filter-regexp').up().show();
		else $('csp-filter-search').up().show();
		csp_change_pagenum(1);
	}
	else {
		csp_filter_result('csp-filter-all', csp_idx.total);
	}
}

function csp_search_val(elem, expr) {
	var term = $(elem).value;
	var ignore_case = $('ignorecase_val').checked;
	var is_expr = (typeof(expr) == "object");
	if (is_expr) {
		term = expr; ignore_case = false;
		$('s_translation').clear();
	} else {
		if (ignore_case) term = term.toLowerCase();
	}
	$('s_original').clear();
	$$("a.csp-filter").each(function(e) { e.removeClassName('current')});
	csp_idx.cur = [];
	try{
		for (i=0; i<csp_searchbase.size(); i++) {
			if (Object.isArray(csp_pofile[csp_searchbase[i]].val)) {
				if (csp_pofile[csp_searchbase[i]].val.find(function(s){ return (ignore_case ? s.toLowerCase().include(term) : s.match(term)); })) csp_idx.cur.push(csp_searchbase[i]);
			}
			else{
				if ( (ignore_case ? csp_pofile[csp_searchbase[i]].val.toLowerCase().include(term) : csp_pofile[csp_searchbase[i]].val.match(term) ) ) csp_idx.cur.push(csp_searchbase[i]);
			}
		}
	} catch(e) {
		//in case of half ready typed regexp catch it silently
		csp_idx.cur = csp_idx.total;
	}
	$('csp-filter-search').up().hide();
	$('csp-filter-regexp').up().hide();
	if (term) {
		if (is_expr) $('csp-filter-regexp').up().show();
		else $('csp-filter-search').up().show();
		csp_change_pagenum(1);
	} else {
		csp_filter_result('csp-filter-all', csp_idx.total);
	}
}

function csp_search_result(elem) {
	window.clearTimeout(csp_search_timer);
	if ($(elem).id == "s_original") {
		csp_search_timer = this.csp_search_key.delay(csp_search_interval, elem);
	}else{
		csp_search_timer = this.csp_search_val.delay(csp_search_interval, elem);
	}
}

function csp_exec_expression(elem) {
	var s = $("csp-dialog-expression").value;
	var t = /^\/(.*)\/([gi]*)/;
	var a = t.exec(s);
	var r = (a != null ? RegExp(a[1], a[2]) : RegExp(s, ''));
	if (elem == "s_original") {
		csp_search_key(elem, r);
	}else{
		csp_search_val(elem, r);
	}
	csp_cancel_dialog();
}

function csp_search_regexp(elem) {
	$(elem).blur();
	$('csp-dialog-caption').update("<?php _e( 'Extended Expression Search', 'translation-toolkit' ); ?>");
	$("csp-dialog-body").update(
		"<div><strong><?php _e( 'Expression:', 'translation-toolkit' ); ?></strong></div>"+
		"<input type=\"text\" id=\"csp-dialog-expression\" style=\"width:98%;font-size:11px;line-height:normal;\" value=\"\"\>"+
		"<div style=\"margin-top:10px; color:#888;\"><strong><?php _e( 'Examples: <small>Please refer to official Perl regular expression descriptions</small>', 'translation-toolkit' ); ?></strong></div>"+
		'<div style="height: 215px; overflow:scroll; ?>'+
		<?php require( plugin_dir_path( TranslationToolkit::get_file() ) . 'includes/js-help-perlreg.php' ); ?>
		'</div>'+
		"<p style=\"margin:5px 0 0 0;text-align:center; padding-top: 5px;border-top: solid 1px #aaa;\">"+
		"<input class=\"button\" type=\"submit\" onclick=\"return csp_exec_expression('"+elem+"' );\" value=\"  <?php echo _e( 'Search', 'translation-toolkit' ); ?>  \"/>"+
		"</p>"
	).setStyle({'padding' : '10px'});
	tb_show(null,"#TB_inline?height=385&width=600&inlineId=csp-dialog-container&modal=true",false);
	$("csp-dialog-expression").focus();
}

function csp_translate_none(elem, source, dest) {
	$(elem).blur();
	$(elem).down().show();
}

function csp_save_translation(elem, isplural, additional_action){
	$(elem).blur();

	msgid = $('csp-dialog-msgid').value;
	msgstr = '';

	glue = (Prototype.Browser.Opera ? '\1' : '\0' ); //opera bug: can't send embedded 0 in strings!

	if (isplural) {
		msgid = [$('csp-dialog-msgid').value, $('csp-dialog-msgid-plural').value].join(glue);
		msgstr = [];
		if (csp_num_plurals == 1){
			msgstr = $('csp-dialog-msgstr-0').value;
		}
		else {
			for (pl=0;pl<csp_num_plurals; pl++) {
				msgstr.push($('csp-dialog-msgstr-'+pl).value);
			}
			msgstr = msgstr.join(glue);
		}
	} else{
		msgstr = $('csp-dialog-msgstr').value;
	}
	idx = parseInt($('csp-dialog-msg-idx').value);
	if (additional_action != 'close') {
		$('csp-dialog-body').hide();
		$('csp-dialog-saving').show();
	}
	//add the context in front of again
	if (csp_pofile[idx].ctx) msgid = csp_pofile[idx].ctx+ String.fromCharCode(4) + msgid;

	jQuery('#csp-credentials > form').find('input').each(function(i, e) {
		if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
		var s = jQuery(e).attr('name' );
		var v = jQuery(e).val();
		csp_ajax_params[s] = v;
	});

	csp_ajax_params.action = 'csp_po_save_catalog_entry';
	csp_ajax_params.path = csp_path;
	csp_ajax_params.file = csp_file;
	csp_ajax_params.isplural = isplural;
	csp_ajax_params.msgid = msgid;
	csp_ajax_params.msgstr = msgstr;
	csp_ajax_params.msgidx = idx;

	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				if (isplural && (csp_num_plurals != 1)) {
					csp_pofile[idx].val = msgstr.split(glue);
				}
				else{
					csp_pofile[idx].val = msgstr;
				}
				//TODO: check also erasing fields !!!!
				if (!msgstr.empty() && (csp_idx.open.indexOf(idx) != -1)) {
					csp_idx.open = csp_idx.open.without(idx);
//					csp_idx.cur = csp_idx.cur.without(idx); //TODO: only allowed if this is not total !!!
				}else if (msgstr.empty() && (csp_idx.open.indexOf(idx) == -1)) {
					csp_idx.open.push(idx);
				}
				csp_change_pagenum(csp_pagenum);
				if (additional_action != 'close') {
					var lin_idx = csp_idx.cur.indexOf(idx);
					if (additional_action == 'prev') {
						lin_idx--;
					}
					if (additional_action == 'next') {
						lin_idx++;
					}
					if (Math.floor(lin_idx / csp_pagesize) != csp_pagenum -1) {
						csp_change_pagenum(Math.floor(lin_idx / csp_pagesize) + 1);
					}
					$('csp-dialog-saving').hide();
					$('csp-dialog-body').show();
					csp_edit_catalog($$("#msg-row-"+csp_idx.cur[lin_idx]+" a.tr-edit-link")[0]);
				}
				else {
					csp_cancel_dialog();
				}
				csp_ajax_params.action = '';
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__( 'User Credentials required', 'translation-toolkit' )); ?></b>',
						buttons: {
							"<?php echo esc_js(__( 'Ok', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_save_translation(elem, isplural, additional_action);
							},
							"<?php echo esc_js(__( 'Cancel', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
								if (additional_action != 'close') {
									$('csp-dialog-body').show();
									$('csp-dialog-saving').hide();
								}
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				} else {
					$('csp-dialog-saving').hide();
					$('csp-dialog-body').show();
					//opera bug: Opera has in case of error no valid responseText (always empty), even if server sends it! Ensure status text instead (dirty fallback)
					csp_show_error( (Prototype.Browser.Opera ? transport.statusText : transport.responseText));
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;
}

function csp_suppress_enter(event) {
	if(event.keyCode == Event.KEY_RETURN) Event.stop(event);
}

function csp_copy_catalog(elem) {
	elem = $(elem);
	elem.blur();

	jQuery('#csp-credentials > form').find('input').each(function(i, e) {
		if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
		var s = jQuery(e).attr('name' );
		var v = jQuery(e).val();
		csp_ajax_params[s] = v;
	});

	var msg_idx = parseInt(elem.up().up().id.replace('msg-row-',''));
	msgid = csp_pofile[msg_idx].key;
	msgstr = csp_pofile[msg_idx].key;
	if(Object.isArray(csp_pofile[msg_idx].key)) {
		msgid = csp_pofile[msg_idx].key.join("\0");
		if (csp_num_plurals == 1) {
			msgstr = csp_pofile[msg_idx].key[0];
		}
		else{
			msgstr = msgid;
		}
	}

	csp_ajax_params.action = 'csp_po_save_catalog_entry';
	csp_ajax_params.path = csp_path;
	csp_ajax_params.file = csp_file;
	csp_ajax_params.isplural =  Object.isArray(csp_pofile[msg_idx].key);
	csp_ajax_params.msgid = msgid;
	csp_ajax_params.msgstr = msgstr;
	csp_ajax_params.msgidx = msg_idx;

	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				idx = msg_idx;
				if (Object.isArray(csp_pofile[msg_idx].key) && (csp_num_plurals != 1)) {
					csp_pofile[idx].val = msgstr.split("\0");
				}
				else{
					csp_pofile[idx].val = msgstr;
				}
				//TODO: check also erasing fields !!!!
				if (!msgstr.empty() && (csp_idx.open.indexOf(idx) != -1)) {
					csp_idx.open = csp_idx.open.without(idx);
				}
				csp_change_pagenum(csp_pagenum);
				csp_ajax_params.action = '';
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__( 'User Credentials required', 'translation-toolkit' )); ?></b>',
						buttons: {
							"<?php echo esc_js(__( 'Ok', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click' );
							},
							"<?php echo esc_js(__( 'Cancel', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;
}

function csp_edit_catalog(elem) {
	elem = $(elem);
	elem.blur();
	elem.up().up().addClassName('highlight-editing' );
	var msg_idx = parseInt(elem.up().up().id.replace('msg-row-',''));
	$('csp-dialog-caption').update("<?php _e( 'Edit Catalog Entry', 'translation-toolkit' ); ?>");
	if (Object.isArray(csp_pofile[msg_idx].key)) {
		trans = '';
		for (pl=0;pl<csp_num_plurals; pl++) {
			if (!csp_destlang.empty()) {
				switch(pl){
					case 0:
						trans += "<div style=\"margin-top:10px;height:20px;\"><strong class=\"alignleft\"><?php _e( 'Plural Index Result =', 'translation-toolkit' ); ?> "+pl+"</strong><a class=\"alignright clickable service-api\" onclick=\"csp_translate_"+csp_api_type+"(this, 'csp-dialog-msgid', 'csp-dialog-msgstr-0' );\"><img style=\"display:none;\" src=\"<?php echo plugin_dir_url( TranslationToolkit::get_file() ); ?>images/loading-small.gif\" />&nbsp;<?php _e( 'translate with API Service by', 'translation-toolkit' ); ?> "+csp_api_type.capitalize()+"</a><br class=\"clear\" /></div>";
					break;
					case 1:
						trans += "<div style=\"margin-top:10px;height:20px;\"><strong class=\"alignleft\"><?php _e( 'Plural Index Result =', 'translation-toolkit' ); ?> "+pl+"</strong><a class=\"alignright clickable service-api\" onclick=\"csp_translate_"+csp_api_type+"(this, 'csp-dialog-msgid-plural', 'csp-dialog-msgstr-1' );\"><img style=\"display:none;\" src=\"<?php echo plugin_dir_url( TranslationToolkit::get_file() ); ?>images/loading-small.gif\" />&nbsp;<?php _e( 'translate with API Service by', 'translation-toolkit' ); ?> "+csp_api_type.capitalize()+"</a><br class=\"clear\" /></div>";
					break;
					default:
						trans += "<div style=\"margin-top:10px;height:20px;\"><strong><?php _e( 'Plural Index Result =', 'translation-toolkit' ); ?> "+pl+"</strong></div>";
					break;
				}
			} else{
				trans += "<div style=\"margin-top:10px;\"><strong><?php _e( 'Plural Index Result =', 'translation-toolkit' ); ?> "+pl+"</strong></div>";
			}
			if (csp_num_plurals == 1) {
				trans += "<textarea id=\"csp-dialog-msgstr-"+pl+"\" class=\"csp-area-multi\" cols=\"50\" rows=\"1\" style=\"width:98%;font-size:11px;line-height:normal;\">"+csp_pofile[msg_idx].val.escapeHTML()+"</textarea>";
			} else{
				trans += "<textarea id=\"csp-dialog-msgstr-"+pl+"\" class=\"csp-area-multi\" cols=\"50\" rows=\"1\" style=\"width:98%;font-size:11px;line-height:normal;\">"+csp_pofile[msg_idx].val[pl].escapeHTML()+"</textarea>";
			}
		}

		$("csp-dialog-body").update(
			"<small style=\"display:block;text-align:right;\"><b><?php _e( 'Access Keys:', 'translation-toolkit' ); ?></b> <em>ALT</em> + <em>Shift</em> + [<b>p</b>]revious | [<b>s</b>]ave | [<b>n</b>]next</small>"+
			"<div><strong><?php _e( 'Singular:', 'translation-toolkit' ); ?></strong></div>"+
			"<textarea id=\"csp-dialog-msgid\" class=\"csp-area-multi\" cols=\"50\" rows=\"1\" style=\"width:98%;font-size:11px;line-height:normal;\" readonly=\"readonly\">"+csp_pofile[msg_idx].key[0].escapeHTML()+"</textarea>"+
			"<div style=\"margin-top:10px;\"><strong><?php _e( 'Plural:', 'translation-toolkit' ); ?></strong></div>"+
			"<textarea id=\"csp-dialog-msgid-plural\" class=\"csp-area-multi\" cols=\"50\" rows=\"1\" style=\"width:98%;font-size:11px;line-height:normal;\" readonly=\"readonly\">"+csp_pofile[msg_idx].key[1].escapeHTML()+"</textarea>"+
			"<div style=\"font-weight:bold;padding-top: 5px;border-bottom: dotted 1px #aaa;\"><?php _e("Plural Index Calculation:", 'translation-toolkit' ); ?>&nbsp;&nbsp;&nbsp;<span style=\"color:#D54E21;\">"+csp_func_plurals+"</span></div>"+
			trans+
			"<p style=\"margin:5px 0 0 0;text-align:center; padding-top: 5px;border-top: solid 1px #aaa;\">"+
			"<input class=\"button\""+(csp_idx.cur.indexOf(msg_idx) > 0 ? "" : " disabled=\"disabled\"")+" type=\"submit\" onclick=\"return csp_save_translation(this, true, 'prev' );\" value=\"  <?php echo _e( '« Save & Previous', 'translation-toolkit' ); ?>  \" accesskey=\"p\"/>&nbsp;&nbsp;&nbsp;&nbsp;"+
			"<input class=\"button\" type=\"submit\" onclick=\"return csp_save_translation(this, true, 'close' );\" value=\"  <?php echo _e( 'Save', 'translation-toolkit' ); ?>  \" accesskey=\"s\"/>"+
			"&nbsp;&nbsp;&nbsp;&nbsp;<input class=\"button\""+(csp_idx.cur.indexOf(msg_idx)+1 < csp_idx.cur.size() ? "" : " disabled=\"disabled\"")+" type=\"submit\" onclick=\"return csp_save_translation(this, true, 'next' );\" value=\"  <?php echo _e( 'Save & Next »', 'translation-toolkit' ); ?>  \" accesskey=\"n\"/>"+
			"</p><input id=\"csp-dialog-msg-idx\" type=\"hidden\" value=\""+msg_idx+"\" />"
		).setStyle({'padding' : '10px'});
	}else{
		$("csp-dialog-body").update(
			"<small style=\"display:block;text-align:right;\"><b><?php _e( 'Access Keys:', 'translation-toolkit' ); ?></b> <em>ALT</em> + <em>Shift</em> + [p]revious | [s]ave | [n]next</small>"+
			"<div><strong><?php _e( 'Original:', 'translation-toolkit' ); ?></strong></div>"+
			"<textarea id=\"csp-dialog-msgid\" class=\"csp-area-single\" cols=\"50\" rows=\"7\" style=\"width:98%;font-size:11px;line-height:normal;\" readonly=\"readonly\">"+csp_pofile[msg_idx].key.escapeHTML()+"</textarea>"
			+ (csp_destlang.empty() ?
			"<div style=\"margin-top:10px;\"><strong><?php _e( 'Translation:', 'translation-toolkit' ); ?></strong></div>"
			:
			 "<div style=\"margin-top:10px;height:20px;\"><strong class=\"alignleft\"><?php _e( 'Translation:', 'translation-toolkit' ); ?></strong><a class=\"alignright clickable service-api\" onclick=\"csp_translate_"+csp_api_type+"(this, 'csp-dialog-msgid', 'csp-dialog-msgstr' );\"><img style=\"display:none;\" align=\"left\" src=\"<?php echo plugin_dir_url( TranslationToolkit::get_file() ); ?>images/loading-small.gif\" />&nbsp;<?php _e( 'translate with API Service by', 'translation-toolkit' ); ?> "+csp_api_type.capitalize()+"</a><br class=\"clear\" /></div>"
			 ) +
			"<textarea id=\"csp-dialog-msgstr\" class=\"csp-area-single\" cols=\"50\" rows=\"7\" style=\"width:98%;font-size:11px;line-height:normal;\">"+csp_pofile[msg_idx].val.escapeHTML()+"</textarea>"+
			"<p style=\"margin:5px 0 0 0;text-align:center; padding-top: 5px;border-top: solid 1px #aaa;\">"+
			"<input class=\"button\""+(csp_idx.cur.indexOf(msg_idx) > 0 ? "" : " disabled=\"disabled\"")+" type=\"submit\" onclick=\"return csp_save_translation(this, false, 'prev' );\" value=\"  <?php echo _e( '« Save & Previous', 'translation-toolkit' ); ?>  \" accesskey=\"p\"/>&nbsp;&nbsp;&nbsp;&nbsp;"+
			"<input class=\"button\" type=\"submit\" onclick=\"return csp_save_translation(this, false, 'close' );\" value=\"  <?php echo _e( 'Save', 'translation-toolkit' ); ?>  \" accesskey=\"s\"/>"+
			"&nbsp;&nbsp;&nbsp;&nbsp;<input class=\"button\""+(csp_idx.cur.indexOf(msg_idx)+1 < csp_idx.cur.size() ? "" : " disabled=\"disabled\"")+" type=\"submit\" onclick=\"return csp_save_translation(this, false, 'next' );\" value=\"  <?php echo _e( 'Save & Next »', 'translation-toolkit' ); ?>  \" accesskey=\"n\"/>"+
			"</p><input id=\"csp-dialog-msg-idx\" type=\"hidden\" value=\""+msg_idx+"\" />"
		).setStyle({'padding' : '10px'});
	}
	tb_show(null,"#TB_inline?height="+(csp_num_plurals > 2 && Object.isArray(csp_pofile[msg_idx].key) ? '520' : '385')+"&width=680&inlineId=csp-dialog-container&modal=true",false);
	$$('#csp-dialog-body textarea').each(function(e) {
		e.observe('keydown', csp_suppress_enter);
		e.observe('keypress', csp_suppress_enter);
		e.observe('keyup', csp_suppress_enter);
	});
	$("csp-dialog-msgstr", "csp-dialog-msgstr-0").each(function(e) {
		csp_focus_editor.defer(e);
	});
	return false;
}

function csp_focus_editor(e) {
	try{e.focus();}catch(a){};
}

function csp_view_phpfile(elem, phpfile, idx) {
	elem.blur();
	glue = (Prototype.Browser.Opera ? '\1' : '\0' ); //opera bug: can't send embedded 0 in strings!
	msgid = csp_pofile[idx].key;
	if (Object.isArray(msgid)) {
		msgid = msgid.join(glue);
	}
	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>', {
			parameters: {
				action: 'csp_po_dlg_show_source',
				path: csp_path,
				file: phpfile,
				msgid: msgid
			},
			onSuccess: function(transport) {
				//own <iframe> creation, because of POST content filling into inline thickbox
				var iframe = null;
				$('csp-dialog-caption').update("<?php _e( 'File:', 'translation-toolkit' ); ?> "+phpfile.split(':')[0]);
				$('csp-dialog-body').insert(iframe = new Element('iframe', {'class' : 'csp-dialog-iframe', 'frameBorder' : '0'}).writeAttribute({'width' : '100%', 'height' : '570px', 'margin': '0'})).setStyle({'padding' : '0px'});
				tb_show(null,"#TB_inline?height=600&width=600&inlineId=csp-dialog-container&modal=true",false);
				iframe.contentWindow.document.open();
				iframe.contentWindow.document.write(transport.responseText);
				iframe.contentWindow.document.close();
			}
		}
	);
	return false;
}

function csp_generate_mofile(elem) {
	elem.blur();
	$('csp-generate-mofile').show();
	$('catalog-last-saved').hide();

	jQuery('#csp-credentials > form').find('input').each(function(i, e) {
		if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
		var s = jQuery(e).attr('name' );
		var v = jQuery(e).val();
		csp_ajax_params[s] = v;
	});

	csp_ajax_params.action = 'csp_po_generate_mo_file';
	csp_ajax_params.pofile = csp_path + csp_file;
	csp_ajax_params.textdomain = $('csp-mo-textdomain-val').value;

	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>',
		{
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
			$('csp-generate-mofile').hide();
			$('catalog-last-saved').show();
				new Effect.Highlight($('catalog-last-saved').update(transport.responseJSON.filetime), { startcolor: '#25FF00', endcolor: '#FFFFCF' });
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js(__( 'User Credentials required', 'translation-toolkit' )); ?></b>',
						buttons: {
							"<?php echo esc_js(__( 'Ok', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click' );
							},
							"<?php echo esc_js(__( 'Cancel', 'translation-toolkit' )); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
								$('csp-generate-mofile').hide();
								$('catalog-last-saved').show();
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				}else {
					$('csp-generate-mofile').hide();
					$('catalog-last-saved').show();
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;
}

function csp_create_languange_path(elem, path) {
	elem.blur();

	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name' );
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	} else {
		csp_ajax_params.action = 'csp_po_create_language_path';
		csp_ajax_params.path = path;
	}

	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>',
		{
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				window.location.reload();
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js( __( 'User Credentials required', 'translation-toolkit' ) ); ?></b>',
						buttons: {
							"<?php echo esc_js( __( 'Ok', 'translation-toolkit' ) ); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click' );
							},
							"<?php echo esc_js( __( 'Cancel', 'translation-toolkit' ) ); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				} else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;
}

function csp_create_pot_indicator(elem, potfile) {
	elem.blur();

	if(csp_ajax_params.action.length) {
		jQuery('#csp-credentials > form').find('input').each(function(i, e) {
			if ((jQuery(e).attr('type') == 'radio') && !jQuery(e).attr('checked')) return;
			var s = jQuery(e).attr('name' );
			var v = jQuery(e).val();
			csp_ajax_params[s] = v;
		});
	}else{
		csp_ajax_params.action = 'csp_po_create_pot_indicator';
		csp_ajax_params.potfile = potfile;
	}

	new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>',
		{
			parameters: csp_ajax_params,
			onSuccess: function(transport) {
				window.location.reload();
			},
			onFailure: function(transport) {
				if (transport.status == '401') {
					jQuery('#csp-credentials').html(transport.responseText).dialog({
						width: '500px',
						closeOnEscape: false,
						modal: true,
						resizable: false,
						title: '<b><?php echo esc_js( __( 'User Credentials required', 'translation-toolkit' ) ); ?></b>',
						buttons: {
							"<?php echo esc_js( __( 'Ok', 'translation-toolkit' ) ); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								jQuery(elem).trigger('click' );
							},
							"<?php echo esc_js( __( 'Cancel', 'translation-toolkit' ) ); ?>": function() {
								jQuery('#csp-credentials').dialog("close");
								csp_ajax_params.action = '';
							}
						},
						open: function(event, ui) {
							jQuery('#csp-credentials').show().css('width', 'auto' );
						},
						close: function() {
							jQuery('#csp-credentials').dialog("destroy");
						}
					});
					jQuery('#upgrade').hide().attr('disabled', 'disabled' );
				}else {
					csp_show_error(transport.responseText);
					csp_ajax_params.action = '';
				}
			}
		}
	);
	return false;
}

jQuery(document).ready(function() {
	jQuery('#enable_low_memory_mode').click(function(e) {
		jQuery('#enable_low_memory_mode_indicator').toggle();
		mode = jQuery(e.target).is(':checked' );
		new Ajax.Request('<?php echo get_admin_url( null, '/admin-ajax.php' ); ?>',
			{
				parameters: {
					action: 'csp_po_change_low_memory_mode',
					mode: mode
				},
				onSuccess: function(transport) {
					jQuery('#enable_low_memory_mode_indicator').toggle();
				}
			});
		csp_chuck_size = (jQuery(e.target).is(':checked') ? 1 : 20);
	});

	jQuery('.question-help').live('click', function(event) {
		event.preventDefault();
		window.scrollTo(0,0);
		jQuery('#tab-link-'+jQuery(this).attr('rel')+' a').trigger('click' );
		if (!jQuery('#contextual-help-link').hasClass('screen-meta-active')) jQuery('#contextual-help-link').trigger('click' );
	});
});

/* TODO: implement context sensitive help
function csp_process_online_help(event) {
	if (event) {
		if (event.keyCode == 112) {
			Event.stop(event);
			//TODO: launch appropriated help ajax here for none IE
			return false;
		}
	}else{
		//TODO: launch appropriated help ajax here for IE
		return false;
	}
	return true;
}

function csp_term_help_key(event) {
	if(event.keyCode == 112) {
		Event.stop(event);
		return false;
	}
	return true;
}

if (Prototype.Browser.IE) {
	document.onhelp = csp_process_online_help;
}else{
	document.observe("keydown", csp_process_online_help);
}
document.observe("keyup", csp_term_help_key);
document.observe("keypress", csp_term_help_key);
*/

/* ]]> */
</script>