<?
error_reporting(-1);
ini_set('display_errors', 'On');

session_name('fauxdb');
session_start();

$body_class = 'admin';
require_once('header.php');

if (isset($_SESSION['fd_status'])) { ?>
	<div class="row">
		<div class="small-12 medium-8 medium-offset-2 nav">
				<a href="/fauxdb/new.php" class="add primary button">New DB</a>
				<a href="/fauxdb/logout.php" class="logout neg button">Logout</a>
		</div>
	</div>
	<div class="row">
		<p class="small-12 medium-10 medium-offset-1 text-center"><em>Select any database to edit</em></p>
		<a style="display:none;" class="ajax" data-ajax="<?=md5($_SESSION['user']['projects_url'])?>"></a>
		<table class="small-12 medium-10 medium-offset-1 dbs">
			<thead class="headers">
				<tr>
					<th>Name</th>
					<th>Data link</th>
					<th>Embed code</th>
					<th>Site link</th>
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
				<?
					require_once('class_fauxdb.php');
					$fd = new Fauxdb;
					$fd->get_dbs($_SESSION['user']);
				?>
			</tbody>
		</table>
	</div>
<? }
else { ?>
	<div class="row">
		<div class="small-12 medium-6 medium-centered columns login">
			<form data-abide action="fauxly.php" method="POST">
				<input type="hidden" name="purpose" value="login" />
				<div class="row">
					<div class="small-4 columns">
						<label class="right inline" for="fd_site">Site:</label>
					</div>
					<div class="small-8 columns">
						<input required type="text" placeholder="Your news organization" name="fd_site" />
					</div>
				</div>
				<div class="row">
					<div class="small-4 columns">
						<label class="right inline" for="fd_pass">Password:</label>
					</div>
					<div class="small-8 columns">
						<input required type="password" placeholder="Password" name="fd_pass" />
					</div>
				</div>
				<div class="row">
					<button class="button small">Login</button>
				</div>
			</form>
		</div>
	</div>
<? } 

require_once('footer.php');

?>