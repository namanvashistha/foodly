/* Foodly role auth — tab switching + error-driven tab open */

function showTab(name) {
	document.querySelectorAll('.tab').forEach(function (t) {
		t.classList.toggle('active', t.dataset.tab === name);
	});
	document.querySelectorAll('.tab-panel').forEach(function (p) {
		p.classList.toggle('active', p.dataset.panel === name);
	});
}

document.querySelectorAll('.tab').forEach(function (t) {
	t.addEventListener('click', function () { showTab(t.dataset.tab); });
});

/* open the relevant tab when the server returned an auth error */
(function () {
	var logErr = document.getElementById('log_error_msg');
	var signErr = document.getElementById('sign_error_msg');
	if (signErr && signErr.textContent.trim() === 'email already exists') {
		showTab('signup');
	} else if (logErr && logErr.textContent.trim() === 'incorrect email or password') {
		showTab('login');
	}
})();
