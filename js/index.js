<<<<<<< HEAD
var log_error=document.getElementById("log_error_msg").innerHTML ;
var sign_error = document.getElementById("sign_error_msg").innerHTML;

if (log_error=="incorrect email or password") {
	var log_pop = document.getElementById('id01');
	log_pop.style.display="block";
}   

if (sign_error=="email already exists") {
	var sign_pop = document.getElementById('id02');
	sign_pop.style.display="block";
=======
var modal = document.getElementById('id01');

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
>>>>>>> b8d639f1b9754f59a42d0d4b79c2852848c604d1
}


