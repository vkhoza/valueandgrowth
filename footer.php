<tr>
<td class='bodycolumn' style='vertical-align:top;'>
<table style='height:100%;background:rgb(240,240,240);border-spacing:0px;width:100%;min-width:60%;'>
<tr>
	<td style='height:100%;padding:0px;width:10%;vertical-align:top;'>
		<div id='menuOnLeft' class='noshow' style='display:none;height:100%;width:201px;border-right:solid 1px rgb(220,220,220);background:white;'>
			<div id='leftmenumove' style='width:200px;height:100%;'>
			<div id='leftDiv' class='leftmenu noshow' style='height:220px;width:100%;display:none;border-bottom:solid 1px rgb(240, 240, 240);'></div>
			<div id='summaryDiv' class='leftmenu noshow' style='height:150px;width:100%;display:none;'></div>
			<div id='subordinatesDiv' class='leftmenu noshow' style='border-top:solid 1px rgb(240, 240, 240);height:180px;
			font-style:italic;width:100%;display:none;border-top:rgb(240, 240, 240);margin-top:5px;'>
				<div id='subordinates' class='box one-third' style='background:white;'>
				<div style='font-style:normal;font-size:20px;text-indent:7px;'>Team Members</div>
				<div class='box-inner' style='width:99%;border:hidden;'>
					<div class='box-content' style='overflow:auto;height:135px;font-size:11px;'></div>
					<div class='box-controls' style='border:hidden;'></div>
				</div>
				</div>
			</div>
			</div>
		</div>
	</td>
	<td style='width:90%;vertical-align:top;'>
		<table style='height:100%;width:100%;'>
		<tr>
			<td id='rightDiv' style='vertical-align:top;'></td>
		</tr>
		<tr style='height:40px;'>
			<td style='width:100%;vertical-align:top;'>
				<div id='footer' <?php echo ( ! isset($user) ) ? "style='margin-left:250px;'" : "";?> >
				Colbrad. Copyright &copy 2014. All rights reserved. v1.0 
				</div>
			</td>
		</tr>
		</table>
	</td>
</tr>
</table>
</td>
</tr>
</table>

<div id='Templates'>

<?php 
require('templates.php');
require('admin-templates.php');
require('moretemplates.php');
?>

</div>

<div class="greyedoutbackdrop"></div>

</body>
</html>