/* Foodly landing — modals, reveal-on-scroll */

function openModal(id) {
	document.getElementById(id).style.display = 'block';
	document.body.style.overflow = 'hidden';
}
function closeModal(id) {
	document.getElementById(id).style.display = 'none';
	document.body.style.overflow = '';
}
function switchModal(from, to) {
	closeModal(from);
	openModal(to);
}

/* close on backdrop click */
document.querySelectorAll('.modal').forEach(function (m) {
	m.addEventListener('click', function (e) {
		if (e.target === m) closeModal(m.id);
	});
});

/* close on Escape */
document.addEventListener('keydown', function (e) {
	if (e.key === 'Escape') {
		document.querySelectorAll('.modal').forEach(function (m) {
			if (m.style.display === 'block') closeModal(m.id);
		});
	}
});

/* reopen the relevant modal when the server returned an auth error */
var logError = document.getElementById('log_error_msg');
var signError = document.getElementById('sign_error_msg');
if (logError && logError.textContent.trim() === 'incorrect email or password') {
	openModal('id01');
}
if (signError && signError.textContent.trim() === 'email already exists') {
	openModal('id02');
}

/* reveal on scroll */
var observer = new IntersectionObserver(function (entries) {
	entries.forEach(function (entry) {
		if (entry.isIntersecting) {
			entry.target.classList.add('in');
			observer.unobserve(entry.target);
		}
	});
}, { threshold: 0.12 });
document.querySelectorAll('.reveal').forEach(function (el) { observer.observe(el); });
