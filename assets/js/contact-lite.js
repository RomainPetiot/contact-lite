document.addEventListener('DOMContentLoaded', function(){
	if(document.getElementById('form-contact')){
		document.getElementById('form-contact').addEventListener('submit', function(e) {
		    e.preventDefault();
			document.getElementById('cl_submitBtn').disabled = true;
			document.getElementById('cl_submitBtn').innerHTML = '*';
			var form = document.forms.namedItem("form-contact");
			var formData = new FormData(form);
		    xhr = new XMLHttpRequest();
			xhr.open('POST', cl_ajaxUrl, true);
			xhr.onload = function() {
			    if (xhr.status === 200) {
					el = document.getElementById('cl_modal');
					text = '<p>Votre message a été envoyé,<br>il sera traité rapidement.</p>';
					cl_openModal(el, text);
			    }
				document.getElementById('cl_submitBtn').disabled = false;
			};
			xhr.send(formData);
		});


	}
});

function cl_recaptchaCallback() {
	$('#submitBtn').removeAttr('disabled');
};

function cl_openModal(el, text = ""){
	if(text.length){
		el.children[1].innerHTML = text;
	}
	cl_addClass(el, 'openModal');
}
function cl_closeModal(){
	el = document.getElementById('cl_modal');
	cl_removeClass(el, 'openModal');
	document.location.href = home_url;
}

function cl_hasClass(el, className) {
  if (el.classList)
    return el.classList.contains(className)
  else
    return !!el.className.match(new RegExp('(\\s|^)' + className + '(\\s|$)'))
}

function cl_addClass(el, className) {
  if (el.classList)
    el.classList.add(className)
  else if (!cl_hasClass(el, className)) el.className += " " + className
}

function cl_removeClass(el, className) {
  if (el.classList)
    el.classList.remove(className)
  else if (cl_hasClass(el, className)) {
    var reg = new RegExp('(\\s|^)' + className + '(\\s|$)')
    el.className=el.className.replace(reg, ' ')
  }
}
