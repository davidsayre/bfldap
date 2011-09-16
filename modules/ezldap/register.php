<?php

/*
 * LDAP register new user
 * TODO: ldap suceess creates EZ user (copy from existing kernel/user/register.php)
 * 
 */


// LDAP write change password

$ini = eZINI::instance();
$currentUser = eZUser::currentUser(); //TBD
$currentUserID = $currentUser->attribute( "contentobject_id" ); //TBD
$http = eZHTTPTool::instance();
$Module = $Params['Module'];
$userRedirectURI = '';
$errors = Array();
$success = 0;

//not sure if this is the best way..
$ezLDAPWrite = new ezLDAPWrite;

$userRedirectURI = $Module->actionParameter( 'UserRedirectURI' );

if ( $http->hasSessionVariable( "LastAccessesURI" ) )
     $userRedirectURI = $http->sessionVariable( "LastAccessesURI" );

$redirectionURI = $userRedirectURI;
if ( $redirectionURI == '' )
     $redirectionURI = $ini->variable( 'SiteSettings', 'DefaultPage' );

if( !isset( $login ) ) $login = '';
if( !isset( $email ) ) $email = '';
if( !isset( $password ) ) $password = '';
if( !isset( $confirmPassword ) ) $confirmPassword = '';
if( !isset( $firstname ) ) $firstname = '';
if( !isset( $lastname ) ) $lastname = '';

if ( $http->hasPostVariable( "OKButton" ) )
{

	//May opt for email instead of login
	if ( $http->hasPostVariable( "login" )) { $login = $http->postVariable( "login" ); }
	if ( $http->hasPostVariable( "email" )) { $email = $http->postVariable( "email" ); }
    if ( $http->hasPostVariable( "password" )) { $password = $http->postVariable( "password" ); }
	if ( $http->hasPostVariable( "confirmPassword" )) { $confirmPassword = $http->postVariable( "confirmPassword" ); }
	if ( $http->hasPostVariable( "firstname" )) { $firstname = $http->postVariable( "firstname" ); }
	if ( $http->hasPostVariable( "lastname" )) { $lastname = $http->postVariable( "lastname" ); }

	$returnArray = $ezLDAPWrite->createUser($login, $email, $password, $confirmPassword, $firstname, $lastname);
	$errors = $returnArray['errors'];
	$success = $returnArray['success'];
}

if ( $http->hasPostVariable( "CancelButton" ) )
{
    if ( $http->hasPostVariable( "RedirectOnCancel" ) )
    {
        return $Module->redirectTo( $http->postVariable( "RedirectOnCancel" ) );
    }
    eZRedirectManager::redirectTo( $Module, $redirectionURI );
    return;
}


$Module->setTitle( "Register" );
// Template handling
require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( "module", $Module );
$tpl->setVariable( "http", $http );
$tpl->setVariable( "login", $login );
//$tpl->setVariable( "userAccount", $user );
//$tpl->setVariable( "password", $password );
//$tpl->setVariable( "confirmPassword", $confirmPassword );
$tpl->setVariable( "email", $email );
$tpl->setVariable( "firstname", $firstname );
$tpl->setVariable( "lastname", $lastname );
$tpl->setVariable( "errors", $errors);
$tpl->setVariable( "success", $success);

$Result = array();
$Result['path'] = array( array( 'text' => ezi18n( 'kernel/user', 'User' ),
                                'url' => false ),
                         array( 'text' => ezi18n( 'kernel/user', 'Register' ),
                                'url' => false ) );
$Result['content'] = $tpl->fetch( "design:ezldap/register.tpl" );

?>
