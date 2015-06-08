//细分标签js
<script type="text/javascript" src="jslib/jQuery.js"></script>
$(document).ready(function() {
    $('.tabContent').hide();
	$('.tabHeader > li:eq(0)').addClass('active');
	$('.tabHeader > li').click(showHideTabs);
});
function showHideTabs()
{
	var allLi = $('.tabHeader > li').removeClass('active')
	$(this).addClass('active');
}