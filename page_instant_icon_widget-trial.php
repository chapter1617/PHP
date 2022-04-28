<?php
/*
Template Name: _instant_icon_widget-trial
*/
?>
<?php get_header(); ?>

<div class="container">
<div class="post-wrapper">
	<div class="row subpage-title">
		<h1><?php _e('Instant Icons, Your Blog Helper', 'ipin'); ?></h1>
		<br />
	</div>

		<div class="stampinup">
		<h2>Give our Instant Icons tool a test spin</h2>
		<p><font color="red">NOTE: This TRIAL version will NOT produce the HTML for your BLOG or a PDF version to print.  You must be a member for full access.</font></p>
		<form method="post">
			<div class="formarea">
				<div class="fieldbox">
					<label for="affiliateid">Enter your demo number</label>
					<input type="text" name="affiliateid" id="affiliateid" onchange="updateDemoID()">
				</div>
				<div class="fieldbox">
					<label for="affiliatecountry">Select your country</label>
					<select name="affiliatecountry" id="affiliatecountry" onchange="updateCountry()">
						<option value="US" selected>United States</option>
						<option value="CA">Canada</option>
						<option value="UK">United Kingdom</option>
						<option value="AU">Australia</option>
						<option value="NZ">New Zealand</option>
						<option value="FR">France</option>
					</select>
				</div>
				<div class="fieldbox">
					<label for="productnumbers">Enter Stampin' Up product numbers</label>
					<textarea name="productnumbers" id="productnumbers"></textarea>
					<em>Separate products with a comma or place one per line.</em>
					<p><font color="red">NOTE: For A4 Cardstock, please read <a href="http://forum.sudsol.org/Instant-Icons-Invalid-number-tp30283p30296.html">this article</a> in the SUDSOL Discussion Forum.</font></p>
				</div>
					<div class="fieldbox c">
					<button id="addproducts" onclick="return checkProductID();">Add Products</button><button id="clearproducts" onclick="return clearProducts();">Clear</button>
					<br><em>NOTE: There may be a momentary delay after selecting Add Products for Preview page to generate.</em>
				</div>
			</div>
		</form>

		<div id="stampinup_errors_box"></div>

		<div id="stampinup_results">
			<h3>Preview</h3>
				<div id="stampinuppreview"></div>




<!-- http://sudsol.org/sudsol.net/get_pdf.php -->
			<form enctype="multipart/form-data" method="POST" action="http://sudsol.org/member/converter3.php">
				<h3>Become a member to copy this code to your BLOG</h3>
					<textarea disabled id="widgetcode" name="widgetcode"></textarea>
			
				<h3>Download it as PDF</h3>
				<div class="fieldbox">
					<label for="name">Project Title</label>
					<input type="text" name="name" id="name">
				</div>
				<div class="fieldbox">
					<label for="info">Enter additional info</label>
					<textarea name="info" id="info"></textarea>
				</div>
				
				<div class="fieldbox">
					<label for="info">Attach an Image</label>
					<input name="logo" type="file" accept="image/*"/>
				</div>
				
				<button type="button" disabled value="Download PDF">Download PDF - Signup for Access</button>
			</form>
				



		</div>	
		</div>

</div>
</div>



<?php get_footer(); ?>