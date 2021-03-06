<?php
//This will be the loading page for wany based on logins.
//When a user submits their username and password, the form will be submitted to this page.
//Then this page will check the database to see if a user under that account and password exists.
//If the account does exist then we move to the home page, if not then we return to the splash page


/*%******************************************************************************************%*/
// SETUP

  error_reporting(-1);
	header("Content-type: text/plain; charset=utf-8");
	require_once './sdk.class.php';


/*%******************************************************************************************%*/


$dynamodb = new AmazonDynamoDB();
$tablename = 'userlist';
$name = $_POST["user"];
$value = $_POST["pword"];
//from here we need to compare to the dynamo table of accounts and passwords
$scanresponse = $dynamodb->scan(array(
    'TableName'    => $tablename,
	'ScanFilter'      => array(
        'user' => array(
            'ComparisonOperator' => AmazonDynamoDB::CONDITION_EQUAL,
            'AttributeValueList' => array(
                array( AmazonDynamoDB::TYPE_STRING => $name )
            )
        ),
    )
));

if ($scanresponse->isOK())
{
	if($scanresponse->body->Items==0)
	{
			header('Location: index.php');
	}
	$temppass = (string) $scanresponse->body->Items->password->{AmazonDynamoDB::TYPE_STRING};
	$verifiedStatus = (string) $scanresponse->body->Items->verified->{AmazonDynamoDB::TYPE_STRING};
	$adminStatus = (string) $scanresponse->body->Items->admin->{AmazonDynamoDB::TYPE_STRING};
	if ($temppass == $value && $verifiedStatus == "1")
	{
		session_start();
		$_SESSION['user'] = $name;
		$_SESSION['admin'] = $adminStatus;
		header('Location: homepage.php');
	}
	else
	{
		header('Location: index.php');
	}
}

	


?>
