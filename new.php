<?
error_reporting(-1);
ini_set('display_errors', 'On');

session_name('fauxdb');
session_start();

$body_class = 'new';
require_once('header.php'); 
?>

<div class="row">
	<div class="small-12 medium-10 medium-offset-1">
		<h2 class="text-center">Add a new dataset</h2>
		<p><strong>Step 1</strong>: Put your data into Google Sheets. If at all possible, please actually use your organization's data account as the owner, but if you must just make sure your sheet is at least shared with the account.</p>
		<p><strong>Step 2</strong>:  Enter the Goole Doc URL below. The ID is found after /spreadsheets/ and then a letter. In the URL below, it's bolded: <br /><span class="light">https://docs.google.com/spreadsheets/d/<strong>19Rlp6U8qe4U0Z0KxJMDWu7_ZZvaSoG0hxAfU3yUX950</strong>/edit#gid=1124612860</span></p>
		<input type="text" name="gdoc_id" required placeholder="Google Sheet ID" />
		<input type="hidden" name="ajax" value="<?=$_SESSION['user']['gscript_url'] ?>" />
		<div class="text-center">
			<button data-purpose="step1" class="primary">Next step</button>
		</div>
	</div>
</div>
<div class="row">
	<div class="step2 small-12 medium-10 medium-offset-1 hide columns">
			<h3>Select columns</h3>
			<p>Becuase we're living in a responsive world, we need to select which fields should be shown in the regular view and which should only be shown on detail pages. Also, there may be some fields you don't want to show at all (but want to keep for internal purposes) — leave those in the "Hidden" column. Fields that are in the default set should go in the "Default column"; fields for the details page should be in the "Details" column.</p>
			<div class="small-6 medium-4 columns drop">
				<h4>Hidden</h4>
				<ul class="fields" data-purpose="hidden">
				</ul>
			</div>
			<div class="small-6 small-only columns">
				
			</div>
			<div class="small-6 medium-4 columns drop">
				<h4>Shown</h4>
				<ul class="fields" data-purpose="shown">
				</ul>
			</div>
			<div class="small-6 medium-4 columns drop">
				<h4>Details</h4>
				<ul class="fields" data-purpose="details">
				</ul>
			</div>
			<div class="text-center small-12 columns">
				<button data-purpose="step2" class="primary">Next step</button>
			</div>
	</div>
</div>
<div class="row">
	<div class="step3 small-12 medium-10 medium-offset-1 hide columns">
			<h3>Admin stuff</h3>
			<label>Name your sheet (this is going to be the header):</label>
			<input type="text" name="name" required placeholder="e.g., '2013-14 York/Adams County Teacher Salaries'" />
			<label>When users go to search, you should give them a hint (what fields would be best to search). Examples might include "Enter a teacher's name, school or district to search" for the teacher's salary database, or "Enter an address to search" for the deed sales database.</label>
			<input type="text" name="search_text" required placeholder="Enter search text" />
			<div class="text-center small-12 columns">
				<button data-purpose="submit" class="primary">Create DB</button>
			</div>
		</form>
	</div>
</div>

<? require_once('footer.php'); ?>