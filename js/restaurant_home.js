/* Foodly restaurant dashboard — dynamic menu fields + user menu */

var item = 1;
function add_fields() {
	item++;
	var objTo = document.getElementById('item_fileds');
	var row = document.createElement("div");
	row.className = "item-row";
	row.innerHTML =
		'<div class="item-row-head">Item ' + item + '</div>' +
		'<div class="item-grid">' +
			'<input class="input" type="text" name="item_name[]" placeholder="Item name">' +
			'<input class="input" type="text" name="item_price[]" placeholder="Price">' +
			'<input class="input" type="text" name="item_discount[]" placeholder="Discount %" maxlength="3">' +
			'<input class="input" type="text" name="item_desc[]" placeholder="Description">' +
		'</div>';
	objTo.appendChild(row);
}

function myFunction() {
	document.getElementById("myDropdown").classList.toggle("show");
}

window.addEventListener('click', function (e) {
	if (!e.target.closest('.dropbtn')) {
		var d = document.getElementById("myDropdown");
		if (d && d.classList.contains('show')) d.classList.remove('show');
	}
});
