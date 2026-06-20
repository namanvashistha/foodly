/* Foodly food donation — dynamic item rows */

var item = 1;
function add_fields() {
	item++;
	var objTo = document.getElementById('item_fileds');
	var row = document.createElement("div");
	row.className = "item-row";
	row.innerHTML =
		'<div class="item-row-head">Item ' + item + '</div>' +
		'<div class="don-grid">' +
			'<input class="input" type="text" name="item_name[]" placeholder="Item name">' +
			'<input class="input" type="text" name="item_quan[]" placeholder="Quantity">' +
		'</div>';
	objTo.appendChild(row);
}
