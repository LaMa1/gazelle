<?

authorize();

/*
 $DonateID = (int) $_REQUEST['donateid'];


$DB->query("SELECT public FROM bitcoin_donations 
                         WHERE state='unused' AND ID='$DonateID' AND userid='$UserID'");
if ($DB->record_count() == 0) error("Could not find address with ID='$DonateID', please contact an admin.");
list($public) = $DB->next_record();
 */
/*
$DB->query("SELECT ID, public, userID FROM bitcoin_addresses ORDER BY ID LIMIT 1");
if ($DB->record_count() < 1) {
    error("Failed to get an address, if this error persists we probably need to add some addresses, please contact an admin"); 
} else {
    // got an unused address 
    list($addID, $public, $staffID) = $DB->next_record();

    $DB->query("DELETE FROM bitcoin_addresses WHERE ID=$addID");
    if ($DB->affected_rows()==1) { // delete succeeded - we can issue this address
        
    } else {
        // maybe another user grabbed it at the same time? try again...
        error("Address was already used! - please reload the page, if this error persists please contact an admin");
    }
}
*//*
$time = sqltime();
$DB->query("INSERT INTO bitcoin_donations ( state, public, time, userID, staffID, received, amount_euro, comment)
                                   VALUES ( 'submitted', '$public', '$time', '$UserID', '$LoggedUser[ID]')");
        
        $ID = $DB->inserted_id();
        $user_addresses = array( array($ID, $public, $time) );
        
        
$DB->query("INSERT INTO bitcoin_donations   ( state='submitted',
                                                     bitcoin_rate='$eur_rate',
                                                     received='$time',
                                                     amount_bitcoin='$balance',
                                                     amount_euro='$amount',
                                                     comment='$comment'
                                                 WHERE ID='$DonateID' ");
$DB->query("INSERT INTO bitcoin_donations ( staffID, state, public, time, userID, received, bitcoin_rate, amount_bitcoin, `amount_euro`, `comment`) 
        VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6],[value-7],[value-8],[value-9],[value-10],[value-11])");



$DB->query("INSERT INTO bitcoin_donations SET state='submitted',
                                                     bitcoin_rate='$eur_rate',
                                                     received='$time',
                                                     amount_bitcoin='$balance',
                                                     amount_euro='$amount',
                                                     comment='$comment'
                                                 WHERE ID='$DonateID' ");
*/

if (!check_perms('users_give_donor'))  error(403);

if (!is_numeric($_REQUEST['userid'])) error(0); //  || !is_numeric($_REQUEST['donateid'])
if (!is_numeric($_REQUEST['amount'])) error(0);

$UserID = (int) $_REQUEST['userid'];
$amount = round($_REQUEST['amount'], 2);

$public = '';
for($i==0;$i<10;$i++){
    $try = 'DO_NOT_USE_'. make_secret(30);
    // strictly speaking we should check this 50 char random string is unique... 
    $DB->query("SELECT ID FROM bitcoin_donations WHERE public='$try'");
    if($DB->record_count()<1){
        $public = $try;
        break;
    }
}
// either there is a bug or the laws of probability have stopped working if its not unique after 10 tries
if ($public=='') error("Could not create a unique dummy address.... something is fubar! (harass a coder immediately)");

 
$time = sqltime();
$comment = "(manual payment) donated for ";

if ($_REQUEST['donategb']) {
    //$deduct_bytes = floor($amount) * DEDUCT_GB_PER_EURO * 1024 * 1024 * 1024; // 1 euro per gb
    $deduct_bytes = get_donate_deduction($amount);
    $comment .= "ratio: - " . get_size($deduct_bytes);
} else {
    $comment .= "love";
   
    $DB->query("SELECT ID, Title, Badge, Rank, Image, Description FROM badges WHERE Type='Donor' AND Cost<='" . (int)round($amount) . "' ORDER BY Cost DESC LIMIT 1");
    if ($DB->record_count() > 0) {
        list($badgeid, $title, $badge, $rank, $image, $description) = $DB->next_record();
        $comment .= " (received badge: $title)";
    }
}

$comment = db_string($comment);
/*
$DB->query("UPDATE bitcoin_donations SET state='submitted',
                                                     bitcoin_rate='$eur_rate',
                                                     received='$time',
                                                     amount_bitcoin='$balance',
                                                     amount_euro='$amount',
                                                     comment='$comment'
                                                 WHERE ID='$DonateID' ");
*/

