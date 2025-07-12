$(document).ready(function() {
 setTimeout(() => {
    $('.site-wrapper').fadeIn()
}, 1000);
    (function() {
        let n = 12;
while(n > 0) {
	$(".animation-container").append($(".animation-container").children().first().clone());
	n -= 1;
}
    })(); 
});