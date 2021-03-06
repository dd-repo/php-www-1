<?php

if( !defined('PROPER_START') )
{
	header("HTTP/1.0 403 Forbidden");
	exit;
}

// Form secure
if( $security->hasAccess('/panel') )
	$user = security::get('USER');
$time = time();
$random = md5(uniqid($time, true));
$_SESSION[$random] = 1;

/*
// Get issues in progress
require_once 'on/status/vendor/autoload.php';

$client = new Redmine\Client('https://projets.olympe.in', $GLOBALS['CONFIG']['REDMINE_TOKEN']);
$issues = $client->api('issue')->all(array('project_id' => 'maintenances'));
$issues = $issues['issues'];
*/

/* 
require_once 'on/status/vendor/autoload.php';

// projets.olympe.in status
$url = 'https://projets.olympe.in';
libxml_use_internal_errors(true);
$doc = new DOMDocument();
$doc->loadHTMLFile($url);
libxml_clear_errors();
$title = $doc->getElementsByTagName('title')->item(0);
$chaine = $title->nodeValue;
$dispo = "0";
*/

$content = "
		<div class=\"head-light\">
			<div class=\"container\">
";

/*
$content .= "
			<h2 class=\"dark\">{$lang['issues']}</h2>
				
				<table>
					<tr>
						<th style=\"color: #a4a4a4; text-align: center; width: 40px;\">#</th>
						<th style=\"color: #a4a4a4;\">{$lang['type']}</th>
						<th style=\"color: #a4a4a4;\">{$lang['resume']}</th>
						<th style=\"color: #a4a4a4;\">{$lang['priority']}</th>
						<th style=\"color: #a4a4a4;\">{$lang['date']}</th>
						<th style=\"color: #a4a4a4;\">{$lang['status']}</th>
						<th style=\"color: #a4a4a4;\">{$lang['updated']}</th>
					</tr>
";

if( count($issues) > 0 )
{
	foreach( $issues as $i )
	{
		$content .= "
				<tr>
					<td style=\"text-align: center; width: 40px;\"><a href=\"https://projets.olympe.in/issues/{$i['id']}\"><img src=\"/{$GLOBALS['CONFIG']['SITE']}/images/icons/issue.png\" /></a></td>
					<td>".$lang['tracker_' . $i['tracker']['id']]."</td>
					<td><a href=\"https://projets.olympe.in/issues/{$i['id']}\">{$i['subject']}</a></td>
					<td>".$lang['priority_' . $i['priority']['id']]."</td>
					<td>".date($lang['dateformatsimple'], strtotime($i['start_date']))."</td>
					<td>".$lang['status2_' . $i['status']['id']]."</td>
					<td>".date($lang['dateformat'], strtotime($i['updated_on']))."</td>
				</tr>
		";
	}
}
else
{
	$content .= "
				<tr>
					<td colspan=\"7\" style=\"text-align: center; width: 40px;\">
					".$lang['intervention']."
					</td>
				</tr>
	";

}

$content .= "</table>";
*/


	$content .= "
			<h1 class=\"dark\">{$lang['title']}</h1>
			</div>
		</div>
		<div class=\"content\">
				<div class=\"left\">
					<h4>{$lang['send']}</h4>
					<p>{$lang['send_text']}</p>
					<br />
					<form action=\"/about/contact_action\" method=\"post\" id=\"contact\">
						<fieldset>
							<input class=\"auto\" style=\"width: 300px;\" type=\"text\" name=\"name\" required placeholder=\"{$lang['name']}\" />
						</fieldset>
		";
		
if(!$user) {
	$content .= "
						<fieldset>
							<input class=\"auto\" style=\"width: 300px;\" type=\"text\" name=\"account\" placeholder=\"{$lang['account']}\" />
						</fieldset>
	";
}

