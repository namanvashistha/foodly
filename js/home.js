
	function show_chat_box(){
	var chat_box=document.getElementById('chat-box');
	if(chat_box.style.display=="none")
		chat_box.style.display='block';
	else
		chat_box.style.display='none';
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
$(document).click(function(){
 $("#myDropdown").hide('slow'); 
});

$("#myDropdown").click(function(e){
  e.stopPropagation(); 
});
