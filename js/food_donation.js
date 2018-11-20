var modal = document.getElementById('id01');

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

var item = 1;
function add_fields() {
	item++;
   	var objTo = document.getElementById('item_fileds');
   	var divtest = document.createElement("div");
   	divtest.innerHTML = '<div class="label">Item ' + item +':</div><div class="content"><span>item name:<input type="text" name="item_name[]"/></span> <span>Quantity: <input type="text" name="item_price[]" /><span></div>';
    objTo.appendChild(divtest);
}