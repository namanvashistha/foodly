/* Foodly home — user menu, support chat toggle, restaurant search */

function myFunction() {
	document.getElementById("myDropdown").classList.toggle("show");
}

/* close the dropdown when clicking outside the button */
window.addEventListener('click', function (e) {
	if (!e.target.closest('.dropbtn')) {
		var d = document.getElementById("myDropdown");
		if (d && d.classList.contains('show')) d.classList.remove('show');
	}
});

function show_chat_box() {
	var box = document.getElementById('chat-box');
	box.classList.toggle('open');
}

/* live filter of the restaurant grid */
function filterRestaurants() {
	var term = document.getElementById('restaurant-search').value.trim().toLowerCase();
	var cards = document.querySelectorAll('#restaurant-grid .rcard');
	var shown = 0;
	cards.forEach(function (c) {
		var match = c.getAttribute('data-name').indexOf(term) !== -1;
		c.style.display = match ? '' : 'none';
		if (match) shown++;
	});
	var none = document.getElementById('no-results');
	if (none) none.style.display = (shown === 0) ? '' : 'none';
}
