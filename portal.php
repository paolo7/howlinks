<?php
	echo '<center>
		<div style="width:40%;margin-top:50px;background-color:white;border-radius:15px;
					color: #444444;
					background-color: #ddd;
					background-image: linear-gradient(#E5E5E5, #CFCFCF);box-shadow: -1px 2px 5px 1px rgba(0, 0, 0, 0.7);
					padding-top:50px;
					padding-bottom:50px;">';
			echo '<img src="logo.png"/><br/>';
			echo '<div style="text-align:justify;width:65%;margin-top:20px;">
				
			</div>';
			
		echo '
	</div>';
			echo '<div style="margin-top:40px;width:80%;">
					<form id="search_form" action="search_sparql.php" method="post">
						<input name="search" style="width:30%"/>
						<input type="submit" id="envoyer" style="width:9%" value="Search"/>
					</form>
				</div>';
				
	echo' <div style="width:40%;margin-top:50px;background-color:white;border-radius:15px;
			color: #444444;
			background-color: #ddd;
			background-image: linear-gradient(#E5E5E5, #CFCFCF);box-shadow: -1px 2px 5px 1px rgba(0, 0, 0, 0.7);
			padding-top:50px;
			padding-bottom:50px;">';
		echo '<div id="search_result">
			Please enter keyworks to look for related processes
		</div>';
	echo '</div>
	
	<div style="margin-top:20px;margin-bottom:20px;font-size:0.6em;color:grey;">Paolo Pareti and Benoit Testu - developed at <a href="http://ri-www.nii.ac.jp/HowLinks/index.html">NII</a> ©</div></center>';
?>

<script type="text/javascript">
	$(document).ready(function(){	
		$('#search_form').on('submit', function() { 
			document.getElementById('search_result').innerHTML = 'Please wait ...';
			var $this = $(this);
			$.ajax({
				url: $this.attr('action'), // le nom du fichier indiqué dans le formulaire
				type: $this.attr('method'), // la méthode indiquée dans le formulaire (get ou post)
				data: $this.serialize(), // je sérialise les données (voir plus loin), ici les $_POST
				success: function(html) { // je récupère la réponse du fichier PHP
					//alert(html);
					document.getElementById('search_result').innerHTML = html;
				}
			});
			return false;
		});
	});
</script>
