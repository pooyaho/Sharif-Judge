<?php
/**
 * Sharif Judge online judge
 * @file footer.php
 * @author Mohammad Javad Naderi <mjnaderi@gmail.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<script>
	$(document).ready(function(){
		$('body').nanoScroller();
		$(window).resize(function(){
			$('body').nanoScroller();
		});
		$('#main_content').resize(function(){
			$('body').nanoScroller();
		});
	});
</script>
</div>
</body>
</html>