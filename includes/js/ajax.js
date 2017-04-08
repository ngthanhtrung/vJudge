/* AJAX */
var req;

function createRequest() {
	// Native XMLHttpRequest object
	if (window.XMLHttpRequest) {
		req = new XMLHttpRequest();
	// IE/Windows ActiveX
	} else if (window.ActiveXObject) {
		req = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req.onreadystatechange = processReqChange;
}

function get(url) {
	createRequest();
	if (admin) req.open("GET", 'index.php?admin&' + url, true);
	else req.open("GET", 'index.php?' + url, true);
	req.send(null);
}

function post(url, params) {
	createRequest();
	if (admin) req.open("POST", 'index.php?admin&' + url, true);
	else req.open("POST", 'index.php?' + url, true);
	
	//Send the proper header information along with the request
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.setRequestHeader("Content-length", params.length);
	req.setRequestHeader("Connection", "close");
	
	req.send(params);
}

function processReqChange() {
	if (req.readyState == 4) { // only if req shows "complete"
		if (req.status == 200) { // only if "OK"
			var xml = req.responseXML.documentElement;
			var componentTag = tryFindTag(xml, 'for');
			var component;
			if (!componentTag) component = '';
			else component = componentTag.firstChild.data;
			var call = '_' + component + '_reply(xml)';
			try { eval(call); } catch (e) { alert("Error when reading data:\n" + req.responseText); }
		} else {
			alert("There was a problem retrieving the XML data:\n" + req.statusText);
		}
	}
}

/* AJAX clone */
var req2;

function createRequest2() {
	// Native XMLHttpRequest object
	if (window.XMLHttpRequest) {
		req2 = new XMLHttpRequest();
	// IE/Windows ActiveX
	} else if (window.ActiveXObject) {
		req2 = new ActiveXObject("Microsoft.XMLHTTP");
	}
	req2.onreadystatechange = processReqChange2;
}

function get2(url) {
	createRequest2();
	if (admin) req2.open("GET", 'index.php?admin&' + url, true);
	else req2.open("GET", 'index.php?' + url, true);
	req2.send(null);
}

function post2(url, params) {
	createRequest2();
	if (admin) req2.open("POST", 'index.php?admin&' + url, true);
	else req2.open("POST", 'index.php?' + url, true);
	
	//Send the proper header information along with the request
	req2.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req2.setRequestHeader("Content-length", params.length);
	req2.setRequestHeader("Connection", "close");
	
	req2.send(params);
}

function processReqChange2() {
	if (req2.readyState == 4) { // only if req shows "complete"
		if (req2.status == 200) { // only if "OK"
			var xml = req2.responseXML.documentElement;
			var componentTag = tryFindTag(xml, 'for');
			var component;
			if (!componentTag) component = '';
			else component = componentTag.firstChild.data;
			var call = '_' + component + '_reply(xml)';
			try { eval(call); } catch (e) { alert("Error when reading data:\n" + req.responseText); }
		} else {
			alert("There was a problem retrieving the XML data:\n" + req.statusText);
		}
	}
}