<?php

//see kernel/user/password.php

$ini = eZINI::instance();
$currentUser = eZUser::currentUser();
$currentUserID = $currentUser->attribute( "contentobject_id" );
$http = eZHTTPTool::instance();
$Module = $Params['Module'];
$message = false;
$userRedirectURI = '';
$PasswordNotValid = '';

//not sure if this is the best way..
$ezLDAPWrite = new ezLDAPWrite;

$userRedirectURI = $Module->actionParameter( 'UserRedirectURI' );

if ( $http->hasSessionVariable( "LastAccessesURI" ) )
     $userRedirectURI = $http->sessionVariable( "LastAccessesURI" );

$redirectionURI = $userRedirectURI;
if ( $redirectionURI == '' )
     $redirectionURI = $ini->variable( 'SiteSettings', 'DefaultPage' );

if( !isset( $Password ) )
    $Password = '';

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
    if ( $http->hasPostVariable( "Password" ) )
    {
        $Password = $http->postVariable( "Password" );
    }   

   	//TODO: Check Current Authentication against LDAP
   	
    $login = $user->attribute( "login" );
    //$type = $user->attribute( "password_hash_type" );
    //$hash = $user->attribute( "password_hash" );
    $site = $user->site();
    
	//Authenticate Against LDAP
	if ( eZOperationHandler::operationIsAvailable( 'authenticate' ) )
	{
		$operationResult = eZOperationHandler::execute( 'ezldap',
				'authenticate', array( 'login'    => $login,
	    	 	'password'  => $Password ) );
		print_r($operationResult,1);
		$message = true;
	} else {
		$authenticated = $ezLDAPWrite->authenticate( $login, $Password );
    }
	     
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

$Module->setTitle( "Authenticate" );
// Template handling
require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( "module", $Module );
$tpl->setVariable( "http", $http );
$tpl->setVariable( "userID", $UserID );
$tpl->setVariable( "userAccount", $user );
$tpl->setVariable( "password", $Password );
$tpl->setVariable( "message", $message );
$tpl->setVariable( "PasswordNotValid", $PasswordNotValid );

$Result = array();
$Result['path'] = array( array( 'text' => ezi18n( 'kernel/user', 'User' ),
                                'url' => false ),
                         array( 'text' => ezi18n( 'kernel/user', 'Authenticate' ),
                                'url' => false ) );
$Result['content'] = $tpl->fetch( "design:ezldap/authenticate.tpl" );

?>
