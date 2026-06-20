/* Foodly support desk — user menu dropdown */

function myFunction() {
	document.getElementById("myDropdown").classList.toggle("show");
}

window.addEventListener('click', function (e) {
	if (!e.target.closest('.dropbtn')) {
		var d = document.getElementById("myDropdown");
		if (d && d.classList.contains('show')) d.classList.remove('show');
	}
});
