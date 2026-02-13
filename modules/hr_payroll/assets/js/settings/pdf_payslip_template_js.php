<script>
	
	$(function() {
		'use strict';
		
		appValidateForm($('.pdf-payslip-template-form'), {
			'name': 'required',
			'payslip_template_id': 'required',
			
		});
	});

			var _templates = [];

			// Create a fixed toolbar container so the toolbar stays visible without clicking into the editor.
			var toolbarId = 'payslip-editor-toolbar';
			if (!document.getElementById(toolbarId)) {
				$('.tc-content.editable').before('<div id="'+toolbarId+'" class="tinymce-fixed-toolbar m-b-5"></div>');
			}

			// Modern TinyMCE config with visible toolbar/menus to make the designer easier to use.
			var modern_editor_settings = {
				selector: 'div.editable',
				inline: true,
				fixed_toolbar_container: '#' + toolbarId,
				promotion: false,
				browser_spellcheck: true,
				branding: false,
				menubar: 'file edit insert view format table tools help',
				toolbar: 'undo redo | formatselect | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media table | removeformat | code fullscreen help',
				toolbar_mode: 'sliding',
				plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount pagebreak',
				contextmenu: "link image media inserttable | cell row column deletetable | paste pastetext searchreplace | visualblocks pagebreak charmap | code",
				file_picker_callback: elFinderBrowser,
				pagebreak_separator: '<p pagebreak=\"true\"></p>',
				quickbars_insert_toolbar: 'image media quicktable | bullist numlist | h2 h3 | pagebreak | hr',
				quickbars_selection_toolbar: 'bold italic underline superscript | forecolor backcolor link | alignleft aligncenter alignright alignjustify | fontfamily fontsize | h2 h3',
				setup: function (editor) {
					editor.addShortcut('Meta+S', '', 'mceSave');

					editor.on('MouseDown ContextMenu', function () {
						if (!is_mobile() && !$('.left-column').hasClass('hide')) {
							contract_full_view();
						}
					});

					editor.on('blur', function () {
						$.Shortcuts.start();
					});

					editor.on('focus', function () {
						$.Shortcuts.stop();
					});
				}
			};

			if (_templates.length > 0) {
				modern_editor_settings.templates = _templates;
				modern_editor_settings.plugins = modern_editor_settings.plugins + ' template';
				modern_editor_settings.contextmenu = modern_editor_settings.contextmenu.replace('inserttable', 'inserttable template');
			}

			if(is_mobile()) {
				modern_editor_settings.inline = false;
				modern_editor_settings.toolbar = _tinymce_mobile_toolbar().join(' ');
			}

			tinymce.init(modern_editor_settings);


			function insert_merge_field(field) {
				var key = $(field).text();
				tinymce.activeEditor.execCommand('mceInsertContent', false, key);
			}

			function contract_full_view() {
				$('.left-column').toggleClass('hide');
				$('.right-column').toggleClass('col-md-7');
				$('.right-column').toggleClass('col-md-12');
				$(window).trigger('resize');
			}

	</script>
