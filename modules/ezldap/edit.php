<?php

/*
 * LDAP write user attributes
 * use attribute mapping
 */


$currentUser = eZUser::currentUser();
$http = eZHTTPTool::instance();
$Module = $Params['Module'];

if ( isset( $Params["UserID"] ) && is_numeric( $Params["UserID"] ) )
    $UserID = $Params["UserID"];
else if ( !$currentUser->isAnonymous() )
    $UserID = $currentUser->attribute( "contentobject_id" );

if ( $Module->isCurrentAction( "ChangePassword" ) )
{
    $Module->redirectTo( $Module->functionURI( "password" ) . "/" . $UserID  );
    return;
}

if ( $Module->isCurrentAction( "ChangeSetting" ) )
{
    $Module->redirectTo( $Module->functionURI( "setting" ) . "/" . $UserID );
    return;
}

if ( $Module->isCurrentAction( "Cancel" ) )
{
    $Module->redirectTo( '/content/view/sitemap/5/' );
    return;
}

if ( $Module->isCurrentAction( "Edit" ) )
{
    $selectedVersion = $http->hasPostVariable( 'SelectedVersion' ) ? $http->postVariable( 'SelectedVersion' ) : 'f';
    $editLanguage = $http->hasPostVariable( 'ContentObjectLanguageCode' ) ? $http->postVariable( 'ContentObjectLanguageCode' ) : '';
    $Module->redirectTo( '/content/edit/' . $UserID . '/' . $selectedVersion . '/' . $editLanguage );
    return;
}

$userAccount = eZUser::fetch( $UserID );
if ( !$userAccount )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
$userObject = $userAccount->attribute( 'contentobject' );
if ( !$userObject )
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
if ( !$userObject->canEdit( ) )
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );

require_once( "kernel/common/template.php" );
$tpl = templateInit();
$tpl->setVariable( "module", $Module );
$tpl->setVariable( "http", $http );
$tpl->setVariable( "userID", $UserID );
$tpl->setVariable( "userAccount", $userAccount );

$Result = array();
$Result['content'] = $tpl->fetch( "design:user/edit.tpl" );
$Result['path'] = array( array( 'text' =>  ezi18n( 'kernel/user', 'User profile' ),
                                'url' => false ) );


?>
