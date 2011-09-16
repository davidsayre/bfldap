<?php

/*
 *  LDAP write change password
 *  This does not effect ez user record
 *  
 */

$ini = eZINI::instance();
$currentUser = eZUser::currentUser();
$currentUserID = $currentUser->attribute( "contentobject_id" );
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
if( !isset( $password ) ) $password = '';
if( !isset( $newPassword ) ) $newPassword = '';
if( !isset( $confirmPassword ) ) $confirmPassword = '';

if ( is_numeric( $Params["UserID"] ) )
    $UserID = $Params["UserID"];
else
    $UserID = $currentUserID;
    
$user = eZUser::fetch( $UserID );
if ( !$user )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
$currentUser = eZUser::currentUser();
if ( $currentUser->attribute( 'contentobject_id' ) != $user->attribute( 'contentobject_id' ) or
     !$currentUser->isLoggedIn() )
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

if ( $http->hasPostVariable( "OKButton" ) )
{
	
    if ( $http->hasPostVariable( "password" )) {$password = $http->postVariable( "password" ); }
	if ( $http->hasPostVariable( "newPassword" )) { $newPassword = $http->postVariable( "newPassword" ); }
	if ( $http->hasPostVariable( "confirmPassword" )) { $confirmPassword = $http->postVariable( "confirmPassword" ); }

    $login = $user->attribute( "login" );
    //$type = $user->attribute( "password_hash_type" );
    //$hash = $user->attribute( "password_hash" );
    $site = $user->site();

	$returnArray = $ezLDAPWrite->changepassword($login, $password, $newPassword, $confirmPassword);
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

$Module->setTitle( "Change Password" );
// Template handling
require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( "module", $Module );
$tpl->setVariable( "http", $http );
$tpl->setVariable( "login", $login );
//$tpl->setVariable( "userAccount", $user );
$tpl->setVariable( "errors", $errors );
$tpl->setVariable( "success", $success);

$Result = array();
$Result['path'] = array( array( 'text' => ezi18n( 'kernel/user', 'User' ),
                                'url' => false ),
                         array( 'text' => ezi18n( 'kernel/user', 'Change password' ),
                                'url' => false ) );
$Result['content'] = $tpl->fetch( "design:ezldap/password.tpl" );

?>
