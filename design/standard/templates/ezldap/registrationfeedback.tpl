{set-block scope=root variable=content_type}text/html{/set-block}
<html><body>
{set-block scope=root variable=subject}{'New user registered at %sitename'|i18n('design/standard/user/register',,hash('%sitename',ezini('SiteSettings','SiteName')))}{/set-block}
{def $userlink = concat("http://", $hostname, concat('content/view/full/',$object.main_node_id)|ezurl(no) )}

{'A new user has registered.'|i18n('design/standard/user/register')}<br/><br/>


{'Account information.'|i18n('design/standard/user/register')}<br/><br/>


{'Username'|i18n('design/standard/user/register','Login name')}: {$user.login}<br/>

{'Email'|i18n('design/standard/user/register')}: {$user.email}<br/><br/>


{'Link to user information'|i18n('design/standard/user/register')}:<br/>

<a href="{$userlink}">{$userlink}</a><br/><br/>


</body></html>
