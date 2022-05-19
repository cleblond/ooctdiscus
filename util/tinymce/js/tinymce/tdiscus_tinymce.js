
  tinymce.init({
    images_dataimg_filter: function(img) {
        return img.hasAttribute('internal-blob');
      },
      menubar:false,
      statusbar: false,
      //entity_encoding : "raw",
    selector: 'textarea',
    current_session_id: $('#phpsessid').val(),
    browser_spellcheck: true,
    relative_urls: false,
    //content_css: 'css/mce-styles.css',
    height: 200,
    image_dimensions: false,
      image_class_list: [
            {title: 'Responsive', value: 'img-responsive'}
      ],
  /*  plugins: [
      'advlist autolink autoresize lists link image charmap print preview anchor',
      'searchreplace visualblocks code fullscreen noneditable',
      'insertdatetime media table textcolor contextmenu paste code responsivefilemanager emoticons oockekule image media template formula'
    ],*/
  plugins: "table textcolor searchreplace code fullscreen insertdatetime paste charmap lists advlist save image imagetools link pagebreak formula emoticons responsivefilemanager oockekule",

  //  toolbar: 'insertfile undo redo | styleselect | fontselect bold italic forecolor backcolor fontsizeselect | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | charmap | emoticons | responsivefilemanager | code image media | template | formula | oockekule',

//toolbar: "undo redo | bold italic underline | fontsizeselect | superscript subscript | bullist numlist outdent indent | forecolor backcolor | link image responsivefilemanager | emoticons | formula | charmap | oockekule",

  toolbar: 'undo redo |  formatselect | bold italic backcolor | superscript subscript | bullist numlist outdent indent | removeformat | formula oockekule',

  removed_menuitems: "newdocument",
  font_formats: 'Arial=arial,helvetica,sans-serif;Courier New=courier new,courier,monospace;AkrutiKndPadmini=Akpdmi-n',
  setup: function (editor) {
           
            
            editor.on('change', function (e) {
            editor.save();
            });


            editor.on('KeyDown', function (e) {

                if ((e.keyCode == 8 || e.keyCode == 46) && editor.selection) { // delete & backspace keys

                    var selectedNode = tinymce.activeEditor.selection.getNode();

                    if (selectedNode && selectedNode.nodeName == 'IMG') {

                       var divid = selectedNode.getAttribute('data-chem-obj').slice(5,13);

                       tinymce.DOM.remove(divid);

                    }
                }
            });




    },

        image_advtab: true ,

        image_caption: true,
        media_live_embeds: true,
        imagetools_cors_hosts: ['tinymce.com', 'codepen.io'],

        //valid_elements:'script[language|type|src], div[style], span[id|class]',
        //valid_elements: 'img[src|style|width|height|data-kekule-widget|data-render-type|data-chem-obj|data-auto-size|data-draw-options|data-predefined-setting|data-auto-size|class]',

         valid_elements : '@[id|class|style|title|dir<ltr?rtl|lang|xml::lang|onclick|ondblclick|'
            + 'onmousedown|onmouseup|onmouseover|onmousemove|onmouseout|onkeypress|'
            + 'onkeydown|onkeyup],a,strong/b,em/i,strike,u,'
            + '#p,-ol[type|compact],-ul[type|compact],-li,br,img[longdesc|usemap|'
            + 'src|border|alt=|title|hspace|vspace|width|height|align],-sub,-sup,'
            + '-blockquote,-table[border=0|cellspacing|cellpadding|width|frame|rules|'
            + 'height|align|summary|bgcolor|background|bordercolor],-tr[rowspan|width|'
            + 'height|align|valign|bgcolor|background|bordercolor],tbody,thead,tfoot,'
            + '#td[colspan|rowspan|width|height|align|valign|bgcolor|background|bordercolor'
            + '|scope],#th[colspan|rowspan|width|height|align|valign|scope],caption,div[id|style|type],'
            + '-span,-code,-pre,address,-h1,-h2,-h3,-h4,-h5,-h6,hr[size|noshade],-font[face'
            + '|size|color],dd,dl,dt,cite,abbr,acronym,del[datetime|cite],ins[datetime|cite],'
            + 'object[classid|width|height|codebase|*],param[name|value|_value],embed[type|width'
            + '|height|src|*],script[src|type],map[name],area[shape|coords|href|alt|target],bdo,'
            + 'button,col[align|char|charoff|span|valign|width],colgroup[align|char|charoff|span|'
            + 'valign|width],dfn,fieldset,form[action|accept|accept-charset|enctype|method],'
            + 'input[accept|alt|checked|disabled|maxlength|name|readonly|size|src|type|value],'
            + 'kbd,label[for],legend,noscript,optgroup[label|disabled],option[disabled|label|selected|value],'
            + 'q[cite],samp,select[disabled|multiple|name|size],small,a[href|target]',

        extended_valid_elements:'@[type], img[src|style|width|height|data-kekule-widget|data-render-type|data-chem-obj|data-predefined-setting|data-auto-size|data-uniqid|class]',

        //external_filemanager_path:"/tsugi/mod/openochem/filemanager/",
        //filemanager_title:"Responsive Filemanager" ,
        //external_plugins: { "filemanager" : "/tsugi/mod/openochem/filemanager/plugin.min.js"},
        //file_browser_callback: function(fieldName, url, objectType, w) { filemanager(fieldName, url, objectType, w); }


  });
