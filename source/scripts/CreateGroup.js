var ckeditor_conf={	
    toolbarGroups : [
	{ name: 'document', groups: ['document', 'doctools' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
	{ name: 'editing', groups: [ 'find', 'selection', 'editing' ] },
	'/',
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'paragraph' ] },
	{ name: 'links', groups: [ 'links' ] },
	{ name: 'insert', groups: [ 'insert' ] },
	{ name: 'styles', groups: [ 'styles' ] },
	{ name: 'colors', groups: [ 'colors' ] },
	{ name: 'tools', groups: [ 'tools' ] },
	{ name: 'others', groups: [ 'others' ] }
    ],
    removeButtons : 'CreateDiv,Flash,Iframe,NewPage,Save,Print,Source,PageBreak',
    uiColor: '#ccffcc',
    filebrowserBrowseUrl: '/source/ckfinder/ckfinder.html',
    filebrowserUploadUrl: '/source/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
};

$(document).ready(function(){
    CKEDITOR.replace( 'Body', ckeditor_conf);
    var img = ImageLoader({
	Replace:"Mini",
	Width:180,
	Height:120,
	ImageUploader:"/Profile/UploadImage",
	Id:"preview"
    }); 
});
