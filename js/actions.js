// coding = utf-8
// using namespace std

/**
 * That file have all the buttons and fields methods.
 */


/**
 * Show a error in the page, using the .error-lb class and specifying the label ID
 * @param {*} id The specific label ID
 * @param {*} message The error message.
 */
function showError(id, message){
	var err_lb = document.getElementById(id);
	err_lb.innerText = message;
	err_lb.style += "visibility: visible;";
}

function hideError(id_err){
	var err = document.getElementById(id_err);
	err.style += "visibility: hidden;";
}


/**
 * @param pas1_inp The reference of the password input tag on the document. Used the ID normally.
 * @param pas2_inp The reference of the password confirmation input tag on the document. Used the ID normally
 * @param nm_inp The reference of the username input tag on the document. Used the ID normally
 * @param err_id The reference of the error label of the page.
 */

function loginck(pas1_inp, pas2_inp, nm_inp, err_id = "#err-lb"){
	var pas1_i = document.getElementById(pas1_inp);
	var pas2_i = document.getElementById(pas2_inp);
	var nm_inp = document.getElementById(nm_inp);
	
	if(pas1_i.value != pas2_i.value)
		showError(err_id, "The passwords don't match!");
	
}