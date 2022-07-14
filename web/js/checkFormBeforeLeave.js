var isSubmitted = false;
var isFormEdited = false;

window.onsubmit = function(e){
    isSubmitted = true;    
    console.log("load fullpage loader");
    $("#fullpage-loader").show();    
};

window.onChange = function(e){
    isFormEdited = true;
};

window.onbeforeunload = function (e){
    if(!isSubmitted){
        //if(isFormEdited){
            return 'Änderungen werden nicht gespeichert!';
        //}
    } 
};