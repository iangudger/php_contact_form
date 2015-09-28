<?php 

	require 'vendor/autoload.php';

	function send_email ($to, $subject, $body, $message)
	{
		$from = "<donotreply@" . $_SERVER['SERVER_NAME'] . ">";
		$sendgrid = new SendGrid($_ENV["SENDGRID_USERNAME"], $_ENV["SENDGRID_PASSWORD"]);
		$email = new SendGrid\Email();

		$email
			->addTo("$to")
			->setFrom("$from")
			->setSubject("Contact Form Submission")
			->setText("$body")
		;

		try {
			$sendgrid->send($email);
			return "<p>" . $message . "</p>";
		} catch(\SendGrid\Exception $e) {
			$error = "$e->getCode()";
			foreach($e->getErrors() as $er) {
				$error .= "$er";
			}
			return "<p>" . $error . "</p>";
		}
	}

	function is_valid_email($value)
	{
		$pattern = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*";
		$pattern .= "@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/";
		if(preg_match($pattern, $value))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	function isEmpty($value)
	{
		if (!is_array($value) and trim($value) == "")
		{
			return true;
		}
		elseif (is_array($value) and empty($value))
		{
			return true;
		}
		elseif (is_array($value))
		{
			foreach ($value as $item)
			{
				if ($item == "")
				{
					return true;
				}
			}
		}
		else
		{
			return false;
		}
	}


	function make_contact_form(){
		$errors = array();
		if (isset($_POST["submit"]))
		{
			if (strlen($_POST["contact_name"]) < 2) $errors["contact_name"] = "Your name must consist of at least 2 characters";
			if (!is_valid_email($_POST["contact_email"])) $errors["contact_email"] = "Please enter a valid email address";
			if (isEmpty($_POST["reason"])) $errors["reason"] = "Please select a reason";
			if (strlen($_POST["contact_message"]) < 2) $errors["contact_message"] = "Please enter your message";
			if (isEmpty($errors))
			{
				$to = "m1samuel@ucsd.edu";
				$from = $_POST["contact_email"];
				$subject = "Contact Form Message";
				$message = "Thank you for contacting us. Your message has been successfully sent to one of our technicians. You should receive a reply within one business day.";
				$body = 'Name: ' . $_POST["contact_name"] . "\n\n";
				$body .= 'Email: ' . $_POST["contact_email"] . "\n\n";
				$body .= 'Phone: ' . $_POST["contact_phone"] . 'Ext: ' . $_POST["contact_ext"] . "\n\n";
				$body .= 'Reason: ' . $_POST["reason"] . "\n\n";
				$body .= "Message:\n" . $_POST["contact_message"] . "\n\n";
				return send_email ($to, $subject, $body, $message, $from);
			}
		}
		$form_code = '<!--Bluehost-->
		<form action="." method="post" id="contactForm">
			<h2>Contact Us</h2>
			<div>
				<label for="contact_name">Name </label><em>(required, at least 2 characters)</em><br />
				<input value="'
				. ((isset($_POST["contact_name"])) ? $_POST["contact_name"] : '') .
				'" minlength="2" class="required' . ((isset($errors["contact_name"])) ? ' form_error_field' : '') . '" size="30" name="contact_name" id="contact_name">'
				. ((isset($errors["contact_name"])) ? ' <span class="form_error_text">' . $errors["contact_name"] . '</span>' : '') . '
			</div>
			<div>
				<label for="contact_email">E-Mail </label><em>(required)</em><br />
				<input value="'
				. ((isset($_POST["contact_email"])) ? $_POST["contact_email"] : '') .
				'" class="required email' . ((isset($errors["contact_email"])) ? ' form_error_field' : '') . '" size="30" name="contact_email" id="contact_email">'
				. ((isset($errors["contact_email"])) ? ' <span class="form_error_text">' . $errors["contact_email"] . '</span>' : '') . '
			</div>
			<div>
				<label for="contact_phone">Phone </label><em>(optional)</em><br />
				<input maxlength="14" value="'
				. ((isset($_POST["contact_phone"])) ? $_POST["contact_phone"] : '') .
				'" class="phone" size="14" name="contact_phone" id="contact_phone">
				<label for="contact_ext">ext. </label>
				<input maxlength="5" value="'
				. ((isset($_POST["contact_ext"])) ? $_POST["contact_ext"] : '') .
				'" class="ext" size="5" name="contact_ext" id="contact_ext">
			</div>
			<div>
				<label for="reason">Choose Reason</label><em>(required)</em><br />
				<select name="reason" class="required reason' . ((isset($errors["reason"])) ? ' form_error_field' : '') . '">
					<option value="">
						Please select
					</option>
					<option ' . (($_POST["reason"] == 'PC Repair') ? 'selected="selected"' : '') . ' value="PC Repair">
						PC Repair
					</option>
					<option ' . (($_POST["reason"] == 'Mac Repair') ? 'selected="selected"' : '') . ' value="Mac Repair">
						Mac Repair
					</option>
					<option ' . (($_POST["reason"] == 'Data Recovery') ? 'selected="selected"' : '') . ' value="Data Recovery">
						Data Recovery
					</option>
					<option ' . (($_POST["reason"] == 'Free Consultaion') ? 'selected="selected"' : '') . ' value="Free Consultaion">
						Free Consultaion
					</option>
					<option ' . (($_POST["reason"] == 'Schedule Service') ? 'selected="selected"' : '') . ' value="Schedule Service">
						Schedule Service
					</option>
					<option ' . (($_POST["reason"] == 'General Inquiry') ? 'selected="selected"' : '') . ' value="General Inquiry">
						General Inquiry
					</option>
					<option ' . (($_POST["reason"] == 'Other') ? 'selected="selected"' : '') . ' value="Other">
						Other
					</option>
				</select>
				'
				. ((isset($errors["reason"])) ? ' <span class="form_error_text">' . $errors["reason"] . '</span>' : '') . '
			</div>
			<div>
				<label for="contact_message">Your message </label><em>(required)</em><br>
				<textarea class="required' . ((isset($errors["contact_message"])) ? ' form_error_field' : '') . '" rows="7" cols="70" name="contact_message" id="contact_message" >'. ((isset($_POST["contact_message"])) ? $_POST["contact_message"] : '') . '</textarea>'
				. ((isset($errors["contact_message"])) ? ' <span class="form_error_text">' . $errors["contact_message"] . '</span>' : '') . '
			</div>
			<div>
				<input type="submit" value="Submit" class="submit" name="submit">
			</div>
		</form>
		';
		return $form_code;
	}

 ?>
<html>
	<head>
		<title>safe-refuge-7349 Contact Form</title>
	</head>
	<body>
		<?php echo make_contact_form(); ?>
	</body>
</html>
