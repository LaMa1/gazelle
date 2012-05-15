<?
enforce_login();
// Get user level
$DB->query("
	SELECT
		i.SupportFor,
		p.DisplayStaff
	FROM users_info as i
	JOIN users_main as m ON m.ID = i.UserID
	JOIN permissions as p ON p.ID = m.PermissionID
	WHERE i.UserID = ".$LoggedUser['ID']
);
list($SupportFor, $DisplayStaff) = $DB->next_record();

if (!($SupportFor != '' || $DisplayStaff == '1')) {
	// Logged in user is not FLS or Staff
	error(403);
}

$Message = isset($_POST['message'])? trim($_POST['message']):false;
$Name = isset($_POST['name'])? trim($_POST['name']):false;

if ($Message && $Name && ($Message != "") && ($Name != "")) {
      $Message = db_string(display_str($Message));
      $Name = db_string(display_str($Name));
	$ID = (int)$_POST['id'];
	if (is_numeric($ID)) {
		if ($ID == 0) {
			// Create new response
			$DB->query("INSERT INTO staff_pm_responses (Message, Name) VALUES ('$Message', '$Name')");
                  // if submit is set then this is not an ajax response - reload page and pass vars for message & return convid
                  if (isset($_POST['submit']) && $_POST['submit'] == 'Save'){
                      $InsertedID = $DB->inserted_id();
                      $ConvID = (int)$_POST['convid'];
                      header("Location: staffpm.php?action=responses&added=$InsertedID".($ConvID>0?"&convid=$ConvID":'')."#old_responses");
                  } else
                      echo 1;
                  
		} else {
			$DB->query("SELECT * FROM staff_pm_responses WHERE ID=$ID");
			if ($DB->record_count() != 0) {
				// Edit response
				$DB->query("UPDATE staff_pm_responses SET Message='$Message', Name='$Name' WHERE ID=$ID");
				echo '2';
			} else {
				// Create new response
				$DB->query("INSERT INTO staff_pm_responses (Message, Name) VALUES ('$Message', '$Name')");
				// if submit is set then this is not an ajax response - reload page and pass vars for message & return convid
                        if (isset($_POST['submit']) && $_POST['submit'] == 'Save'){
                              $InsertedID = $DB->inserted_id();
                              $ConvID = (int)$_POST['convid'];
                              header("Location: staffpm.php?action=responses&added=$InsertedID".($ConvID>0?"&convid=$ConvID":'')."#old_responses");
                        } else
                              echo 1;
			}
		}
	} else {
		// No id
		if (isset($_POST['submit']) && $_POST['submit'] == 'Save'){
                  $ConvID = (int)$_POST['convid'];
                  header("Location: staffpm.php?action=responses&added=-2".($ConvID>0?"&convid=$ConvID":''));
            } else
                  echo -2;
	}
	
} else {
	// No message/name
	if (isset($_POST['submit']) && $_POST['submit'] == 'Save'){
            $ConvID = (int)$_POST['convid'];
            header("Location: staffpm.php?action=responses&added=-1".($ConvID>0?"&convid=$ConvID":''));
      } else
            echo -1;
}
?>