$DB->query("INSERT INTO bitcoin_donations ( state, public, time, userID, staffID, received, amount_euro, comment)
                                   VALUES ( 'submitted', '$public', '$time', '$UserID', '$LoggedUser[ID]',
                                            '$time', '$amount', '$comment')");
        
        $ID = $DB->inserted_id(); 



if ($_REQUEST['donategb']) {

    $DB->query("SELECT Downloaded FROM users_main WHERE ID='$UserID'");
    list($downloaded_bytes) = $DB->next_record();

    $Summary = sqltime() . ' - ' . "[url=/donate.php?action=my_donations&amp;userid=$UserID]Donated: &euro;$amount.[/url] Download removed: " . get_size($deduct_bytes);
    if ($downloaded_bytes < $deduct_bytes)
        $Summary .= " | NOTE: Could only remove " . get_size($downloaded_bytes);
    $summary .= ", by donation system";

    $DB->query("UPDATE users_info as i JOIN users_main as m ON i.UserID=m.ID
                               SET i.AdminComment=CONCAT_WS( '\n', '".db_string($Summary)."', i.AdminComment),
                                   m.Downloaded=(m.Downloaded-'$deduct_bytes')
                             WHERE m.ID='$UserID'");

    $Summary = get_size($deduct_bytes) . " has been deducted from your download.";
    if ($downloaded_bytes < $deduct_bytes)
        $Summary .= " | NOTE: Could only remove " . get_size($downloaded_bytes);

    send_pm($UserID, 0, db_string("Thank-you for your donation"), db_string("[br]We have received your donation of &euro;$amount [br][br]:thankyou:[br][br]$Summary"));
    
} else {

    send_pm($UserID, 0, db_string("Thank-you for your donation"), db_string("[br]We have received your donation of &euro;$amount [br][br]:thankyou:[br][br]It's thanks to members like you that this site can carry on :gjob:"));

    $Summary = "[url=/donate.php?action=my_donations&amp;userid=$UserID]Donated: &euro;$amount.[/url]";

    if ($badgeid) {
        $DB->query("SELECT BadgeID FROM users_badges 
                                 WHERE UserID='$UserID' AND BadgeID='$badgeid' ");
        if ($DB->record_count() == 0) {
            $description = db_string($description);
            $DB->query("INSERT INTO users_badges (UserID, BadgeID, Description) VALUES 
                                                              ($UserID, $badgeid, '$description')");
            // remove lower ranked donor badges
            $DB->query("DELETE ub FROM users_badges AS ub
                                               JOIN badges AS b ON ub.BadgeID=b.ID
                                                   AND b.Badge='$badge' AND b.Rank<$rank
                                                 WHERE ub.UserID='$UserID'");

            $Cache->delete_value('user_badges_ids_' . $UserID);
            $Cache->delete_value('user_badges_' . $UserID);
            $Cache->delete_value('user_badges_' . $UserID . '_limit');
        }
        $Summary .= " Badge added: $title, by donation system";

        send_pm($UserID, 0, db_string("Congratulations you have been awarded the $title"), db_string("[center][br][br][img]http://" . SITE_URL . '/' . STATIC_SERVER . "common/badges/{$image}[/img][br][br][size=5][color=white][bg=#0261a3][br]{$description}[br][br][/bg][/color][/size][/center]"));
    }
    $Summary = db_string(sqltime() . " - $Summary");
    //write_user_log($UserID, $Summary);
    $DB->query("UPDATE users_info 
                               SET Donor='1', AdminComment=CONCAT_WS( '\n', '$Summary', AdminComment)
                             WHERE UserID='$UserID'");
}

if (isset($_REQUEST['convid']) && is_number($_REQUEST['convid'])){
	$ConvID = (int)$_REQUEST['convid'];
    $DB->query("UPDATE staff_pm_conversations SET Status='Resolved', ResolverID='$LoggedUser[ID]' WHERE ID=$ConvID");
	$Cache->delete_value('staff_pm_new_'.$LoggedUser['ID']);
}


$Cache->delete_value('user_info_' . $UserID);
$Cache->delete_value('user_info_heavy_' . $UserID);
$Cache->delete_value('user_stats_'.$UserID);

header("Location: donate.php?action=my_donations&userid=$UserID");
?>