$content .= "		
						<fieldset>
							<input class=\"auto\" style=\"width: 300px;\" type=\"text\" name=\"email\" required id=\"email\" placeholder=\"{$lang['email']}\" />
						</fieldset>
						<fieldset>
							<input class=\"auto\" style=\"width: 300px;\" type=\"text\" name=\"subject\" required placeholder=\"{$lang['subject']}\" />
						</fieldset>
						<fieldset>
							<textarea class=\"auto\" style=\"width: 300px;\" rows=\"10\" name=\"message\" required placeholder=\"{$lang['message']}\"></textarea>
						</fieldset>
						<fieldset>
							<input class=\"auto\" style=\"width: 300px; display:none;\" type=\"text\" name=\"phone\" placeholder=\"\" />
						</fieldset>
						<fieldset>
							<input type=\"submit\" value=\"{$lang['send_now']}\" />
						</fieldset>
						<input style=\"display:none;\" type=\"text\" id=\"ck1\" name=\"ck1\" value=\"{$time}\" />
						<input style=\"display:none;\" type=\"text\" id=\"ck2\" name=\"ck2\" value=\"\" />
						<input style=\"display:none;\" type=\"text\" id=\"email2\" name=\"email2\" value=\"\" />
						<input type=\"hidden\" value=\"{$random}\" name=\"random\" />
					</form>
				</div>
				<div class=\"right border\">
					<a style=\"width: 250px; height: 22px; margin-bottom: 20px;\" onclick=\"$('#report').dialog('open');\" href=\"#\" class=\"button classic\">
						<img src=\"/on/images/warning.png\" style=\"float: left; height:98%;\" alt=\"\" />
						<span style=\"display: block; padding-top: 3px;\">{$lang['report']}</span>
					</a>
				
					<h4>{$lang['infos']}</h4>
					<p>Paris, France</p>
					<p><a href=\"#\">contact<img src=\"/on/images/thin-arobase-2.png\">olympe.in</a></p>
					<p>#olympe@irc.freenode.net</p>
					<br />
					<h4>{$lang['meet']}</h4>
					<p>{$lang['meet_text']}</p>
					<div style=\"width: 330px; height: 330px;\">
						<iframe width=\"330\" height=\"330\" frameborder=\"0\" scrolling=\"no\" marginheight=\"0\" marginwidth=\"0\" src=\"https://maps.google.com/maps?f=q&source=s_q&hl=en&geocode=&q=19%2Brue%2BPierre%2BLescot%C3%A8re%2C%2BParis&ie=UTF8&z=5&t=m&iwloc=near&output=embed\"></iframe>
					</div>
				</div>
				<div class=\"clear\"></div>
				<br /><br />
			</div>
			
			<div id=\"report\" class=\"floatingdialog\">
				<h3 class=\"center\">{$lang['report']}</h3>
				<p style=\"text-align: justify; font-size:13px; line-height:20px;\">{$lang['report_text']}</p>
			</div>
			<div id=\"email_check\" class=\"floatingdialog center\">
				<img src=\"/on/images/icons/notifications/error.png\">
				<br>
				<p>{$lang['email_wrong']}</p>
			</div>
			<script>
				newFlexibleDialog('report', 800);
				newFlexibleDialog('email_check', 250);
				
				$('form#contact').submit(function() {
					$('#ck2').val($('#ck1').val());
					
					var input = $('#email', this);
					var re = new RegExp('^[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*@[a-z0-9]+([_|\.|-]{1}[a-z0-9]+)*[\.]{1}[a-z]{2,6}$', 'i');
					var is_email = re.test(input.val());
					if(!is_email) { 
						$('#email_check').dialog('open');
						return false; 
					}
				});
			</script>
";

if( isset($_GET['report']) )
{
	$content .= "<script type=\"text/javascript\">
					$(document).ready(function() {
						$(\"#report\").dialog(\"open\");
						$(\".ui-dialog-titlebar\").hide();
					});
				</script>
	";
}

/* ========================== OUTPUT PAGE ========================== */
$template->output($content);

?>
