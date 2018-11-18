var item = 1;
function add_fields() {
	item++;
   	var objTo = document.getElementById('item_fileds');
   	var divtest = document.createElement("div");
   	divtest.innerHTML = '<div class="label">Item ' + item +':</div><div class="content"><span>item name:<input type="text" name="item_name[]"/></span> <span>Price: <input type="text" name="item_price[]" /><span> Discount: <input type="text" name="item_discount[]" /></span></span> <span>Description: <input type="text" name="item_desc[]" /></span></div>';
    objTo.appendChild(divtest);
}

function myFunction() {
    document.getElementById("myDropdown").classList.toggle("show");
}

window.onclick = function(e) {
  if (!e.target.matches('.dropbtn')) {
    var myDropdown = document.getElementById("myDropdown");
      if (myDropdown.classList.contains('show')) {
        myDropdown.classList.remove('show');
      }
  }
}
