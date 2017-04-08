var editorList = new Array();
var numEditor = 0;

function addEditor(id) {
	var editor = findId(id);
	if (editor) {
		tinyMCE.execCommand('mceAddControl', true, id);
		editorList[numEditor] = id;
		numEditor++;
	}
}

function removeAllEditors() {
	for (var i = 0; i < numEditor; i++)
		tinyMCE.execCommand('mceRemoveControl', true, editorList[i]);
	numEditor = 0;
	editorList = new Array();
}