// Main
var hash = document.location.hash;
var prevhash = hash;

var q = document.location.search;
if (q.substr(0, 1) == '?') q = q.substring(1);
if (q.indexOf('&') != -1)
	q = q.substring(0, q.indexOf('&'));
var admin;
if (q == 'admin') admin = true;
else admin = false;

setInterval(function() {
    if (document.location.hash != hash) {
		prevhash = hash;
        hash = document.location.hash;
        processChange(hash);
    }
}, 100);

function processChange(h) {
	if (h.substr(0, 1) == '#') h = h.substring(1);
	if (h == '') {
		if (admin) h = 'page,do:list';
		else h = 'page,id:1';
	}
	var params = new Array();
	params = h.split(',');
	if (params[0] != '') {
		var url = 'com=' + params[0];
		var param = '';
		for (var i = 1; i < params.length; i++)
			param = param + ',' + params[i];
		if (param != '') get(url + '&q=' + param.substring(1));
		else get(url);
	}
}

function reload() {
	var href = window.location.href;
	if (prevhash.substring(0, prevhash.indexOf(',')) != hash.substring(0, hash.indexOf(','))) {
		window.location.href = href.substr(0, href.length - hash.length) + prevhash;
		window.location.reload(true);
	}
	else window.location.href = href.substr(0, href.length - hash.length);
}

function redirect(hash, url) {
	window.location.hash = hash;
	get(url);
}

// Message report
var t;
function showMessage(content) {
	var holder = findId('message');
	if (holder) {
		holder.innerHTML = content;
		holder.style.display = 'block';
		window.scrollTo(0, 0);
		if (t) clearTimeout(t);
		t = setTimeout('hideMessage()', 3000)
	}
}

function hideMessage() {
	var holder = findId('message');
	if (holder)
		holder.style.display = 'none';
}

// Load to Main
function loadToMain(content) {
	var m = findId('main');
	if (m) {
		removeAllEditors();
		m.innerHTML = content;
		var list = findTag('textarea', m);
		for (var i = 0; i < list.length; i++) {
			if (list[i].id)
				addEditor(list[i].id);
		}
	}
	window.scrollTo(0, 0);
}